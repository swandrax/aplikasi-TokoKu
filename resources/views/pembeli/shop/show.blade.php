<x-pembeli-layout title="{{ $product->name }} - TokoKu Store">
    <div class="space-y-12 font-sans">
        <!-- Back Button -->
        <a href="{{ route('pembeli.shop.index') }}" class="text-xs font-bold text-slate-500 hover:text-primary-650 flex items-center gap-1.5 transition-colors">
            &larr; Kembali ke Katalog Belanja
        </a>

        <!-- Product Details Panel -->
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <!-- Left: Product Image -->
            <div class="w-full h-80 rounded-2xl overflow-hidden bg-slate-100 flex items-center justify-center text-8xl shadow-inner border border-slate-50">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    📦
                @endif
            </div>

            <!-- Right: Details form -->
            <div class="space-y-6">
                <div>
                    <!-- Category Badge -->
                    <span class="px-2.5 py-0.5 bg-primary-50 border border-primary-100 rounded-lg text-[10px] font-bold text-primary-700 uppercase tracking-wider">
                        {{ $product->category->name }}
                    </span>
                    <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight mt-2 leading-tight">{{ $product->name }}</h2>
                    <span class="text-xs font-mono text-slate-400 block mt-0.5">SKU: TOKOKU-PROD-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>

                <!-- Price and Weight -->
                <div class="grid grid-cols-2 gap-4 py-4 border-y border-slate-50">
                    <div>
                        <span class="text-[9px] uppercase tracking-wider font-bold text-slate-400">Harga Jual</span>
                        <span class="block font-black text-primary-600 text-lg sm:text-xl">Rp {{ number_format((float) $product->price_sell, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[9px] uppercase tracking-wider font-bold text-slate-400">Berat Produk</span>
                        <span class="block font-extrabold text-slate-800 text-sm sm:text-base mt-1">{{ number_format((float) $product->weight, 0, ',', '.') }} gram</span>
                    </div>
                </div>

                <!-- Product Description -->
                <div>
                    <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider mb-2">Deskripsi Produk</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $product->description }}</p>
                </div>

                <!-- Inventory details & purchase form -->
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500">Status Persediaan:</span>
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-lg {{ $availableStock > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                            {{ $availableStock > 0 ? 'Tersedia: ' . $availableStock . ' Unit' : 'Stok Habis' }}
                        </span>
                    </div>

                    @if($availableStock > 0)
                        <form action="{{ route('pembeli.cart.add') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            
                            <div>
                                <label for="quantity" class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Jumlah Pembelian</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $availableStock }}" required
                                           class="w-24 px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-primary-600 text-slate-700 text-center font-bold">
                                    <span class="text-[10px] text-slate-400 font-semibold">Unit</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 bg-primary-650 hover:bg-primary-755 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                                🛒 Masukkan Keranjang
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full py-3 bg-slate-200 text-slate-400 font-bold text-xs uppercase tracking-wider rounded-xl cursor-not-allowed">
                            Stok Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        @if(!$relatedProducts->isEmpty())
            <div class="space-y-4">
                <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider border-l-3 border-primary-600 pl-3">Produk Terkait Lainnya</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $rel)
                        <a href="{{ route('pembeli.shop.show', $rel->slug) }}" class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 flex flex-col justify-between group">
                            <div>
                                <div class="w-full h-32 bg-slate-100 rounded-xl mb-3 flex items-center justify-center text-3xl group-hover:scale-[1.02] transition-transform">
                                    📦
                                </div>
                                <h4 class="font-bold text-xs text-slate-800 leading-snug line-clamp-2 mb-1 group-hover:text-primary-600 transition-colors">{{ $rel->name }}</h4>
                            </div>
                            <span class="text-xs font-extrabold text-primary-600 mt-2 block">Rp {{ number_format((float) $rel->price_sell, 0, ',', '.') }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-pembeli-layout>
