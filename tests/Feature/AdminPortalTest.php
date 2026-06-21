<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Event;
use App\Models\Organization;
use Tests\TestCase;

class AdminPortalTest extends TestCase
{
    // ── Authentication ────────────────────────────────────────────────────────

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_login_with_valid_credentials(): void
    {
        $admin = Admin::factory()->create([
            'email'    => 'owner@test.test',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/admin/login', [
            'email'    => 'owner@test.test',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        Admin::factory()->create(['email' => 'bad@test.test', 'password' => bcrypt('right')]);

        $this->post('/admin/login', ['email' => 'bad@test.test', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_logout_clears_session(): void
    {
        $this->actingAsAdmin();

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function test_dashboard_is_accessible_to_authenticated_admin(): void
    {
        $this->actingAsAdmin()->get('/admin/dashboard')->assertOk();
    }

    // ── Event management ──────────────────────────────────────────────────────

    public function test_admin_can_list_events(): void
    {
        $this->createAwardEvent();

        $this->actingAsAdmin()->get('/admin/events')->assertOk();
    }

    public function test_admin_can_view_event_create_form(): void
    {
        $this->actingAsAdmin()->get('/admin/events/create')->assertOk();
    }

    public function test_admin_can_create_event(): void
    {
        $org = Organization::factory()->create();

        $this->actingAsAdmin()->post('/admin/events', [
            'organization_id'    => $org->id,
            'name'               => 'Test Awards 2025',
            'slug'               => 'test-awards-2025',
            'event_type'         => 'award',
            'ussd_short_id'      => '299',
            'status'             => 'draft',
            'starts_at'          => now()->addHour()->format('Y-m-d H:i:s'),
            'ends_at'            => now()->addDays(7)->format('Y-m-d H:i:s'),
            'voting_rules'       => json_encode([
                'pay_per_vote'           => true,
                'price_per_vote_pesewas' => 200,
            ]),
        ])->assertRedirect();

        $this->assertDatabaseHas('events', ['slug' => 'test-awards-2025']);
    }

    public function test_viewer_cannot_create_event(): void
    {
        $org   = Organization::factory()->create();
        $admin = Admin::factory()->create(['role' => 'viewer']);

        $this->actingAs($admin, 'admin')->post('/admin/events', [
            'organization_id' => $org->id,
            'name'            => 'Sneaky Event',
            'slug'            => 'sneaky-event',
            'event_type'      => 'award',
            'ussd_short_id'   => '288',
            'status'          => 'draft',
        ])->assertForbidden();
    }

    // ── Results export ────────────────────────────────────────────────────────

    public function test_admin_can_download_csv_results(): void
    {
        $event = $this->createAwardEvent();

        $this->actingAsAdmin()
            ->get("/admin/events/{$event->id}/results/export/csv")
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_download_pdf_certificate(): void
    {
        $event = $this->createAwardEvent();

        $this->actingAsAdmin()
            ->get("/admin/events/{$event->id}/results/export/pdf")
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    // ── Category & nominee management (smoke tests) ───────────────────────────

    public function test_category_manager_page_loads(): void
    {
        $event = $this->createAwardEvent();

        $this->actingAsAdmin()
            ->get("/admin/events/{$event->id}/categories")
            ->assertOk();
    }

    public function test_nominee_manager_page_loads(): void
    {
        $event    = $this->createAwardEvent();
        $category = $this->createCategoryWithNominees($event);

        $this->actingAsAdmin()
            ->get("/admin/events/{$event->id}/categories/{$category->id}/nominees")
            ->assertOk();
    }

    // ── Payment reconciliation ────────────────────────────────────────────────

    public function test_payment_reconciliation_page_loads(): void
    {
        $event = $this->createAwardEvent();

        $this->actingAsAdmin()
            ->get("/admin/events/{$event->id}/payments")
            ->assertOk();
    }

    // ── Rate limiter on login ─────────────────────────────────────────────────

    public function test_login_rate_limit_blocks_after_10_attempts(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/admin/login', ['email' => 'x@x.com', 'password' => 'wrong']);
        }

        $this->post('/admin/login', ['email' => 'x@x.com', 'password' => 'wrong'])
            ->assertStatus(429);
    }
}
