<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'account_status',
        'is_superadmin',
        'email_verified_at',
        'email_verification_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_token',
    ];

    protected $casts = [
        'password'           => 'hashed',
        'is_superadmin'      => 'boolean',
        'email_verified_at'  => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isManager(): bool
    {
        return in_array($this->role, ['owner', 'manager']);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_superadmin;
    }

    public function isApproved(): bool
    {
        return $this->account_status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->account_status === 'pending';
    }

    /**
     * Whether this admin may open a campaign's admin pages.
     *
     * Organisers see only their own campaigns. A superadmin runs the platform
     * itself and belongs to its own organisation, so an org comparison would
     * lock them out of every campaign but their own — they are scoped by the
     * superadmin flag instead.
     */
    public function canAccessEvent(Event $event): bool
    {
        return $this->isSuperAdmin()
            || $this->organization_id === $event->organization_id;
    }

    public function canManageEvent(Event $event): bool
    {
        return $this->canAccessEvent($event)
            && ($this->isManager() || $this->isSuperAdmin());
    }
}
