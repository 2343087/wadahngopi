<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#6F4E37">
    <link rel="manifest" href="/manifest.json?v=2">
    <link rel="icon" type="image/png" href="{{ asset('wadahicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('wadahicon.png') }}">

    <title>@yield('title', 'WadahNgopi')</title>

    <!-- Fonts - Modern & Premium -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons - Phosphor & Bootstrap (Original Set) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="main-container">
        @yield('content')

        <nav class="bottom-nav">
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="{{ request()->routeIs('home') ? 'ph-fill ph-house' : 'ph ph-house' }}"></i>
                <span>BERANDA</span>
            </a>
            <a href="{{ route('explore') }}" class="nav-item {{ request()->routeIs('explore') ? 'active' : '' }}">
                <i class="{{ request()->routeIs('explore') ? 'ph-fill ph-compass' : 'ph ph-compass' }}"></i>
                <span>JELAJAHI</span>
            </a>
            <a href="{{ route('saved') }}" class="nav-item {{ request()->routeIs('saved') ? 'active' : '' }}">
                <i
                    class="{{ request()->routeIs('saved') ? 'ph-fill ph-bookmark-simple' : 'ph ph-bookmark-simple' }}"></i>
                <span>SIMPAN</span>
            </a>
            <a href="{{ route('information') }}"
                class="nav-item {{ request()->routeIs('information*') ? 'active' : '' }}">
                <i class="{{ request()->routeIs('information*') ? 'ph-fill ph-newspaper' : 'ph ph-newspaper' }}"></i>
                <span>INFO</span>
            </a>
        </nav>
    </div>

    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(registrations => {
                for (let registration of registrations) {
                    registration.unregister();
                }
            });
        }
    </script>
    @stack('scripts')
</body>

</html>