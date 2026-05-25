<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if active
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.'])->withInput();
            }

            // Check if email verified (for pembeli/customer only, admin & kasir can bypass or have verified seed accounts)
            if ($user->isPembeli() && is_null($user->email_verified_at)) {
                $userId = $user->id;
                Auth::logout();
                session(['otp_user_id' => $userId]);
                return redirect()->route('otp.verify')->with('error', 'Silakan verifikasi email Anda terlebih dahulu.');
            }

            $request->session()->regenerate();

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isKasir()) {
                return redirect()->intended(route('kasir.dashboard'));
            } else {
                return redirect()->intended(route('pembeli.shop.index'));
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    public function landingPage()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isKasir()) {
                return redirect()->route('kasir.dashboard');
            } else {
                return redirect()->route('pembeli.shop.index');
            }
        }

        return redirect()->route('login');
    }
}
