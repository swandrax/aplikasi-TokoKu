<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'role' => 'required|in:pembeli,kasir,admin',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        $otpCode = (string) rand(100000, 999999);
        $otpExpiresAt = now()->addMinutes(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'otp_code' => $otpCode,
            'otp_expires_at' => $otpExpiresAt,
            'email_verified_at' => null,
            'is_active' => true,
        ]);

        // Send OTP Verification Email
        try {
            Mail::to($user->email)->send(new OtpVerificationMail(
                $user, 
                $otpCode, 
                $otpExpiresAt->timezone('Asia/Jakarta')->format('H:i')
            ));
        } catch (\Exception $e) {
            Log::error("Failed to send OTP verification email to {$user->email}: " . $e->getMessage());
            // Fallback for easy local testing: store otp in session flash to let the user see it
            Session::flash('otp_fallback', "Dev Fallback: Kode OTP Anda adalah {$otpCode}");
        }

        // Store the user ID in session to know who is being verified
        Session::put('otp_user_id', $user->id);

        return redirect()->route('otp.verify')->with('success', 'Registrasi berhasil! Silakan masukkan kode OTP yang dikirim ke email Anda.');
    }
}
