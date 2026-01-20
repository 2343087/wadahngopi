<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#6F4E37">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">

    <title>@yield('title', 'WadahNgopi')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/0.160.0/three.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-coffee: #6F4E37;
            --color-coffee-dark: #3E2723;
            --color-coffee-light: #A67B5B;
            --color-cream: #FDFBF9;
            --color-cream-dark: #F5EBE0;
            --color-earth-green: #4A5D23;
            --color-text: #2C1810;
            --color-text-muted: #8D776D;
            --shadow-sm: 0 4px 12px rgba(111, 78, 55, 0.04);
            --shadow-md: 0 10px 30px rgba(111, 78, 55, 0.08);
            --shadow-lg: 0 20px 40px rgba(62, 39, 35, 0.12);
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(111, 78, 55, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f7f3f0;
            color: var(--color-text);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .main-container {
            width: 100%;
            max-width: 480px;
            /* Slightly tighter for better app feel */
            margin: 0 auto;
            min-height: 100vh;
            background: var(--color-cream);
            position: relative;
            padding-bottom: 90px;
            box-shadow: 0 0 60px rgba(62, 39, 35, 0.05);
        }

        /* Fluid on mobile, fixed on tablet+ */
        @media (max-width: 480px) {
            .main-container {
                box-shadow: none;
            }
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 40px);
            max-width: 440px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            display: flex;
            justify-content: space-around;
            padding: 12px 10px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(62, 39, 35, 0.12);
            z-index: 1000;
            border: 1px solid rgba(111, 78, 55, 0.05);
        }

        .nav-item {
            text-decoration: none;
            color: var(--color-text-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            font-size: 0.65rem;
            font-weight: 700;
            transition: var(--transition-smooth);
            width: 60px;
        }

        .nav-item i {
            font-size: 1.25rem;
            transition: var(--transition-smooth);
        }

        .nav-item.active {
            color: var(--color-coffee);
        }

        .nav-item.active i {
            transform: translateY(-2px);
            color: var(--color-coffee);
        }

        .nav-item:active {
            transform: scale(0.9);
        }

        /* Utility Classes */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--shadow-sm);
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        /* Global UI Elements */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            transition: var(--transition-smooth);
            cursor: pointer;
            border: none;
            gap: 8px;
        }

        .btn-primary {
            background: var(--color-coffee);
            color: white;
            box-shadow: 0 8px 16px rgba(111, 78, 55, 0.2);
        }

        .btn-primary:active {
            transform: scale(0.98);
            box-shadow: 0 4px 8px rgba(111, 78, 55, 0.2);
        }

        .card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }
    </style>
</head>

<body>
    <div class="main-container">
        @yield('content')

        <nav class="bottom-nav">
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-house-door{{ request()->routeIs('home') ? '-fill' : '' }}"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('explore') }}" class="nav-item {{ request()->routeIs('explore') ? 'active' : '' }}">
                <i class="bi bi-search{{ request()->routeIs('explore') ? '-fill' : '' }}"></i>
                <span>Explore</span>
            </a>
            <a href="{{ route('saved') }}" class="nav-item {{ request()->routeIs('saved') ? 'active' : '' }}">
                <i class="bi bi-bookmark{{ request()->routeIs('saved') ? '-fill' : '' }}"></i>
                <span>Simpan</span>
            </a>
            <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
                <i class="bi bi-person{{ request()->routeIs('profile') ? '-fill' : '' }}"></i>
                <span>Profil</span>
            </a>
        </nav>
    </div>

    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
</body>

</html>