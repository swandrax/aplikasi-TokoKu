@extends('layouts.admin')

@section('header_title', 'Kelola Produk Barang')

@section('content')
<div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6">
    <div class="flex items-center justify-between border-b border-slate-50 pb-4">
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Katalog Produk Barang</h3>
            <p class="text-[10px] text-slate-400 mt-1">Daftar produk dagangan yang terdaftar beserta harga jual, berat, dan jumlah sisa stok FIFO.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition-all duration-300">
            + Tambah Produk baru
        </a>
    </div>

    <!-- Product List Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead>
                <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                    <th class="p-3">Produk</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3 text-right">Harga Jual</th>
                    <th class="p-3 text-center">Berat</th>
                    <th class="p-3 text-center">Sisa Stok (FIFO)</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products as $product)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded-xl border border-slate-100">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-lg">📦</div>
                                @endif
                                <div>
                                    <span class="font-bold text-slate-800 block leading-tight">{{ $product->name }}</span>
                                    <span class="text-[9px] text-slate-400 font-mono tracking-wide mt-0.5 block">{{ $product->slug }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-lg border border-indigo-100/50">
                                {{ $product->category->name }}
                            </span>
                        </td>
                        <td class="p-3 text-right font-extrabold text-indigo-600">
                            Rp {{ number_format((float) $product->price_sell, 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-center text-slate-500 font-medium">
                            {{ number_format((float) $product->weight, 0, ',', '.') }}g
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-lg {{ $product->available_stock > 5 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700 font-bold' }}">
                                {{ $product->available_stock }} unit
                            </span>
                        </td>
                        <td class="p-3 text-center">
                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-lg {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $product->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </td>
                        <td class="p-3 text-center flex items-center justify-center gap-2">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg font-bold text-[10px] transition-colors">
                                Edit
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg font-bold text-[10px] transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada produk terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="pt-4 border-t border-slate-50">
        {{ $products->links() }}
    </div>
</div>
@endsection
