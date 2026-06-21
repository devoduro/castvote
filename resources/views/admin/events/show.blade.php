<x-layouts.admin>
    <x-slot name="title">{{ $event->name }}</x-slot>

    <div class="mb-4 flex items-center gap-3 text-sm">
        <a href="{{ route('admin.events.index') }}" class="text-gray-400 hover:text-gray-600">← Events</a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-600">{{ $event->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <livewire:admin.category-manager :event="$event" />
        </div>
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-700 mb-3">Quick Links</h3>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('admin.events.results', $event) }}"
                       class="flex items-center gap-2 text-orange-600 hover:underline">📊 Live Results</a>
                    <a href="{{ route('admin.events.payments', $event) }}"
                       class="flex items-center gap-2 text-blue-600 hover:underline">💳 Payments</a>
                    <a href="{{ route('admin.events.fraud', $event) }}"
                       class="flex items-center gap-2 text-red-500 hover:underline">🚨 Fraud Signals</a>
                    <a href="{{ route('admin.events.edit', $event) }}"
                       class="flex items-center gap-2 text-gray-500 hover:underline">⚙️ Edit Event</a>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-700 mb-2">Exports</h3>
                <div class="space-y-2 text-sm">
                    <a href="{{ route('admin.events.export.results-pdf', $event) }}"
                       class="block text-orange-600 hover:underline">Download Results PDF</a>
                    <a href="{{ route('admin.events.export.payments-csv', $event) }}"
                       class="block text-blue-600 hover:underline">Download Payments CSV</a>
                    <a href="{{ route('admin.events.export.audit-csv', $event) }}"
                       class="block text-gray-500 hover:underline">Download Audit Log CSV</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
