<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'audit_log';

    protected $fillable = [
        'admin_id',
        'action',
        'subject_type',
        'subject_id',
        'meta',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'meta'       => 'array',
        'created_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public static function record(
        string $action,
        ?Model $subject = null,
        array $meta = [],
        ?int $adminId = null,
    ): self {
        return static::create([
            'admin_id'     => $adminId ?? auth('admin')->id(),
            'action'       => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->getKey(),
            'meta'         => $meta,
            'ip_address'   => request()->ip(),
            'created_at'   => now(),
        ]);
    }
}
