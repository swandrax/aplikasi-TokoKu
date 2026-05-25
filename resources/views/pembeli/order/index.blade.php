<x-pembeli-layout title="Riwayat Belanja Saya - TokoKu">
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 space-y-6 font-sans">
        <div class="border-b border-slate-50 pb-4">
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Histori Belanja Saya</h3>
            <p class="text-[10px] text-slate-400 mt-1">Daftar transaksi, rincian biaya, dan status pelacakan pesanan Anda di TokoKu Store.</p>
        </div>

        <!-- History Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="text-[10px] text-slate-400 font-bold uppercase tracking-wider bg-slate-50">
                        <th class="p-3">Tanggal Pesan</th>
                        <th class="p-3">No. Transaksi</th>
                        <th class="p-3 text-right">Total Belanja</th>
                        <th class="p-3 text-center">Metode Bayar</th>
                        <th class="p-3 text-center">Status</th>
                        <th class="p-3 text-center">Aksi Pelacakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 text-slate-500 font-medium">
                                {{ $order->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }}
                            </td>
                            <td class="p-3 font-bold text-indigo-600">{{ $order->order_number }}</td>
                            <td class="p-3 text-right font-extrabold text-slate-800">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                            <td class="p-3 text-center text-slate-600 font-semibold">{{ $order->payment_method }}</td>
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-lg {{ $order->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : ($order->status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="p-3 text-center">
                                <a href="{{ route('pembeli.order.show', $order->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-650 rounded-xl font-bold text-[10px] transition-colors">
                                    Lacak Detail &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Anda belum pernah melakukan transaksi pembelian apa pun.</td>
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
</x-pembeli-layout>
