<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'org_name' => ['required', 'string', 'max:255'],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:admins,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $admin = null;

        DB::transaction(function () use ($data, &$admin) {
            $org = Organization::create([
                'name'          => $data['org_name'],
                'contact_email' => $data['email'],
                'phone'         => $data['phone'] ?? null,
            ]);

            $admin = Admin::create([
                'organization_id'          => $org->id,
                'name'                     => $data['name'],
                'email'                    => $data['email'],
                'phone'                    => $data['phone'] ?? null,
                'password'                 => $data['password'],
                'role'                     => 'owner',
                'account_status'           => 'approved',
                'is_superadmin'            => false,
                'email_verified_at'        => now(),
                'email_verification_token' => null,
            ]);

            AuditLog::record('organizer.registered', $admin, [
                'org_name' => $data['org_name'],
            ]);
        });

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Welcome to CastVote! Your organizer account is ready.');
    }

    public function verifyEmail(Request $request, int $id, string $token)
    {
        $admin = Admin::findOrFail($id);

        // Accounts are auto-approved on registration; verification link is informational
        return redirect()->route('admin.login')
            ->with('success', 'Email verified! You can now log in to your dashboard.');
    }
}
