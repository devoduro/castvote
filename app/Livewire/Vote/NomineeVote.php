<?php

namespace App\Livewire\Vote;

use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Services\PaymentInitiationService;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Voting panel on a nominee's profile page.
 *
 * A thin view-model over the existing flow: it calls the same
 * PaymentInitiationService and opens the same Paystack Inline popup as the
 * category ballot, so there is one payment path in the application.
 */
class NomineeVote extends Component
{
    public Event    $event;
    public Category $category;
    public Nominee  $nominee;

    /** 'form' while collecting details, 'paying' once the popup is open. */
    public string $step = 'form';

    public int    $quantity = 1;
    public string $phone    = '';

    public array  $paystackConfig   = [];
    public string $pendingReference = '';

    public function mount(Nominee $nominee): void
    {
        $this->nominee  = $nominee;
        $this->category = $nominee->category;
        $this->event    = $nominee->category->event;
    }

    /** Upper bound for one transaction: the organiser's cap, or 50. */
    #[Computed]
    public function maxQuantity(): int
    {
        return min(50, $this->event->maxVotesPerVoter() ?? 50);
    }

    #[Computed]
    public function amountPesewas(): int
    {
        return $this->quantity * $this->event->pricePerVotePesewas();
    }

    #[Computed]
    public function amountGhs(): string
    {
        return number_format($this->amountPesewas() / 100, 2);
    }

    public function increment(): void
    {
        $this->quantity = min($this->maxQuantity(), $this->quantity + 1);
    }

    public function decrement(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function setQuantity(int $value): void
    {
        $this->quantity = max(1, min($this->maxQuantity(), $value));
    }

    public function updatedQuantity(): void
    {
        $this->setQuantity((int) $this->quantity);
    }

    public function proceedToPayment(PaymentInitiationService $paymentService): void
    {
        if (! $this->event->isLive()) {
            $this->addError('quantity', 'Voting for this campaign is not currently open.');
            return;
        }

        $rules = [
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $this->maxQuantity()],
        ];

        if ($this->event->isPayPerVote()) {
            $rules['phone'] = ['required', 'string', 'regex:/^(\+?233|0)[2-9][0-9]{8}$/'];
        }

        $this->validate($rules, [
            'phone.required' => 'Enter the Mobile Money number you want to pay with.',
            'phone.regex'    => 'Enter a valid Ghana mobile number, e.g. 024 123 4567.',
            'quantity.min'   => 'Choose at least 1 vote.',
            'quantity.max'   => 'You can buy up to ' . $this->maxQuantity() . ' votes in one transaction.',
        ]);

        $payment = $paymentService->createPendingPayment(
            event:      $this->event,
            phone:      $this->phone ?: '0000000000',
            nomineeId:  $this->nominee->id,
            categoryId: $this->category->id,
            quantity:   $this->quantity,
            channel:    'web',
        );

        $this->pendingReference = $payment->provider_reference;

        $this->paystackConfig = $paymentService->paystackInlineConfig(
            payment:     $payment,
            callbackUrl: route('vote.payment-callback'),
        );

        $this->step = 'paying';

        $this->dispatch('open-paystack-popup', config: $this->paystackConfig);
    }

    public function backToForm(): void
    {
        $this->step = 'form';
    }

    public function render()
    {
        return view('livewire.vote.nominee-vote');
    }
}
