<footer class="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 pt-12 pb-6 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Brand & Description -->
            <div class="col-span-1 md:col-span-1">
                <a href="{{ route('pembeli.shop.index') }}" class="flex items-center gap-2 group mb-4">
                    <span class="text-3xl group-hover:rotate-12 transition-transform duration-300">🛒</span>
                    <span class="font-extrabold text-2xl tracking-tight bg-gradient-to-r from-primary-500 to-secondary-500 bg-clip-text text-transparent">TokoKu</span>
                </a>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">
                    Temukan produk-produk terbaik dengan harga terjangkau. Belanja mudah, aman, dan nyaman.
                </p>
                <!-- Social Media (Placeholder icons) -->
                <div class="flex gap-4">
                    <a href="#" class="w-8 h-8 rounded-full bg-primary-50 dark:bg-slate-800 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-primary-50 dark:bg-slate-800 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm3 8h-1.35c-.538 0-.65.221-.65.778v1.222h2l-.209 2h-1.791v7h-3v-7h-2v-2h2v-2.308c0-1.769.931-2.692 3.029-2.692h1.971v3z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-primary-50 dark:bg-slate-800 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:bg-primary-100 dark:hover:bg-slate-700 transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Direct Links -->
            <div class="col-span-1">
                <h3 class="font-bold text-slate-800 dark:text-slate-200 mb-4">{{ __('messages.direct_links') }}</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400 transition-colors">{{ __('messages.welcome') }}</a></li>
                    <li><a href="{{ route('pembeli.shop.index') }}" class="text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400 transition-colors">{{ __('messages.shop') }}</a></li>
                    <li><a href="{{ route('pembeli.cart.index') }}" class="text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400 transition-colors">{{ __('messages.cart') }}</a></li>
                    <li><a href="{{ route('pembeli.order.index') }}" class="text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400 transition-colors">{{ __('messages.orders') }}</a></li>
                    <li><a href="{{ route('pembeli.profile.index') }}" class="text-sm text-slate-500 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400 transition-colors">{{ __('messages.profile') }}</a></li>
                </ul>
            </div>

            <!-- Contact Us -->
            <div class="col-span-1">
                <h3 class="font-bold text-slate-800 dark:text-slate-200 mb-4">{{ __('messages.contact_us') }}</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <span class="text-xl">📍</span>
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            <strong>{{ __('messages.address') }}:</strong><br>
                            Jl. Teknologi Canggih No.123<br>
                            Jakarta, Indonesia
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="text-xl">📞</span>
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            <strong>{{ __('messages.phone') }}:</strong> +62 812-3456-7890
                        </div>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="text-xl">✉️</span>
                        <div class="text-sm text-slate-500 dark:text-slate-400">
                            <strong>{{ __('messages.email') }}:</strong> hello@tokoku.com
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Maps Embed -->
            <div class="col-span-1">
                <h3 class="font-bold text-slate-800 dark:text-slate-200 mb-4">Peta Lokasi</h3>
                <div class="rounded-xl overflow-hidden shadow-sm h-40">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126920.24036109986!2d106.74711736199216!3d-6.229746522339655!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x100c5e82dd4b820!2sJakarta%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1689000000000!5m2!1sid!2sid" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-100 dark:border-slate-800 pt-6 flex flex-col md:flex-row items-center justify-between">
            <p class="text-xs text-slate-400 dark:text-slate-500 mb-4 md:mb-0">
                &copy; {{ date('Y') }} TokoKu Online Store. {{ __('messages.footer_rights') }}
            </p>
            <div class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                {{ __('messages.developer') }}: <span class="font-semibold text-primary-500">Antigravity Team</span>
            </div>
        </div>
    </div>
</footer>
