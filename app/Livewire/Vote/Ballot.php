<?php

namespace App\Livewire\Vote;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Services\PaymentInitiationService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Ballot extends Component
{
    public Event $event;

    // Step: 'browse' | 'confirm' | 'paying'
    public string $step = 'browse';

    public ?int    $selectedCategoryId = null;
    public ?int    $selectedNomineeId  = null;
    public int     $quantity           = 1;
    public string  $phone              = '';

    // Paystack inline config (set just before showing the popup)
    public array  $paystackConfig = [];
    public string $pendingReference = '';

    public function mount(Event $event): void
    {
        $this->event              = $event;
        $this->selectedCategoryId = $event->categories()->value('id');
    }

    #[Computed]
    public function categories()
    {
        return $this->event->categories()->orderBy('display_order')->get();
    }

    #[Computed]
    public function nominees()
    {
        if (!$this->selectedCategoryId) return collect();
        return Nominee::where('category_id', $this->selectedCategoryId)
            ->orderBy('display_order')
            ->get();
    }

    #[Computed]
    public function selectedCategory()
    {
        return $this->selectedCategoryId
            ? Category::find($this->selectedCategoryId)
            : null;
    }

    #[Computed]
    public function selectedNominee()
    {
        return $this->selectedNomineeId
            ? Nominee::find($this->selectedNomineeId)
            : null;
    }

    public function selectCategory(int $id): void
    {
        $this->selectedCategoryId = $id;
        $this->selectedNomineeId  = null;
        $this->step = 'browse';
    }

    public function selectNominee(int $id): void
    {
        $this->selectedNomineeId = $id;

        if (!$this->event->isPayPerVote()) {
            $this->quantity = 1;
        }

        $this->step = 'confirm';
    }

    public function backToBrowse(): void
    {
        $this->step = 'browse';
        $this->selectedNomineeId = null;
    }

    public function proceedToPayment(PaymentInitiationService $paymentService): void
    {
        $rules = [
            'phone'    => ['required', 'string', 'regex:/^(\+?233|0)[2-9][0-9]{8}$/'],
            'quantity' => ['required', 'integer', 'min:1', 'max:50'],
        ];

        if (!$this->event->isPayPerVote()) {
            unset($rules['phone']);
        }

        $this->validate($rules, [
            'phone.regex'    => 'Please enter a valid Ghana mobile number (e.g. 0244123456).',
            'quantity.min'   => 'Minimum 1 vote.',
            'quantity.max'   => 'Maximum 50 votes per transaction.',
        ]);

        if (!$this->selectedNomineeId || !$this->selectedCategoryId) {
            $this->addError('quantity', 'Please select a nominee first.');
            return;
        }

        $payment = $paymentService->createPendingPayment(
            event:       $this->event,
            phone:       $this->phone ?: '0000000000',
            nomineeId:   $this->selectedNomineeId,
            categoryId:  $this->selectedCategoryId,
            quantity:    $this->quantity,
            channel:     'web',
        );

        $this->pendingReference = $payment->provider_reference;

        // Build Paystack Inline config — passed to JS
        $this->paystackConfig = $paymentService->paystackInlineConfig(
            payment:     $payment,
            callbackUrl: route('vote.payment-callback'),
        );

        $this->step = 'paying';

        // Dispatch browser event to open Paystack popup
        $this->dispatch('open-paystack-popup', config: $this->paystackConfig);
    }

    public function render()
    {
        return view('livewire.vote.ballot');
    }
}
