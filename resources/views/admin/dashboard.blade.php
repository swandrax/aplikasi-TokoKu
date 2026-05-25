@extends('layouts.admin')

@section('header_title', 'Dashboard Ringkasan')

@section('content')
<div class="space-y-8">
    <!-- Stat Widgets Row -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white/80 backdrop-blur-md border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-650 text-2xl font-bold">
                💰
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Total Penjualan</span>
                <h3 id="total-penjualan" class="text-xl font-extrabold text-slate-800 mt-1">Rp {{ number_format((float) $totalSales, 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Low Stock Alert Card -->
        <div class="bg-white/80 backdrop-blur-md border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 text-2xl font-bold">
                ⚠️
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Stok Kritis (< 5)</span>
                <h3 id="stok-kritis" class="text-xl font-extrabold text-rose-600 mt-1">{{ $lowStockProducts->count() }} Produk</h3>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="bg-white/80 backdrop-blur-md border border-slate-100 rounded-3xl p-6 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-2xl font-bold">
                👥
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold tracking-widest text-slate-400">Akun Pengguna Aktif</span>
                <h3 id="user-aktif" class="text-xl font-extrabold text-slate-800 mt-1">{{ $activeUsers }} User</h3>
            </div>
        </div>
    </div>

    <!-- Details Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders (2/3 columns) -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4 border-b border-slate-50 pb-4">
                <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Transaksi Terbaru</h3>
                <a href="{{ route('admin.reports.index') }}" class="text-[10px] font-bold text-indigo-650 hover:underline">Semua Transaksi &rarr;</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                            <th class="p-3">Order Number</th>
                            <th class="p-3">Pelanggan</th>
                            <th class="p-3">Tanggal</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $order)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-3 font-bold text-indigo-600">{{ $order->order_number }}</td>
                                <td class="p-3">{{ $order->user->name }}</td>
                                <td class="p-3 text-slate-500">{{ $order->created_at->timezone('Asia/Jakarta')->format('d M, H:i') }}</td>
                                <td class="p-3 text-right font-semibold">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                                <td class="p-3 text-center">
                                    <span class="px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider rounded-lg {{ $order->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-400">Belum ada transaksi di sistem.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Critical Stock Items (1/3 column) -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4 border-b border-slate-50 pb-4">
                <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider text-rose-600">Peringatan Stok Kritis</h3>
                <a href="{{ route('admin.stock.index') }}" class="text-[10px] font-bold text-indigo-650 hover:underline">Kelola &rarr;</a>
            </div>

            <div class="space-y-4">
                @forelse($criticalStock as $prod)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-rose-50/30 border border-rose-100/50">
                        <div>
                            <h4 class="font-bold text-xs text-slate-800 leading-tight">{{ $prod->name }}</h4>
                            <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-wider">{{ $prod->category->name }}</span>
                        </div>
                        <span class="px-2 py-0.5 bg-rose-500 text-white font-bold text-[10px] rounded-lg">
                            {{ $prod->available_stock }} Unit
                        </span>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400">
                        <span class="text-3xl block mb-2">🎉</span>
                        <p class="text-xs">Semua produk memiliki stok yang aman!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
