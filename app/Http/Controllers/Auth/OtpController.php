<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OtpController extends Controller
{
    public function showVerifyForm()
    {
        if (!Session::has('otp_user_id')) {
            return redirect()->route('register')->with('error', 'Silakan daftar terlebih dahulu.');
        }

        $user = User::query()->find(Session::get('otp_user_id'));
        if (!$user) {
            return redirect()->route('register')->with('error', 'User tidak ditemukan.');
        }

        return view('auth.otp-verify', compact('user'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ], [
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.size' => 'Kode OTP harus 6 digit.',
        ]);

        if (!Session::has('otp_user_id')) {
            return redirect()->route('register')->with('error', 'Sesi verifikasi telah berakhir. Silakan daftar kembali.');
        }

        $user = User::query()->find(Session::get('otp_user_id'));
        if (!$user) {
            return redirect()->route('register')->with('error', 'User tidak ditemukan.');
        }

        if ($user->otp_code !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Kode OTP yang dimasukkan salah.'])->withInput();
        }

        if (now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Kode OTP telah kedaluwarsa. Silakan kirim ulang OTP.'])->withInput();
        }

        // OTP verified successfully
        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        // Clear verification session
        Session::forget('otp_user_id');

        // Log in the user
        Auth::login($user);

        return redirect()->route('pembeli.shop.index')->with('success', 'Akun Anda berhasil diverifikasi! Selamat berbelanja.');
    }

    public function resend()
    {
        if (!Session::has('otp_user_id')) {
            return redirect()->route('register')->with('error', 'Sesi verifikasi telah berakhir. Silakan daftar kembali.');
        }

        $user = User::query()->find(Session::get('otp_user_id'));
        if (!$user) {
            return redirect()->route('register')->with('error', 'User tidak ditemukan.');
        }

        $otpCode = (string) rand(100000, 999999);
        $otpExpiresAt = now()->addMinutes(10);

        $user->otp_code = $otpCode;
        $user->otp_expires_at = $otpExpiresAt;
        $user->save();

        try {
            Mail::to($user->email)->send(new OtpVerificationMail(
                $user,
                $otpCode,
                $otpExpiresAt->timezone('Asia/Jakarta')->format('H:i')
            ));
        } catch (\Exception $e) {
            Log::error("Failed to resend OTP verification email to {$user->email}: " . $e->getMessage());
            Session::flash('otp_fallback', "Dev Fallback: Kode OTP baru Anda adalah {$otpCode}");
        }

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
