@extends('layouts.guest', ['title' => 'Verifikasi Email'])

@section('content')
    <div class="text-center mb-6">
        <p class="text-xs text-indigo-200">
            Kami telah mengirimkan 6-digit kode OTP ke email <strong class="text-white">{{ $user->email }}</strong>. Silakan masukkan kode tersebut di bawah ini.
        </p>
    </div>

    <!-- Dev Fallback OTP Alert -->
    @if (session('otp_fallback'))
        <div class="mb-5 p-4 text-xs text-indigo-900 bg-indigo-50 border border-indigo-200 rounded-2xl text-center font-bold shadow-inner">
            💡 {{ session('otp_fallback') }}
        </div>
    @endif

    <form action="{{ route('otp.verify') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- OTP Input -->
        <div>
            <label for="otp_code" class="block text-xs font-bold text-indigo-200 uppercase tracking-wider text-center mb-3">Kode Verifikasi (6 Digit)</label>
            <input type="text" name="otp_code" id="otp_code" maxlength="6" required autofocus placeholder="123456" 
                   class="w-full text-center py-4 text-2xl font-extrabold tracking-[0.4em] bg-slate-900/50 border border-slate-700/50 rounded-2xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-slate-700 transition-all select-all">
            @error('otp_code')
                <span class="text-[10px] text-rose-400 font-bold block mt-2 text-center">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white font-bold text-sm rounded-2xl shadow-lg hover:shadow-indigo-500/20 hover:scale-[1.01] transition-all duration-300 active:scale-95 focus:outline-none">
            Verifikasi Akun
        </button>
    </form>

    <!-- Resend Section -->
    <div class="mt-8 text-center text-xs text-indigo-300">
        Tidak menerima kode verifikasi? 
        <form action="{{ route('otp.resend') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="font-bold text-white hover:text-indigo-200 hover:underline focus:outline-none bg-transparent border-none p-0 cursor-pointer">Kirim Ulang OTP</button>
        </form>
    </div>
@endsection

