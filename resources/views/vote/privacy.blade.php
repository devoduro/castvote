<x-layouts.public>
<x-slot name="title">Privacy Policy</x-slot>
<x-slot name="description">How ClickVote collects, uses and retains voter data, in line with Ghana's Data Protection Act, 2012 (Act 843).</x-slot>

<x-site.page-header eyebrow="Legal" title="Privacy Notice"
                    intro="How we handle your data, in compliance with Ghana's Data Protection Act, 2012 (Act 843)." />

<div class="site py-10 sm:py-14">
    <article class="card p-6 sm:p-10 max-w-3xl mx-auto">

        <section class="mb-8">
            <h2 class="text-[18px] font-extrabold text-ink-900 mb-2.5">1. Data controller</h2>
            <p class="text-[14.5px] text-ink-600 leading-relaxed">
                ClickVote operates as a data processor on behalf of event organisers (the data controllers),
                who are registered with Ghana's Data Protection Commission.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-[18px] font-extrabold text-ink-900 mb-2.5">2. Data we collect</h2>
            <ul class="flex flex-col gap-2.5">
                @foreach([
                    ['Mobile phone number', 'Used to process your vote payment and send an SMS confirmation.'],
                    ['Mobile Money network', 'Used to route the payment charge to the right provider.'],
                    ['Vote choice', 'The nominee and category, linked to your payment for audit purposes.'],
                    ['IP address', 'Collected for fraud prevention.'],
                ] as [$item, $why])
                    <li class="flex items-start gap-2.5">
                        <x-ui.icon name="check" :size="15" :stroke="3" class="text-brand-600 mt-1" />
                        <span class="text-[14.5px] text-ink-600 leading-relaxed">
                            <strong class="text-ink-900 font-bold">{{ $item }}</strong> — {{ $why }}
                        </span>
                    </li>
                @endforeach
            </ul>

            <p class="flex items-start gap-3 rounded-2xl bg-amber-50 border border-amber-200 p-4 mt-5">
                <x-ui.icon name="lock" :size="18" class="text-amber-700 mt-px" />
                <span class="text-[13.5px] text-amber-900 leading-relaxed">
                    <strong>We never see or store your Mobile Money PIN.</strong> You enter it directly with your
                    mobile network operator, in their own secure system.
                </span>
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-[18px] font-extrabold text-ink-900 mb-2.5">3. Purpose of processing</h2>
            <p class="text-[14.5px] text-ink-600 leading-relaxed">
                Your data is processed solely to authenticate your payment, record your vote in the event
                ledger, send an SMS confirmation, and detect and prevent fraudulent voting patterns.
            </p>
        </section>

        <section class="mb-8">
            <h2 class="text-[18px] font-extrabold text-ink-900 mb-2.5">4. Data retention</h2>
            <ul class="flex flex-col gap-2.5">
                @foreach([
                    ['Award events', 'Phone numbers are retained for 12 months after the event closes, then deleted.'],
                    ['Elections with an anonymous tally', 'Phone numbers are irreversibly removed from the votes ledger at event close. Payment records keep the number for 7 years for financial reconciliation, as required by Ghana\'s electronic transactions regulations.'],
                    ['Audit logs', 'Retained for 3 years.'],
                ] as [$item, $rule])
                    <li class="flex items-start gap-2.5">
                        <x-ui.icon name="clock" :size="15" class="text-brand-600 mt-1" />
                        <span class="text-[14.5px] text-ink-600 leading-relaxed">
                            <strong class="text-ink-900 font-bold">{{ $item }}:</strong> {{ $rule }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="mb-8">
            <h2 class="text-[18px] font-extrabold text-ink-900 mb-2.5">5. Your rights (Act 843, Section 18)</h2>
            <p class="text-[14.5px] text-ink-600 leading-relaxed">
                You have the right to access, correct or request deletion of your personal data. Contact the
                event organiser
                @if(config('clickvote.contact.email'))
                    or email
                    <a href="mailto:{{ config('clickvote.contact.email') }}"
                       class="text-brand-700 font-semibold hover:underline">{{ config('clickvote.contact.email') }}</a>.
                @else
                    to make a request.
                @endif
            </p>
        </section>

        <section>
            <h2 class="text-[18px] font-extrabold text-ink-900 mb-2.5">6. Third parties</h2>
            <p class="text-[14.5px] text-ink-600 leading-relaxed">
                Payment data is shared with <strong class="text-ink-900">Paystack</strong>, a licensed payment
                service provider, solely to process the Mobile Money charge. Arkesel receives your phone number
                to deliver SMS confirmations. No data is sold to third parties.
            </p>
        </section>

        <p class="text-[12.5px] text-ink-400 mt-9 pt-6 border-t border-ink-100">
            Last updated: {{ date('F Y') }}
        </p>
    </article>
</div>

</x-layouts.public>
