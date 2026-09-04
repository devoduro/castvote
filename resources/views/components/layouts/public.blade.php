<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Vote' }} — ClickVote Ghana</title>
    <meta name="description" content="{{ $description ?? 'Discover awards, support your favourite nominees, cast secure votes and take part in events across Ghana.' }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <x-theme />
    @livewireStyles
    {{ $head ?? '' }}
</head>
<body class="min-h-screen flex flex-col bg-[#faf9fc] font-sans antialiased">

<a href="#main" class="skip-link">Skip to main content</a>

<x-site.nav />

<main id="main" class="flex-1">
    {{ $slot }}
</main>

<x-site.footer />

<x-ui.toasts />

@livewireScripts
{{ $scripts ?? '' }}
</body>
</html>
