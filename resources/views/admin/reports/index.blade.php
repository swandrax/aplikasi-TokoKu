@extends('layouts.admin')

@section('header_title', 'Laporan & Ekspor Transaksi')

@section('content')
<div class="space-y-6">
    <!-- Filter Block Card -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider mb-4 pb-2 border-b border-slate-50">Filter & Ekspor Dokumen</h3>

        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <!-- Start Date -->
            <div>
                <label for="start_date" class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" 
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700">
            </div>

            <!-- End Date -->
            <div>
                <label for="end_date" class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" 
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Status Pembayaran</label>
                <select name="status" id="status" 
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700">
                    <option value="">Semua Status...</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas / Paid</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Cashier Filter -->
            <div>
                <label for="kasir_id" class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Petugas Kasir</label>
                <select name="kasir_id" id="kasir_id" 
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700">
                    <option value="">Semua Kasir / Online...</option>
                    @foreach($cashiers as $kasir)
                        <option value="{{ $kasir->id }}" {{ request('kasir_id') == $kasir->id ? 'selected' : '' }}>{{ $kasir->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Form Buttons -->
            <div class="sm:col-span-4 flex items-center justify-between border-t border-slate-50 pt-4 mt-2">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs shadow-md transition-all">
                    Filter Laporan
                </button>

                <div class="flex items-center gap-3">
                    <!-- Export Excel -->
                    <a href="{{ route('admin.reports.excel', request()->all()) }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md flex items-center gap-1.5 transition-colors">
                        📊 Ekspor Excel
                    </a>

                    <!-- Export PDF -->
                    <a href="{{ route('admin.reports.pdf', request()->all()) }}" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md flex items-center gap-1.5 transition-colors">
                        📄 Ekspor PDF Ringkasan
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table Block Card -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6">
        <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider mb-4 pb-2 border-b border-slate-50">Daftar Hasil Pencarian</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                        <th class="p-3">Tanggal</th>
                        <th class="p-3">Order Number</th>
                        <th class="p-3">Pelanggan</th>
                        <th class="p-3">Kasir</th>
                        <th class="p-3 text-right">Total Transaksi</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 text-slate-500 font-medium">
                                {{ $order->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i') }}
                            </td>
                            <td class="p-3 font-bold text-indigo-600">{{ $order->order_number }}</td>
                            <td class="p-3 font-bold text-slate-800">{{ $order->user->name }}</td>
                            <td class="p-3 text-slate-600">{{ $order->kasir ? $order->kasir->name : 'Self Checkout' }}</td>
                            <td class="p-3 text-right font-extrabold text-slate-850">
                                Rp {{ number_format((float) $order->total, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-lg {{ $order->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($order->status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('struk.pdf', $order->id) }}" class="px-2 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold text-[9px] rounded-lg transition-colors">
                                    Unduh PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">Tidak ada transaksi ditemukan yang cocok dengan kriteria filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="pt-4 border-t border-slate-50">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
