<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrganizerEmailVerification;
use App\Models\Admin;
use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
                'account_status'           => 'pending',
                'is_superadmin'            => false,
                'email_verification_token' => Str::random(64),
            ]);

            AuditLog::record('organizer.registered', $admin, [
                'org_name' => $data['org_name'],
            ]);
        });

        Mail::to($admin->email)->send(new OrganizerEmailVerification($admin));

        return redirect()->route('admin.login')
            ->with('success', 'Account created! Check your email to verify your address. Your account will then be reviewed and approved.');
    }

    public function verifyEmail(Request $request, int $id, string $token)
    {
        $admin = Admin::findOrFail($id);

        if ($admin->email_verified_at) {
            return redirect()->route('admin.login')
                ->with('success', 'Email already verified. You can log in once your account is approved.');
        }

        if (!$admin->email_verification_token || !hash_equals($admin->email_verification_token, $token)) {
            abort(403, 'Invalid or expired verification link.');
        }

        $admin->update([
            'email_verified_at'        => now(),
            'email_verification_token' => null,
        ]);

        AuditLog::record('account.email_verified', $admin);

        return redirect()->route('admin.login')
            ->with('success', 'Email verified! Your account is pending admin approval. You will be notified once it is approved.');
    }
}
