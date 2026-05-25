@extends('layouts.kasir')

@section('header_title', 'Point of Sale (POS) - Kasir Workspace')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 font-sans items-start h-[calc(100vh-140px)]">
    <!-- Left Pane: Product Catalog Grid (2/3 columns) -->
    <div class="lg:col-span-2 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col h-full overflow-hidden">
        <!-- Search bar -->
        <div class="mb-4 flex items-center justify-between gap-4">
            <form action="{{ route('kasir.pos.index') }}" method="GET" class="flex-1 relative flex items-center">
                <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Cari produk di POS (Boyer-Moore)..." 
                       class="w-full px-4 py-2.5 pl-10 text-xs bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:border-indigo-650 focus:bg-white text-slate-700 transition-all">
                <span class="absolute left-3.5 text-slate-400 text-xs">🔍</span>
                @if(!empty($keyword))
                    <a href="{{ route('kasir.pos.index') }}" class="absolute right-3.5 text-slate-400 hover:text-slate-600 text-[10px] font-bold">Clear</a>
                @endif
            </form>
        </div>

        <!-- Scrollable Product Grid -->
        <div class="flex-grow overflow-y-auto grid grid-cols-2 sm:grid-cols-3 gap-4 pr-2 scrollbar-thin">
            @forelse($products as $prod)
                <button type="button" 
                        onclick="addToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ (float) $prod->price_sell }}, {{ $prod->available_stock }})"
                        {{ $prod->available_stock <= 0 ? 'disabled' : '' }}
                        class="text-left bg-slate-50 hover:bg-indigo-50/50 border border-slate-100 hover:border-indigo-200/50 p-3.5 rounded-2xl flex flex-col justify-between shadow-sm hover:shadow-md transition-all duration-200 group active:scale-98 {{ $prod->available_stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                    <div>
                        <!-- Image placeholder / category badge -->
                        <div class="relative w-full h-24 bg-slate-200 rounded-xl mb-3 flex items-center justify-center text-2xl overflow-hidden shadow-inner">
                            📦
                            <span class="absolute top-1.5 left-1.5 px-2 py-0.5 text-[8px] font-bold uppercase tracking-wider rounded-md {{ $prod->available_stock > 0 ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                {{ $prod->available_stock > 0 ? 'Stok: ' . $prod->available_stock : 'Habis' }}
                            </span>
                        </div>
                        <h4 class="font-bold text-xs text-slate-800 leading-snug line-clamp-2 mb-1 group-hover:text-indigo-600 transition-colors">{!! $prod->highlighted_name !!}</h4>
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block">{{ $prod->category->name }}</span>
                    </div>
                    <span class="text-xs font-extrabold text-indigo-600 mt-2 block">Rp {{ number_format((float) $prod->price_sell, 0, ',', '.') }}</span>
                </button>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    <span class="text-3xl block mb-2">🔍</span>
                    <h4 class="font-bold text-xs text-slate-700">Tidak ada produk ditemukan.</h4>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Right Pane: Transaction Receipt Cart (1/3 column) -->
    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between h-full overflow-hidden">
        <div>
            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider mb-4 pb-2 border-b border-slate-50">Struk Pembelanjaan POS</h3>
            
            <!-- Cart Items List Scrollable -->
            <div id="pos-cart-list" class="max-h-48 overflow-y-auto divide-y divide-slate-100 pr-2 scrollbar-thin space-y-2 pb-4">
                <!-- Javascript will render items here -->
                <div class="text-center py-8 text-slate-400 text-xs font-medium">Keranjang POS Kosong. Klik produk di sebelah kiri untuk menambahkan.</div>
            </div>
        </div>

        <!-- Price breakdown and Form Fields -->
        <form action="{{ route('kasir.pos.checkout') }}" method="POST" id="checkout-form" class="mt-4 border-t border-slate-100 pt-4 space-y-4">
            @csrf
            <input type="hidden" name="cart" id="cart-input-value">

            <!-- Subtotal / Taxes / Discount / Total -->
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-500 font-semibold">
                    <span>Subtotal:</span>
                    <span id="pos-subtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-slate-500 font-semibold">
                    <span>Pajak (11%):</span>
                    <span id="pos-tax">Rp 0</span>
                </div>
                <div class="flex justify-between text-slate-500 font-semibold items-center">
                    <span>Diskon (Rp):</span>
                    <input type="number" name="discount_amount" id="discount-input" value="0" min="0" oninput="calculateTotals()"
                           class="w-24 px-2 py-1 text-[11px] text-right bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-indigo-650 focus:bg-white text-slate-700">
                </div>
                <div class="flex justify-between font-extrabold text-sm border-t border-slate-50 pt-2 text-slate-800">
                    <span>TOTAL:</span>
                    <span id="pos-total" class="text-indigo-650">Rp 0</span>
                </div>
            </div>

            <!-- Customer Email Profile lookup -->
            <div>
                <label for="pembeli_email" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Alamat Email Pembeli</label>
                <input type="email" name="pembeli_email" id="pembeli_email" value="{{ old('pembeli_email') }}" placeholder="Contoh: pembeli@tokoku.com" required 
                       class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-650 focus:bg-white text-slate-700 transition-all">
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Metode Pembayaran</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="flex flex-col items-center justify-center p-2 border border-slate-200 rounded-xl cursor-pointer text-slate-600 hover:bg-slate-50 font-bold text-[10px] text-center select-none" id="label-tunai">
                        <input type="radio" name="payment_method" value="Tunai" checked class="hidden" onclick="selectPayment('Tunai')">
                        💵 Tunai
                    </label>
                    <label class="flex flex-col items-center justify-center p-2 border border-slate-200 rounded-xl cursor-pointer text-slate-600 hover:bg-slate-50 font-bold text-[10px] text-center select-none" id="label-transfer">
                        <input type="radio" name="payment_method" value="Transfer Bank" class="hidden" onclick="selectPayment('Transfer Bank')">
                        🏦 Transfer
                    </label>
                    <label class="flex flex-col items-center justify-center p-2 border border-slate-200 rounded-xl cursor-pointer text-slate-600 hover:bg-slate-50 font-bold text-[10px] text-center select-none" id="label-qris">
                        <input type="radio" name="payment_method" value="Qris" class="hidden" onclick="selectPayment('Qris')">
                        📱 QRIS
                    </label>
                    <label class="flex flex-col items-center justify-center p-2 border border-slate-200 rounded-xl cursor-pointer text-slate-600 hover:bg-slate-50 font-bold text-[10px] text-center select-none" id="label-debit">
                        <input type="radio" name="payment_method" value="Debit" class="hidden" onclick="selectPayment('Debit')">
                        💳 Debit
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="button" onclick="showPaymentGateway()" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-md transition-all active:scale-[0.98]">
                Proses Pembayaran
            </button>
        </form>
    </div>
