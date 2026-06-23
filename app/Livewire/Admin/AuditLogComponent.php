<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogComponent extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $orgAdminIds = \App\Models\Admin::where('organization_id', auth('admin')->user()->organization_id)
            ->orWhere(fn($q) => $q->where('is_superadmin', true))
            ->pluck('id');

        if (auth('admin')->user()->isSuperAdmin()) {
            $query = AuditLog::with('admin')->latest('created_at');
        } else {
            $query = AuditLog::with('admin')
                ->whereIn('admin_id', $orgAdminIds)
                ->latest('created_at');
        }

        $logs = $query
            ->when($this->search, fn($q) => $q->where('action', 'like', "%{$this->search}%"))
            ->paginate(30);

        return view('livewire.admin.audit-log', compact('logs'));
    }
}
