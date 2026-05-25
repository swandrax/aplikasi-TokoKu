<x-pembeli-layout title="TokoKu Store - Premium Catalog">
    <div class="space-y-8 font-sans">
        <!-- Banner Carousel / Welcoming Hero -->
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-primary-600 via-primary-700 to-primary-900 text-white p-8 sm:p-12 shadow-md transition-colors duration-300">
            <div class="max-w-xl space-y-4">
                <span class="px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-[10px] font-bold uppercase tracking-wider">🎉 Promo Spesial Bulan Ini</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-none">Temukan Produk Unggulan & Premium</h2>
                <p class="text-xs text-primary-100 leading-relaxed">
                    Dapatkan kemudahan berbelanja online berkualitas tinggi dengan jaminan keaslian barang, pengiriman cepat, dan sistem pendukung pelanggan bertenaga kecerdasan buatan.
                </p>
            </div>
            <span class="absolute right-12 bottom-0 text-9xl opacity-10 hidden sm:block">🛍️</span>
        </div>

        <!-- Catalog Sidebar Filters & Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            <!-- Sidebar Filter Panel -->
            <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm space-y-6 transition-colors duration-300">
                <!-- Kategori Filter -->
                <div>
                    <h3 class="font-extrabold text-slate-800 dark:text-slate-200 text-xs uppercase tracking-wider mb-3">Pilih Kategori</h3>
                    <div class="space-y-1.5">
                        <a href="{{ route('pembeli.shop.index', request()->except(['category_id', 'page'])) }}" 
                           class="flex items-center justify-between px-3 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition-colors {{ is_null(request('category_id')) ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <span>Semua Kategori</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('pembeli.shop.index', array_merge(request()->all(), ['category_id' => $cat->id, 'page' => 1])) }}" 
                               class="flex items-center justify-between px-3 py-1.5 rounded-xl text-xs font-semibold tracking-wide transition-colors {{ request('category_id') == $cat->id ? 'bg-primary-50 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <span>{{ $cat->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range Filter -->
                <form action="{{ route('pembeli.shop.index') }}" method="GET" class="space-y-4 border-t border-slate-50 dark:border-slate-700 pt-4">
                    @if(request('category_id'))
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    @endif
                    @if(request('keyword'))
                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                    @endif

                    <h3 class="font-extrabold text-slate-800 dark:text-slate-200 text-xs uppercase tracking-wider">Batas Harga (Rp)</h3>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" min="0" 
                               class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-primary-600 dark:focus:border-primary-400 focus:bg-white dark:focus:bg-slate-800 text-slate-700 dark:text-slate-300">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" min="0" 
                               class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:border-primary-600 dark:focus:border-primary-400 focus:bg-white dark:focus:bg-slate-800 text-slate-700 dark:text-slate-300">
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition-all shadow-md active:scale-98">
                        Terapkan Filter
                    </button>
                    @if(request('min_price') || request('max_price'))
                        <a href="{{ route('pembeli.shop.index', request()->except(['min_price', 'max_price'])) }}" class="block text-center text-[10px] font-bold text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:underline">Hapus Filter Harga</a>
                    @endif
                </form>
            </div>

            <!-- Product Grid Catalog (3/4 column) -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Search Result Header -->
                @if(!empty($keyword))
                    <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-semibold">
                        <span>Menampilkan hasil pencarian untuk:</span>
                        <span class="px-2.5 py-0.5 bg-primary-50 dark:bg-primary-900/50 border border-primary-100 dark:border-primary-800 rounded-full font-bold text-primary-700 dark:text-primary-300">"{{ $keyword }}"</span>
                        <a href="{{ route('pembeli.shop.index', request()->except(['keyword'])) }}" class="text-[10px] text-slate-400 hover:text-rose-500 font-bold ml-2">Clear</a>
                    </div>
                @endif

                <!-- Product Grid Container -->
                <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @include('partials.product-cards', ['products' => $products])
                </div>

                <!-- AJAX Load More Button -->
                @if($hasMore)
                    <div class="text-center pt-4" id="load-more-container">
                        <button type="button" id="load-more-btn" onclick="loadMoreProducts()" 
                                class="px-6 py-3 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 font-bold text-xs uppercase tracking-wider rounded-2xl shadow-sm hover:shadow transition-all active:scale-[0.98] inline-flex items-center gap-2">
                            <span>🔄</span> Muat Lebih Banyak
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Lazy Loading AJAX script -->
    <script>
        let currentPage = 1;
        
        async function loadMoreProducts() {
            const btn = document.getElementById('load-more-btn');
            const container = document.getElementById('product-grid');
            const btnContainer = document.getElementById('load-more-container');

            currentPage++;
            btn.disabled = true;
            btn.innerHTML = `<span>⏳</span> Memuat...`;

            // Prepare URL query params
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('page', currentPage);

            try {
                const response = await fetch(`${window.location.pathname}?${urlParams.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    
                    // Append HTML nodes
                    container.insertAdjacentHTML('beforeend', data.html);

                    // Re-trigger Image Lazy Loader Intersection Observer
                    if (window.dispatchEvent) {
                        window.dispatchEvent(new Event('DOMContentLoaded'));
                    }

                    // Check if more items exist
                    if (data.has_more) {
                        btn.disabled = false;
                        btn.innerHTML = `<span>🔄</span> Muat Lebih Banyak`;
                    } else {
                        btnContainer.remove();
                    }
                }
            } catch (err) {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = `<span>🔄</span> Muat Lebih Banyak`;
                alert('Gagal memuat produk tambahan. Silakan coba lagi.');
            }
        }
    </script>
</x-pembeli-layout>
