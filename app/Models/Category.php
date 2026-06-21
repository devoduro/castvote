<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'code',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function nominees(): HasMany
    {
        return $this->hasMany(Nominee::class)->orderBy('display_order');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function totalVotes(): int
    {
        return (int) $this->votes()->sum('quantity');
    }
}
