<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Vote;
use App\Services\VoteIntegrityService;
use Tests\TestCase;

class VoteIntegrityTest extends TestCase
{
    private VoteIntegrityService $integrity;

    protected function setUp(): void
    {
        parent::setUp();
        $this->integrity = app(VoteIntegrityService::class);
    }

    public function test_clean_event_has_no_violations(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $payment = Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'success',
            'provider_reference' => 'ps_ok_001',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
        ]);

        Vote::factory()->create([
            'event_id'   => $event->id,
            'nominee_id' => $nominee->id,
            'category_id'=> $category->id,
            'payment_id' => $payment->id,
            'quantity'   => 1,
        ]);

        $violations = $this->integrity->verify($event);

        $this->assertEmpty($violations);
    }

    public function test_detects_vote_on_non_success_payment(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $payment = Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'pending',
            'provider_reference' => 'ps_pending',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
        ]);

        Vote::factory()->create([
            'event_id'   => $event->id,
            'nominee_id' => $nominee->id,
            'category_id'=> $category->id,
            'payment_id' => $payment->id,
            'quantity'   => 1,
        ]);

        $violations = $this->integrity->verify($event);

        $this->assertNotEmpty($violations);
        $this->assertTrue($violations->pluck('type')->contains('vote_on_non_success_payment'));
    }

    public function test_detects_quantity_mismatch(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $payment = Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 500,
            'status'             => 'success',
            'provider_reference' => 'ps_qty_mismatch',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 5],
        ]);

        // Vote says 3 but payment metadata says 5
        Vote::factory()->create([
            'event_id'   => $event->id,
            'nominee_id' => $nominee->id,
            'category_id'=> $category->id,
            'payment_id' => $payment->id,
            'quantity'   => 3,
        ]);

        $violations = $this->integrity->verify($event);

        $this->assertTrue($violations->pluck('type')->contains('quantity_mismatch'));
    }

    public function test_detects_orphaned_vote(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        // A vote on a paid campaign with no payment behind it. A dangling
        // payment_id cannot be created — the foreign key forbids it — so the
        // detectable case is a null one.
        Vote::factory()->create([
            'event_id'    => $event->id,
            'nominee_id'  => $nominee->id,
            'category_id' => $category->id,
            'payment_id'  => null,
            'quantity'    => 1,
        ]);

        $violations = $this->integrity->verify($event);

        $this->assertTrue($violations->pluck('type')->contains('orphaned_vote'));
    }

    public function test_a_free_campaign_vote_without_payment_is_not_an_orphan(): void
    {
        // Free campaigns (USSD or web) record votes with no payment at all,
        // so the orphan check must not fire on every one of them.
        $event = $this->createAwardEvent([
            'voting_rules' => [
                'pay_per_vote'           => false,
                'price_per_vote_pesewas' => 0,
            ],
        ]);
        $category = $this->createCategoryWithNominees($event);

        Vote::factory()->create([
            'event_id'    => $event->id,
            'nominee_id'  => $category->nominees()->first()->id,
            'category_id' => $category->id,
            'payment_id'  => null,
            'quantity'    => 1,
        ]);

        $violations = $this->integrity->verify($event);

        $this->assertFalse($violations->pluck('type')->contains('orphaned_vote'));
    }

    public function test_detects_uncredited_successful_payment(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        // Successful payment but no vote row. The check allows a 10-minute
        // grace period for a webhook still in flight, so age it past that.
        Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'success',
            'provider_reference' => 'ps_uncredited',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
            'created_at'         => now()->subMinutes(15),
        ]);

        $violations = $this->integrity->verify($event);

        $this->assertTrue($violations->pluck('type')->contains('uncredited_payment'));
    }

    public function test_ledger_replay_totals_match_vote_table(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominees = $category->nominees()->take(2)->get();

        foreach ($nominees as $index => $nominee) {
            $qty     = ($index + 1) * 3;
            $payment = Payment::factory()->create([
                'event_id'           => $event->id,
                'amount_pesewas'     => $qty * 100,
                'status'             => 'success',
                'provider_reference' => "ps_replay_{$index}",
                'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => $qty],
            ]);

            Vote::factory()->create([
                'event_id'   => $event->id,
                'nominee_id' => $nominee->id,
                'category_id'=> $category->id,
                'payment_id' => $payment->id,
                'quantity'   => $qty,
            ]);
        }

        $replay = $this->integrity->ledgerReplay($event);

        foreach ($nominees as $nominee) {
            $dbTotal     = Vote::where('nominee_id', $nominee->id)->sum('quantity');
            $replayTotal = $replay->firstWhere('nominee_id', $nominee->id)?->total_votes ?? 0;

            $this->assertEquals($dbTotal, $replayTotal);
        }
    }
}
