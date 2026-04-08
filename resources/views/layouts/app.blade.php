<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <meta property="og:image" content="@yield('og_image', asset('wadahngopi.png'))">
    <meta property="og:site_name" content="WadahNgopi">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'WadahNgopi - Portal Cafe Kalimantan')">
    <meta name="twitter:description"
        content="@yield('twitter_description', 'Temukan cafe dan tempat nongkrong terbaik di Kalimantan.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('wadahngopi.png'))">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="manifest" href="/manifest.json?v=2">
    <link rel="icon" type="image/png" href="{{ asset('wadahngopi.png') }}">
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

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZMD82PEJKP"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-ZMD82PEJKP');
    </script>

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

    {{-- Splash Screen — First Load Only (per session) --}}
    <div id="splash-screen" class="splash-screen"
        style="display:none; position:fixed; inset:0; z-index:99999; background:#1A0F0A; display:flex; align-items:center; justify-content:center;">
        <div class="splash-logo" style="display:flex; flex-direction:column; align-items:center;">
            <div class="splash-icon"
                style="width:56px; height:56px; border-radius:16px; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#F59E0B;">
                <img src="{{ asset('wadahngopi.png') }}" alt="WadahNgopi"
                    style="width:36px; height:36px; object-fit:contain;">
            </div>
            <div class="splash-brand" style="margin-top:16px;">
                <div class="splash-brand-name">Wadah<span>Ngopi</span></div>
                <div class="splash-brand-tagline">Jelajahi Kopi Favoritmu</div>
            </div>
        </div>
        <div class="splash-loader">
            <div class="splash-loader-bar"></div>
        </div>
    </div>
    <script>
        (function () {
            var s = document.getElementById('splash-screen');
            if (!sessionStorage.getItem('wadah-splash')) {
                s.style.display = '';
                sessionStorage.setItem('wadah-splash', '1');
                setTimeout(function () {
                    s.classList.add('splash-hide');
                    setTimeout(function () { s.remove(); }, 500);
                }, 1400);
            } else {
                s.remove();
            }
        })();
    </script>

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

    {{-- Onboarding Flow (first visit only) --}}
    <x-onboarding />

    {{-- Pull-to-Refresh Coffee Drip Indicator --}}
    <div class="ptr-container" id="ptr-indicator-wrap">
        <div class="ptr-indicator" id="ptr-indicator">
            {{-- Steam particles --}}
            <span class="ptr-steam"></span>
            <span class="ptr-steam"></span>
            <span class="ptr-steam"></span>

            {{-- Drip droplet --}}
            <div class="ptr-drip"></div>

            {{-- Coffee Cup SVG --}}
            <svg class="ptr-cup" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Cup body --}}
                <path
                    d="M12 18 C12 16, 14 14, 16 14 L44 14 C46 14, 48 16, 48 18 L46 48 C46 52, 42 54, 38 54 L22 54 C18 54, 14 52, 14 48 Z"
                    fill="#3E2723" stroke="#2C1810" stroke-width="1.5" />
                {{-- Cup handle --}}
                <path d="M48 24 C54 24, 58 30, 58 36 C58 42, 54 46, 48 44" stroke="#3E2723" stroke-width="3" fill="none"
                    stroke-linecap="round" />
                {{-- Cup rim highlight --}}
                <rect x="14" y="14" width="32" height="3" rx="1.5" fill="#5D4037" opacity="0.5" />
                {{-- Coffee fill (controlled by JS via clipPath) --}}
                <defs>
                    <clipPath id="coffee-clip">
                        <rect id="coffee-level" x="14" y="54" width="32" height="0" />
                    </clipPath>
                </defs>
                <path d="M14 18 L12.5 48 C13 52, 17 54, 22 54 L38 54 C42 54, 45 52, 46 48 L48 18 Z" fill="#6F4E37"
                    clip-path="url(#coffee-clip)" />
                {{-- Coffee surface shine --}}
                <ellipse cx="30" cy="20" rx="12" ry="2" fill="#8D6E63" opacity="0.2" />
            </svg>

            {{-- Text --}}
            <span class="ptr-text" id="ptr-text">Tarik untuk refresh</span>
        </div>
    </div>

    <div class="main-container" id="main-container">
        @yield('content')

        <nav class="bottom-nav" role="navigation" aria-label="Menu utama">
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}"
                wire:navigate aria-label="Beranda">
                <i class="{{ request()->routeIs('home') ? 'ph-fill ph-house' : 'ph ph-house' }}" aria-hidden="true"></i>
                <span>BERANDA</span>
            </a>
            <a href="{{ route('roastery') }}" class="nav-item {{ request()->routeIs('roastery') ? 'active' : '' }}"
                wire:navigate aria-label="Roastery">
                <i class="{{ request()->routeIs('roastery') ? 'ph-fill ph-coffee-bean' : 'ph ph-coffee-bean' }}"
                    aria-hidden="true"></i>
                <span>ROASTERY</span>
            </a>
            <a href="{{ route('explore') }}" class="nav-item {{ request()->routeIs('explore') ? 'active' : '' }}"
                wire:navigate aria-label="Jelajahi Cafe">
                <i class="{{ request()->routeIs('explore') ? 'ph-fill ph-compass' : 'ph ph-compass' }}"
                    aria-hidden="true"></i>
                <span>JELAJAHI</span>
            </a>
            <a href="{{ route('saved') }}" class="nav-item {{ request()->routeIs('saved') ? 'active' : '' }}"
                wire:navigate aria-label="Simpan Favorit">
                <i class="{{ request()->routeIs('saved') ? 'ph-fill ph-bookmark-simple' : 'ph ph-bookmark-simple' }}"
                    aria-hidden="true"></i>
                <span>SIMPAN</span>
            </a>
            <a href="{{ route('information') }}"
                class="nav-item {{ request()->routeIs('information*') ? 'active' : '' }}" wire:navigate
                aria-label="Info & Berita">
                <i class="{{ request()->routeIs('information*') ? 'ph-fill ph-newspaper' : 'ph ph-newspaper' }}"
                    aria-hidden="true"></i>
                <span>INFO</span>
            </a>
        </nav>
    </div>

    <script>
        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
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
                    if (status === 500 || status === 0) {
                        preventDefault();
                        // Show friendly toast instead of crashing
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { message: 'Terjadi kesalahan, coba lagi ya ☕', type: 'error' }
                        }));
                    }
                })
            })
        });
    </script>

    {{-- Pull-to-Refresh Coffee Drip Logic --}}
    <script>
        (function () {
            const MAX_PULL = 120;
            const TRIGGER_THRESHOLD = 80;
            let startY = 0;
            let pulling = false;
            let refreshing = false;

            const indicator = document.getElementById('ptr-indicator');
            const coffeeLevel = document.getElementById('coffee-level');
            const ptrText = document.getElementById('ptr-text');
            const mainContainer = document.getElementById('main-container');

            if (!indicator || !coffeeLevel || !mainContainer) return;

            // Only listen on the main container
            mainContainer.addEventListener('touchstart', function (e) {
                if (refreshing) return;
                if (window.scrollY > 5) return; // Not at top
                startY = e.touches[0].clientY;
                pulling = true;
            }, { passive: true });

            mainContainer.addEventListener('touchmove', function (e) {
                if (!pulling || refreshing) return;
                const currentY = e.touches[0].clientY;
                const delta = Math.max(0, currentY - startY);

                if (delta <= 0) return;

                const progress = Math.min(delta / MAX_PULL, 1);
                const translateY = -70 + (progress * 86); // -70 → 16

                indicator.style.transform = 'translateY(' + translateY + 'px) scale(' + (0.6 + progress * 0.4) + ')';
                indicator.classList.toggle('visible', progress > 0.05);

                // Fill coffee cup via clipPath
                const fillHeight = progress * 36; // Max 36px fill
                const fillY = 54 - fillHeight;
                coffeeLevel.setAttribute('y', fillY);
                coffeeLevel.setAttribute('height', fillHeight);

                // Update text
                if (progress >= 1) {
                    ptrText.textContent = 'Lepaskan ☕';
                } else {
                    ptrText.textContent = 'Tarik untuk refresh';
                }
            }, { passive: true });

            mainContainer.addEventListener('touchend', function () {
                if (!pulling || refreshing) return;
                pulling = false;

                const currentFill = parseFloat(coffeeLevel.getAttribute('height') || 0);
                const progress = currentFill / 36;

                if (progress >= (TRIGGER_THRESHOLD / MAX_PULL)) {
                    // Trigger refresh!
                    refreshing = true;
                    indicator.classList.add('refreshing');
                    ptrText.textContent = 'Menyeduh...';

                    // Keep cup full
                    coffeeLevel.setAttribute('y', '18');
                    coffeeLevel.setAttribute('height', '36');

                    setTimeout(function () {
                        window.location.reload();
                    }, 1200);
                } else {
                    // Snap back
                    indicator.classList.remove('visible');
                    indicator.style.transform = 'translateY(-70px) scale(0.6)';
                    coffeeLevel.setAttribute('y', '54');
                    coffeeLevel.setAttribute('height', '0');
                }
            }, { passive: true });

            // Re-init after Livewire navigation
            document.addEventListener('livewire:navigated', function () {
                refreshing = false;
                pulling = false;
                indicator.classList.remove('visible', 'refreshing');
                indicator.style.transform = 'translateY(-70px) scale(0.6)';
                coffeeLevel.setAttribute('y', '54');
                coffeeLevel.setAttribute('height', '0');
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>