@extends('layouts.admin')

@section('header_title', 'Tambah Produk Baru')

@section('content')
<div class="max-w-3xl bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6">
    <div class="border-b border-slate-50 pb-4">
        <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Form Tambah Produk</h3>
        <p class="text-[10px] text-slate-400 mt-1">Daftarkan produk baru Anda dengan melengkapi rincian formulir di bawah ini.</p>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Produk</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Asus ROG Strix Gaming" required 
                       class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
                @error('name')
                    <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <!-- Category Selector -->
            <div>
                <label for="category_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kategori Produk</label>
                <select name="category_id" id="category_id" required 
                        class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
                    <option value="" disabled selected>Pilih Kategori...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <!-- Price Field -->
            <div>
                <label for="price_sell" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Harga Jual (Rp)</label>
                <input type="number" name="price_sell" id="price_sell" value="{{ old('price_sell') }}" placeholder="Contoh: 150000" min="0" required 
                       class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
                @error('price_sell')
                    <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <!-- Weight Field -->
            <div>
                <label for="weight" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Berat Produk (gram)</label>
                <input type="number" name="weight" id="weight" value="{{ old('weight') }}" placeholder="Contoh: 450" min="0" required 
                       class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
                @error('weight')
                    <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Description Field -->
        <div>
            <label for="description" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Deskripsi Produk</label>
            <textarea name="description" id="description" rows="5" placeholder="Tuliskan spesifikasi, keunggulan, dan rincian produk..." 
                      class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">{{ old('description') }}</textarea>
            @error('description')
                <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Image Upload Field -->
        <div>
            <label for="image" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Foto Produk (Maks 2MB)</label>
            <input type="file" name="image" id="image" accept="image/png, image/jpeg, image/jpg, image/webp" 
                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
            @error('image')
                <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Status Toggle -->
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" checked class="w-4 h-4 text-indigo-650 bg-slate-50 border border-slate-200 rounded focus:ring-indigo-500 focus:outline-none">
            <label for="is_active" class="text-xs font-bold text-slate-600 cursor-pointer">Aktifkan Produk Langsung</label>
        </div>

        <!-- Submit & Cancel Actions -->
        <div class="flex items-center gap-3 pt-4 border-t border-slate-50">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition-all">
                Simpan Produk
            </button>
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-250 text-slate-500 rounded-xl font-bold text-xs transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