</div>

<!-- Payment Gateway Preview Modal -->
<div id="payment-modal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl transform scale-95 transition-transform duration-300" id="payment-modal-content">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                <span class="p-2 bg-indigo-100 text-indigo-600 rounded-xl">💳</span> Payment Gateway Preview
            </h3>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <div class="text-center mb-6">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Tagihan</p>
            <h2 class="text-3xl font-extrabold text-indigo-650 mt-1" id="modal-total-amount">Rp 0</h2>
        </div>

        <div id="pg-content-tunai" class="hidden payment-content text-center py-8 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="text-5xl mb-4">💵</div>
            <h4 class="font-bold text-slate-800 mb-1">Pembayaran Tunai</h4>
            <p class="text-xs text-slate-500">Silakan terima uang tunai dari pelanggan sesuai total tagihan.</p>
        </div>

        <div id="pg-content-qris" class="hidden payment-content text-center py-6 bg-slate-50 rounded-2xl border border-slate-100">
            <h4 class="font-extrabold text-slate-800 mb-4 tracking-widest text-xl flex items-center justify-center gap-2">
                <span class="text-blue-600">Q</span><span class="text-blue-500">R</span><span class="text-red-500">I</span><span class="text-yellow-500">S</span>
            </h4>
            <div class="w-40 h-40 bg-white border-4 border-slate-200 rounded-xl mx-auto flex items-center justify-center shadow-sm relative">
                <!-- Fake QR Code Pattern -->
                <div class="grid grid-cols-4 grid-rows-4 gap-1 w-32 h-32 opacity-80">
                    <div class="bg-slate-800 col-span-2 row-span-2 rounded-tl-lg"></div><div class="bg-slate-800 rounded-tr-lg"></div><div class="bg-white"></div>
                    <div class="bg-white"></div><div class="bg-slate-800"></div><div class="bg-slate-800"></div><div class="bg-slate-800"></div>
                    <div class="bg-slate-800 col-span-2 row-span-2 rounded-bl-lg"></div><div class="bg-slate-800"></div><div class="bg-white"></div>
                    <div class="bg-white"></div><div class="bg-slate-800 rounded-br-lg"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-8 h-8 bg-white rounded-md shadow-md flex items-center justify-center font-bold text-[8px] text-indigo-600">MOCK</div>
                </div>
            </div>
            <p class="text-[10px] text-slate-500 mt-4">Minta pelanggan scan QR Code ini menggunakan aplikasi pembayaran.</p>
        </div>

        <div id="pg-content-transfer" class="hidden payment-content py-6 px-4 bg-slate-50 rounded-2xl border border-slate-100">
            <h4 class="font-bold text-slate-800 mb-4 text-center">Virtual Account (VA)</h4>
            <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-slate-200">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase block mb-1">Bank BCA</span>
                    <p class="font-mono text-lg font-bold tracking-widest text-slate-700">88000 <span class="text-indigo-600">123456789</span></p>
                </div>
                <button class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[10px] font-bold rounded-lg transition-colors">SALIN</button>
            </div>
            <p class="text-[10px] text-center text-slate-500 mt-4">Silakan transfer tepat sejumlah total tagihan ke Virtual Account di atas.</p>
        </div>

        <div id="pg-content-debit" class="hidden payment-content text-center py-6 bg-slate-50 rounded-2xl border border-slate-100">
            <div class="text-5xl mb-4">💳</div>
            <h4 class="font-bold text-slate-800 mb-1">Kartu Debit/Kredit</h4>
            <p class="text-xs text-slate-500 mb-4">Silakan gesek/dip kartu pelanggan pada mesin EDC.</p>
            <div class="inline-flex items-center gap-2">
                <span class="px-2 py-1 bg-white border border-slate-200 rounded text-[10px] font-bold text-blue-800 italic">VISA</span>
                <span class="px-2 py-1 bg-white border border-slate-200 rounded text-[10px] font-bold text-red-600 italic">MasterCard</span>
                <span class="px-2 py-1 bg-white border border-slate-200 rounded text-[10px] font-bold text-orange-600 italic">GPN</span>
            </div>
        </div>

        <div class="mt-6">
            <button onclick="document.getElementById('checkout-form').submit()" class="w-full py-3.5 bg-indigo-650 hover:bg-indigo-700 text-white font-bold text-sm uppercase tracking-wider rounded-2xl shadow-lg shadow-indigo-650/30 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                ✅ Konfirmasi & Cetak Struk
            </button>
        </div>
        <p class="text-center mt-3 text-[10px] text-slate-400">* Ini adalah pratinjau (preview) gateway pembayaran.</p>
    </div>
