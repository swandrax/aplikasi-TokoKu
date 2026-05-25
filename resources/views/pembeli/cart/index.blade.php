<x-pembeli-layout title="Keranjang Belanja - TokoKu">
    <div class="space-y-8 font-sans" id="cart-workspace">
        <h2 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider border-l-3 border-indigo-600 pl-3">Keranjang Belanja Anda</h2>

        @if($cartItems->isEmpty())
            <div class="py-16 text-center text-slate-400 bg-white border border-slate-100 rounded-3xl shadow-sm">
                <span class="text-5xl block mb-4">🛒</span>
                <h4 class="font-bold text-sm text-slate-700">Keranjang belanja Anda kosong</h4>
                <p class="text-xs text-slate-400 mt-1 mb-6">Silakan kembali ke katalog toko kami untuk menambahkan produk unggulan.</p>
                <a href="{{ route('pembeli.shop.index') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-xs uppercase tracking-wider shadow-md transition-all">
                    Belanja Sekarang
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <!-- Cart Items List (2/3 columns) -->
                <div class="lg:col-span-2 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm divide-y divide-slate-100">
                    @foreach($cartItems as $item)
                        <div class="flex items-center justify-between py-4 first:pt-0 last:pb-0" id="cart-item-row-{{ $item->id }}">
                            <!-- Image and Title -->
                            <div class="flex items-center gap-3 flex-1 min-w-0 pr-4">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-2xl shadow-inner border border-slate-50">
                                    📦
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-xs text-slate-800 truncate leading-snug">{{ $item->product->name }}</h4>
                                    <span class="text-[9px] text-slate-400 uppercase font-bold tracking-wider">{{ $item->product->category->name }} | {{ number_format((float) $item->product->weight, 0, ',', '.') }}g</span>
                                </div>
                            </div>

                            <!-- Qty Adjuster & Price -->
                            <div class="flex items-center gap-6">
                                <!-- Qty -->
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="updateCartQty({{ $item->id }}, -1)" class="w-7 h-7 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-lg text-xs transition-colors">-</button>
                                    <input type="number" id="cart-qty-input-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="{{ $item->available_stock }}" readonly
                                           class="w-12 text-center text-xs font-extrabold bg-transparent text-slate-800 select-all border-none">
                                    <button type="button" onclick="updateCartQty({{ $item->id }}, 1)" class="w-7 h-7 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-lg text-xs transition-colors">+</button>
                                </div>

                                <!-- Subtotal per Item -->
                                <div class="text-right min-w-[100px]">
                                    <span id="item-subtotal-{{ $item->id }}" class="font-extrabold text-xs text-slate-800">Rp {{ number_format($item->product->price_sell * $item->quantity, 0, ',', '.') }}</span>
                                    <span class="block text-[8px] text-slate-400 font-semibold mt-0.5">Rp {{ number_format($item->product->price_sell, 0, ',', '.') }} / unit</span>
                                </div>

                                <!-- Remove Form -->
                                <form action="{{ route('pembeli.cart.remove') }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                    @csrf
                                    <input type="hidden" name="cart_id" value="{{ $item->id }}">
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-slate-150 transition-colors focus:outline-none">
                                        ❌
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Receipt Cart Summary (1/3 column) -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                    <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider mb-4 pb-2 border-b border-slate-50">Ringkasan Pembayaran</h3>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500 font-semibold">
                            <span>Subtotal Barang:</span>
                            <span id="cart-subtotal">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500 font-semibold">
                            <span>Pajak PPN (11%):</span>
                            <span id="cart-tax">Rp {{ number_format($subtotal * 0.11, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500 font-semibold border-b border-slate-50 pb-3">
                            <span>Estimasi Berat Total:</span>
                            <span>{{ number_format($totalWeight, 0, ',', '.') }} gram</span>
                        </div>
                        <div class="flex justify-between font-black text-sm text-slate-800 pt-1">
                            <span>TOTAL BAYAR:</span>
                            <span id="cart-total" class="text-indigo-600">Rp {{ number_format($subtotal + ($subtotal * 0.11), 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('pembeli.order.checkout') }}" class="block text-center w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all active:scale-[0.98] mt-4">
                        Lanjut ke Checkout
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- AJAX Cart Quantity Updating -->
    <script>
        async function updateCartQty(cartId, change) {
            const input = document.getElementById(`cart-qty-input-${cartId}`);
            let currentQty = parseInt(input.value);
            let targetQty = currentQty + change;

            if (targetQty < 1) return; // Prevent less than 1 unit

            // Temporarily disable buttons
            input.disabled = true;

            try {
                const response = await fetch('/toko/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        cart_id: cartId,
                        quantity: targetQty
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    input.value = targetQty;
                    // Update DOM totals
                    document.getElementById(`item-subtotal-${cartId}`).textContent = data.item_subtotal;
                    document.getElementById('cart-subtotal').textContent = data.cart_subtotal;
                    document.getElementById('cart-total').textContent = data.cart_total;
                    
                    // Recalculate tax
                    const subtotalNum = parseFloat(data.cart_subtotal.replace(/[^0-9]/g, ''));
                    const taxNum = subtotalNum * 0.11;
                    document.getElementById('cart-tax').textContent = 'Rp ' + taxNum.toLocaleString('id-ID');

                    // Fire a custom cart poller update to keep header badge in sync
                    if (window.dispatchEvent) {
                        window.dispatchEvent(new Event('DOMContentLoaded'));
                    }
                } else {
                    alert(data.message || 'Gagal memperbarui kuantitas.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            } finally {
                input.disabled = false;
            }
        }
    </script>
</x-pembeli-layout>
