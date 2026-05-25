@extends('layouts.admin')

@section('header_title', 'Edit Kategori Produk')

@section('content')
<div class="max-w-2xl bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6">
    <div class="border-b border-slate-50 pb-4">
        <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Form Edit Kategori</h3>
        <p class="text-[10px] text-slate-400 mt-1">Ubah detail kategori produk Anda dengan mengisi formulir di bawah ini.</p>
    </div>

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Kategori</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" placeholder="Contoh: Elektronik Premium" required 
                   class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
            @error('name')
                <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Description Field -->
        <div>
            <label for="description" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Deskripsi (Opsional)</label>
            <textarea name="description" id="description" rows="4" placeholder="Tuliskan deskripsi singkat mengenai kategori produk ini..." 
                      class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">{{ old('description', $category->description) }}</textarea>
            @error('description')
                <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Status Toggle -->
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" {{ $category->is_active ? 'checked' : '' }} class="w-4 h-4 text-indigo-650 bg-slate-50 border border-slate-200 rounded focus:ring-indigo-500 focus:outline-none">
            <label for="is_active" class="text-xs font-bold text-slate-600 cursor-pointer">Aktifkan Kategori</label>
        </div>

        <!-- Submit & Cancel Actions -->
        <div class="flex items-center gap-3 pt-4 border-t border-slate-50">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition-all">
                Perbarui Kategori
            </button>
            <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-250 text-slate-500 rounded-xl font-bold text-xs transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
