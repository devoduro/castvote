<?php

namespace Tests\Feature;

use App\Models\EligibleVoter;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WebVotingPortalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Http::fake();
    }

    // ── Public event listing ──────────────────────────────────────────────────

    public function test_vote_index_shows_live_events(): void
    {
        $event = $this->createAwardEvent(['status' => 'live']);

        $this->get('/vote')->assertOk()->assertSee($event->name);
    }

    public function test_draft_event_does_not_appear_in_listing(): void
    {
        $event = $this->createAwardEvent(['status' => 'draft']);

        $this->get('/vote')->assertOk()->assertDontSee($event->name);
    }

    // ── Voting page access ────────────────────────────────────────────────────

    public function test_live_event_ballot_page_loads(): void
    {
        $event    = $this->createAwardEvent();
        $this->createCategoryWithNominees($event);

        $this->get("/vote/events/{$event->slug}")->assertOk();
    }

    public function test_closed_event_redirects_from_ballot_page(): void
    {
        $event = $this->createAwardEvent([
            'status'  => 'closed',
            'ends_at' => now()->subHour(),
        ]);

        $this->get("/vote/events/{$event->slug}")->assertRedirect('/vote');
    }

    public function test_draft_event_redirects_from_ballot_page(): void
    {
        $event = $this->createAwardEvent(['status' => 'draft']);

        $this->get("/vote/events/{$event->slug}")->assertRedirect('/vote');
    }

    // ── Paystack callback ─────────────────────────────────────────────────────

    public function test_paystack_callback_with_success_reference_shows_confirmed_page(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $payment = Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'success',
            'provider_reference' => 'ps_web_ok',
            'phone_number'       => '0244123456',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
        ]);

        $this->get("/vote/callback?reference=ps_web_ok&trxref=ps_web_ok")
            ->assertRedirect("/vote/confirmed/ps_web_ok");
    }

    public function test_confirmed_page_shows_vote_details(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        $payment = Payment::factory()->create([
            'event_id'           => $event->id,
            'amount_pesewas'     => 100,
            'status'             => 'success',
            'provider_reference' => 'ps_conf_01',
            'phone_number'       => '0244123456',
            'metadata'           => ['nominee_id' => $nominee->id, 'category_id' => $category->id, 'quantity' => 1],
        ]);

        Vote::factory()->create([
            'event_id'   => $event->id,
            'nominee_id' => $nominee->id,
            'category_id'=> $category->id,
            'payment_id' => $payment->id,
            'quantity'   => 1,
        ]);

        $this->get("/vote/confirmed/ps_conf_01")
            ->assertOk()
            ->assertSee($nominee->name);
    }

    // ── Eligibility gate ──────────────────────────────────────────────────────

    public function test_eligibility_gate_page_shows_for_election_event(): void
    {
        $event = $this->createAwardEvent([
            'event_type'  => 'election',
            'voting_rules' => [
                'pay_per_vote'              => false,
                'price_per_vote_pesewas'    => 0,
                'max_votes_per_voter'       => 1,
                'requires_eligibility_list' => true,
                'anonymous_tally'           => true,
            ],
        ]);
        $this->createCategoryWithNominees($event);

        $this->get("/vote/events/{$event->slug}")->assertOk()->assertSee('Student ID');
    }

    public function test_eligible_voter_can_proceed_to_ballot(): void
    {
        $event = $this->createAwardEvent([
            'event_type'  => 'election',
            'voting_rules' => [
                'pay_per_vote'              => false,
                'price_per_vote_pesewas'    => 0,
                'max_votes_per_voter'       => 1,
                'requires_eligibility_list' => true,
                'anonymous_tally'           => true,
            ],
        ]);
        $this->createCategoryWithNominees($event);

        EligibleVoter::factory()->create([
            'event_id'   => $event->id,
            'identifier' => 'UG-10123456',
        ]);

        // POST identifier to eligibility gate
        $response = $this->post("/vote/events/{$event->slug}/verify", [
            'identifier' => 'UG-10123456',
        ]);

        $response->assertOk();
        $response->assertSee('Ballot'); // ballot component should be visible
    }

    public function test_ineligible_voter_sees_error(): void
    {
        $event = $this->createAwardEvent([
            'event_type'  => 'election',
            'voting_rules' => [
                'pay_per_vote'              => false,
                'price_per_vote_pesewas'    => 0,
                'max_votes_per_voter'       => 1,
                'requires_eligibility_list' => true,
                'anonymous_tally'           => true,
            ],
        ]);
        $this->createCategoryWithNominees($event);

        $response = $this->post("/vote/events/{$event->slug}/verify", [
            'identifier' => 'UG-NOTREGISTERED',
        ]);

        $response->assertOk();
        $response->assertSee('not on the eligible voters list');
    }

    public function test_voter_who_already_voted_cannot_vote_again(): void
    {
        $event = $this->createAwardEvent([
            'event_type'  => 'election',
            'voting_rules' => [
                'pay_per_vote'              => false,
                'price_per_vote_pesewas'    => 0,
                'max_votes_per_voter'       => 1,
                'requires_eligibility_list' => true,
                'anonymous_tally'           => true,
            ],
        ]);
        $this->createCategoryWithNominees($event);

        EligibleVoter::factory()->create([
            'event_id'   => $event->id,
            'identifier' => 'UG-VOTED',
            'has_voted'  => true,
        ]);

        $response = $this->post("/vote/events/{$event->slug}/verify", [
            'identifier' => 'UG-VOTED',
        ]);

        $response->assertOk();
        $response->assertSee('already cast your vote');
    }

    // ── Privacy page ──────────────────────────────────────────────────────────

    public function test_privacy_policy_page_loads(): void
    {
        $this->get('/vote/privacy')->assertOk();
    }
}
