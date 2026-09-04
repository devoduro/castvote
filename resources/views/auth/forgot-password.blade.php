<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password — ClickVote Admin</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <x-theme />
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col" style="background:#faf9fc">

<main class="flex-1 flex items-center justify-center p-5">
    <div class="w-full" style="max-width:420px">

        <div class="flex justify-center mb-7">
            <x-ui.logo :size="40" :href="route('home')" />
        </div>

        <div class="card p-6 sm:p-8">
            <livewire:admin.forgot-password />
        </div>

        <p class="text-center text-[12.5px] text-ink-400 mt-6 leading-relaxed">
            Codes are sent by SMS to the number on your organiser account.<br>
            Need help? Contact your ClickVote administrator.
        </p>
    </div>
</main>

@livewireScripts
</body>
</html>
