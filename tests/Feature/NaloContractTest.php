<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Conformance with Nalo's published USSD API documentation.
 *
 * The doc's request carries only four fields:
 *     { USERID, MSISDN, USERDATA, MSGTYPE }
 * and the reply five:
 *     { USERID, MSISDN, USERDATA, MSG, MSGTYPE }
 *
 * Critically there is **no SESSIONID**. The doc is explicit: "The only unique
 * parameter is the msisdn. Set session id with the msisdn in order to track
 * the session." Every test here therefore posts the bare four-field payload,
 * unlike UssdVotingTest which also exercises the SESSIONID path some
 * aggregators add.
 */
class NaloContractTest extends TestCase
{
    use RefreshDatabase;

    private const USER_ID = 'NALOTest';

    private const MSISDN = '233244123456';

    private Event $event;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.nalo.user_id' => null,
            'ussd.response_format'  => 'nalo',
            'services.speso.api_key' => 'sk_test_dummy',
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
            'ussd_short_id'   => '134',
            'ussd_shortcode'  => '*920*134#',
            'starts_at'       => now()->subDay(),
            'ends_at'         => now()->addDays(7),
            'status'          => 'live',
            'voting_rules'    => ['pay_per_vote' => true, 'price_per_vote_pesewas' => 100],
        ]);

        $this->category = Category::create([
            'event_id'      => $this->event->id,
            'name'          => 'Artiste of the Year',
            'code'          => 'AOTY',
            'display_order' => 1,
        ]);

        Nominee::create([
            'category_id'   => $this->category->id,
            'name'          => 'Sarkodie',
            'code'          => '01',
            'display_order' => 1,
        ]);
    }

    /**
     * Post exactly the payload Nalo documents — four fields, no SESSIONID.
     */
    private function nalo(string $userData, bool $msgType, string $msisdn = self::MSISDN, string $userId = self::USER_ID)
    {
        return $this->postJson('/api/ussd/callback', [
            'USERID'   => $userId,
            'MSISDN'   => $msisdn,
            'USERDATA' => $userData,
            'MSGTYPE'  => $msgType,
        ]);
    }

    // ── Request / response contract ──────────────────────────────────────

    public function test_the_reply_carries_exactly_the_five_documented_fields(): void
    {
        $response = $this->nalo('', true);

        $response->assertOk();

        $this->assertSame(
            ['MSG', 'MSGTYPE', 'MSISDN', 'USERDATA', 'USERID'],
            collect($response->json())->keys()->sort()->values()->all()
        );
    }

    public function test_the_reply_echoes_userid_msisdn_and_userdata(): void
    {
        // The doc's sample echoes the initial USERDATA ("9") straight back.
        $this->nalo('', true);

        $this->nalo('9', false)
            ->assertJsonPath('USERID', self::USER_ID)
            ->assertJsonPath('MSISDN', self::MSISDN)
            ->assertJsonPath('USERDATA', '9');
    }

    public function test_the_reply_is_json(): void
    {
        $this->nalo('', true)
            ->assertHeader('Content-Type', 'application/json');
    }

    public function test_msgtype_is_true_while_the_session_continues(): void
    {
        $this->nalo('', true)->assertJsonPath('MSGTYPE', true);
    }

    public function test_msgtype_is_false_on_the_closing_screen(): void
    {
        $this->nalo('', true);

        $this->nalo('0', false)->assertJsonPath('MSGTYPE', false);   // Exit
    }

    public function test_the_endpoint_only_accepts_post(): void
    {
        $this->get('/api/ussd/callback')->assertStatus(405);
    }

    // ── Session management, keyed on MSISDN ──────────────────────────────

    public function test_a_session_is_tracked_by_msisdn_when_no_sessionid_is_sent(): void
    {
        $this->nalo('', true);                       // welcome
        $second = $this->nalo('1', false);           // vote -> categories

        // Progress was carried across requests using MSISDN alone.
        $this->assertStringContainsString('Select a category', $second->json('MSG'));
    }

    public function test_two_callers_do_not_share_a_session(): void
    {
        // Same USERID for both — it identifies the integration, not the caller.
        $this->nalo('', true, msisdn: '233244111111');
        $this->nalo('1', false, msisdn: '233244111111');   // caller A is deeper in

        $b = $this->nalo('', true, msisdn: '233209999999');

        $this->assertStringContainsString('1. Vote', $b->json('MSG'));
        $this->assertStringNotContainsString('Select a category', $b->json('MSG'));
    }

    public function test_redialling_resets_a_half_finished_session(): void
    {
        // The doc's sample unsets the session when MSGTYPE is true again,
        // "in case the user cancelled initial screen".
        $this->nalo('', true);
        $this->nalo('1', false);            // now on the category screen

        $fresh = $this->nalo('', true);     // dials in again

        $this->assertStringContainsString('1. Vote', $fresh->json('MSG'));
        $this->assertStringNotContainsString('Select a category', $fresh->json('MSG'));
    }

    public function test_userdata_on_the_initial_dial_does_not_skip_the_welcome_screen(): void
    {
        // Nalo's sample initial dial carries USERDATA "9"; it identifies the
        // dialled extension, and must not be consumed as a menu choice.
        $response = $this->nalo('9', true);

        $this->assertStringContainsString('1. Vote', $response->json('MSG'));
    }

    // ── Message constraints ──────────────────────────────────────────────

    public function test_every_screen_stays_within_the_message_limit(): void
    {
        $screens = [
            $this->nalo('', true),
            $this->nalo('1', false),
            $this->nalo('1', false),
            $this->nalo('1', false),
            $this->nalo('2', false),
        ];

        foreach ($screens as $i => $screen) {
            $this->assertLessThanOrEqual(
                \App\Ussd\Responses\GatewayResponse::MAX_MESSAGE,
                mb_strlen($screen->json('MSG')),
                "Screen {$i} is too long for a handset"
            );
        }
    }

    public function test_messages_use_bare_line_feeds(): void
    {
        // The doc's own MSG samples separate lines with \n.
        $message = $this->nalo('', true)->json('MSG');

        $this->assertStringNotContainsString("\r", $message);
        $this->assertStringContainsString("\n", $message);
    }

    // ── Access control ───────────────────────────────────────────────────

    public function test_a_foreign_userid_is_rejected_once_ours_is_configured(): void
    {
        config(['services.nalo.user_id' => '*920*134']);

        $this->nalo('', true, userId: '*920*134')->assertJsonPath('MSGTYPE', true);

        $this->nalo('', true, userId: 'SOMEONE_ELSE', msisdn: '233201112222')
            ->assertJsonPath('MSGTYPE', false);
    }

    public function test_a_missing_msisdn_is_handled_gracefully(): void
    {
        $this->postJson('/api/ussd/callback', [
            'USERID'   => self::USER_ID,
            'USERDATA' => '',
            'MSGTYPE'  => true,
        ])->assertOk()->assertJsonPath('MSGTYPE', false);
    }
}
