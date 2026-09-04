<div>
    @if($step === 'identify')
        <form wire:submit="sendCode" class="flex flex-col gap-5">
            <div>
                <h1 class="text-[22px] font-extrabold text-ink-900">Reset your password</h1>
                <p class="text-[13.5px] text-ink-500 mt-1.5 leading-relaxed">
                    Enter your account email and we'll text a one-time code to the phone number on
                    the account.
                </p>
            </div>

            <div>
                <label for="fp-email" class="label">Email address</label>
                <input id="fp-email" wire:model="email" type="email" autocomplete="email" required autofocus
                       placeholder="you@example.com"
                       class="input @error('email') is-error @enderror">
                @error('email')
                    <p class="error-msg" role="alert"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                @enderror
            </div>

            <x-ui.btn type="submit" loading="sendCode" variant="primary" size="lg" block icon="mobile">
                Send code
            </x-ui.btn>

            <a href="{{ route('admin.login') }}" class="btn btn-ghost btn-sm self-center">
                <x-ui.icon name="arrow-left" :size="15" /> Back to sign in
            </a>
        </form>

    @elseif($step === 'verify')
        <form wire:submit="verifyCode" class="flex flex-col gap-5">
            <div>
                <h1 class="text-[22px] font-extrabold text-ink-900">Enter the code</h1>
                <p class="text-[13.5px] text-ink-500 mt-1.5 leading-relaxed">
                    If an account exists for <strong class="text-ink-800">{{ $email }}</strong>, we've
                    sent a code
                    @if($maskedPhone)
                        to <span class="font-mono text-ink-800">{{ $maskedPhone }}</span>.
                    @else
                        to the phone number on it.
                    @endif
                    It expires in {{ \App\Services\OtpService::TTL_MINUTES }} minutes.
                </p>
            </div>

            @if($devCode)
                {{-- Speso is not configured, so the code was logged rather than texted. --}}
                <p class="flex items-start gap-2.5 rounded-xl bg-amber-50 border border-amber-200 p-3.5
                          text-[12.5px] text-amber-900 leading-relaxed">
                    <x-ui.icon name="info" :size="16" class="mt-px" />
                    <span>
                        SMS is not configured, so no message was sent. Development code:
                        <strong class="font-mono">{{ $devCode }}</strong>
                    </span>
                </p>
            @endif

            <div>
                <label for="fp-code" class="label">One-time code</label>
                <input id="fp-code" wire:model="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                       required autofocus placeholder="000000" maxlength="8"
                       class="input font-mono text-center tracking-[.4em] text-[18px] @error('code') is-error @enderror">
                @error('code')
                    <p class="error-msg" role="alert"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                @enderror
            </div>

            <x-ui.btn type="submit" loading="verifyCode" variant="primary" size="lg" block>
                Verify code
            </x-ui.btn>

            <button type="button" wire:click="startOver" class="btn btn-ghost btn-sm self-center">
                Use a different email
            </button>
        </form>

    @elseif($step === 'reset')
        <form wire:submit="resetPassword" class="flex flex-col gap-5">
            <div>
                <h1 class="text-[22px] font-extrabold text-ink-900">Choose a new password</h1>
                <p class="text-[13.5px] text-ink-500 mt-1.5">At least 8 characters, with a letter and a number.</p>
            </div>

            <div>
                <label for="fp-password" class="label">New password</label>
                <input id="fp-password" wire:model="password" type="password" autocomplete="new-password"
                       required autofocus class="input @error('password') is-error @enderror">
                @error('password')
                    <p class="error-msg" role="alert"><x-ui.icon name="warning" :size="14" /> {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fp-password2" class="label">Confirm new password</label>
                <input id="fp-password2" wire:model="password_confirmation" type="password"
                       autocomplete="new-password" required class="input">
            </div>

            <x-ui.btn type="submit" loading="resetPassword" variant="primary" size="lg" block icon="lock">
                Update password
            </x-ui.btn>
        </form>

    @else
        <div class="text-center flex flex-col gap-5">
            <span class="w-16 h-16 rounded-full bg-green-50 text-green-600 flex items-center justify-center mx-auto">
                <x-ui.icon name="check" :size="32" :stroke="2.6" />
            </span>
            <div>
                <h1 class="text-[22px] font-extrabold text-ink-900">Password updated</h1>
                <p class="text-[13.5px] text-ink-500 mt-1.5">You can now sign in with your new password.</p>
            </div>
            <x-ui.btn :href="route('admin.login')" variant="primary" size="lg" block icon-end="arrow-right">
                Go to sign in
            </x-ui.btn>
        </div>
    @endif
</div>
