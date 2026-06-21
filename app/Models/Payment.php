<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'provider',
        'provider_reference',
        'amount_pesewas',
        'currency',
        'phone_number',
        'momo_network',
        'status',
        'raw_webhook_payload',
        'metadata',
        'verified_at',
    ];

    protected $casts = [
        'amount_pesewas'      => 'integer',
        'raw_webhook_payload' => 'array',
        'metadata'            => 'array',
        'verified_at'         => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function vote(): HasOne
    {
        return $this->hasOne(Vote::class);
    }

    public function amountInGhs(): string
    {
        return number_format($this->amount_pesewas / 100, 2);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function markSuccess(array $webhookPayload): void
    {
        $this->update([
            'status'              => 'success',
            'raw_webhook_payload' => $webhookPayload,
            'verified_at'         => now(),
        ]);
    }

    public function markFailed(array $webhookPayload): void
    {
        $this->update([
            'status'              => 'failed',
            'raw_webhook_payload' => $webhookPayload,
        ]);
    }
}
