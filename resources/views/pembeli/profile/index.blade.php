<x-pembeli-layout title="Profil Saya - TokoKu">
    <div class="max-w-2xl bg-white border border-slate-100 rounded-3xl shadow-sm p-6 sm:p-8 space-y-6 font-sans">
        <div class="border-b border-slate-50 pb-4">
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Pengaturan Profil & Keamanan</h3>
            <p class="text-[10px] text-slate-400 mt-1">Ubah nama lengkap, alamat email, atau perbarui kata sandi keamanan Anda.</p>
        </div>

        <form action="{{ route('pembeli.profile.update') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Name Field -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                       class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-650 focus:bg-white text-slate-700 transition-all">
                @error('name')
                    <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required 
                       class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-650 focus:bg-white text-slate-700 transition-all">
                @error('email')
                    <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-slate-50 pt-5">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin diubah" 
                           class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-650 focus:bg-white text-slate-700 transition-all">
                    @error('password')
                        <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ulangi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Kosongkan jika tidak ingin diubah" 
                           class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-650 focus:bg-white text-slate-700 transition-all">
                    @error('password_confirmation')
                        <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all active:scale-[0.98] mt-4">
                Perbarui Informasi Profil
            </button>
        </form>
    </div>
</x-pembeli-layout>
