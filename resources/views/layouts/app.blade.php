<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script>document.documentElement.classList.add('js-active')</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#6F4E37">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'WadahNgopi')</title>
    
    {{-- Original SEO & Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

    {{-- Splash Screen Manual --}}
    <div id="splash-screen" class="splash-screen"
        style="position:fixed; inset:0; z-index:99999; background:#1A0F0A; display:flex; align-items:center; justify-content:center;">
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
            const hideSplash = () => {
                s.style.opacity = '0';
                setTimeout(() => s.remove(), 500);
            };
            window.addEventListener('load', hideSplash);
            setTimeout(hideSplash, 1200);
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

        {{-- BALIKIN KE POSISI ASLI --}}
        <nav class="bottom-nav">
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}" wire:navigate>
                <i class="{{ request()->routeIs('home') ? 'ph-fill ph-house' : 'ph ph-house' }}"></i>
                <span class="nav-label">BERANDA</span>
            </a>
            <a href="{{ route('roastery') }}" class="nav-item {{ request()->routeIs('roastery') ? 'active' : '' }}" wire:navigate>
                <i class="{{ request()->routeIs('roastery') ? 'ph-fill ph-coffee-bean' : 'ph ph-coffee-bean' }}"></i>
                <span class="nav-label">ROASTERY</span>
            </a>
            <a href="{{ route('explore') }}" class="nav-item {{ request()->routeIs('explore') ? 'active' : '' }}" wire:navigate>
                <i class="{{ request()->routeIs('explore') ? 'ph-fill ph-compass' : 'ph ph-compass' }}"></i>
                <span class="nav-label">JELAJAHI</span>
            </a>
            <a href="{{ route('saved') }}" class="nav-item {{ request()->routeIs('saved') ? 'active' : '' }}" wire:navigate>
                <i class="{{ request()->routeIs('saved') ? 'ph-fill ph-bookmark-simple' : 'ph ph-bookmark-simple' }}"></i>
                <span class="nav-label">SIMPAN</span>
            </a>
            <a href="{{ route('information') }}" class="nav-item {{ request()->routeIs('information*') ? 'active' : '' }}" wire:navigate>
                <i class="{{ request()->routeIs('information*') ? 'ph-fill ph-newspaper' : 'ph ph-newspaper' }}"></i>
                <span class="nav-label">INFO</span>
            </a>
        </nav>
    </div>

    @stack('scripts')
</body>
</html>