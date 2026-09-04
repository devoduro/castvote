<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use App\Models\AuditLog;
use App\Services\OtpService;
use App\Ussd\Support\PhoneNumber;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;
use Throwable;

/**
 * Password reset for organiser accounts, by one-time code over SMS.
 *
 * Reset is keyed on the account's phone number rather than email, because
 * that is the channel Speso can actually reach and the one Ghanaian
 * organisers check. Three steps: identify, verify the code, set a password.
 */
class ForgotPassword extends Component
{
    public string $step = 'identify';   // identify | verify | reset | done

    public string $email    = '';
    public string $code     = '';
    public string $password = '';
    public string $password_confirmation = '';

    /** Set once a code has been sent; never exposed to the browser. */
    public string $maskedPhone = '';

    /** Local-fallback code, shown on screen only when Speso is not wired up. */
    public ?string $devCode = null;

    // ── Step 1: identify the account ─────────────────────────────────────

    public function sendCode(OtpService $otp): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        // Rate limit per email and per IP so this cannot be used to enumerate
        // accounts or to bill out SMS.
        $key = 'pwreset:'.mb_strtolower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('email', 'Too many attempts. Try again in '
                .ceil(RateLimiter::availableIn($key) / 60).' minute(s).');

            return;
        }

        RateLimiter::hit($key, 900);

        $admin = Admin::where('email', $this->email)->first();

        // Always advance to the code screen, whether or not the account
        // exists — a different response here would confirm which emails are
        // registered.
        if ($admin && filled($admin->phone)) {
            $this->maskedPhone = PhoneNumber::mask($admin->phone);

            try {
                $this->devCode = $otp->send(
                    phone:   PhoneNumber::normalize($admin->phone),
                    purpose: 'password_reset',
                    message: 'Your ClickVote password reset code is %otp_code%. It expires in '
                        .OtpService::TTL_MINUTES.' minutes.',
                );
            } catch (Throwable $e) {
                report($e);

                $this->addError('email', 'We could not send the code right now. Please try again shortly.');

                return;
            }
        }

        $this->step = 'verify';
    }

    // ── Step 2: verify the code ──────────────────────────────────────────

    public function verifyCode(OtpService $otp): void
    {
        $this->validate([
            'code' => ['required', 'string', 'min:4', 'max:8'],
        ]);

        $admin = Admin::where('email', $this->email)->first();

        if (! $admin || blank($admin->phone)
            || ! $otp->verify(PhoneNumber::normalize($admin->phone), 'password_reset', trim($this->code))) {
            $this->addError('code', 'That code is not valid or has expired.');

            return;
        }

        $this->step    = 'reset';
        $this->devCode = null;
    }

    // ── Step 3: set the new password ─────────────────────────────────────

    public function resetPassword(): void
    {
        $this->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $admin = Admin::where('email', $this->email)->first();

        if (! $admin) {
            $this->addError('password', 'Something went wrong. Please start again.');
            $this->step = 'identify';

            return;
        }

        $admin->forceFill([
            'password'       => Hash::make($this->password),
            'remember_token' => null,
        ])->save();

        AuditLog::record('admin.password_reset', $admin, ['channel' => 'sms_otp'], $admin->id);

        $this->reset('password', 'password_confirmation', 'code');
        $this->step = 'done';
    }

    public function startOver(): void
    {
        $this->reset();
        $this->step = 'identify';
    }

    public function render()
    {
        return view('livewire.admin.forgot-password');
    }
}
