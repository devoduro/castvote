<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EligibleVoter extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'identifier',
        'eligible',
        'voted_at',
    ];

    protected $casts = [
        'eligible'  => 'boolean',
        'voted_at'  => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function hasVoted(): bool
    {
        return $this->voted_at !== null;
    }

    public function markVoted(): void
    {
        $this->update(['voted_at' => now()]);
    }
}
