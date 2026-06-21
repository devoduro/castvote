<?php

namespace App\Livewire\Vote;

use App\Models\EligibleVoter;
use App\Models\Event;
use Livewire\Component;

class EligibilityGate extends Component
{
    public Event  $event;
    public string $identifier = '';   // student index number or shareholder ID
    public bool   $verified   = false;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function verify(): void
    {
        $this->validate([
            'identifier' => ['required', 'string', 'min:3', 'max:50'],
        ], [
            'identifier.required' => 'Please enter your student index number or voter ID.',
        ]);

        $voter = EligibleVoter::where('event_id', $this->event->id)
            ->where('identifier', trim($this->identifier))
            ->where('eligible', true)
            ->first();

        if (!$voter) {
            $this->addError('identifier', 'This ID is not on the eligible voters list for this event.');
            return;
        }

        if ($voter->hasVoted()) {
            $this->addError('identifier', 'This ID has already been used to vote in this event.');
            return;
        }

        // Mark as voted immediately to prevent race conditions
        $voter->markVoted();

        $this->verified = true;
    }

    public function render()
    {
        return view('livewire.vote.eligibility-gate');
    }
}
