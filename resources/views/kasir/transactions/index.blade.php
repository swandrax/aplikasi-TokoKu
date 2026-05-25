@extends('layouts.kasir')

@section('header_title', 'Riwayat Transaksi Saya')

@section('content')
<div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6 font-sans">
    <div class="border-b border-slate-50 pb-4">
        <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Histori Penjualan POS</h3>
        <p class="text-[10px] text-slate-400 mt-1">Daftar semua struk pesanan yang berhasil Anda proses hari ini dan sebelumnya.</p>
    </div>

    <!-- History Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead>
                <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Order Number</th>
                    <th class="p-3">Pelanggan</th>
                    <th class="p-3 text-right">Subtotal</th>
                    <th class="p-3 text-right">Potongan</th>
                    <th class="p-3 text-right">Total Akhir</th>
                    <th class="p-3 text-center">Aksi Struk</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3 text-slate-500 font-medium">
                            {{ $order->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }}
                        </td>
                        <td class="p-3 font-bold text-indigo-600">{{ $order->order_number }}</td>
                        <td class="p-3 font-bold text-slate-800">{{ $order->user->name }}</td>
                        <td class="p-3 text-right text-slate-500">Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</td>
                        <td class="p-3 text-right text-rose-600 font-medium">-Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</td>
                        <td class="p-3 text-right font-extrabold text-indigo-650">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                        <td class="p-3 text-center flex justify-center gap-2">
                            <!-- Reprint direct trigger -->
                            <a href="{{ route('struk.print', $order->id) }}" target="_blank" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-650 rounded-lg font-bold text-[10px] transition-colors">
                                Cetak Struk
                            </a>
                            <!-- Download PDF -->
                            <a href="{{ route('struk.pdf', $order->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg font-bold text-[10px] transition-colors">
                                Unduh PDF
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Anda belum memproses transaksi penjualan apa pun.</td>
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
@endsection
