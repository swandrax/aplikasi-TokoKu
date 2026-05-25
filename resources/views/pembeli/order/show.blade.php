<x-pembeli-layout title="Detail Pesanan - TokoKu">
    <div class="max-w-4xl mx-auto space-y-8 font-sans">
        <!-- Back Button -->
        <a href="{{ route('pembeli.order.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-650 flex items-center gap-1.5 transition-colors">
            &larr; Kembali ke Histori Belanja
        </a>

        <!-- Order Header Panel -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-[9px] uppercase tracking-wider font-bold text-slate-400">Nomor Transaksi</span>
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">{{ $order->order_number }}</h2>
                <span class="text-xs text-slate-400 block mt-0.5">Dipesan pada: {{ $order->created_at->timezone('Asia/Jakarta')->format('d F Y, H:i') }} WIB</span>
            </div>

            <!-- Real-time status container -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[9px] uppercase tracking-wider font-bold text-slate-400 block mb-0.5">Status Pembayaran</span>
                    <span id="order-status-badge" class="px-3 py-1 font-bold text-xs uppercase tracking-wider rounded-xl {{ $order->status === 'paid' ? 'bg-emerald-500 text-white' : ($order->status === 'cancelled' ? 'bg-rose-500 text-white' : 'bg-amber-500 text-white') }}">
                        {{ $order->status }}
                    </span>
                </div>

                <!-- Reprint PDF & Print direct buttons -->
                @if($order->status === 'paid')
                    <div class="flex gap-2">
                        <a href="{{ route('struk.pdf', $order->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-colors">
                            Unduh PDF Struk
                        </a>
                        <a href="{{ route('struk.print', $order->id) }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition-colors">
                            🖨️ Cetak
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            <!-- Order items list (2/3 columns) -->
            <div class="md:col-span-2 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider pb-2 border-b border-slate-50">Rincian Barang Belanja</h3>

                <div class="divide-y divide-slate-100">
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0 flex-1 pr-3">
                                <h4 class="font-bold text-xs text-slate-850 truncate leading-snug">{{ $item->product_name }}</h4>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ $item->quantity }} unit x Rp {{ number_format((float) $item->price, 0, ',', '.') }}</span>
                            </div>
                            <span class="font-extrabold text-xs text-slate-850">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Billing summaries & tracking (1/3 column) -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider pb-2 border-b border-slate-50">Ringkasan Biaya</h3>

                <div class="space-y-2 text-xs border-b border-slate-50 pb-3">
                    <div class="flex justify-between text-slate-500 font-semibold">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format((float) $order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 font-semibold">
                        <span>Pajak PPN (11%):</span>
                        <span>Rp {{ number_format((float) $order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 font-semibold">
                        <span>Diskon Belanja:</span>
                        <span class="text-rose-600">-Rp {{ number_format((float) $order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between font-black text-sm text-slate-800">
                    <span>TOTAL BAYAR:</span>
                    <span class="text-indigo-650">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</span>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-[10px] space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-bold uppercase tracking-wide">Metode Bayar:</span>
                        <strong class="text-slate-700">{{ $order->payment_method }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-bold uppercase tracking-wide">Dibayar Pada:</span>
                        <strong id="order-paid-time" class="text-slate-700">{{ $order->paid_at ? $order->paid_at->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : 'Menunggu Verifikasi' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AJAX Polling Script to check status every 10 seconds -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const badge = document.getElementById('order-status-badge');
            const paidTime = document.getElementById('order-paid-time');

            async function pollOrderStatus() {
                try {
                    const response = await fetch('/api/internal/order-status/{{ $order->id }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (response.ok) {
                        const data = await response.json();

                        // 1. Update Badge text and color classes
                        badge.textContent = data.status;
                        badge.className = "px-3 py-1 font-bold text-xs uppercase tracking-wider rounded-xl " + data.status_badge_color;

                        // 2. Update Paid timestamp block
                        if (data.status === 'paid' && data.paid_at) {
                            paidTime.textContent = data.paid_at + ' WIB';
                        } else if (data.status === 'cancelled' && data.cancelled_at) {
                            paidTime.textContent = 'Dibatalkan (' + data.cancelled_at + ' WIB)';
                        }
                    }
                } catch (err) {
                    console.error('Polling order status error:', err);
                }
            }

            // Poll every 10 seconds (10000ms)
            setInterval(pollOrderStatus, 10000);
        });
    </script>
</x-pembeli-layout>
