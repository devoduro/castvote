<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Event;
use App\Models\Organization;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\EventForm;
use App\Livewire\Admin\NomineeManager;
use Livewire\Livewire;
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
        // Event CRUD is a Livewire component, not a REST endpoint.
        $admin = Admin::factory()->create(['role' => 'owner']);

        Livewire::actingAs($admin, 'admin')
            ->test(EventForm::class)
            ->set('name', 'Test Awards 2025')
            ->set('event_type', 'award')
            ->set('status', 'draft')
            ->set('starts_at', now()->addHour()->format('Y-m-d\TH:i'))
            ->set('ends_at', now()->addDays(7)->format('Y-m-d\TH:i'))
            ->set('pay_per_vote', true)
            ->set('price_per_vote_pesewas', 200)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('events', [
            'name'            => 'Test Awards 2025',
            'organization_id' => $admin->organization_id,
        ]);
    }

    public function test_viewer_cannot_create_event(): void
    {
        $viewer = Admin::factory()->create(['role' => 'viewer']);

        Livewire::actingAs($viewer, 'admin')
            ->test(EventForm::class)
            ->set('name', 'Sneaky Event')
            ->set('event_type', 'award')
            ->set('status', 'draft')
            ->call('save');

        $this->assertDatabaseMissing('events', ['name' => 'Sneaky Event']);
    }

    // ── Read-only accounts must not mutate ────────────────────────────────────

    public function test_viewer_cannot_create_a_category(): void
    {
        // Livewire actions are callable straight from the browser, so hiding
        // the buttons is not authorization.
        $viewer = Admin::factory()->create(['role' => 'viewer']);
        $event  = $this->createAwardEvent(['organization_id' => $viewer->organization_id]);

        Livewire::actingAs($viewer, 'admin')
            ->test(CategoryManager::class, ['event' => $event])
            ->set('name', 'Injected Category')
            ->set('code', 'XX')
            ->set('display_order', 1)
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'Injected Category']);
    }

    public function test_viewer_cannot_delete_a_category(): void
    {
        $viewer   = Admin::factory()->create(['role' => 'viewer']);
        $event    = $this->createAwardEvent(['organization_id' => $viewer->organization_id]);
        $category = $this->createCategoryWithNominees($event);

        Livewire::actingAs($viewer, 'admin')
            ->test(CategoryManager::class, ['event' => $event])
            ->call('delete', $category->id)
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_viewer_cannot_delete_a_nominee(): void
    {
        $viewer   = Admin::factory()->create(['role' => 'viewer']);
        $event    = $this->createAwardEvent(['organization_id' => $viewer->organization_id]);
        $category = $this->createCategoryWithNominees($event);
        $nominee  = $category->nominees()->first();

        Livewire::actingAs($viewer, 'admin')
            ->test(NomineeManager::class, ['event' => $event, 'category' => $category])
            ->call('delete', $nominee->id)
            ->assertForbidden();

        $this->assertDatabaseHas('nominees', ['id' => $nominee->id]);
    }

    public function test_a_manager_can_still_manage_categories(): void
    {
        $manager = Admin::factory()->create(['role' => 'manager']);
        $event   = $this->createAwardEvent(['organization_id' => $manager->organization_id]);

        Livewire::actingAs($manager, 'admin')
            ->test(CategoryManager::class, ['event' => $event])
            ->call('openCreate')
            ->set('name', 'Best New Act')
            ->set('code', 'BNA')
            ->set('display_order', 1)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', ['name' => 'Best New Act']);
    }

    // ── Results export ────────────────────────────────────────────────────────

    public function test_admin_can_download_csv_results(): void
    {
        $admin = Admin::factory()->create();
        $event = $this->createAwardEvent(['organization_id' => $admin->organization_id]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/events/{$event->id}/export/payments-csv")
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_download_pdf_results(): void
    {
        $admin = Admin::factory()->create();
        $event = $this->createAwardEvent(['organization_id' => $admin->organization_id]);

        $this->actingAs($admin, 'admin')
            ->get("/admin/events/{$event->id}/export/results-pdf")
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_an_admin_cannot_export_another_organisations_event(): void
    {
        $admin = Admin::factory()->create();
        $event = $this->createAwardEvent();   // a different organisation

        $this->actingAs($admin, 'admin')
            ->get("/admin/events/{$event->id}/export/payments-csv")
            ->assertForbidden();
    }

    // ── Category & nominee management (smoke tests) ───────────────────────────

    public function test_category_manager_page_loads(): void
    {
        $admin = Admin::factory()->create();
        $event = $this->createAwardEvent(['organization_id' => $admin->organization_id]);

        // Categories are managed on the event page, not a /categories route.
        $this->actingAs($admin, 'admin')
            ->get("/admin/events/{$event->id}")
            ->assertOk();
    }

    public function test_nominee_manager_page_loads(): void
    {
        $admin    = Admin::factory()->create();
        $event    = $this->createAwardEvent(['organization_id' => $admin->organization_id]);
        $category = $this->createCategoryWithNominees($event);

        $this->actingAs($admin, 'admin')
            ->get("/admin/events/{$event->id}/categories/{$category->id}/nominees")
            ->assertOk();
    }

    // ── Payment reconciliation ────────────────────────────────────────────────

    public function test_payment_reconciliation_page_loads(): void
    {
        $admin = Admin::factory()->create();
        $event = $this->createAwardEvent(['organization_id' => $admin->organization_id]);

        $this->actingAs($admin, 'admin')
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
