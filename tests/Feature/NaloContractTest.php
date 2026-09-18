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

    /** POST a raw body with an arbitrary Content-Type. */
    private function raw(string $body, string $contentType)
    {
        return $this->call(
            'POST', '/api/ussd/callback', [], [], [], ['CONTENT_TYPE' => $contentType], $body
        );
    }

    public function test_a_json_body_without_a_json_content_type_is_still_parsed(): void
    {
        // Regression: Laravel only parses JSON when the Content-Type says so.
        // Otherwise the whole document becomes a single form key and MSISDN
        // vanishes, so a real dial was refused with "no MSISDN in payload".
        $body = '{"USERID":"*920*134","MSISDN":"233244123456","USERDATA":"","MSGTYPE":true}';

        $response = $this->raw($body, 'text/plain');

        $response->assertOk()->assertJsonPath('MSISDN', '233244123456');
        $this->assertStringContainsString('1. Vote', $response->json('MSG'));
    }

    public function test_a_json_body_with_bare_uppercase_booleans_is_still_parsed(): void
    {
        // Observed from the live gateway: MSGTYPE serialised as bare TRUE,
        // which is not valid JSON, so even json_decode() alone fails.
        $body = '{"USERID":"*920*134","MSISDN":"233244123456","USERDATA":"","MSGTYPE":TRUE}';

        $response = $this->raw($body, 'text/plain');

        $response->assertOk()->assertJsonPath('MSISDN', '233244123456');
        $this->assertStringContainsString('1. Vote', $response->json('MSG'));
    }

    public function test_bare_uppercase_msgtype_still_marks_the_session_as_new(): void
    {
        // TRUE must survive the repair as a boolean, not a string, or the
        // caller is dropped into a stale session instead of a fresh one.
        $this->nalo('', true);
        $this->nalo('1', false);        // now on the category screen

        $fresh = $this->raw(
            '{"USERID":"*920*134","MSISDN":"'.self::MSISDN.'","USERDATA":"","MSGTYPE":TRUE}',
            'text/plain'
        );

        $this->assertStringContainsString('1. Vote', $fresh->json('MSG'));
        $this->assertStringNotContainsString('Select a category', $fresh->json('MSG'));
    }

    public function test_a_quoted_uppercase_literal_is_not_mangled_by_the_repair(): void
    {
        // The leniency must not rewrite TRUE inside a string value.
        $body = '{"USERID":"*920*134","MSISDN":"233244123456","USERDATA":"TRUE","MSGTYPE":TRUE}';

        $this->raw($body, 'text/plain')
            ->assertOk()
            ->assertJsonPath('USERDATA', 'TRUE');
    }

    public function test_a_body_that_is_not_json_at_all_is_still_refused(): void
    {
        $this->raw('this is not a payload', 'text/plain')
            ->assertOk()
            ->assertJsonPath('MSGTYPE', false);
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

    public function test_a_new_caller_does_not_wipe_another_callers_session(): void
    {
        // Regression: the package's Record::flush() is Cache::clear(), so a
        // fresh dial used to empty the whole cache and throw every other
        // in-progress voter back to the welcome screen.
        $this->nalo('', true, msisdn: '233244111111');
        $this->nalo('1', false, msisdn: '233244111111');     // A: category screen
        $this->nalo('1', false, msisdn: '233244111111');     // A: nominee screen

        $this->nalo('', true, msisdn: '233209999999');       // B dials in fresh

        // A's next reply must land on the quantity screen, not restart.
        $a = $this->nalo('1', false, msisdn: '233244111111');

        $this->assertStringContainsString('How many votes', $a->json('MSG'));
    }

    public function test_a_new_caller_does_not_wipe_the_gateway_log(): void
    {
        \App\Ussd\Support\GatewayLog::clear();

        $this->nalo('', true, msisdn: '233244111111');
        $this->nalo('', true, msisdn: '233209999999');

        // Both dials were recorded — the second did not erase the first.
        $this->assertCount(2, \App\Ussd\Support\GatewayLog::recent());
    }

    public function test_reset_clears_every_key_a_session_writes(): void
    {
        // Flow::SESSION_KEYS is maintained by hand. If a new state starts
        // writing a key nobody adds to that list, stale data would leak
        // into the caller's next dial — so walk the whole flow, reset, and
        // assert the record is genuinely empty.
        $this->nalo('', true);
        $this->nalo('1', false);     // categories
        $this->nalo('1', false);     // nominees
        $this->nalo('1', false);     // quantity
        $this->nalo('2', false);     // confirm

        $store  = config('ussd.cache_store') ?: config('cache.default');
        $record = new \Sparors\Ussd\Record(\Illuminate\Support\Facades\Cache::store($store), 'msisdn-0244123456');

        // Sanity: the walk really did populate the session.
        $this->assertTrue($record->has('nominee_id'), 'flow did not populate the record');

        \App\Ussd\Support\Flow::resetSession($record);

        foreach (\App\Ussd\Support\Flow::SESSION_KEYS as $key) {
            $this->assertFalse(
                $record->has($key),
                "'{$key}' survived the reset"
            );
        }
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

    // ── A closed ballot must not kill the shortcode ──────────────────────

    public function test_the_shortcode_still_answers_after_voting_closes(): void
    {
        // Regression: a campaign past its end date made the shortcode reply
        // "No voting campaign is open right now", which reads like an outage.
        $this->event->update(['ends_at' => now()->subDay()]);

        $response = $this->nalo('', true);

        $this->assertStringContainsString('Voting has closed', $response->json('MSG'));
        $this->assertStringContainsString('Test Music Awards', $response->json('MSG'));
    }

    public function test_a_closed_campaign_does_not_offer_the_ballot(): void
    {
        $this->event->update(['status' => 'closed']);

        $message = $this->nalo('', true)->json('MSG');

        $this->assertStringNotContainsString('1. Vote', $message);
        $this->assertStringContainsString('1. My votes', $message);
    }

    public function test_a_closed_campaign_still_answers_my_votes(): void
    {
        $this->event->update(['status' => 'closed']);

        $this->nalo('', true);

        // On a closed campaign "My votes" is option 1, not 2.
        $this->assertStringNotContainsString(
            'Invalid', $this->nalo('1', false)->json('MSG')
        );
    }

    public function test_a_closed_campaign_refuses_to_start_a_ballot(): void
    {
        // Defence in depth: the menu never offers it, but a session that was
        // open when the campaign closed must not slip through.
        $this->event->update(['ends_at' => now()->addHour()]);
        $this->nalo('', true);

        $this->event->update(['ends_at' => now()->subHour()]);

        $message = $this->nalo('1', false)->json('MSG');

        $this->assertStringNotContainsString('Select a category', $message);
    }

    public function test_a_draft_campaign_is_still_unreachable(): void
    {
        // Reachability was widened to closed campaigns only — a campaign that
        // has not opened yet must not be dialable.
        $this->event->update(['status' => 'draft']);

        $this->nalo('', true)
            ->assertJsonPath('MSG', 'No voting campaign is open right now. Please try again later.');
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

    public function test_a_foreign_userid_is_rejected_once_strict_mode_is_on(): void
    {
        config(['services.nalo.user_id' => '*920*134', 'services.nalo.strict_user_id' => true]);

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
