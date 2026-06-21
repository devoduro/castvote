<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Payment;
use App\Services\PaystackWebhookService;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentReconciliation extends Component
{
    use WithPagination;

    public Event  $event;
    public string $statusFilter = '';
    public string $search       = '';

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function reverify(int $paymentId, PaystackWebhookService $webhookService): void
    {
        $payment = Payment::where('event_id', $this->event->id)
            ->where('status', 'pending')
            ->findOrFail($paymentId);

        $result = $webhookService->reverify($payment);

        $this->dispatch('notify',
            type:    $result ? 'success' : 'info',
            message: $result ? 'Payment verified and vote credited.' : 'Still pending at Paystack.',
        );
    }

    public function render()
    {
        $payments = Payment::where('event_id', $this->event->id)
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn($q) => $q->where('phone_number', 'like', "%{$this->search}%")
                ->orWhere('provider_reference', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(25);

        $summary = Payment::where('event_id', $this->event->id)
            ->selectRaw('status, COUNT(*) as count, SUM(amount_pesewas) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('livewire.admin.payment-reconciliation', compact('payments', 'summary'));
    }
}
