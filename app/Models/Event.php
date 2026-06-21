<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'event_type',
        'voting_rules',
        'ussd_shortcode',
        'ussd_short_id',
        'starts_at',
        'ends_at',
        'status',
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

    public function isLive(): bool
    {
        return $this->status === 'live'
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function priceInGhs(): string
    {
        return number_format($this->pricePerVotePesewas() / 100, 2);
    }
}
