<x-pembeli-layout title="Selesaikan Pesanan - TokoKu">
    <div class="max-w-4xl mx-auto space-y-8 font-sans">
        <a href="{{ route('pembeli.cart.index') }}" class="text-xs font-bold text-slate-500 hover:text-indigo-650 flex items-center gap-1.5 transition-colors">
            &larr; Kembali ke Keranjang
        </a>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            <!-- Checkout Form Pane (2/3 columns) -->
            <div class="md:col-span-2 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="border-b border-slate-50 pb-4">
                    <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider">Form Alamat & Pembayaran</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Masukkan alamat pengiriman dan unggah bukti transfer/pembayaran Anda.</p>
                </div>

                <form action="{{ route('pembeli.order.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Shipping Address Field -->
                    <div>
                        <label for="address" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Alamat Pengiriman Lengkap</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="Contoh: Jl. Diponegoro No. 12, Bandung, Jawa Barat" required 
                               class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 focus:bg-white text-slate-700 transition-all">
                        @error('address')
                            <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Payment Method Radio Buttons -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 font-bold text-[10px] text-slate-700 select-none text-center" id="label-tunai">
                                <input type="radio" name="payment_method" value="Tunai" checked class="hidden" onclick="selectCheckoutPayment('Tunai')">
                                💵 Tunai (COD)
                            </label>
                            <label class="flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 font-bold text-[10px] text-slate-700 select-none text-center" id="label-transfer">
                                <input type="radio" name="payment_method" value="Transfer Bank" class="hidden" onclick="selectCheckoutPayment('Transfer Bank')">
                                🏦 Bank Transfer
                            </label>
                            <label class="flex flex-col items-center justify-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 font-bold text-[10px] text-slate-700 select-none text-center" id="label-qris">
                                <input type="radio" name="payment_method" value="Qris" class="hidden" onclick="selectCheckoutPayment('Qris')">
                                📱 QRIS E-Wallet
                            </label>
                        </div>
                    </div>

                    <!-- Payment Proof Upload (Shown when Transfer or Qris chosen) -->
                    <div id="payment-proof-block" class="hidden bg-slate-50 border border-slate-100 p-4 rounded-2xl space-y-3">
                        <label for="payment_proof" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Unggah Bukti Transfer / Pembayaran</label>
                        <input type="file" name="payment_proof" id="payment_proof" accept="image/png, image/jpeg, image/jpg, image/webp" 
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        <span class="text-[9px] text-slate-400 block mt-1">Gunakan bukti transfer BCA/Mandiri ke Rekening TokoKu: <strong>123-4567-890</strong> a.n TokoKu Store.</span>
                        @error('payment_proof')
                            <span class="text-[10px] text-rose-500 font-bold block mt-1.5">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="note" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan Pesanan (Opsional)</label>
                        <input type="text" name="note" id="note" value="{{ old('note') }}" placeholder="Contoh: Titip di satpam jika tidak ada orang" 
                               class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-650 focus:bg-white text-slate-700 transition-all">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all active:scale-[0.98]">
                        Selesaikan Transaksi & Bayar
                    </button>
                </form>
            </div>

            <!-- Receipt Breakdown Sidebar (1/3 column) -->
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider mb-4 pb-2 border-b border-slate-50">Daftar Belanja</h3>

                <!-- Cart Items Checklist list -->
                <div class="space-y-3 max-h-48 overflow-y-auto divide-y divide-slate-50 pr-2 scrollbar-thin">
                    @foreach($cartItems as $item)
                        <div class="flex items-center justify-between text-xs py-2 first:pt-0">
                            <div class="min-w-0 flex-1 pr-2">
                                <h4 class="font-bold text-slate-800 truncate leading-snug">{{ $item->product->name }}</h4>
                                <span class="text-[10px] text-slate-400 font-semibold">{{ $item->quantity }} unit x Rp {{ number_format($item->product->price_sell, 0, ',', '.') }}</span>
                            </div>
                            <span class="font-extrabold text-slate-800">Rp {{ number_format($item->product->price_sell * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 pt-4 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-500 font-semibold">
                        <span>Subtotal:</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500 font-semibold">
                        <span>Pajak (11%):</span>
                        <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-black text-sm text-slate-850 pt-2 border-t border-slate-50">
                        <span>TOTAL AKHIR:</span>
                        <span class="text-indigo-650">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout interaction javascript helper -->
    <script>
        function selectCheckoutPayment(method) {
            document.getElementById('label-tunai').className = "flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer text-slate-700 hover:bg-slate-50 font-bold text-[10px] text-center select-none " + (method === 'Tunai' ? 'border-indigo-650 bg-indigo-50/50 text-indigo-700' : 'border-slate-200');
            document.getElementById('label-transfer').className = "flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer text-slate-700 hover:bg-slate-50 font-bold text-[10px] text-center select-none " + (method === 'Transfer Bank' ? 'border-indigo-650 bg-indigo-50/50 text-indigo-700' : 'border-slate-200');
            document.getElementById('label-qris').className = "flex flex-col items-center justify-center p-3 border rounded-xl cursor-pointer text-slate-700 hover:bg-slate-50 font-bold text-[10px] text-center select-none " + (method === 'Qris' ? 'border-indigo-650 bg-indigo-50/50 text-indigo-700' : 'border-slate-200');

            const proofBlock = document.getElementById('payment-proof-block');
            if (method === 'Transfer Bank' || method === 'Qris') {
                proofBlock.classList.remove('hidden');
            } else {
                proofBlock.classList.add('hidden');
            }
        }

        // Set default payment highlight on startup
        selectCheckoutPayment('Tunai');
    </script>
</x-pembeli-layout>
