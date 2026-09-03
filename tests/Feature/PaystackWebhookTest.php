<?php

namespace Tests\Feature;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PaystackWebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Http::fake();
    }

    // ── Signature verification ────────────────────────────────────────────────

    public function test_bad_signature_returns_200_but_credits_no_votes(): void
    {
        // Paystack best-practice: always return 200 (otherwise they retry forever)
        // but take no action on unsigned payloads.
        $payload = $this->paystackChargeSuccessPayload('ref_bad_sig', 100);

        $response = $this->withHeaders(['X-Paystack-Signature' => 'invalidsignature'])
            ->postJson('/api/webhooks/paystack', $payload);

        $response->assertOk();
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_missing_signature_header_returns_200_but_credits_no_votes(): void
    {
        $payload = $this->paystackChargeSuccessPayload('ref_no_sig', 100);

        $response = $this->postJson('/api/webhooks/paystack', $payload);

        $response->assertOk();
        $this->assertDatabaseCount('votes', 0);
    }

    // ── charge.success ────────────────────────────────────────────────────────

    public function test_charge_success_credits_votes_and_marks_payment(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $payment = Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 200,
            'status'             => 'pending',
            'provider_reference' => 'ps_ref_001',
            'phone_number'       => '0244123456',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 2],
        ]);

        $response = $this->postPaystackWebhook(
            $this->paystackChargeSuccessPayload('ps_ref_001', 200, '0244123456')
        );

        $response->assertOk();

        $this->assertDatabaseHas('payments', [
            'id'     => $payment->id,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('votes', [
            'event_id'    => $event->id,
            'nominee_id'  => $nominee->id,
            'payment_id'  => $payment->id,
            'quantity'    => 2,
            'voter_phone' => '0244123456',
        ]);

    }

    public function test_charge_success_is_idempotent_on_duplicate_delivery(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'pending',
            'provider_reference' => 'ps_ref_dupe',
            'phone_number'       => '0244123456',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
        ]);

        $webhook = $this->paystackChargeSuccessPayload('ps_ref_dupe', 100, '0244123456');

        // Deliver twice
        $this->postPaystackWebhook($webhook)->assertOk();
        $this->postPaystackWebhook($webhook)->assertOk();

        // Exactly one vote row
        $this->assertDatabaseCount('votes', 1);
    }

    public function test_charge_success_with_unknown_reference_is_ignored(): void
    {
        $response = $this->postPaystackWebhook(
            $this->paystackChargeSuccessPayload('ref_unknown_xyz', 100)
        );

        // 200, not 404: an unknown reference is acknowledged so Paystack
        // stops retrying a webhook we will never be able to settle.
        $response->assertOk();
        $this->assertDatabaseCount('votes', 0);
    }

    public function test_charge_success_amount_mismatch_does_not_credit_votes(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 500,
            'status'             => 'pending',
            'provider_reference' => 'ps_ref_mismatch',
            'phone_number'       => '0244123456',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 5],
        ]);

        // Paystack reports only 100 pesewas (wrong amount)
        $response = $this->postPaystackWebhook(
            $this->paystackChargeSuccessPayload('ps_ref_mismatch', 100)
        );

        $response->assertOk();
        $this->assertDatabaseCount('votes', 0);

        // An underpayment is settled as failed rather than left pending, so a
        // later correct webhook cannot credit votes that were never paid for.
        $this->assertDatabaseHas('payments', [
            'provider_reference' => 'ps_ref_mismatch',
            'status'             => 'failed',
        ]);
    }

    // ── charge.failed ─────────────────────────────────────────────────────────

    public function test_charge_failed_marks_payment_as_failed(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'pending',
            'provider_reference' => 'ps_ref_fail',
            'phone_number'       => '0244123456',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
        ]);

        $response = $this->postPaystackWebhook(
            $this->paystackChargeFailedPayload('ps_ref_fail')
        );

        $response->assertOk();

        $this->assertDatabaseHas('payments', [
            'provider_reference' => 'ps_ref_fail',
            'status'             => 'failed',
        ]);

        $this->assertDatabaseCount('votes', 0);
    }

    public function test_charge_failed_is_idempotent(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'pending',
            'provider_reference' => 'ps_ref_fail_dupe',
            'phone_number'       => '0244123456',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
        ]);

        $webhook = $this->paystackChargeFailedPayload('ps_ref_fail_dupe');

        $this->postPaystackWebhook($webhook)->assertOk();
        $this->postPaystackWebhook($webhook)->assertOk(); // second delivery — must not throw

        $this->assertDatabaseHas('payments', [
            'provider_reference' => 'ps_ref_fail_dupe',
            'status'             => 'failed',
        ]);
    }

    // ── Unhandled event type ──────────────────────────────────────────────────

    public function test_unhandled_event_type_returns_200(): void
    {
        $payload = ['event' => 'transfer.success', 'data' => ['reference' => 'ref_x']];

        $response = $this->postPaystackWebhook($payload);

        $response->assertOk();
    }

    // ── Anonymous tally — voter_phone not stored ──────────────────────────────

    public function test_charge_success_does_not_store_phone_for_anonymous_tally_event(): void
    {
        $event = $this->createAwardEvent([
            'voting_rules' => [
                'pay_per_vote'              => true,
                'price_per_vote_pesewas'    => 100,
                'max_votes_per_voter'       => null,
                'requires_eligibility_list' => false,
                'anonymous_tally'           => true,
            ],
        ]);
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'pending',
            'provider_reference' => 'ps_ref_anon',
            'phone_number'       => '0244123456',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
        ]);

        $this->postPaystackWebhook(
            $this->paystackChargeSuccessPayload('ps_ref_anon', 100, '0244123456')
        )->assertOk();

        // voter_phone must be NULL for anonymous_tally events
        $vote = \App\Models\Vote::first();
        $this->assertNull($vote->voter_phone);
    }
}
