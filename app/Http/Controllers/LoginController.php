<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function loginBackend()
    {
        if (Auth::check()) {
            return Auth::user()->isCustomer()
                ? redirect()->route('frontend.catalog.index')
                : redirect()->route('backend.beranda');
        }

        return view('backend.v_login.login', [
            'judul' => 'Login',
        ]);
    }

    public function authenticateBackend(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->status == 0) {
                Auth::logout();
                return back()->withInput()->with('alert', $this->modalAlert(
                    'warning',
                    'Akun Belum Aktif',
                    'User belum aktif. Silakan hubungi Admin.'
                ));
            }
            $request->session()->regenerate();
            if (Auth::user()->role === User::ROLE_CUSTOMER) {
                return redirect()->intended(route('frontend.catalog.index'))->with('alert', $this->modalAlert(
                    'success',
                    'Login Berhasil',
                    'Selamat datang di katalog produk.'
                ));
            }

            return redirect()->intended(route('backend.beranda'))->with('alert', $this->modalAlert(
                'success',
                'Login Berhasil',
                'Selamat datang kembali di dashboard.'
            ));
        }
        return back()->withInput()->with('alert', $this->modalAlert(
            'error',
            'Login Gagal',
            'Email atau password tidak sesuai.'
        ));
    }

    public function registerBackend()
    {
        if (Auth::check()) {
            return Auth::user()->isCustomer()
                ? redirect()->route('frontend.catalog.index')
                : redirect()->route('backend.beranda');
        }

        return view('backend.v_login.register', [
            'judul' => 'Register',
        ]);
    }

    public function storeRegisterBackend(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|max:255|email|unique:user,email',
            'role' => 'required|in:1,2',
            'hp' => 'required|min:10|max:13',
            'password' => 'required|min:4|confirmed',
        ]);

        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/';

        if (!preg_match($pattern, (string) $request->input('password'))) {
            return redirect()->back()
                ->withErrors([
                    'password' => 'Password harus terdiri dari kombinasi huruf besar, huruf kecil, angka, dan simbol karakter.',
                ])
                ->withInput();
        }

        $validatedData['status'] = 1;
        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('backend.login')->with('alert', $this->modalAlert(
            'success',
            'Registrasi Berhasil',
            'Akun berhasil dibuat. Silakan login dengan email dan password yang didaftarkan.'
        ));
    }

    public function logoutBackend()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect(route('backend.login'))->with('alert', $this->modalAlert(
            'success',
            'Logout Berhasil',
            'Anda berhasil keluar dari sistem.'
        ));
    }
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
            return back()->with('alert', $this->modalAlert(
                'success',
                'Email Terkirim',
                'Tautan pemulihan kata sandi telah dikirim ke email Anda.'
            ));
        }

        return back()->with('alert', $this->modalAlert(
            'error',
            'Gagal',
            'Tidak dapat menemukan pengguna dengan email tersebut.'
        ));
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('backend.v_login.reset', [
            'judul' => 'Reset Password',
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:4|confirmed',
        ]);

        $status = \Illuminate\Support\Facades\Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Illuminate\Support\Str::random(60));
                $user->save();
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return redirect()->route('backend.login')->with('alert', $this->modalAlert(
                'success',
                'Berhasil',
                'Kata sandi Anda telah berhasil direset.'
            ));
        }

        return back()->withErrors(['email' => __($status)])->with('alert', $this->modalAlert(
            'error',
            'Gagal',
            'Gagal mereset kata sandi Anda.'
        ));
    }
}
