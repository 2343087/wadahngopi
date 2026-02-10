<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#6F4E37">
    <meta name="robots" content="index, follow">
    <meta name="author" content="AK Kreatif">

    {{-- SEO Meta Tags --}}
    <title>@yield('title', 'WadahNgopi - Portal Cafe Terbaik di Kalimantan')</title>
    <meta name="description"
        content="@yield('meta_description', 'WadahNgopi adalah portal pencarian cafe dan tempat nongkrong terbaik di Kalimantan. Temukan spot ngopi nyaman dengan fasilitas lengkap, WiFi, dan suasana aesthetic.')">
    <meta name="keywords"
        content="@yield('meta_keywords', 'cafe kalimantan, tempat nongkrong, kedai kopi, wadah ngopi, coffee shop, wifi cafe, aesthetic cafe')">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', 'WadahNgopi - Portal Cafe Terbaik di Kalimantan')">
    <meta property="og:description"
        content="@yield('og_description', 'Temukan cafe dan tempat nongkrong terbaik di Kalimantan. WiFi kencang, suasana nyaman, menu lengkap.')">
    <meta property="og:image" content="@yield('og_image', asset('wadahicon.png'))">
    <meta property="og:site_name" content="WadahNgopi">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'WadahNgopi - Portal Cafe Kalimantan')">
    <meta name="twitter:description"
        content="@yield('twitter_description', 'Temukan cafe dan tempat nongkrong terbaik di Kalimantan.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('wadahicon.png'))">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="manifest" href="/manifest.json?v=2">
    <link rel="icon" type="image/png" href="{{ asset('wadahicon.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}">

    <!-- Fonts - Modern & Premium -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons - Phosphor & Bootstrap (Original Set) -->
    <script src="https://unpkg.com/@phosphor-icons/web" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">



    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body x-data="{ 
    toast: { show: false, message: '', type: 'success' },
    showToast(message, type = 'success') {
        this.toast.show = true;
        this.toast.message = message;
        this.toast.type = type;
        setTimeout(() => { this.toast.show = false; }, 3000);
    }
}" @toast.window="showToast($event.detail.message, $event.detail.type)">

    {{-- Global Toast Notification --}}
    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed top-8 left-1/2 -translate-x-1/2 z-[9999] pointer-events-none" x-cloak>
        <div class="px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 backdrop-blur-xl border border-white/20"
            :class="toast.type === 'success' ? 'bg-espresso/90 text-white' : 'bg-rose-500/90 text-white'">
            <i class="ph-bold" :class="toast.type === 'success' ? 'ph-check-circle' : 'ph-warning-circle'"></i>
            <span class="text-sm font-bold" x-text="toast.message"></span>
        </div>
    </div>

    <div class="main-container">
        @yield('content')

        <nav class="bottom-nav">
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="{{ request()->routeIs('home') ? 'ph-fill ph-house' : 'ph ph-house' }}"></i>
                <span>BERANDA</span>
            </a>
            <a href="{{ route('roastery') }}" class="nav-item {{ request()->routeIs('roastery') ? 'active' : '' }}">
                <i class="{{ request()->routeIs('roastery') ? 'ph-fill ph-coffee-bean' : 'ph ph-coffee-bean' }}"></i>
                <span>ROASTERY</span>
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

        // Livewire Page Expired Auto-Handler (No Popup)
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    if (status === 419) {
                        preventDefault();
                        window.location.reload();
                    }
                })
            })
        });
    </script>
    @stack('scripts')
</body>

</html>