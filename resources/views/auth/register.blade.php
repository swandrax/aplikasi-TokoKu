@extends('layouts.guest')

@section('content')
    <form action="{{ route('register') }}" method="POST" class="space-y-4">
        @csrf
        
        <!-- Name Field -->
        <div>
            <label for="name" class="block text-xs font-bold text-indigo-200 uppercase tracking-wider mb-2">Nama Lengkap</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-400">👤</span>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required 
                       class="w-full pl-10 pr-4 py-3 text-xs bg-slate-900/50 border border-slate-700/50 rounded-2xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-slate-500 transition-all">
            </div>
            @error('name')
                <span class="text-[10px] text-rose-400 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

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
            <label for="password" class="block text-xs font-bold text-indigo-200 uppercase tracking-wider mb-2">Kata Sandi</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-400">🔑</span>
                <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" required 
                       class="w-full pl-10 pr-4 py-3 text-xs bg-slate-900/50 border border-slate-700/50 rounded-2xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-slate-500 transition-all">
            </div>
            @error('password')
                <span class="text-[10px] text-rose-400 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password Confirmation Field -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-indigo-200 uppercase tracking-wider mb-2">Konfirmasi Kata Sandi</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-400">🔑</span>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi" required 
                       class="w-full pl-10 pr-4 py-3 text-xs bg-slate-900/50 border border-slate-700/50 rounded-2xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white placeholder-slate-500 transition-all">
            </div>
            @error('password_confirmation')
                <span class="text-[10px] text-rose-400 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Role Selection -->
        <div>
            <label for="role" class="block text-xs font-bold text-indigo-200 uppercase tracking-wider mb-2">Daftar Sebagai (Role)</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-indigo-400">🛡️</span>
                <select name="role" id="role" required class="w-full pl-10 pr-4 py-3 text-xs bg-slate-900/50 border border-slate-700/50 rounded-2xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-white transition-all appearance-none">
                    <option value="pembeli" class="text-slate-900" {{ old('role') == 'pembeli' ? 'selected' : '' }}>User / Pembeli</option>
                    <option value="kasir" class="text-slate-900" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                    <option value="admin" class="text-slate-900" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-indigo-400 pointer-events-none text-[10px]">▼</span>
            </div>
            @error('role')
                <span class="text-[10px] text-rose-400 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
            <p class="text-[10px] text-amber-200/70 mt-1.5">⚠️ Akses Admin/Kasir tersedia di publik hanya untuk pengujian.</p>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700 text-white font-bold text-sm rounded-2xl shadow-lg hover:shadow-indigo-500/20 hover:scale-[1.01] transition-all duration-300 active:scale-95 focus:outline-none mt-2">
            Daftar Akun Baru
        </button>
    </form>

    <!-- Sign In Link -->
    <div class="mt-8 text-center text-xs text-indigo-300">
        Sudah memiliki akun? 
        <a href="{{ route('login') }}" class="font-bold text-white hover:text-indigo-200 hover:underline transition-all">Masuk Di Sini</a>
    </div>
@endsection
