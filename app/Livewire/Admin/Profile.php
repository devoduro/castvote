<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Profile extends Component
{
    // Account fields
    public string $name  = '';
    public string $email = '';
    public string $phone = '';

    // Org fields
    public string $orgName    = '';
    public string $orgEmail   = '';
    public string $orgWebsite = '';
    public string $orgPhone   = '';

    // Password change
    public string $currentPassword   = '';
    public string $newPassword        = '';
    public string $confirmPassword    = '';

    public function mount(): void
    {
        $admin = auth('admin')->user();
        $org   = $admin->organization;

        $this->name  = $admin->name;
        $this->email = $admin->email;
        $this->phone = $admin->phone ?? '';

        $this->orgName    = $org?->name ?? '';
        $this->orgEmail   = $org?->contact_email ?? '';
        $this->orgWebsite = $org?->website ?? '';
        $this->orgPhone   = $org?->phone ?? '';
    }

    public function saveAccount(): void
    {
        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:admins,email,' . auth('admin')->id()],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        auth('admin')->user()->update([
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
        ]);

        AuditLog::record('profile.updated', auth('admin')->user());
        session()->flash('profile_success', 'Account details updated.');
    }

    public function saveOrganization(): void
    {
        $this->validate([
            'orgName'    => ['required', 'string', 'max:255'],
            'orgEmail'   => ['required', 'email', 'max:255'],
            'orgWebsite' => ['nullable', 'url', 'max:255'],
            'orgPhone'   => ['nullable', 'string', 'max:20'],
        ]);

        auth('admin')->user()->organization?->update([
            'name'          => $this->orgName,
            'contact_email' => $this->orgEmail,
            'website'       => $this->orgWebsite ?: null,
            'phone'         => $this->orgPhone ?: null,
        ]);

        AuditLog::record('organization.updated', auth('admin')->user()->organization);
        session()->flash('org_success', 'Organization details updated.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required'],
            'newPassword'     => ['required', 'confirmed', Password::min(8)],
        ], [], [
            'newPassword'  => 'new password',
            'confirmPassword' => 'confirm password',
        ]);

        $admin = auth('admin')->user();

        if (!Hash::check($this->currentPassword, $admin->password)) {
            $this->addError('currentPassword', 'Current password is incorrect.');
            return;
        }

        $admin->update(['password' => $this->newPassword]);
        $this->reset('currentPassword', 'newPassword', 'confirmPassword');

        AuditLog::record('password.changed', $admin);
        session()->flash('password_success', 'Password changed successfully.');
    }

    public function render()
    {
        return view('livewire.admin.profile');
    }
}
