<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Event;
use Illuminate\Support\Str;
use Livewire\Component;

class EventForm extends Component
{
    public ?Event $event = null;

    public string $name        = '';
    public string $event_type  = 'award';
    public string $ussd_shortcode = '';
    public string $ussd_short_id  = '';
    public string $starts_at   = '';
    public string $ends_at     = '';
    public string $status      = 'draft';

    // voting_rules fields
    public bool   $pay_per_vote              = true;
    public int    $price_per_vote_pesewas    = 100;
    public ?int   $max_votes_per_voter       = null;
    public bool   $requires_eligibility_list = false;
    public bool   $anonymous_tally           = false;

    public function mount(?Event $event = null): void
    {
        if ($event && $event->exists) {
            $this->event  = $event;
            $this->fill($event->only('name', 'event_type', 'ussd_shortcode', 'ussd_short_id', 'status'));
            $this->starts_at = $event->starts_at?->format('Y-m-d\TH:i') ?? '';
            $this->ends_at   = $event->ends_at?->format('Y-m-d\TH:i')   ?? '';

            $rules = $event->voting_rules ?? [];
            $this->pay_per_vote              = (bool) ($rules['pay_per_vote'] ?? true);
            $this->price_per_vote_pesewas    = (int)  ($rules['price_per_vote_pesewas'] ?? 100);
            $this->max_votes_per_voter       = isset($rules['max_votes_per_voter']) ? (int) $rules['max_votes_per_voter'] : null;
            $this->requires_eligibility_list = (bool) ($rules['requires_eligibility_list'] ?? false);
            $this->anonymous_tally           = (bool) ($rules['anonymous_tally'] ?? false);
        }
    }

    public function save(): void
    {
        $admin = auth('admin')->user();

        if (!$admin->isManager()) {
            $this->addError('name', 'You do not have permission to save events.');
            return;
        }

        $validated = $this->validate([
            'name'                    => 'required|string|max:255',
            'event_type'              => 'required|in:award,agm,election',
            'ussd_shortcode'          => 'nullable|string|max:30',
            'ussd_short_id'           => 'nullable|string|max:20',
            'starts_at'               => 'nullable|date',
            'ends_at'                 => 'nullable|date|after_or_equal:starts_at',
            'status'                  => 'required|in:draft,live,closed',
            'pay_per_vote'            => 'boolean',
            'price_per_vote_pesewas'  => 'required|integer|min:0',
            'max_votes_per_voter'     => 'nullable|integer|min:1',
            'requires_eligibility_list' => 'boolean',
            'anonymous_tally'         => 'boolean',
        ]);

        $data = [
            'organization_id' => $admin->organization_id,
            'name'            => $this->name,
            'slug'            => Str::slug($this->name) . '-' . Str::random(5),
            'event_type'      => $this->event_type,
            'ussd_shortcode'  => $this->ussd_shortcode ?: null,
            'ussd_short_id'   => $this->ussd_short_id  ?: null,
            'starts_at'       => $this->starts_at       ?: null,
            'ends_at'         => $this->ends_at         ?: null,
            'status'          => $this->status,
            'voting_rules'    => [
                'pay_per_vote'              => $this->pay_per_vote,
                'price_per_vote_pesewas'    => $this->price_per_vote_pesewas,
                'max_votes_per_voter'       => $this->max_votes_per_voter,
                'requires_eligibility_list' => $this->requires_eligibility_list,
                'anonymous_tally'           => $this->anonymous_tally,
            ],
        ];

        if ($this->event && $this->event->exists) {
            // Keep slug stable on update
            unset($data['slug']);
            $this->event->update($data);
            AuditLog::record('event.updated', $this->event);
            session()->flash('success', 'Event updated.');
        } else {
            $event = Event::create($data);
            AuditLog::record('event.created', $event);
            session()->flash('success', 'Event created.');
        }

        $this->redirect(route('admin.events.index'));
    }

    public function render()
    {
        return view('livewire.admin.event-form');
    }
}
