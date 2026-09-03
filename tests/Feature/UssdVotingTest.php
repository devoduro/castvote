<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UssdVotingTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    private Category $category;

    private Nominee $nominee;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.speso.api_key'        => 'sk_test_dummy',
            'services.speso.webhook_secret' => 'whsec_test',
            'services.speso.base_url'       => 'https://business.speso.co/api/v1',
        ]);

        $org = Organization::create([
            'name'          => 'Ghana Music Awards Ltd',
            'contact_email' => 'awards@example.test',
        ]);

        $this->event = Event::create([
            'organization_id' => $org->id,
            'name'            => 'Test Music Awards',
            'slug'            => 'test-music-awards',
            'event_type'      => 'award',
            'ussd_short_id'   => '240',
            'ussd_shortcode'  => '*928*240#',
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

    /** Drive one step of the USSD session. */
    private function dial(string $input, string $type = 'response', string $session = 'sess-1')
    {
        return $this->postJson('/api/ussd/callback', [
            'session_id'   => $session,
            'msisdn'       => '233244123456',
            'network'      => 'MTN',
            'service_code' => '*928*240#',
            'input'        => $input,
            'type'         => $type,
        ]);
    }

    public function test_dialling_the_shortcode_shows_the_campaign_menu(): void
    {
        $response = $this->dial('', 'initiation');

        $response->assertOk()
            ->assertJsonPath('action', 'prompt')
            ->assertJsonFragment(['action' => 'prompt']);

        $this->assertStringContainsString('Test Music Awards', $response->json('message'));
        $this->assertStringContainsString('1. Vote', $response->json('message'));
    }

    public function test_menu_uses_bare_line_feeds(): void
    {
        // The package builds menus with PHP_EOL (CRLF on Windows); gateways
        // expect LF, and a stray CR renders as a box on some handsets.
        $message = $this->dial('', 'initiation')->json('message');

        $this->assertStringNotContainsString("\r", $message);
        $this->assertStringContainsString("\n1. Vote", $message);
    }

    public function test_unknown_shortcode_is_turned_away(): void
    {
        $response = $this->postJson('/api/ussd/callback', [
            'session_id'   => 'sess-unknown',
            'msisdn'       => '233244123456',
            'service_code' => '*928*999#',
            'input'        => '',
            'type'         => 'initiation',
        ]);

        $response->assertOk()->assertJsonPath('action', 'end');
        $this->assertStringContainsString('No voting campaign is open', $response->json('message'));
    }

    public function test_full_vote_flow_creates_a_pending_speso_collection(): void
    {
        Http::fake([
            '*/collections' => Http::response(['success' => true, 'speso_reference' => 'SPS_123'], 200),
        ]);

        $this->dial('', 'initiation');       // welcome
        $this->dial('1');                    // vote
        $this->dial('1');                    // category 1
        $this->dial('1');                    // nominee 1
        $this->dial('5');                    // quantity

        $confirm = $this->dial('1');         // confirm

        $confirm->assertOk()->assertJsonPath('action', 'end');
        $this->assertStringContainsString('Approve the Mobile Money prompt', $confirm->json('message'));

        $payment = Payment::sole();
        $this->assertSame('speso', $payment->provider);
        $this->assertSame('pending', $payment->status);
        $this->assertSame(500, $payment->amount_pesewas);
        $this->assertSame('0244123456', $payment->phone_number);
        $this->assertSame('ussd', $payment->metadata['channel']);
        $this->assertSame($this->nominee->id, $payment->metadata['nominee_id']);
        $this->assertSame(5, $payment->metadata['quantity']);

        // The vote is only credited once Speso confirms the collection.
        $this->assertSame(0, Vote::count());

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/collections')
            && $request['order_id'] === $payment->provider_reference
            && (float) $request['amount'] === 5.0
            && $request['network'] === 'MTN');
    }

    public function test_quantity_must_be_within_range(): void
    {
        $this->dial('', 'initiation');
        $this->dial('1');
        $this->dial('1');
        $this->dial('1');

        $response = $this->dial('999');

        $response->assertOk()->assertJsonPath('action', 'prompt');
        $this->assertStringContainsString('Enter a number between 1 and 50', $response->json('message'));
    }

    public function test_cancelling_at_confirmation_creates_no_payment(): void
    {
        $this->dial('', 'initiation');
        $this->dial('1');
        $this->dial('1');
        $this->dial('1');
        $this->dial('2');

        $response = $this->dial('0');

        $response->assertOk()->assertJsonPath('action', 'end');
        $this->assertStringContainsString('cancelled', $response->json('message'));
        $this->assertSame(0, Payment::count());
    }

    public function test_speso_webhook_credits_the_vote(): void
    {
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        $this->dial('', 'initiation');
        $this->dial('1');
        $this->dial('1');
        $this->dial('1');
        $this->dial('3');
        $this->dial('1');

        $payment = Payment::sole();

        $this->postSignedWebhook([
            'order_id'        => $payment->provider_reference,
            'speso_reference' => 'SPS_123',
            'status'          => 'completed',
            'amount'          => 3.00,
        ])->assertOk();

        $vote = Vote::sole();
        $this->assertSame(3, $vote->quantity);
        $this->assertSame('ussd', $vote->channel);
        $this->assertSame($this->nominee->id, $vote->nominee_id);
        $this->assertSame('success', $payment->fresh()->status);
    }

    public function test_speso_webhook_is_idempotent(): void
    {
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        $this->dial('', 'initiation');
        $this->dial('1');
        $this->dial('1');
        $this->dial('1');
        $this->dial('2');
        $this->dial('1');

        $payload = [
            'order_id'        => Payment::sole()->provider_reference,
            'speso_reference' => 'SPS_123',
            'status'          => 'completed',
            'amount'          => 2.00,
        ];

        $this->postSignedWebhook($payload)->assertOk();
        $this->postSignedWebhook($payload)->assertOk();

        $this->assertSame(1, Vote::count());
    }

    public function test_speso_webhook_rejects_an_amount_mismatch(): void
    {
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        $this->dial('', 'initiation');
        $this->dial('1');
        $this->dial('1');
        $this->dial('1');
        $this->dial('4');
        $this->dial('1');

        $this->postSignedWebhook([
            'order_id'        => Payment::sole()->provider_reference,
            'speso_reference' => 'SPS_123',
            'status'          => 'completed',
            'amount'          => 1.00, // paid less than the ballot cost
        ])->assertOk();

        $this->assertSame(0, Vote::count());
        $this->assertSame('failed', Payment::sole()->status);
    }

    public function test_speso_webhook_rejects_an_unsigned_request(): void
    {
        $this->postJson('/api/webhooks/speso', [
            'order_id'        => 'cv_whatever',
            'speso_reference' => 'SPS_123',
            'status'          => 'completed',
            'amount'          => 1.00,
        ])->assertStatus(401);
    }

    private function postSignedWebhook(array $payload)
    {
        $body      = json_encode($payload);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$body, config('services.speso.webhook_secret'));

        return $this->call(
            'POST',
            '/api/webhooks/speso',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'          => 'application/json',
                'HTTP_ACCEPT'           => 'application/json',
                'HTTP_X_SPESO_SIGNATURE' => "t={$timestamp},v1={$signature}",
            ],
            $body,
        );
    }
}
