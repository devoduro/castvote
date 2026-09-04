<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Vote;
use App\Ussd\Responses\GatewayResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Covers the USSD voting flow end to end against Nalo's contract:
 *   IN  { USERID, MSISDN, USERDATA, MSGTYPE, NETWORK, SESSIONID }
 *   OUT { USERID, MSISDN, USERDATA, MSG, MSGTYPE }
 * MSGTYPE is true for a first request inbound, and true for "keep the session
 * open" outbound.
 */
class UssdVotingTest extends TestCase
{
    use RefreshDatabase;

    private const USER_ID = 'NALOTest';

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
            'services.nalo.user_id'         => null,
            'ussd.response_format'          => 'nalo',
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

    /** Drive one step of a Nalo USSD session. */
    private function dial(string $userData, bool $firstRequest = false, string $session = 'sess-1', string $userId = self::USER_ID)
    {
        return $this->postJson('/api/ussd/callback', [
            'USERID'    => $userId,
            'MSISDN'    => '233244123456',
            'USERDATA'  => $userData,
            'MSGTYPE'   => $firstRequest,
            'NETWORK'   => 'MTN',
            'SESSIONID' => $session,
        ]);
    }

    // ── Contract ─────────────────────────────────────────────────────────

    public function test_dialling_returns_nalo_shaped_response(): void
    {
        $response = $this->dial('', firstRequest: true);

        $response->assertOk()
            ->assertJsonPath('USERID', self::USER_ID)
            ->assertJsonPath('MSISDN', '233244123456')
            ->assertJsonPath('MSGTYPE', true);   // keep the session open

        $this->assertStringContainsString('Test Music Awards', $response->json('MSG'));
        $this->assertStringContainsString('1. Vote', $response->json('MSG'));
    }

    public function test_a_terminal_screen_sets_msgtype_false(): void
    {
        $this->dial('', firstRequest: true);

        $response = $this->dial('0');   // Exit

        $response->assertOk()->assertJsonPath('MSGTYPE', false);
        $this->assertStringContainsString('Thank you', $response->json('MSG'));
    }

    public function test_every_screen_fits_nalo_message_limit(): void
    {
        $screens = [
            $this->dial('', firstRequest: true),
            $this->dial('1'),   // vote -> categories
            $this->dial('1'),   // category -> nominees
            $this->dial('1'),   // nominee -> quantity
            $this->dial('2'),   // quantity -> confirm
        ];

        foreach ($screens as $i => $screen) {
            $message = $screen->json('MSG');

            $this->assertLessThanOrEqual(
                GatewayResponse::MAX_MESSAGE,
                mb_strlen($message),
                "Screen {$i} exceeds Nalo's ".GatewayResponse::MAX_MESSAGE.'-character limit'
            );
        }
    }

    public function test_menu_uses_bare_line_feeds(): void
    {
        // The package builds menus with PHP_EOL (CRLF on Windows); gateways
        // expect LF, and a stray CR renders as a box on some handsets.
        $message = $this->dial('', firstRequest: true)->json('MSG');

        $this->assertStringNotContainsString("\r", $message);
        $this->assertStringContainsString("\n1. Vote", $message);
    }

    public function test_a_wrong_user_id_is_rejected_when_one_is_configured(): void
    {
        config(['services.nalo.user_id' => self::USER_ID]);

        $response = $this->dial('', firstRequest: true, userId: 'ATTACKER');

        $response->assertOk()->assertJsonPath('MSGTYPE', false);
        $this->assertStringContainsString('unavailable', $response->json('MSG'));
    }

    public function test_sessions_are_keyed_separately_per_caller(): void
    {
        // USERID is shared across every caller on the integration, so it must
        // never be used as the session key.
        $this->dial('', firstRequest: true, session: 'caller-a');
        $this->dial('1', session: 'caller-a');   // caller A is on the category screen

        $b = $this->dial('', firstRequest: true, session: 'caller-b');

        $this->assertStringContainsString('1. Vote', $b->json('MSG'));
        $this->assertStringNotContainsString('Select a category', $b->json('MSG'));
    }

    // ── Voting flow ──────────────────────────────────────────────────────

