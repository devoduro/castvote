<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
        'event_type',
        'voting_rules',
        'ussd_shortcode',
        'ussd_short_id',
        'starts_at',
        'ends_at',
        'status',
        'flyer_path',
    ];

    protected $casts = [
        'voting_rules' => 'array',
        'starts_at'    => 'datetime',
        'ends_at'      => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('display_order');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function eligibleVoters(): HasMany
    {
        return $this->hasMany(EligibleVoter::class);
    }

    public function ussdSessions(): HasMany
    {
        return $this->hasMany(UssdSession::class);
    }

    // Voting rules convenience accessors

    public function isPayPerVote(): bool
    {
        return (bool) ($this->voting_rules['pay_per_vote'] ?? false);
    }

    public function pricePerVotePesewas(): int
    {
        return (int) ($this->voting_rules['price_per_vote_pesewas'] ?? 100);
    }

    public function maxVotesPerVoter(): ?int
    {
        $max = $this->voting_rules['max_votes_per_voter'] ?? null;
        return $max !== null ? (int) $max : null;
    }

    public function requiresEligibilityList(): bool
    {
        return (bool) ($this->voting_rules['requires_eligibility_list'] ?? false);
    }

    public function isAnonymousTally(): bool
    {
        return (bool) ($this->voting_rules['anonymous_tally'] ?? false);
    }

    /**
     * Whether the organiser has opted to publish standings on the public site.
     * Defaults to false so tallies stay admin-only unless explicitly released.
     */
    public function resultsArePublic(): bool
    {
        return (bool) ($this->voting_rules['public_results'] ?? false);
    }

    public function isLive(): bool
    {
        return $this->status === 'live'
            && now()->between($this->starts_at, $this->ends_at);
    }

    /**
     * Whether the ballot has shut — closed by the organiser, or past its end.
     *
     * A closed campaign is still *reachable* by USSD so callers can check the
     * votes they already paid for; only the ballot itself is withdrawn. A
     * draft campaign is not closed, it has simply not opened yet.
     */
    public function votingHasClosed(): bool
    {
        if ($this->status === 'closed') {
            return true;
        }

        return $this->status === 'live'
            && $this->ends_at !== null
            && now()->greaterThan($this->ends_at);
    }

    public function priceInGhs(): string
    {
        return number_format($this->pricePerVotePesewas() / 100, 2);
    }

    public function flyerUrl(): ?string
    {
        return $this->flyer_path ? asset('storage/' . $this->flyer_path) : null;
    }

    /** Human label for the campaign type, used across cards and headers. */
    public function typeLabel(): string
    {
        return match ($this->event_type) {
            'award'    => 'Awards',
            'election' => 'Election',
            'agm'      => 'AGM',
            default    => Str::headline((string) $this->event_type),
        };
    }

    public function statusLabel(): string
    {
        return $this->isLive() ? 'Voting open' : ($this->status === 'draft' ? 'Coming soon' : 'Voting closed');
    }

    public function totalVotes(): int
    {
        return (int) $this->votes()->sum('quantity');
    }

    public function nomineeCount(): int
    {
        return Nominee::whereIn('category_id', $this->categories()->select('id'))->count();
    }
}
