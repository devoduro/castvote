<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use App\Models\AuditLog;
use Livewire\Component;

class AccountApprovals extends Component
{
    public string $tab = 'pending';

    public function approve(int $adminId): void
    {
        $this->guardSuperAdmin($adminId);

        $admin = Admin::findOrFail($adminId);
        $admin->update(['account_status' => 'approved']);

        AuditLog::record('account.approved', $admin, [], auth('admin')->id());

        session()->flash('success', "{$admin->name}'s account has been approved.");
    }

    public function reject(int $adminId): void
    {
        $this->guardSuperAdmin($adminId);

        $admin = Admin::findOrFail($adminId);
        $admin->update(['account_status' => 'rejected']);

        AuditLog::record('account.rejected', $admin, [], auth('admin')->id());

        session()->flash('success', "{$admin->name}'s account has been rejected.");
    }

    private function guardSuperAdmin(int $adminId): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
        abort_if($adminId === auth('admin')->id(), 403);
    }

    public function render()
    {
        $accounts = Admin::with('organization')
            ->where('is_superadmin', false)
            ->when($this->tab === 'pending',  fn($q) => $q->where('account_status', 'pending'))
            ->when($this->tab === 'approved', fn($q) => $q->where('account_status', 'approved'))
            ->when($this->tab === 'rejected', fn($q) => $q->where('account_status', 'rejected'))
            ->latest()
            ->paginate(20);

        $counts = [
            'pending'  => Admin::where('is_superadmin', false)->where('account_status', 'pending')->count(),
            'approved' => Admin::where('is_superadmin', false)->where('account_status', 'approved')->count(),
            'rejected' => Admin::where('is_superadmin', false)->where('account_status', 'rejected')->count(),
        ];

        return view('livewire.admin.account-approvals', compact('accounts', 'counts'));
    }
}
