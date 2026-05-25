<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Auth' }} - TokoKu</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind Stylesheet -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 min-h-screen flex items-center justify-center p-4 selection:bg-indigo-500 selection:text-white">
    <div class="w-full max-w-md bg-white/10 backdrop-blur-xl border border-white/15 rounded-3xl p-8 shadow-2xl transition-all duration-300 transform scale-100 hover:scale-[1.01] hover:border-white/20">
        <!-- Logo Block -->
        <div class="text-center mb-8">
            <span class="text-4xl inline-block animate-pulse">🛒</span>
            <div class="flex items-center justify-center gap-3">
                <img src="{{ asset('favicon.svg') }}" alt="Logo" class="w-10 h-10">
                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-200 via-indigo-100 to-white mt-2 tracking-tight">TokoKu</h1>
            </div>
            <p class="text-xs text-indigo-300 mt-1 uppercase tracking-widest font-semibold">Premium Online Store</p>
        </div>

        <!-- Session Status / Errors -->
        @if (session('success'))
            <div class="mb-4 p-4 text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-2 animate-alert-pop">
                <span>✅</span>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 text-xs text-rose-800 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-2 animate-alert-pop">
                <span>⚠️</span>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- Content Injection -->
        @yield('content')
    </div>
</body>
</html>
