<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nominee extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'photo_path',
        'bio',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function totalVotes(): int
    {
        return (int) $this->votes()->sum('quantity');
    }

    public function photoUrl(): string
    {
        return $this->photo_path
            ? asset('storage/' . $this->photo_path)
            : asset('images/default-nominee.png');
    }
}
