<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UssdSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'arkesel_session_id',
        'event_id',
        'phone_number',
        'current_step',
        'payload',
        'status',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function markCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markExpired(): void
    {
        $this->update(['status' => 'expired']);
    }
}
