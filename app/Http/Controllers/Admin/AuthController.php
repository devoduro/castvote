<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $admin = Auth::guard('admin')->user();

            if ($admin->isPending()) {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'email' => 'Your account is pending admin approval. You will be notified once approved.',
                ])->onlyInput('email');
            }

            if ($admin->account_status === 'rejected') {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                return back()->withErrors([
                    'email' => 'Your account application was not approved. Please contact support.',
                ])->onlyInput('email');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
