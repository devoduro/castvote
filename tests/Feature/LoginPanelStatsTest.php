<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Nominee;
use App\Models\Organization;
use App\Models\Vote;
use App\Support\PlatformStats;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * The marketing panel on the sign-in page.
 *
 * It shipped with invented figures — "500+ Organizers", "2M+ Votes Cast",
 * "99.9% Uptime" and a mock campaign showing 1,284 votes and GH₵6,420 of
 * revenue — on a public page, to a platform that had none of that. These tests
 * hold the panel to two rules: every number is a query, and nothing an
 * organiser keeps private appears on it.
 */
class LoginPanelStatsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    // ── No invented figures ──────────────────────────────────────────────

    public function test_the_sign_in_page_carries_none_of_the_fabricated_figures(): void
    {
        $response = $this->get('/admin/login')->assertOk();

        foreach (['500+', '2M+', '99.9%', 'Miss Ghana 2026', '1,284', '6,420'] as $invented) {
            $response->assertDontSee($invented, escape: false);
        }
    }

    public function test_the_counters_report_real_totals(): void
    {
        $event = $this->createAwardEvent();
        Organization::create(['name' => 'Second Org', 'contact_email' => 'two@example.test']);

        $category = Category::create([
            'event_id' => $event->id, 'name' => 'Best Act', 'code' => 'BA', 'display_order' => 1,
        ]);
        $nominee = Nominee::create([
            'category_id' => $category->id, 'name' => 'Someone', 'code' => '01', 'display_order' => 1,
        ]);

        Vote::create([
            'event_id' => $event->id, 'category_id' => $category->id,
            'nominee_id' => $nominee->id, 'quantity' => 5, 'channel' => 'web',
        ]);
        Vote::create([
            'event_id' => $event->id, 'category_id' => $category->id,
            'nominee_id' => $nominee->id, 'quantity' => 3, 'channel' => 'ussd',
        ]);

        $counters = collect(PlatformStats::counters())->keyBy('label');

        $this->assertSame('2', $counters['Organisers']['value']);
        $this->assertSame('8', $counters['Votes cast']['value']);   // summed quantity, not rows
        $this->assertSame('1', $counters['Campaigns']['value']);
    }

    public function test_a_platform_with_nothing_on_it_shows_no_counters(): void
    {
        // "0 Organisers" is a worse first impression than no panel at all.
        $this->assertSame([], PlatformStats::counters());
    }

    public function test_large_totals_are_abbreviated(): void
    {
        $method = new \ReflectionMethod(PlatformStats::class, 'compact');
        $method->setAccessible(true);

        $this->assertSame('940', $method->invoke(null, 940));
        $this->assertSame('1.2K', $method->invoke(null, 1234));
        $this->assertSame('12K', $method->invoke(null, 12_400));
        $this->assertSame('2M', $method->invoke(null, 2_000_000));
    }

    // ── Nothing private leaks ────────────────────────────────────────────

    public function test_the_featured_campaign_never_exposes_tallies_or_revenue(): void
    {
        // A campaign's vote counts stay hidden until its organiser publishes
        // them. An unauthenticated sign-in page must not be the way around it.
        $event    = $this->createAwardEvent(['name' => 'Private Tally Awards']);
        $category = Category::create([
            'event_id' => $event->id, 'name' => 'Best Act', 'code' => 'BA', 'display_order' => 1,
        ]);
        $nominee = Nominee::create([
            'category_id' => $category->id, 'name' => 'Someone', 'code' => '01', 'display_order' => 1,
        ]);

        Vote::create([
            'event_id' => $event->id, 'category_id' => $category->id,
            'nominee_id' => $nominee->id, 'quantity' => 7777, 'channel' => 'web',
        ]);

        $featured = PlatformStats::featuredCampaign();

        $this->assertNotNull($featured);
        $this->assertSame('Private Tally Awards', $featured['name']);

        foreach (['votes', 'revenue', 'earnings', 'tally'] as $forbidden) {
            $this->assertArrayNotHasKey($forbidden, $featured);
        }

        // And it must not reach the rendered page either.
        $this->get('/admin/login')->assertOk()->assertDontSee('7777', escape: false);
    }

    public function test_the_featured_campaign_reports_its_real_shape(): void
    {
        $event    = $this->createAwardEvent(['name' => 'Shape Awards']);
        $category = Category::create([
            'event_id' => $event->id, 'name' => 'Best Act', 'code' => 'BA', 'display_order' => 1,
        ]);

        foreach (['01', '02', '03'] as $i => $code) {
            Nominee::create([
                'category_id' => $category->id, 'name' => "Nominee {$code}",
                'code' => $code, 'display_order' => $i + 1,
            ]);
        }

        $featured = PlatformStats::featuredCampaign();

        $this->assertSame(1, $featured['categories']);
        $this->assertSame(3, $featured['nominees']);
    }

    public function test_a_platform_with_no_campaign_shows_no_card(): void
    {
        $this->assertNull(PlatformStats::featuredCampaign());

        $this->get('/admin/login')->assertOk();   // and still renders
    }
}
