@extends('layouts.guest')

@section('content')
    <form action="{{ route('login') }}" method="POST" class="space-y-5">
        @csrf
        
        <!-- Email Field -->
        <div>
            <label for="email" class="block text-xs font-bold text-indigo-200 uppercase tracking-wider mb-2">Alamat Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-400">📧</span>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="contoh@tokoku.com" required 
                       class="w-full pl-10 pr-4 py-3 text-xs bg-slate-900/50 border border-slate-700/50 rounded-2xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-slate-500 transition-all">
            </div>
            @error('email')
                <span class="text-[10px] text-rose-400 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label for="password" class="block text-xs font-bold text-indigo-200 uppercase tracking-wider">Kata Sandi</label>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-400">🔑</span>
                <input type="password" name="password" id="password" placeholder="••••••••" required 
                       class="w-full pl-10 pr-4 py-3 text-xs bg-slate-900/50 border border-slate-700/50 rounded-2xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-slate-500 transition-all">
            </div>
            @error('password')
                <span class="text-[10px] text-rose-400 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between text-xs text-indigo-300">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 bg-slate-950 border border-slate-800 rounded focus:ring-indigo-600 text-indigo-600 accent-indigo-600">
                <span>Ingat saya</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white font-bold text-sm rounded-2xl shadow-lg hover:shadow-indigo-500/20 hover:scale-[1.01] transition-all duration-300 active:scale-95 focus:outline-none">
            Masuk Sekarang
        </button>
    </form>

    <!-- Sign Up Link -->
    <div class="mt-8 text-center text-xs text-indigo-300">
        Belum memiliki akun? 
        <a href="{{ route('register') }}" class="font-bold text-white hover:text-indigo-200 hover:underline transition-all">Daftar Akun Baru</a>
    </div>
@endsection
