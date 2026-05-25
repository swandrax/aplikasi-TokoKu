<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'TokoKu Store' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS Stylesheet -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 5px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
    <!-- Theme Script -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen flex flex-col selection:bg-primary-500 selection:text-white transition-colors duration-300">
    <!-- Navbar -->
    <header class="sticky top-0 z-40 bg-white/70 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Left Side Logo -->
            <a href="{{ route('pembeli.shop.index') }}" class="flex items-center gap-2 group">
                <span class="text-2xl group-hover:rotate-12 transition-transform duration-300">🛒</span>
                <img src="{{ asset('favicon.svg') }}" alt="Logo" class="w-8 h-8 group-hover:scale-110 transition-transform">
                <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-400 dark:to-primary-600 bg-clip-text text-transparent">TokoKu</span>
            </a>

            <!-- Search Bar in Header -->
            <form action="{{ route('pembeli.shop.index') }}" method="GET" class="hidden md:flex items-center w-full max-w-sm relative">
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="{{ __('messages.search_placeholder') }}" 
                       class="w-full px-4 py-2 pl-10 text-xs bg-slate-100 dark:bg-slate-800 focus:bg-white dark:focus:bg-slate-700 border border-transparent focus:border-primary-600 dark:focus:border-primary-400 rounded-2xl focus:outline-none transition-all dark:text-slate-200">
                <span class="absolute left-3 text-slate-400 dark:text-slate-500 text-xs">🔍</span>
            </form>

            <!-- Right Navigation Items -->
            <div class="flex items-center gap-4">
                <!-- Language Switcher -->
                <div class="relative group hidden sm:block">
                    <button class="flex items-center gap-1 text-slate-500 hover:text-primary-600 dark:text-slate-400 dark:hover:text-primary-400 text-xs font-bold uppercase tracking-wider transition-colors focus:outline-none">
                        🌐 {{ strtoupper(App::getLocale()) }}
                    </button>
                    <div class="absolute right-0 mt-2 w-32 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                        <a href="{{ route('lang.switch', ['lang' => 'id']) }}" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ App::getLocale() == 'id' ? 'font-bold text-primary-600 dark:text-primary-400' : '' }}">🇮🇩 Indonesia</a>
                        <a href="{{ route('lang.switch', ['lang' => 'en']) }}" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 {{ App::getLocale() == 'en' ? 'font-bold text-primary-600 dark:text-primary-400' : '' }}">🇬🇧 English</a>
                    </div>
                </div>

                <!-- Theme Toggle -->
                <button id="theme-toggle" class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-full transition-all focus:outline-none" title="Toggle Theme">
                    <!-- Sun icon for dark mode (to switch to light) -->
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.32a1 1 0 011.415 0l.708.707a1 1 0 01-1.414 1.415l-.708-.707a1 1 0 010-1.415zM16 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zm-2.32 4.22a1 1 0 010 1.415l-.707.708a1 1 0 01-1.415-1.414l.707-.708a1 1 0 011.415 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-4.22-2.32a1 1 0 01-1.415 0l-.708-.707a1 1 0 011.414-1.415l.708.707a1 1 0 010 1.415zM4 10a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm2.32-4.22a1 1 0 010-1.415l.707-.708a1 1 0 011.415 1.414l-.707.708a1 1 0 01-1.415 0z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    <!-- Moon icon for light mode (to switch to dark) -->
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                </button>

                <!-- Shop Link -->
                <a href="{{ route('pembeli.shop.index') }}" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-bold text-xs uppercase tracking-wider transition-colors hidden sm:block">{{ __('messages.shop') }}</a>
                
                <!-- Order History Link -->
                <a href="{{ route('pembeli.order.index') }}" class="text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 font-bold text-xs uppercase tracking-wider transition-colors">{{ __('messages.orders') }}</a>

                <!-- Shopping Cart Icon with Live Badge -->
                <a href="{{ route('pembeli.cart.index') }}" class="relative p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-full transition-all focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5h6.75M8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm6.75 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <span id="cart-badge" class="hidden absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[9px] font-bold leading-none text-white bg-primary-600 rounded-full transform translate-x-1 -translate-y-1">0</span>
                </a>

                <!-- Notification Dropdown -->
                @include('partials.notification-bell')

                <!-- User Dropdown & Profile -->
                <div class="h-8 border-l border-slate-200 dark:border-slate-700"></div>
                
                <a href="{{ route('pembeli.profile.index') }}" class="text-xs font-bold text-slate-700 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 flex items-center gap-1.5 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center text-primary-700 dark:text-primary-300 font-extrabold text-sm shadow-inner">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden lg:inline">{{ Auth::user()->name }}</span>
                </a>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 dark:hover:text-rose-400 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none" title="{{ __('messages.logout') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Floating Flash Banners -->
        @if (session('success'))
            <div class="mb-6 p-4 text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-2 shadow-sm animate-alert-pop">
                <span>✅</span>
                <div class="font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 text-xs text-rose-800 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-2 shadow-sm animate-alert-pop">
                <span>⚠️</span>
                <div class="font-semibold">{{ session('error') }}</div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    @include('partials.footer')

    <!-- Chatbot Floating Widget -->
    @include('partials.chatbot-widget')

    <!-- Native lazy loading & Real-time Cart badge poller -->
    <script>
        // Dark Mode Toggle Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            const isDark = document.documentElement.classList.contains('dark');

            if (isDark) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // 1. Intersection Observer for Image Lazy Loading
            const lazyImages = document.querySelectorAll('.lazy-img');
            if ('IntersectionObserver' in window) {
                const imgObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.onload = () => {
                                img.classList.remove('opacity-0');
                                img.classList.add('opacity-100');
                            };
                            imgObserver.unobserve(img);
                        }
                    });
                });
                lazyImages.forEach(img => imgObserver.observe(img));
            } else {
                // Fallback for older browsers
                lazyImages.forEach(img => {
                    img.src = img.dataset.src;
                    img.classList.remove('opacity-0');
                });
            }

            // 2. Real-time shopping cart count poller (polls every 10 seconds)
            const cartBadge = document.getElementById('cart-badge');
            async function pollCartCount() {
                try {
                    const res = await fetch('/api/internal/cart-count', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (data.count > 0) {
                            cartBadge.textContent = data.count;
                            cartBadge.classList.remove('hidden');
                        } else {
                            cartBadge.classList.add('hidden');
                        }
                    }
                } catch (err) {
                    console.error('Cart polling error:', err);
                }
            }

            pollCartCount();
            setInterval(pollCartCount, 10000);
        });
    </script>
</body>
</html>
