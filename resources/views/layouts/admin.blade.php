<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TokoKu</title>
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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex selection:bg-indigo-500 selection:text-white">
    <!-- Sidebar Left Fixed -->
    <aside class="w-64 bg-slate-900 text-slate-300 fixed inset-y-0 left-0 flex flex-col z-20 shadow-xl border-r border-slate-800">
        <!-- Sidebar Brand Header -->
        <div class="h-16 flex items-center gap-3 px-6 bg-slate-950/40 border-b border-slate-800/80">
            <span class="text-2xl animate-spin [animation-duration:8s]">⚙️</span>
            <div>
                <img src="{{ asset('favicon.svg') }}" alt="Logo" class="w-8 h-8 brightness-0 invert">
                <span class="font-extrabold text-white tracking-tight text-lg">TokoKu Admin</span>
                <span class="block text-[9px] font-bold text-indigo-400 uppercase tracking-widest leading-none mt-0.5">Control Center</span>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto scrollbar-thin">
            <!-- Dashboard Link -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-650/30' : 'hover:bg-slate-800 hover:text-white' }}">
                <span>📊</span> Dashboard
            </a>

            <!-- Kategori CRUD -->
            <a href="{{ route('admin.categories.index') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-650/30' : 'hover:bg-slate-800 hover:text-white' }}">
                <span>🏷️</span> Kategori Produk
            </a>

            <!-- Produk CRUD -->
            <a href="{{ route('admin.products.index') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ request()->routeIs('admin.products.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-650/30' : 'hover:bg-slate-800 hover:text-white' }}">
                <span>📦</span> Produk Barang
            </a>

            <!-- FIFO Stock Batch -->
            <a href="{{ route('admin.stock.index') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ request()->routeIs('admin.stock.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-650/30' : 'hover:bg-slate-800 hover:text-white' }}">
                <span>📉</span> Manajemen Stok FIFO
            </a>

            <!-- User Accounts CRUD -->
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-650/30' : 'hover:bg-slate-800 hover:text-white' }}">
                <span>👥</span> Manajemen Pengguna
            </a>

            <!-- Reports Excel/PDF -->
            <a href="{{ route('admin.reports.index') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-650/30' : 'hover:bg-slate-800 hover:text-white' }}">
                <span>📄</span> Laporan Transaksi
            </a>

            <!-- Activity System Logs -->
            <a href="{{ route('admin.logs') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ request()->routeIs('admin.logs') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-650/30' : 'hover:bg-slate-800 hover:text-white' }}">
                <span>📜</span> Log Aktivitas
            </a>

            <!-- Chatbot Prompts -->
            <a href="{{ route('admin.chatbot-prompts.index') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ request()->routeIs('admin.chatbot-prompts.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-650/30' : 'hover:bg-slate-800 hover:text-white' }}">
                <span>🤖</span> Chatbot Prompts
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/20 text-center">
            <span class="text-[10px] text-slate-500">Administrator Panel</span>
        </div>
    </aside>

    <!-- Right Side Content Scrollable -->
    <div class="flex-grow pl-64 flex flex-col min-h-screen">
        <!-- Top Header -->
        <header class="h-16 bg-white border-b border-slate-100 px-8 flex items-center justify-between sticky top-0 z-10 shadow-sm">
            <h2 class="font-extrabold text-slate-800 text-sm uppercase tracking-wider">
                @yield('header_title', 'Admin Panel')
            </h2>

            <div class="flex items-center gap-4">
                <!-- User Profile badge -->
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center font-bold text-sm text-white shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="text-left leading-none hidden sm:block">
                        <span class="font-bold text-xs text-slate-800 block">{{ Auth::user()->name }}</span>
                        <span class="text-[9px] font-semibold text-slate-400 block uppercase tracking-wider">Super Administrator</span>
                    </div>
                </div>

                <div class="h-6 border-l border-slate-200"></div>

                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl font-bold text-xs transition-colors focus:outline-none">
                        <span>🚪</span> Keluar
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-grow p-8">
            <!-- Flash alert banners -->
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

            @yield('content')
        </main>
    </div>

    <!-- AJAX Dashboard statistics polling (Every 10 seconds) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to fetch and update stats dynamically
            async function pollStats() {
                try {
                    const res = await fetch("{{ route('api.internal.dashboard_stats') }}", {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        
                        // Dynamically update DOM elements if they exist
                        const salesElem = document.getElementById('total-penjualan');
                        const lowStockElem = document.getElementById('stok-kritis');
                        const usersElem = document.getElementById('user-aktif');

                        if (salesElem) salesElem.textContent = data.total_penjualan;
                        if (lowStockElem) lowStockElem.textContent = data.stok_kritis;
                        if (usersElem) usersElem.textContent = data.user_aktif;
                    }
                } catch (err) {
                    console.error('AJAX Polling stats failed:', err);
                }
            }

            // Perform initial load poll
            pollStats();

            // Run poller every 10 seconds (10000ms)
            setInterval(pollStats, 10000);
        });
    </script>
</body>
</html>
