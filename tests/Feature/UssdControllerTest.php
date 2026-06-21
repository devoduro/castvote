<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UssdControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        Http::fake(); // never hit Arkesel/Paystack for real
    }

    // ── Text-mode (Arkesel default) ───────────────────────────────────────────

    public function test_initial_dial_returns_con_with_event_name(): void
    {
        $event = $this->createAwardEvent(['ussd_short_id' => '240']);
        $this->createCategoryWithNominees($event);

        $payload = $this->arkeselPayload('s001', '*928*240#', '0244123456', '');

        $response = $this->postJson('/api/ussd/callback', $payload);

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringStartsWith('CON ', $body);
        $this->assertStringContainsString($event->name, $body);
    }

    public function test_second_step_selecting_vote_shows_categories(): void
    {
        $event    = $this->createAwardEvent(['ussd_short_id' => '240']);
        $category = $this->createCategoryWithNominees($event);

        // First request (empty text = initial dial)
        $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s002', '*928*240#', '0244123456', ''));

        // Second request (user pressed 1 = Vote)
        $response = $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s002', '*928*240#', '0244123456', '1'));

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringStartsWith('CON ', $body);
        $this->assertStringContainsString($category->name, $body);
    }

    public function test_full_happy_path_ends_with_payment_pending_message(): void
    {
        Queue::fake();

        $event    = $this->createAwardEvent(['ussd_short_id' => '240']);
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();
        $phone    = '0244123456';
        $svcCode  = '*928*240#';

        // Step 1 — initial
        $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s003', $svcCode, $phone, ''));

        // Step 2 — choose Vote (1)
        $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s003', $svcCode, $phone, '1'));

        // Step 3 — choose category code
        $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s003', $svcCode, $phone, $category->code));

        // Step 4 — choose nominee code
        $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s003', $svcCode, $phone, $nominee->code));

        // Step 5 — choose quantity
        $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s003', $svcCode, $phone, '2'));

        // Step 6 — confirm (1)
        $response = $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s003', $svcCode, $phone, '1'));

        $response->assertOk();
        $body = $response->getContent();
        $this->assertStringStartsWith('END ', $body);
        $this->assertStringContainsString('Payment request sent', $body);

        $this->assertDatabaseHas('payments', [
            'event_id'    => $event->id,
            'status'      => 'pending',
            'phone_number' => $phone,
        ]);

        Queue::assertPushed(\App\Jobs\InitiatePaystackCharge::class);
    }

    public function test_entering_zero_cancels_at_any_step(): void
    {
        $event = $this->createAwardEvent(['ussd_short_id' => '240']);
        $this->createCategoryWithNominees($event);

        // Initial dial
        $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s004', '*928*240#', '0244123456', ''));

        // Press 0 instead of 1
        $response = $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s004', '*928*240#', '0244123456', '0'));

        $response->assertOk();
        $this->assertStringStartsWith('END ', $response->getContent());
    }

    public function test_unknown_short_id_returns_end(): void
    {
        $response = $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s005', '*928*999#', '0244123456', ''));

        $response->assertOk();
        $this->assertStringStartsWith('END ', $response->getContent());
    }

    public function test_closed_event_returns_end(): void
    {
        $event = $this->createAwardEvent([
            'ussd_short_id' => '240',
            'status'        => 'closed',
        ]);

        $response = $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s006', '*928*240#', '0244123456', ''));

        $response->assertOk();
        $this->assertStringStartsWith('END ', $response->getContent());
    }

    // ── JSON-mode (some Arkesel configurations) ───────────────────────────────

    public function test_json_mode_payload_returns_con_json(): void
    {
        config(['services.arkesel.ussd_mode' => 'json']);

        $event = $this->createAwardEvent(['ussd_short_id' => '240']);
        $this->createCategoryWithNominees($event);

        $payload = [
            'msisdn'      => '0244123456',
            'userData'    => '',
            'newSession'  => true,
            'sessionId'   => 'json_sess_001',
            'serviceCode' => '*928*240#',
        ];

        $response = $this->postJson('/api/ussd/callback', $payload);

        $response->assertOk()->assertJsonStructure(['continueSession', 'message']);
        $this->assertTrue($response->json('continueSession'));
        $this->assertStringContainsString($event->name, $response->json('message'));
    }

    // ── Rate limiting ─────────────────────────────────────────────────────────

    public function test_rate_limiter_blocks_after_threshold(): void
    {
        $event = $this->createAwardEvent(['ussd_short_id' => '240']);
        $this->createCategoryWithNominees($event);

        // 120 per minute is the limit — send 121 and expect a 429
        for ($i = 0; $i < 120; $i++) {
            $this->postJson('/api/ussd/callback',
                $this->arkeselPayload("s_flood_{$i}", '*928*240#', '0244999999', ''));
        }

        $response = $this->postJson('/api/ussd/callback',
            $this->arkeselPayload('s_flood_121', '*928*240#', '0244999999', ''));

        $response->assertStatus(429);
    }
}
