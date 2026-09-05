<?php

namespace Tests\Feature;

use App\Livewire\Vote\Ballot;
use App\Livewire\Vote\NomineeVote;
use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The web voting channel: the Livewire ballot, the nominee-page panel, and
 * settlement of the Paystack charge webhook into a Vote.
 */
class WebVotingPaymentTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    private Category $category;

    private Nominee $nominee;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.paystack.secret' => 'sk_test_secret', 'services.paystack.public_key' => 'pk_test_key']);

        $org = Organization::create([
            'name'          => 'Ghana Music Awards Ltd',
            'contact_email' => 'awards@example.test',
        ]);

        $this->event = Event::create([
            'organization_id' => $org->id,
            'name'            => 'Test Music Awards',
            'slug'            => 'test-music-awards',
            'event_type'      => 'award',
            'starts_at'       => now()->subDay(),
            'ends_at'         => now()->addDays(7),
            'status'          => 'live',
            'voting_rules'    => [
                'pay_per_vote'           => true,
                'price_per_vote_pesewas' => 100,
                'max_votes_per_voter'    => null,
            ],
        ]);

        $this->category = Category::create([
            'event_id'      => $this->event->id,
            'name'          => 'Artiste of the Year',
            'code'          => 'AOTY',
            'display_order' => 1,
        ]);

        $this->nominee = Nominee::create([
            'category_id'   => $this->category->id,
            'name'          => 'Sarkodie',
            'code'          => '01',
            'display_order' => 1,
        ]);
    }

    /** POST a Paystack charge event with a valid signature. */
    private function webhook(string $reference, int $amount, string $event = 'charge.success', bool $forge = false)
    {
        $payload = [
            'event' => $event,
            'data'  => [
                'reference' => $reference,
                'amount'    => $amount,
                'currency'  => 'GHS',
                'status'    => $event === 'charge.success' ? 'success' : 'failed',
            ],
        ];

        $body = json_encode($payload);
        $sig  = $forge
            ? str_repeat('0', 128)
            : hash_hmac('sha512', $body, config('services.paystack.secret'));

        return $this->call('POST', '/api/webhooks/paystack', [], [], [], [
            'CONTENT_TYPE'              => 'application/json',
            'HTTP_X_PAYSTACK_SIGNATURE' => $sig,
        ], $body);
    }

    private function pendingPayment(string $reference, int $amount = 300, int $quantity = 3): Payment
    {
        return Payment::create([
            'event_id'           => $this->event->id,
            'provider'           => 'paystack',
            'provider_reference' => $reference,
            'amount_pesewas'     => $amount,
            'currency'           => 'GHS',
            'phone_number'       => '0244123456',
            'momo_network'       => 'mtn',
            'status'             => 'pending',
            'metadata'           => [
                'nominee_id'  => $this->nominee->id,
                'category_id' => $this->category->id,
                'quantity'    => $quantity,
                'channel'     => 'web',
            ],
        ]);
    }

    // ── The ballot ───────────────────────────────────────────────────────

    public function test_the_ballot_page_loads_for_a_live_event(): void
    {
        $this->get("/vote/events/{$this->event->slug}")
            ->assertOk()
            ->assertSee($this->event->name);
    }

    public function test_choosing_a_nominee_moves_to_confirmation(): void
    {
        Livewire::test(Ballot::class, ['event' => $this->event])
            ->assertSet('step', 'browse')
            ->call('selectNominee', $this->nominee->id)
            ->assertSet('step', 'confirm')
            ->assertSet('selectedNomineeId', $this->nominee->id);
    }

    public function test_the_quantity_stepper_drives_the_amount(): void
    {
        Livewire::test(Ballot::class, ['event' => $this->event])
            ->call('selectNominee', $this->nominee->id)
            ->assertSet('quantity', 1)
            ->call('increment')->call('increment')
            ->assertSet('quantity', 3)
            ->call('decrement')
            ->assertSet('quantity', 2);
    }

    public function test_the_stepper_is_clamped_at_both_ends(): void
    {
        $component = Livewire::test(Ballot::class, ['event' => $this->event])
            ->call('selectNominee', $this->nominee->id)
            ->call('decrement')
            ->assertSet('quantity', 1);

        $component->set('quantity', 999)->assertSet('quantity', Ballot::MAX_QUANTITY);
    }

    public function test_an_invalid_phone_creates_no_payment(): void
    {
        Livewire::test(Ballot::class, ['event' => $this->event])
            ->call('selectNominee', $this->nominee->id)
            ->set('phone', '123')
            ->call('proceedToPayment')
            ->assertHasErrors('phone')
            ->assertSet('step', 'confirm');

        $this->assertSame(0, Payment::count());
    }

    public function test_a_valid_ballot_opens_the_paystack_popup(): void
    {
        $component = Livewire::test(Ballot::class, ['event' => $this->event])
            ->call('selectNominee', $this->nominee->id)
            ->call('increment')->call('increment')   // 3 votes
            ->set('phone', '0244123456')
            ->call('proceedToPayment')
            ->assertHasNoErrors()
            ->assertSet('step', 'paying')
            ->assertDispatched('open-paystack-popup');

        $payment = Payment::sole();
        $this->assertSame('paystack', $payment->provider);
        $this->assertSame('pending', $payment->status);
        $this->assertSame(300, $payment->amount_pesewas);   // 3 x GHS 1.00
        $this->assertSame('web', $payment->metadata['channel']);
        $this->assertSame(3, $payment->metadata['quantity']);
        $this->assertSame($payment->provider_reference, $component->get('pendingReference'));

        // No vote until the provider confirms.
        $this->assertSame(0, Vote::count());
    }

    public function test_the_nominee_page_panel_also_takes_a_vote(): void
    {
        Livewire::test(NomineeVote::class, ['nominee' => $this->nominee])
            ->call('setQuantity', 10)
            ->set('phone', '0244123456')
            ->call('proceedToPayment')
            ->assertHasNoErrors()
            ->assertSet('step', 'paying');

        $payment = Payment::sole();
        $this->assertSame(1000, $payment->amount_pesewas);
        $this->assertSame($this->nominee->id, $payment->metadata['nominee_id']);
    }

    // ── Settlement ───────────────────────────────────────────────────────

    public function test_a_confirmed_charge_credits_the_vote(): void
    {
        $payment = $this->pendingPayment('ps_ok_1');

        $this->webhook('ps_ok_1', 300)->assertOk();

        $vote = Vote::sole();
        $this->assertSame(3, $vote->quantity);
        $this->assertSame('web', $vote->channel);
        $this->assertSame($this->nominee->id, $vote->nominee_id);
        $this->assertSame('0244123456', $vote->voter_phone);
        $this->assertSame('success', $payment->fresh()->status);
    }

    public function test_a_replayed_webhook_does_not_double_credit(): void
    {
        $this->pendingPayment('ps_replay_1');

        $this->webhook('ps_replay_1', 300)->assertOk();
        $this->webhook('ps_replay_1', 300)->assertOk();

        $this->assertSame(1, Vote::count());
    }

    public function test_an_underpayment_is_refused(): void
    {
        $payment = $this->pendingPayment('ps_short_1');

        $this->webhook('ps_short_1', 100)->assertOk();

        $this->assertSame(0, Vote::count());
        $this->assertSame('failed', $payment->fresh()->status);
    }

    public function test_an_underpaid_reference_cannot_be_settled_later(): void
    {
        // Regression: a payment failed for underpayment used to be creditable
        // by a follow-up webhook carrying the correct amount.
        $payment = $this->pendingPayment('ps_resurrect_1');

        $this->webhook('ps_resurrect_1', 100)->assertOk();   // fails it
        $this->webhook('ps_resurrect_1', 300)->assertOk();   // must not credit

        $this->assertSame(0, Vote::count());
        $this->assertSame('failed', $payment->fresh()->status);
    }

    public function test_a_failed_charge_cannot_be_settled_later(): void
    {
        $payment = $this->pendingPayment('ps_failed_1');

        $this->webhook('ps_failed_1', 300, 'charge.failed')->assertOk();
        $this->webhook('ps_failed_1', 300)->assertOk();

        $this->assertSame(0, Vote::count());
        $this->assertSame('failed', $payment->fresh()->status);
    }

    public function test_a_forged_signature_changes_nothing(): void
    {
        $payment = $this->pendingPayment('ps_forged_1');

        $this->webhook('ps_forged_1', 300, forge: true);

        $this->assertSame(0, Vote::count());
        $this->assertSame('pending', $payment->fresh()->status);
    }

    public function test_an_unknown_reference_credits_nothing(): void
    {
        $this->webhook('ps_ghost_1', 300)->assertOk();

        $this->assertSame(0, Vote::count());
    }

    public function test_an_anonymous_tally_does_not_store_the_voter_phone(): void
    {
        $rules = $this->event->voting_rules;
        $rules['anonymous_tally'] = true;
        $this->event->update(['voting_rules' => $rules]);

        $this->pendingPayment('ps_anon_1');
        $this->webhook('ps_anon_1', 300)->assertOk();

        $this->assertNull(Vote::sole()->voter_phone);
    }

    // ── Confirmation & receipt ───────────────────────────────────────────

    public function test_the_confirmation_page_shows_the_transaction(): void
    {
        $this->pendingPayment('ps_conf_1');
        $this->webhook('ps_conf_1', 300);

        $this->get('/vote/confirmed?ref=ps_conf_1')
            ->assertOk()
            ->assertSee('Vote Successful!')
            ->assertSee($this->nominee->name)
            ->assertSee('ps_conf_1');
    }

    public function test_a_pending_payment_shows_as_processing(): void
    {
        $this->pendingPayment('ps_pending_1');

        $this->get('/vote/confirmed?ref=ps_pending_1')
            ->assertOk()
            ->assertSee('Payment processing');
    }

    public function test_a_receipt_pdf_is_downloadable(): void
    {
        $this->pendingPayment('ps_receipt_1');
        $this->webhook('ps_receipt_1', 300);

        $response = $this->get('/vote/receipt/ps_receipt_1');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
    }

    public function test_a_receipt_for_an_unknown_reference_is_not_found(): void
    {
        $this->get('/vote/receipt/ps_nothing_here')->assertNotFound();
    }
}