</div>

<!-- Reprint / print auto window trigger -->
@if(session('print_order_id'))
    <script>
        window.onload = function() {
            window.open("{{ route('struk.print', session('print_order_id')) }}", '_blank', 'width=400,height=600');
        };
    </script>
@endif

<script>
    let cart = [];

    function addToCart(id, name, price, stock) {
        const item = cart.find(x => x.id === id);
        if (item) {
            if (item.qty < stock) {
                item.qty++;
            } else {
                alert(`Batas stok tercapai. Sisa stok tersedia: ${stock} unit.`);
            }
        } else {
            cart.push({ id, name, price, qty: 1, stock });
        }
        renderCart();
    }

    function updateQty(id, change) {
        const item = cart.find(x => x.id === id);
        if (item) {
            item.qty += change;
            if (item.qty <= 0) {
                cart = cart.filter(x => x.id !== id);
            } else if (item.qty > item.stock) {
                alert(`Batas stok tercapai. Sisa stok tersedia: ${item.stock} unit.`);
                item.qty = item.stock;
            }
        }
        renderCart();
    }

    function selectPayment(method) {
        document.getElementById('label-tunai').className = "flex flex-col items-center justify-center p-2 border rounded-xl cursor-pointer text-slate-600 hover:bg-slate-50 font-bold text-[10px] text-center select-none " + (method === 'Tunai' ? 'border-indigo-650 bg-indigo-50/50 text-indigo-700' : 'border-slate-200');
        document.getElementById('label-transfer').className = "flex flex-col items-center justify-center p-2 border rounded-xl cursor-pointer text-slate-600 hover:bg-slate-50 font-bold text-[10px] text-center select-none " + (method === 'Transfer Bank' ? 'border-indigo-650 bg-indigo-50/50 text-indigo-700' : 'border-slate-200');
        document.getElementById('label-qris').className = "flex flex-col items-center justify-center p-2 border rounded-xl cursor-pointer text-slate-600 hover:bg-slate-50 font-bold text-[10px] text-center select-none " + (method === 'Qris' ? 'border-indigo-650 bg-indigo-50/50 text-indigo-700' : 'border-slate-200');
        document.getElementById('label-debit').className = "flex flex-col items-center justify-center p-2 border rounded-xl cursor-pointer text-slate-600 hover:bg-slate-50 font-bold text-[10px] text-center select-none " + (method === 'Debit' ? 'border-indigo-650 bg-indigo-50/50 text-indigo-700' : 'border-slate-200');
    }

    function showPaymentGateway() {
        if (cart.length === 0) {
            alert('Keranjang POS kosong.');
            return;
        }
        
        const email = document.getElementById('pembeli_email').value;
        if (!email) {
            alert('Silakan isi email pembeli terlebih dahulu.');
            return;
        }

        const method = document.querySelector('input[name="payment_method"]:checked').value;
        
        // Hide all pg content
        document.querySelectorAll('.payment-content').forEach(el => el.classList.add('hidden'));
        
        // Show selected pg content
        if (method === 'Tunai') document.getElementById('pg-content-tunai').classList.remove('hidden');
        if (method === 'Transfer Bank') document.getElementById('pg-content-transfer').classList.remove('hidden');
        if (method === 'Qris') document.getElementById('pg-content-qris').classList.remove('hidden');
        if (method === 'Debit') document.getElementById('pg-content-debit').classList.remove('hidden');

        // Set total
        document.getElementById('modal-total-amount').textContent = document.getElementById('pos-total').textContent;

        // Show Modal
        const modal = document.getElementById('payment-modal');
        const modalContent = document.getElementById('payment-modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
    }

    function closePaymentModal() {
        const modal = document.getElementById('payment-modal');
        const modalContent = document.getElementById('payment-modal-content');
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function renderCart() {
        const container = document.getElementById('pos-cart-list');
        const cartInput = document.getElementById('cart-input-value');
        
        // Write to hidden input for form submission
        cartInput.value = JSON.stringify(cart);

        if (cart.length === 0) {
            container.innerHTML = `<div class="text-center py-8 text-slate-400 text-xs font-medium">Keranjang POS Kosong. Klik produk di sebelah kiri untuk menambahkan.</div>`;
            calculateTotals();
            return;
        }

        container.innerHTML = cart.map(item => `
            <div class="flex items-center justify-between py-2 border-b border-slate-50">
                <div class="flex-1 min-w-0 pr-2">
                    <h5 class="font-bold text-xs text-slate-800 truncate">${item.name}</h5>
                    <span class="text-[10px] text-slate-400 font-semibold">Rp ${formatNumber(item.price)}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="updateQty(${item.id}, -1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-250 flex items-center justify-center font-bold text-xs">-</button>
                    <span class="font-extrabold text-xs text-slate-800 min-w-[20px] text-center">${item.qty}</span>
                    <button type="button" onclick="updateQty(${item.id}, 1)" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-250 flex items-center justify-center font-bold text-xs">+</button>
                </div>
                <span class="font-extrabold text-xs text-slate-800 ml-4 text-right min-w-[70px]">Rp ${formatNumber(item.price * item.qty)}</span>
            </div>
        `).join('');

        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        cart.forEach(item => {
            subtotal += item.price * item.qty;
        });

        const tax = subtotal * 0.11;
        const discount = parseFloat(document.getElementById('discount-input').value) || 0;
        const total = subtotal + tax - discount;

        document.getElementById('pos-subtotal').textContent = `Rp ${formatNumber(subtotal)}`;
        document.getElementById('pos-tax').textContent = `Rp ${formatNumber(tax)}`;
        document.getElementById('pos-total').textContent = `Rp ${formatNumber(Math.max(0, total))}`;
    }

    function formatNumber(num) {
        return num.toLocaleString('id-ID');
    }

    // Set default payment highlight on startup
    selectPayment('Tunai');
</script>
@endsection
