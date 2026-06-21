<?php

namespace Tests\Unit;

use App\Services\UssdSessionService;
use App\Ussd\UssdState;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class UssdStateMachineTest extends TestCase
{
    private UssdSessionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        // Use array cache for tests — no Redis required in CI
        config(['cache.default' => 'array']);
        $this->service = app(UssdSessionService::class);
    }

    // ── Welcome step ─────────────────────────────────────────────────────────

    public function test_welcome_returns_con_when_event_is_live(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);

        $state = UssdState::fresh('sess_001', $event->ussd_short_id, $event->id);

        $response = $this->service->menuWelcome($state);

        $this->assertFalse($response->isFinal);
        $this->assertStringContainsString($event->name, $response->text);
        $this->assertStringContainsString('1. Vote', $response->text);
    }

    public function test_welcome_returns_end_when_no_event_found(): void
    {
        $state    = UssdState::fresh('sess_002', '999', null);
        $response = $this->service->menuWelcome($state);

        $this->assertTrue($response->isFinal);
        $this->assertStringContainsString('no active voting event', $response->text);
    }

    public function test_welcome_returns_end_when_event_not_live(): void
    {
        $event = $this->createAwardEvent(['status' => 'draft']);
        $state = UssdState::fresh('sess_003', $event->ussd_short_id, $event->id);

        $response = $this->service->menuWelcome($state);

        $this->assertTrue($response->isFinal);
    }

    // ── Category step ─────────────────────────────────────────────────────────

    public function test_category_step_shows_category_list_on_first_entry(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);

        $state       = UssdState::fresh('sess_004', $event->ussd_short_id, $event->id);
        $state->step = 'category';

        $response = $this->service->menuCategory($state, '1');

        $this->assertFalse($response->isFinal);
        $this->assertStringContainsString($category->code, $response->text);
        $this->assertStringContainsString($category->name, $response->text);
    }

    public function test_category_step_accepts_valid_code(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);

        $state       = UssdState::fresh('sess_005', $event->ussd_short_id, $event->id);
        $state->step = 'category';
        $state->data = ['awaiting_category' => true];

        $response = $this->service->menuCategory($state, $category->code);

        $this->assertFalse($response->isFinal);
        $this->assertStringContainsString('Nominee', $response->text);
        // Should list nominees
        $nominee = $category->nominees()->first();
        $this->assertStringContainsString($nominee->name, $response->text);
    }

    public function test_category_step_rejects_invalid_code(): void
    {
        $event = $this->createAwardEvent();
        $this->createCategoryWithNominees($event);

        $state       = UssdState::fresh('sess_006', $event->ussd_short_id, $event->id);
        $state->step = 'category';
        $state->data = ['awaiting_category' => true];

        $response = $this->service->menuCategory($state, '99');

        $this->assertFalse($response->isFinal);
        $this->assertStringContainsString('Invalid category', $response->text);
    }

    public function test_zero_input_exits_at_any_step(): void
    {
        $event = $this->createAwardEvent();
        $state       = UssdState::fresh('sess_007', $event->ussd_short_id, $event->id);
        $state->step = 'category';

        $response = $this->service->menuCategory($state, '0');

        $this->assertTrue($response->isFinal);
        $this->assertStringContainsString('Goodbye', $response->text);
    }

    // ── Nominee step ──────────────────────────────────────────────────────────

    public function test_nominee_step_accepts_valid_code(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $state       = UssdState::fresh('sess_008', $event->ussd_short_id, $event->id);
        $state->step = 'nominee';
        $state->data = [
            'category_id'   => $category->id,
            'category_name' => $category->name,
        ];

        $response = $this->service->menuNominee($state, $nominee->code);

        $this->assertFalse($response->isFinal);
        // Should ask for quantity (pay_per_vote event)
        $this->assertStringContainsString('How many votes', $response->text);
    }

    public function test_nominee_step_rejects_invalid_code(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);

        $state       = UssdState::fresh('sess_009', $event->ussd_short_id, $event->id);
        $state->step = 'nominee';
        $state->data = [
            'category_id'   => $category->id,
            'category_name' => $category->name,
        ];

        $response = $this->service->menuNominee($state, '99');

        $this->assertFalse($response->isFinal);
        $this->assertStringContainsString('Invalid nominee', $response->text);
    }

    // ── Quantity step ─────────────────────────────────────────────────────────

    public function test_quantity_step_accepts_valid_number(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $state       = UssdState::fresh('sess_010', $event->ussd_short_id, $event->id);
        $state->step = 'quantity';
        $state->data = [
            'category_id'   => $category->id,
            'category_name' => $category->name,
            'nominee_id'    => $nominee->id,
            'nominee_name'  => $nominee->name,
        ];

        $response = $this->service->menuQuantity($state, '5');

        $this->assertFalse($response->isFinal);
        $this->assertStringContainsString('GHS', $response->text);
        $this->assertStringContainsString('5', $response->text);
        $this->assertStringContainsString('Confirm', $response->text);
    }

    public function test_quantity_step_rejects_zero(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $state       = UssdState::fresh('sess_011', $event->ussd_short_id, $event->id);
        $state->step = 'quantity';
        $state->data = [
            'category_id'   => $category->id,
            'category_name' => $category->name,
            'nominee_id'    => $nominee->id,
            'nominee_name'  => $nominee->name,
        ];

        $response = $this->service->menuQuantity($state, '0');

        $this->assertFalse($response->isFinal);
        $this->assertStringContainsString('between 1 and 50', $response->text);
    }

    public function test_quantity_step_rejects_above_limit(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $state       = UssdState::fresh('sess_012', $event->ussd_short_id, $event->id);
        $state->step = 'quantity';
        $state->data = [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'nominee_id'  => $nominee->id,
            'nominee_name'=> $nominee->name,
        ];

        $response = $this->service->menuQuantity($state, '51');

        $this->assertFalse($response->isFinal);
        $this->assertStringContainsString('between 1 and 50', $response->text);
    }

    // ── Confirm step ──────────────────────────────────────────────────────────

    public function test_confirm_cancel_ends_session(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $state       = UssdState::fresh('sess_013', $event->ussd_short_id, $event->id);
        $state->step = 'confirm';
        $state->data = [
            'category_id'    => $category->id,
            'category_name'  => $category->name,
            'nominee_id'     => $nominee->id,
            'nominee_name'   => $nominee->name,
            'quantity'       => 3,
            'amount_pesewas' => 300,
        ];

        $response = $this->service->menuConfirm($state, '2', '0244123456');

        $this->assertTrue($response->isFinal);
        $this->assertStringContainsString('cancelled', $response->text);
    }

    public function test_confirm_creates_pending_payment_and_dispatches_job(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        \Illuminate\Support\Facades\Http::fake(); // never hit Paystack

        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $state       = UssdState::fresh('sess_014', $event->ussd_short_id, $event->id);
        $state->step = 'confirm';
        $state->data = [
            'category_id'    => $category->id,
            'category_name'  => $category->name,
            'nominee_id'     => $nominee->id,
            'nominee_name'   => $nominee->name,
            'quantity'       => 2,
            'amount_pesewas' => 200,
        ];

        $response = $this->service->menuConfirm($state, '1', '0244123456');

        $this->assertTrue($response->isFinal);
        $this->assertStringContainsString('Payment request sent', $response->text);

        // Payment row created
        $this->assertDatabaseHas('payments', [
            'event_id'   => $event->id,
            'status'     => 'pending',
            'phone_number' => '0244123456',
        ]);

        // Job dispatched (not executed in test — Http::fake prevents Paystack call)
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\InitiatePaystackCharge::class);
    }

    // ── Network detection ─────────────────────────────────────────────────────

    public function test_mtn_prefix_detected_correctly(): void
    {
        $service = $this->service;
        $method  = new \ReflectionMethod($service, 'detectNetwork');
        $method->setAccessible(true);

        $this->assertSame('mtn',        $method->invoke($service, '0244123456'));
        $this->assertSame('mtn',        $method->invoke($service, '0554123456'));
        $this->assertSame('vodafone',   $method->invoke($service, '0201234567'));
        $this->assertSame('vodafone',   $method->invoke($service, '0501234567'));
        $this->assertSame('airteltigo', $method->invoke($service, '0261234567'));
        $this->assertSame('airteltigo', $method->invoke($service, '0271234567'));
        // International format
        $this->assertSame('mtn',        $method->invoke($service, '+233244123456'));
        $this->assertSame('vodafone',   $method->invoke($service, '233201234567'));
    }
}
