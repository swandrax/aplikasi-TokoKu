@extends('layouts.admin')

@section('header_title', 'Input Batch Stok Masuk (FIFO)')

@section('content')
<div class="max-w-2xl bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6">
    <div class="border-b border-slate-50 pb-4">
        <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Form Pengadaan Stok FIFO</h3>
        <p class="text-[10px] text-slate-400 mt-1">Gunakan form ini untuk merekam batch persediaan barang masuk yang baru. Sistem akan mengalokasikan stok ini sesuai urutan masuk (FIFO).</p>
    </div>

    <form action="{{ route('admin.stock.store') }}" method="POST" class="space-y-5">
        @csrf

        <!-- Product Selector -->
        <div>
            <label for="product_id" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Produk</label>
            <select name="product_id" id="product_id" required 
                    class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
                <option value="" disabled selected>Pilih Produk...</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
            @error('product_id')
                <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Quantity Field -->
            <div>
                <label for="quantity" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jumlah Stok Masuk (Unit)</label>
                <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" placeholder="Contoh: 50" min="1" required 
                       class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
                @error('quantity')
                    <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>

            <!-- Purchase Price Field -->
            <div>
                <label for="purchase_price" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Harga Beli Per Unit (Rp)</label>
                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" placeholder="Contoh: 120000" min="0" required 
                       class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
                @error('purchase_price')
                    <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Supplier Name Field -->
        <div>
            <label for="supplier_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Supplier (Opsional)</label>
            <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name') }}" placeholder="Contoh: PT. Global Indo Ritel" 
                   class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
            @error('supplier_name')
                <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Note Field -->
        <div>
            <label for="note" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan Tambahan (Opsional)</label>
            <input type="text" name="note" id="note" value="{{ old('note') }}" placeholder="Contoh: Stok awal pengadaan Q2" 
                   class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
            @error('note')
                <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit & Cancel Actions -->
        <div class="flex items-center gap-3 pt-4 border-t border-slate-50">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition-all">
                Input Batch Stok
            </button>
            <a href="{{ route('admin.stock.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-250 text-slate-500 rounded-xl font-bold text-xs transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
