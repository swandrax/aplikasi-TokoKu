@extends('layouts.admin')

@section('header_title', 'Tambah Chatbot Prompt')

@section('content')
<div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h3 class="font-extrabold text-slate-800 text-lg">Form Tambah Prompt</h3>
        <a href="{{ route('admin.chatbot-prompts.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">← Kembali</a>
    </div>

    <form action="{{ route('admin.chatbot-prompts.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Judul Tombol <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: 👕 Rekomendasi Baju"
                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white text-sm transition-colors">
            @error('title') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-[10px] text-slate-400 mt-1">Teks pendek yang akan muncul di tombol (Bisa menggunakan Emoji).</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Teks Prompt <span class="text-rose-500">*</span></label>
            <textarea name="prompt_text" required rows="3" placeholder="Tuliskan pertanyaan lengkap yang akan dikirim ke AI..."
                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white text-sm transition-colors">{{ old('prompt_text') }}</textarea>
            @error('prompt_text') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-[10px] text-slate-400 mt-1">Pesan aktual yang dikirim pengguna ke KikiBot jika menekan tombol di atas.</p>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" {{ old('is_active', true) ? 'checked' : '' }}>
            <label for="is_active" class="text-sm font-semibold text-slate-700 cursor-pointer">Aktifkan Prompt Ini</label>
        </div>

        <div class="pt-4 border-t border-slate-100">
            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Simpan Prompt Baru
            </button>
        </div>
    </form>
</div>
@endsection
