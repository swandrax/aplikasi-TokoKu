@forelse($products as $product)
    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-700 p-4 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex flex-col justify-between group">
        <div>
            <!-- Product Image with Lazy Loading Skeleton -->
            <div class="relative w-full h-40 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 mb-3 group-hover:scale-[1.02] transition-transform duration-300">
                @if($product->image)
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'></svg>" 
                         data-src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         loading="lazy" 
                         class="lazy-img w-full h-full object-cover opacity-0 transition-opacity duration-500">
                @else
                    <!-- High-quality seeded category illustrations fallback -->
                    <div class="w-full h-full flex flex-col items-center justify-center bg-primary-50 dark:bg-slate-600 text-primary-400 dark:text-primary-300">
                        <span class="text-4xl mb-1">📦</span>
                        <span class="text-[9px] uppercase font-bold tracking-wider text-primary-300 dark:text-primary-200">{{ $product->category->name }}</span>
                    </div>
                @endif
                
                <!-- Stock Status Badge -->
                <span class="absolute top-2.5 left-2.5 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider rounded-lg shadow-sm {{ $product->available_stock > 0 ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                    {{ $product->available_stock > 0 ? 'Tersedia: ' . $product->available_stock : 'Habis' }}
                </span>
            </div>

            <!-- Category & Weight -->
            <div class="flex justify-between items-center mb-1 text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider">
                <span>{{ $product->category->name }}</span>
                <span>{{ number_format((float) $product->weight, 0, ',', '.') }}g</span>
            </div>

            <!-- Product Title with Boyer-Moore Highlights -->
            <h4 class="font-bold text-slate-800 dark:text-slate-200 text-sm mb-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors leading-tight line-clamp-2">
                {!! $product->highlighted_name !!}
            </h4>
        </div>

        <!-- Pricing and CTAs -->
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div>
                <span class="text-[9px] block text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">Harga Jual</span>
                <span class="text-sm font-extrabold text-primary-600 dark:text-primary-400">Rp {{ number_format((float) $product->price_sell, 0, ',', '.') }}</span>
            </div>
            
            @if($product->available_stock > 0)
                <form action="{{ route('pembeli.cart.add') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="flex items-center justify-center p-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all focus:outline-none transform active:scale-95 group-hover:rotate-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </form>
            @else
                <button disabled class="p-2 bg-slate-100 dark:bg-slate-700 text-slate-300 dark:text-slate-500 rounded-xl cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
@empty
    <div class="col-span-full py-12 text-center text-slate-400 bg-white/50 dark:bg-slate-800/50 backdrop-blur-md rounded-2xl border border-slate-100 dark:border-slate-700">
        <span class="text-4xl block mb-2">🔍</span>
        <h4 class="font-bold text-sm text-slate-700 dark:text-slate-300">Tidak ada produk ditemukan</h4>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Silakan coba kata kunci lain atau bersihkan filter Anda.</p>
    </div>
@endforelse

<!-- Intersection Observer lazy image loading script (loaded automatically via parent layout) -->
