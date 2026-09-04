<?php

namespace Tests\Feature;

use App\Livewire\Admin\ForgotPassword;
use App\Models\Admin;
use App\Models\Organization;
use App\Models\OtpCode;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Password reset for organiser accounts by SMS one-time code.
 *
 * Speso issues and verifies the code in production; without SPESO_API_KEY the
 * service falls back to a locally generated code, which is what most of these
 * tests exercise.
 */
class AdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('pwreset:owner@clickvote.test|127.0.0.1');

        // No Speso credentials -> the local fallback is used.
        config(['services.speso.api_key' => null, 'services.speso.sender_id' => null]);

        $org = Organization::create([
            'name'          => 'Ghana Music Awards Ltd',
            'contact_email' => 'awards@example.test',
        ]);

        $this->admin = Admin::create([
            'organization_id' => $org->id,
            'name'            => 'Kwame Asante',
            'email'           => 'owner@clickvote.test',
            'phone'           => '0244123456',
            'password'        => 'old-password-1',
            'role'            => 'owner',
            'account_status'  => 'approved',
        ]);
    }

    public function test_the_reset_page_loads(): void
    {
        $this->get('/admin/forgot-password')
            ->assertOk()
            ->assertSee('Reset your password');
    }

    public function test_the_login_page_links_to_it(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee(route('admin.password.request'), escape: false);
    }

    public function test_a_code_is_issued_and_the_password_can_be_changed(): void
    {
        $component = Livewire::test(ForgotPassword::class)
            ->set('email', 'owner@clickvote.test')
            ->call('sendCode')
            ->assertHasNoErrors()
            ->assertSet('step', 'verify');

        // Masked so the full number is never shown to whoever asked.
        $component->assertSet('maskedPhone', '024****456');

        $code = OtpCode::where('phone', '0244123456')->sole();
        $this->assertSame('password_reset', $code->purpose);
        $this->assertTrue($code->isUsable());

        $component->set('code', $code->code)
            ->call('verifyCode')
            ->assertHasNoErrors()
            ->assertSet('step', 'reset')
            ->set('password', 'new-password-1')
            ->set('password_confirmation', 'new-password-1')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertSet('step', 'done');

        $this->assertTrue(Hash::check('new-password-1', $this->admin->fresh()->password));
        $this->assertNotNull($code->fresh()->consumed_at);
    }

    public function test_the_new_password_actually_signs_in(): void
    {
        $component = Livewire::test(ForgotPassword::class)
            ->set('email', 'owner@clickvote.test')
            ->call('sendCode');

        $component->set('code', OtpCode::sole()->code)
            ->call('verifyCode')
            ->set('password', 'brand-new-99')
            ->set('password_confirmation', 'brand-new-99')
            ->call('resetPassword');

        $this->post('/admin/login', [
            'email'    => 'owner@clickvote.test',
            'password' => 'brand-new-99',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_an_unknown_email_does_not_reveal_itself(): void
    {
        // Same screen, no error — otherwise this endpoint enumerates accounts.
        Livewire::test(ForgotPassword::class)
            ->set('email', 'nobody@clickvote.test')
            ->call('sendCode')
            ->assertHasNoErrors()
            ->assertSet('step', 'verify')
            ->assertSet('maskedPhone', '');

        $this->assertSame(0, OtpCode::count());
    }

    public function test_a_wrong_code_is_rejected(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'owner@clickvote.test')
            ->call('sendCode')
            ->set('code', '000000')
            ->call('verifyCode')
            ->assertHasErrors('code')
            ->assertSet('step', 'verify');

        $this->assertTrue(Hash::check('old-password-1', $this->admin->fresh()->password));
    }

    public function test_a_code_cannot_be_brute_forced(): void
    {
        $otp = app(OtpService::class);
        $otp->send('0244123456', 'password_reset');

        for ($i = 0; $i < 5; $i++) {
            $this->assertFalse($otp->verify('0244123456', 'password_reset', '111111'));
        }

        // The real code no longer works — too many wrong guesses burned it.
        $this->assertFalse($otp->verify('0244123456', 'password_reset', OtpCode::sole()->code));
    }

    public function test_an_expired_code_is_rejected(): void
    {
        $otp = app(OtpService::class);
        $code = $otp->send('0244123456', 'password_reset');

        $this->travel(OtpService::TTL_MINUTES + 1)->minutes();

        $this->assertFalse($otp->verify('0244123456', 'password_reset', $code));
    }

    public function test_a_code_cannot_be_reused(): void
    {
        $otp = app(OtpService::class);
        $code = $otp->send('0244123456', 'password_reset');

        $this->assertTrue($otp->verify('0244123456', 'password_reset', $code));
        $this->assertFalse($otp->verify('0244123456', 'password_reset', $code));
    }

    public function test_requests_are_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            Livewire::test(ForgotPassword::class)
                ->set('email', 'owner@clickvote.test')
                ->call('sendCode')
                ->assertHasNoErrors();
        }

        Livewire::test(ForgotPassword::class)
            ->set('email', 'owner@clickvote.test')
            ->call('sendCode')
            ->assertHasErrors('email');
    }

    public function test_speso_is_used_when_configured(): void
    {
        config([
            'services.speso.api_key'   => 'sk_test_dummy',
            'services.speso.sender_id' => 'ClickVote',
            'services.speso.base_url'  => 'https://business.speso.co/api/v1',
        ]);

        Http::fake([
            '*/otp/request' => Http::response(['success' => true], 200),
            '*/otp/verify'  => Http::response(['success' => true, 'data' => ['verified' => true]], 200),
        ]);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'owner@clickvote.test')
            ->call('sendCode')
            ->assertSet('step', 'verify')
            // Speso holds the code, so nothing is stored locally and nothing
            // is shown on screen.
            ->assertSet('devCode', null)
            ->set('code', '123456')
            ->call('verifyCode')
            ->assertHasNoErrors()
            ->assertSet('step', 'reset');

        $this->assertSame(0, OtpCode::count());

        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/otp/request')
            && $request['phone'] === '0244123456');
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/otp/verify')
            && $request['code'] === '123456');
    }

    public function test_a_weak_password_is_refused(): void
    {
        $component = Livewire::test(ForgotPassword::class)
            ->set('email', 'owner@clickvote.test')
            ->call('sendCode');

        $component->set('code', OtpCode::sole()->code)
            ->call('verifyCode')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('resetPassword')
            ->assertHasErrors('password')
            ->assertSet('step', 'reset');

        $this->assertTrue(Hash::check('old-password-1', $this->admin->fresh()->password));
    }
}
