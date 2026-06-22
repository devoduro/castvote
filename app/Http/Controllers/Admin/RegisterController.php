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

        DB::transaction(function () use ($data) {
            $org = Organization::create([
                'name'          => $data['org_name'],
                'contact_email' => $data['email'],
                'phone'         => $data['phone'] ?? null,
            ]);

            $admin = Admin::create([
                'organization_id' => $org->id,
                'name'            => $data['name'],
                'email'           => $data['email'],
                'phone'           => $data['phone'] ?? null,
                'password'        => $data['password'],
                'role'            => 'owner',
                'account_status'  => 'pending',
                'is_superadmin'   => false,
            ]);

            AuditLog::record('organizer.registered', $admin, [
                'org_name' => $data['org_name'],
            ]);
        });

        return redirect()->route('admin.login')
            ->with('success', 'Account created! It is pending approval. You will be notified by email once approved.');
    }
}