    public function test_full_vote_flow_creates_a_pending_speso_collection(): void
    {
        Http::fake([
            '*/collections' => Http::response(['success' => true, 'speso_reference' => 'SPS_123'], 200),
        ]);

        $this->dial('', firstRequest: true);
        $this->dial('1');    // vote
        $this->dial('1');    // category 1
        $this->dial('1');    // nominee 1
        $this->dial('5');    // quantity

        $confirm = $this->dial('1');

        $confirm->assertOk()->assertJsonPath('MSGTYPE', false);
        $this->assertStringContainsString('Approve the Mobile Money prompt', $confirm->json('MSG'));

        $payment = Payment::sole();
        $this->assertSame('speso', $payment->provider);
        $this->assertSame('pending', $payment->status);
        $this->assertSame(500, $payment->amount_pesewas);
        $this->assertSame('0244123456', $payment->phone_number);
        $this->assertSame('ussd', $payment->metadata['channel']);
        $this->assertSame($this->nominee->id, $payment->metadata['nominee_id']);
        $this->assertSame(5, $payment->metadata['quantity']);

        // The vote is only credited once the provider confirms the collection.
        $this->assertSame(0, Vote::count());

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/collections')
            && $request['order_id'] === $payment->provider_reference
            && (float) $request['amount'] === 5.0
            && $request['network'] === 'MTN');
    }

    public function test_my_votes_branch_renders(): void
    {
        // Regression: RouteWelcomeAction used to return ShowMyVotesAction, but
        // the machine runs exactly one Action between two States and then calls
        // render() on the result — so an Action returning an Action was fatal.
        $this->dial('', firstRequest: true);

        $response = $this->dial('2');

        $response->assertOk()->assertJsonPath('MSGTYPE', false);
        $this->assertStringContainsString('not cast any votes', $response->json('MSG'));
    }

    public function test_my_votes_lists_previous_votes(): void
    {
        Vote::create([
            'event_id'    => $this->event->id,
            'category_id' => $this->category->id,
            'nominee_id'  => $this->nominee->id,
            'quantity'    => 4,
            'channel'     => 'ussd',
            'voter_phone' => '0244123456',
        ]);

        $this->dial('', firstRequest: true);
        $response = $this->dial('2');

        $response->assertOk();
        $this->assertStringContainsString('4 x Sarkodie', $response->json('MSG'));
    }

    public function test_exit_ends_the_session(): void
    {
        $this->dial('', firstRequest: true);

        $response = $this->dial('0');

        $response->assertOk()->assertJsonPath('MSGTYPE', false);
        $this->assertStringContainsString('Thank you', $response->json('MSG'));
    }

    public function test_an_invalid_menu_choice_reprompts(): void
    {
        $this->dial('', firstRequest: true);

        $response = $this->dial('7');

        $response->assertOk()->assertJsonPath('MSGTYPE', true);
        $this->assertStringContainsString('Invalid choice', $response->json('MSG'));
        $this->assertStringContainsString('1. Vote', $response->json('MSG'));
    }

    public function test_redialling_mid_session_resets_state(): void
    {
        $this->dial('', firstRequest: true);
        $this->dial('1');   // now on the category screen

        // Same SESSIONID, MSGTYPE true again — a fresh dial must start clean.
        $response = $this->dial('', firstRequest: true);

        $this->assertStringContainsString('1. Vote', $response->json('MSG'));
        $this->assertStringNotContainsString('Select a category', $response->json('MSG'));
    }

    public function test_nominee_list_paginates(): void
    {
        foreach (['Stonebwoy' => '02', 'King Promise' => '03', 'Black Sherif' => '04'] as $name => $code) {
            Nominee::create([
                'category_id'   => $this->category->id,
                'name'          => $name,
                'code'          => $code,
                'display_order' => (int) $code,
            ]);
        }

        $this->dial('', firstRequest: true);
        $this->dial('1');
        $first = $this->dial('1');

        $this->assertStringContainsString('99. Next', $first->json('MSG'));

        $next = $this->dial('99');
        $this->assertStringContainsString('98. Prev', $next->json('MSG'));
        $this->assertStringContainsString('4. Black Sherif', $next->json('MSG'));

        $back = $this->dial('98');
        $this->assertStringContainsString('1. Sarkodie', $back->json('MSG'));
    }

    public function test_quantity_must_be_within_range(): void
    {
        $this->dial('', firstRequest: true);
        $this->dial('1');
        $this->dial('1');
        $this->dial('1');

        $response = $this->dial('999');

        $response->assertOk()->assertJsonPath('MSGTYPE', true);
        $this->assertStringContainsString('between 1 and 50', $response->json('MSG'));
    }

    public function test_cancelling_at_confirmation_creates_no_payment(): void
    {
        $this->dial('', firstRequest: true);
        $this->dial('1');
        $this->dial('1');
        $this->dial('1');
        $this->dial('2');

        $response = $this->dial('0');

        $response->assertOk()->assertJsonPath('MSGTYPE', false);
        $this->assertStringContainsString('cancelled', $response->json('MSG'));
        $this->assertSame(0, Payment::count());
    }

    public function test_no_live_campaign_ends_the_session(): void
    {
        $this->event->update(['status' => 'closed']);

        $response = $this->dial('', firstRequest: true, session: 'sess-closed');

        $response->assertOk()->assertJsonPath('MSGTYPE', false);
        $this->assertStringContainsString('No voting campaign is open', $response->json('MSG'));
    }

    public function test_a_second_live_campaign_produces_a_picker(): void
    {
        Event::create([
            'organization_id' => $this->event->organization_id,
            'name'            => 'Second Awards',
            'slug'            => 'second-awards',
            'event_type'      => 'award',
            'starts_at'       => now()->subDay(),
            'ends_at'         => now()->addDays(7),
            'status'          => 'live',
            'voting_rules'    => ['pay_per_vote' => true, 'price_per_vote_pesewas' => 100],
        ]);

        $response = $this->dial('', firstRequest: true, session: 'sess-pick');

        $response->assertOk()->assertJsonPath('MSGTYPE', true);
        $this->assertStringContainsString('Select an award', $response->json('MSG'));

        // Picking the first campaign lands on that campaign's own menu.
        $chosen = $this->dial('1', session: 'sess-pick');
        $this->assertStringContainsString('1. Vote', $chosen->json('MSG'));
    }

    // ── Settlement ───────────────────────────────────────────────────────

    public function test_speso_webhook_credits_the_vote(): void
    {
        Http::fake(['*' => Http::response(['success' => true], 200)]);

        $this->voteThrough(3);

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

        $this->voteThrough(2);

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

        $this->voteThrough(4);

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

    /** Walk the menu all the way to a confirmed ballot of $quantity votes. */
    private function voteThrough(int $quantity): void
    {
        $this->dial('', firstRequest: true);
        $this->dial('1');
        $this->dial('1');
        $this->dial('1');
        $this->dial((string) $quantity);
        $this->dial('1');
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
                'CONTENT_TYPE'           => 'application/json',
                'HTTP_ACCEPT'            => 'application/json',
                'HTTP_X_SPESO_SIGNATURE' => "t={$timestamp},v1={$signature}",
            ],
            $body,
        );
    }
}
