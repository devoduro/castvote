<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class EventForm extends Component
{
    use WithFileUploads;

    public ?Event $event = null;

    public $flyer = null;
    public ?string $existingFlyerPath = null;

    public string $name        = '';
    public string $description = '';
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
    public bool   $public_results            = false;

    public function mount(?Event $event = null): void
    {
        if ($event && $event->exists) {
            $this->event  = $event;
            $this->fill($event->only('name', 'event_type', 'ussd_shortcode', 'ussd_short_id', 'status'));
            $this->description = (string) $event->description;
            $this->starts_at = $event->starts_at?->format('Y-m-d\TH:i') ?? '';
            $this->ends_at   = $event->ends_at?->format('Y-m-d\TH:i')   ?? '';

            $rules = $event->voting_rules ?? [];
            $this->pay_per_vote              = (bool) ($rules['pay_per_vote'] ?? true);
            $this->price_per_vote_pesewas    = (int)  ($rules['price_per_vote_pesewas'] ?? 100);
            $this->max_votes_per_voter       = isset($rules['max_votes_per_voter']) ? (int) $rules['max_votes_per_voter'] : null;
            $this->requires_eligibility_list = (bool) ($rules['requires_eligibility_list'] ?? false);
            $this->anonymous_tally           = (bool) ($rules['anonymous_tally'] ?? false);
            $this->public_results            = (bool) ($rules['public_results'] ?? false);
            $this->existingFlyerPath         = $event->flyer_path;
        }
    }

    public function save(): void
    {
        try {
            $admin = auth('admin')->user();

            if (!$admin) {
                $this->addError('name', 'Session expired — please refresh and log in again.');
                return;
            }

            $editing = $this->event && $this->event->exists;

            $permitted = $editing
                ? $admin->canManageEvent($this->event)
                : ($admin->isManager() || $admin->isSuperAdmin());

            if (! $permitted) {
                $this->addError('name', 'You do not have permission to save events.');
                return;
            }

            $this->validate([
                'name'                    => 'required|string|max:255',
                'description'             => 'nullable|string|max:600',
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
                'public_results'          => 'boolean',
                'flyer'                   => 'nullable|image|max:2048',
            ]);

            $flyerPath = $this->existingFlyerPath;
            if ($this->flyer) {
                if ($this->existingFlyerPath) {
                    Storage::disk('public')->delete($this->existingFlyerPath);
                }
                $flyerPath = $this->flyer->store('flyers', 'public');
            }

            $data = [
                'name'            => $this->name,
                'description'     => $this->description ?: null,
                'slug'            => Str::slug($this->name) . '-' . Str::random(5),
                'event_type'      => $this->event_type,
                'ussd_shortcode'  => $this->ussd_shortcode ?: null,
                'ussd_short_id'   => $this->ussd_short_id  ?: null,
                'starts_at'       => $this->starts_at       ?: null,
                'ends_at'         => $this->ends_at         ?: null,
                'status'          => $this->status,
                'flyer_path'      => $flyerPath,
                'voting_rules'    => [
                    'pay_per_vote'              => $this->pay_per_vote,
                    'price_per_vote_pesewas'    => $this->price_per_vote_pesewas,
                    'max_votes_per_voter'       => $this->max_votes_per_voter,
                    'requires_eligibility_list' => $this->requires_eligibility_list,
                    'anonymous_tally'           => $this->anonymous_tally,
                    'public_results'            => $this->public_results,
                ],
            ];

            if ($editing) {
                // Never rewrite organization_id on an update: a superadmin can
                // now edit any campaign, and stamping their own org onto it
                // would quietly transfer the campaign away from its organiser.
                unset($data['slug']);
                $this->event->update($data);
                AuditLog::record('event.updated', $this->event);
                session()->flash('success', 'Event updated.');
            } else {
                $event = Event::create($data + ['organization_id' => $admin->organization_id]);
                AuditLog::record('event.created', $event);
                session()->flash('success', 'Event created.');
            }

            $this->redirect(route('admin.events.index'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // let Livewire handle validation normally
        } catch (\Throwable $e) {
            \Log::error('EventForm::save failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->addError('name', 'Save failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.event-form');
    }
}
