<x-layouts.public>
<x-slot name="title">Privacy Policy</x-slot>

<div class="max-w-2xl mx-auto prose prose-sm text-gray-700">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Privacy Notice</h1>
    <p class="text-xs text-gray-400 mb-6">In compliance with the Ghana Data Protection Act, 2012 (Act 843)</p>

    <h2 class="text-lg font-semibold mt-6 mb-2">1. Data Controller</h2>
    <p>CastVote operates as a data processor on behalf of event organisers (data controllers) registered with Ghana's Data Protection Commission (DPC).</p>

    <h2 class="text-lg font-semibold mt-6 mb-2">2. Data Collected</h2>
    <ul class="list-disc ml-5 space-y-1">
        <li>Mobile phone number — used to process your vote payment and send SMS confirmation</li>
        <li>Mobile Money network — used to route the payment charge</li>
        <li>Vote choice (nominee and category) — linked to your payment for audit purposes</li>
        <li>IP address — collected for fraud prevention</li>
    </ul>
    <p class="mt-2 text-xs text-gray-500 bg-yellow-50 border border-yellow-200 rounded p-3">
        <strong>We never see or store your Mobile Money PIN.</strong> Your PIN is entered directly with your Mobile Network Operator in their own secure system.
    </p>

    <h2 class="text-lg font-semibold mt-6 mb-2">3. Purpose of Processing</h2>
    <p>Your data is processed solely to (a) authenticate your payment, (b) record your vote in the event ledger, (c) send SMS confirmation, and (d) detect and prevent fraudulent voting patterns.</p>

    <h2 class="text-lg font-semibold mt-6 mb-2">4. Data Retention</h2>
    <ul class="list-disc ml-5 space-y-1">
        <li><strong>Award events:</strong> phone numbers retained for 12 months post-event close, then deleted.</li>
        <li><strong>Student elections with anonymous tally:</strong> phone numbers are irreversibly nulled from the votes ledger at event close. Payment records (for financial reconciliation) retain the number for 7 years per Ghana's electronic transactions regulations.</li>
        <li><strong>Audit logs:</strong> retained for 3 years.</li>
    </ul>

    <h2 class="text-lg font-semibold mt-6 mb-2">5. Your Rights (Act 843, Section 18)</h2>
    <p>You have the right to access, correct, or request deletion of your personal data. Contact the event organiser or email <a href="mailto:privacy@castvote.test" class="text-brand-600 hover:underline">privacy@castvote.test</a>.</p>

    <h2 class="text-lg font-semibold mt-6 mb-2">6. Third Parties</h2>
    <p>Payment data is shared with <strong>Paystack</strong> (licensed payment service provider) solely to process the Mobile Money charge. Arkesel receives your phone number to deliver SMS confirmations. No data is sold to third parties.</p>

    <p class="text-xs text-gray-400 mt-8">Last updated: {{ date('F Y') }}</p>
</div>

</x-layouts.public>
