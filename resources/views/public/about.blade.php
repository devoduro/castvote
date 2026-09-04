<x-layouts.public>
<x-slot name="title">About ClickVote</x-slot>
<x-slot name="description">ClickVote is a Ghanaian platform for award voting, nominations and events — secure payments, USSD access and transparent results.</x-slot>

<x-site.page-header eyebrow="About us" title="Voting built for Ghana"
                    intro="ClickVote gives organisers a straightforward way to run award shows, elections and AGMs — and gives voters a fast, secure way to take part from any phone." />

<div class="site py-10 sm:py-14 flex flex-col gap-14 sm:gap-20">

    {{-- Numbers --}}
    <section aria-labelledby="numbers">
        <h2 id="numbers" class="sr-only">ClickVote in numbers</h2>
        <div class="grid gap-4 sm:grid-cols-3">
            <x-ui.stat label="Votes cast" :value="number_format($stats['votes'])" icon="check-circle" tone="brand"
                       sub="Across every campaign on the platform" />
            <x-ui.stat label="Nominees" :value="number_format($stats['nominees'])" icon="users" tone="violet"
                       sub="Listed by organisers" />
            <x-ui.stat label="Campaigns" :value="number_format($stats['events'])" icon="trophy" tone="gold"
                       sub="Awards, elections and AGMs" />
        </div>
    </section>

    {{-- How voting works --}}
    <section aria-labelledby="how">
        <p class="eyebrow">How it works</p>
        <h2 id="how" class="text-ink-900 font-extrabold mt-2 mb-8" style="font-size:clamp(26px,3.4vw,34px)">
            From discovering an award to seeing the result
        </h2>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach([
                ['search',       'Discover',  'Browse live awards, elections and AGMs, or search for a nominee by name or code.'],
                ['users',        'Select',    'Open a category, read the nominee profiles and choose who you want to back.'],
                ['mobile',       'Vote',      'Pick how many votes you want and enter the Mobile Money number you will pay from.'],
                ['lock',         'Pay',       'Approve the prompt on your phone. Payments run through Paystack — your PIN stays with your network.'],
                ['check-circle', 'Confirm',   'Your vote is recorded once the provider confirms the payment. You get a reference and a downloadable receipt.'],
                ['chart',        'Follow',    'Where the organiser has published standings, track the race live on the results page.'],
            ] as $i => [$icon, $heading, $copy])
                <div class="card p-6">
                    <div class="flex items-center gap-3 mb-3.5">
                        <span class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                            <x-ui.icon :name="$icon" :size="19" />
                        </span>
                        <span class="font-display text-[13px] font-extrabold text-ink-300">0{{ $i + 1 }}</span>
                    </div>
                    <h3 class="text-[16.5px] font-extrabold text-ink-900">{{ $heading }}</h3>
                    <p class="text-[13.5px] text-ink-500 leading-relaxed mt-2">{{ $copy }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- For organisers --}}
    <section class="rounded-4xl overflow-hidden" style="background:linear-gradient(140deg,#241038,#14031f)"
             aria-labelledby="organisers">
        <div class="p-7 sm:p-12 grid lg:grid-cols-2 gap-10 items-center">
            <div>
                <p class="eyebrow text-brand-400">For organisers</p>
                <h2 id="organisers" class="text-white font-extrabold mt-2.5 leading-tight" style="font-size:clamp(24px,3.2vw,32px)">
                    Everything you need to run a credible campaign
                </h2>
                <p class="text-white/65 text-[15px] leading-relaxed mt-4">
                    Set up your categories and nominees, open voting on web and USSD, watch the money and
                    the votes in real time, and export what you need at the end.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 mt-7">
                    <x-ui.btn :href="route('admin.register')" variant="primary" icon-end="arrow-right">Create an account</x-ui.btn>
                    <x-ui.btn :href="route('admin.login')" variant="onDark">Organiser login</x-ui.btn>
                </div>
            </div>
            <ul class="grid sm:grid-cols-2 gap-3">
                @foreach([
                    'Categories and nominees you control',
                    'Web plus USSD voting channels',
                    'Live revenue and vote dashboards',
                    'Payment reconciliation tools',
                    'Fraud and audit monitoring',
                    'CSV and PDF exports',
                ] as $feature)
                    <li class="flex items-start gap-2.5 rounded-xl bg-white/[.06] border border-white/10 px-4 py-3">
                        <x-ui.icon name="check" :size="15" :stroke="3" class="text-brand-400 mt-1" />
                        <span class="text-[13.5px] text-white/80 leading-snug">{{ $feature }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" aria-labelledby="faq-heading" class="scroll-mt-24">
        <p class="eyebrow">Help centre</p>
        <h2 id="faq-heading" class="text-ink-900 font-extrabold mt-2 mb-8" style="font-size:clamp(26px,3.4vw,34px)">
            Common questions
        </h2>

        <div class="grid gap-3 lg:grid-cols-2" x-data="{ open: null }">
            @foreach([
                ['How much does a vote cost?', 'It depends on the campaign. Each award sets its own price per vote, shown on the award page and on every nominee profile. Some campaigns are free to vote in.'],
                ['How do I pay?', 'Payments go through Paystack. You can pay with MTN MoMo, Telecel Cash, AirtelTigo Money or a bank card. You approve the prompt on your own phone — ClickVote never sees your PIN.'],
                ['When is my vote counted?', 'As soon as the payment provider confirms your payment. If a payment is still processing, the vote is credited the moment confirmation arrives.'],
                ['Can I vote without internet?', 'Yes, where the organiser has enabled it. Dial the campaign shortcode from any Ghana network and follow the prompts.'],
                ['Why can I not see vote counts?', 'Counts are private unless the organiser publishes them. Campaigns configured as an anonymous tally never show per-nominee figures.'],
                ['Can I get a receipt?', 'Yes. After a successful payment you can download a PDF receipt from the confirmation page, showing the nominee, category, number of votes, amount and reference.'],
                ['Can I get a refund?', 'Votes are final and cannot be reversed. If a payment was taken but the vote was not recorded, contact the organiser with your transaction reference.'],
                ['How do I run my own campaign?', 'Register as an organiser. Once your account is approved you can create a campaign, add categories and nominees, and open voting.'],
            ] as $i => [$question, $answer])
                <div class="card overflow-hidden">
                    <h3>
                        <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}"
                                :aria-expanded="(open === {{ $i }}).toString()" aria-controls="faq-{{ $i }}"
                                class="w-full flex items-center justify-between gap-4 text-left px-5 py-4 hover:bg-ink-50/60 transition">
                            <span class="text-[15px] font-bold text-ink-900">{{ $question }}</span>
                            <span class="text-ink-400 shrink-0 transition-transform"
                                  :class="open === {{ $i }} && 'rotate-180'">
                                <x-ui.icon name="chevron-down" :size="17" />
                            </span>
                        </button>
                    </h3>
                    <div id="faq-{{ $i }}" x-show="open === {{ $i }}" x-collapse x-cloak>
                        <p class="px-5 pb-5 text-[14px] text-ink-600 leading-relaxed">{{ $answer }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Contact --}}
    @php $contact = config('clickvote.contact'); @endphp
    <section class="card p-7 sm:p-10 text-center" aria-labelledby="contact">
        <h2 id="contact" class="text-ink-900 font-extrabold" style="font-size:clamp(22px,3vw,28px)">Still need help?</h2>
        <p class="text-[15px] text-ink-500 mt-2.5 max-w-lg mx-auto leading-relaxed">
            For questions about a specific campaign, contact its organiser — they manage the categories,
            nominees and results.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
            @if($contact['email'])
                <x-ui.btn href="mailto:{{ $contact['email'] }}" variant="primary" icon="mail">{{ $contact['email'] }}</x-ui.btn>
            @endif
            @if($contact['phone'])
                <x-ui.btn href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}" variant="outline" icon="phone">{{ $contact['phone'] }}</x-ui.btn>
            @endif
            <x-ui.btn :href="route('vote.privacy')" variant="outline">Privacy policy</x-ui.btn>
        </div>
        @if($contact['hours'])
            <p class="text-[13px] text-ink-400 mt-5">{{ $contact['hours'] }}</p>
        @endif
    </section>
</div>

</x-layouts.public>
