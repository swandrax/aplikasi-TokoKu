@extends('layouts.kasir')

@section('header_title', 'Dashboard Kasir')

@section('content')
<div class="space-y-8 font-sans">
    <!-- Quick Statistics Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Sales Count Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl font-bold">
                🛒
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Transaksi Hari Ini</span>
                <h3 class="text-xl font-extrabold text-slate-800 mt-1">{{ $todayOrdersCount }} Penjualan</h3>
            </div>
        </div>

        <!-- Revenue Amount Card -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl font-bold">
                💰
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Pendapatan Hari Ini</span>
                <h3 class="text-xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format((float) $todaySalesAmount, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Best Selling Items Table -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 max-w-2xl">
        <div class="border-b border-slate-50 pb-4 mb-4">
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Produk Terlaris Hari Ini</h3>
            <p class="text-[10px] text-slate-400 mt-1">Daftar produk dengan jumlah unit barang keluar terbanyak yang berhasil diproses hari ini.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                        <th class="p-3">Nama Produk</th>
                        <th class="p-3 text-center">Unit Terjual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topProducts as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 font-bold text-slate-800">{{ $item->product_name }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 font-extrabold text-[11px] rounded-lg">
                                    {{ $item->total_qty }} Unit
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="p-8 text-center text-slate-400">Belum ada transaksi sukses diproses hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
