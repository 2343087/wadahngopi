<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script>document.documentElement.classList.add('js-active')</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#6F4E37">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'WadahNgopi')</title>
    
    <link rel="manifest" href="/manifest.json">

    {{-- Original SEO & Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/app.jsx'])
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

    {{-- Splash Screen Manual --}}
    <div id="splash-screen" class="splash-screen"
        style="position:fixed; inset:0; z-index:99999; background:#1A0F0A; display:flex; align-items:center; justify-content:center;">
        <script>
            // Anti-Flicker: Langsung sembunyikan kalau sudah pernah tampil di sesi ini
            if (sessionStorage.getItem('splash_shown')) {
                document.getElementById('splash-screen').style.display = 'none';
            }
        </script>
        <div class="splash-logo" style="display:flex; flex-direction:column; align-items:center;">
            <div class="splash-icon"
                style="width:56px; height:56px; border-radius:16px; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#F59E0B;">
                <img src="{{ asset('wadahngopi.png') }}" alt="WadahNgopi" style="width:36px; height:36px; object-fit:contain;">
            </div>
            <div class="splash-brand" style="margin-top:16px;">
                <div class="splash-brand-name" style="color:white; font-weight:900; font-size:24px;">Wadah<span style="color:#F59E0B;">Ngopi</span></div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const s = document.getElementById('splash-screen');
            if (!s) return;

            // Jika sudah pernah tampil, hapus dari DOM secara instan
            if (sessionStorage.getItem('splash_shown')) {
                s.remove();
                return;
            }

            const hideSplash = () => {
                s.style.transition = 'opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                s.style.opacity = '0';
                setTimeout(() => {
                    s.remove();
                    // Tandai bahwa splash sudah tampil di sesi ini
                    sessionStorage.setItem('splash_shown', 'true');
                }, 600);
            };

            // Tunggu load atau fallback setelah 1.5 detik
            if (document.readyState === 'complete') {
                hideSplash();
            } else {
                window.addEventListener('load', hideSplash);
                setTimeout(hideSplash, 1500); 
            }
        })();
    </script>

    {{-- Global Toast --}}
    <div x-show="toast.show" x-cloak class="fixed top-8 left-1/2 -translate-x-1/2 z-[9999] pointer-events-none">
        <div class="px-6 py-3 rounded-2xl shadow-2xl flex items-center gap-3 backdrop-blur-xl border border-white/20 bg-espresso/90 text-white">
            <span class="text-sm font-bold" x-text="toast.message"></span>
        </div>
    </div>

    <x-onboarding />

    <div class="main-container" id="main-container">
        @yield('content')

        {{-- REACT BOTTOM NAVIGATION (HYBRID) --}}
        <div id="bottom-nav-react" 
             data-current-route="{{ optional(request()->route())->getName() ?? '' }}"
             data-is-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
             data-logout-url="{{ route('logout') }}"
             data-csrf-token="{{ csrf_token() }}"
             data-routes="{{ json_encode([
                 'home' => route('home'),
                 'roastery' => route('roastery'),
                 'explore' => route('explore'),
                 'saved' => route('saved'),
                 'information' => route('information'),
             ]) }}">
        </div>

        {{-- OLD NAV (LEGACY BACKUP) --}}
        {{-- 
        <nav class="bottom-nav">
            ...
        </nav>
        --}}
    </div>

    @stack('scripts')
</body>
</html>