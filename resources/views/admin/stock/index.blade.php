@extends('layouts.admin')

@section('header_title', 'Manajemen Stok (FIFO Inventory)')

@section('content')
<div class="space-y-8">
    <!-- Products Stock Summary and Restock button -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-50 pb-4">
            <div>
                <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Status Persediaan Produk</h3>
                <p class="text-[10px] text-slate-400 mt-1">Daftar sisa stok riil yang tersedia di sistem yang dialokasikan berdasarkan First In First Out (FIFO).</p>
            </div>
            <a href="{{ route('admin.stock.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition-all duration-300">
                + Input Batch Stok Masuk (FIFO)
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($products as $prod)
                <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                    <div>
                        <h4 class="font-bold text-xs text-slate-800 leading-tight">{{ $prod->name }}</h4>
                        <span class="text-[9px] text-slate-400 uppercase font-bold mt-1 block">{{ $prod->category->name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[8px] uppercase tracking-wider font-bold block text-slate-400">Tersedia</span>
                        <span class="px-2.5 py-0.5 font-extrabold text-xs rounded-lg {{ $prod->available_stock > 5 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ $prod->available_stock }} unit
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Stock Log Timeline -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-4">
        <div class="border-b border-slate-50 pb-4">
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Histori Aliran Stok Barang</h3>
            <p class="text-[10px] text-slate-400 mt-1">Timeline mutasi persediaan barang masuk (in), keluar (out), dan penyesuaian (adjustment).</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Produk</th>
                        <th class="p-3 text-center">Tipe</th>
                        <th class="p-3 text-center">Jumlah</th>
                        <th class="p-3">Keterangan</th>
                        <th class="p-3">Operator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 text-slate-500 font-medium">
                                {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                            </td>
                            <td class="p-3 font-bold text-slate-800">{{ $log->product->name }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-lg {{ $log->type === 'in' ? 'bg-emerald-50 text-emerald-700' : ($log->type === 'out' ? 'bg-rose-50 text-rose-750' : 'bg-slate-100 text-slate-650') }}">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="p-3 text-center font-extrabold {{ $log->type === 'in' ? 'text-emerald-600' : ($log->type === 'out' ? 'text-rose-600' : 'text-slate-600') }}">
                                {{ $log->type === 'in' ? '+' : '-' }}{{ $log->quantity }}
                            </td>
                            <td class="p-3 text-slate-500 max-w-xs truncate" title="{{ $log->description }}">
                                {{ $log->description }}
                            </td>
                            <td class="p-3 text-slate-600 font-semibold">{{ $log->creator ? $log->creator->name : 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Belum ada histori aliran stok barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="pt-4 border-t border-slate-50">
            {{ $recentLogs->links() }}
        </div>
    </div>
</div>
@endsection
