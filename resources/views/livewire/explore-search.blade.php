{{-- Livewire Explore Search Component --}}
{{-- Premium Redesign 2026 - Ultra Modern & Responsive --}}
<div x-data="exploreLogic()" x-init="initComponent()" class="block min-h-screen">
    {{-- Livewire data is handled in Alpine x-data script --}}

    @if(config('app.debug'))
        <div class="hidden" id="cafe-debug-count">{{ count($cafes) }}</div>
    @endif

    {{-- Ultra Premium Hero Section --}}
    {{-- Ultra Premium Hero Section --}}
    <header class="explore-hero-2026" :class="{ 'is-compact': isScrolled }"
        @scroll.window="isScrolled = window.pageYOffset > 50">
        {{-- Top Bar Section --}}
        <div class="explore-branding-wrapper">
            <div class="explore-topbar">
                <div class="explore-logo-box">
                    <img src="{{ asset('wadahicon.png') }}" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <h1 class="explore-brand">Wadah<span>Ngopi</span></h1>
                    <p class="explore-tagline">JELAJAHI KOPI FAVORITMU</p>
                </div>
            </div>
        </div>

        {{-- Premium Search Box --}}
        <div class="explore-search-2026" @click.away="showSortMenu = false">
            <div class="search-icon-pulse">
                <i class="ph-bold ph-magnifying-glass"></i>
            </div>
            <input type="text" x-model="search" placeholder="Cari cafe favoritmu..." @keyup.escape="search = ''"
                class="explore-search-input">
            <button class="explore-sort-btn" @click="showSortMenu = !showSortMenu"
                :class="showSortMenu ? 'active' : ''">
                <i class="ph-bold ph-sliders-horizontal"></i>
            </button>

            {{-- Sort Dropdown Premium --}}
            <div class="explore-sort-dropdown" x-show="showSortMenu"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak>
                <div class="sort-dropdown-header">
                    <i class="ph-fill ph-funnel text-amber"></i>
                    <span>Urutkan</span>
                </div>
                <div class="sort-dropdown-body">
                    <button class="sort-dropdown-item"
                        :class="activeSort === 'name_az' && !activeLetter ? 'active' : ''"
                        @click="if (activeSort === 'name_az' && !activeLetter) { activeSort = 'relevance'; } else { activeSort = 'name_az'; activeLetter = null; } showSortMenu = false">
                        <i class="ph-fill ph-sort-ascending"></i>
                        <span> (A-Z)</span>
                    </button>
                    <button class="sort-dropdown-item" :class="activeSort === 'name_za' ? 'active' : ''"
                        @click="if (activeSort === 'name_za') { activeSort = 'relevance'; } else { activeSort = 'name_za'; activeLetter = null; } showSortMenu = false">
                        <i class="ph-fill ph-sort-descending"></i>
                        <span> (Z-A)</span>
                    </button>
                </div>
                <div class="sort-dropdown-alphabet">
                    @foreach (range('A', 'Z') as $char)
                        <button class="sort-dropdown-item" :class="activeLetter === '{{ $char }}' ? 'active' : ''"
                            @click="if (activeLetter === '{{ $char }}') { activeLetter = null; activeSort = 'relevance'; } else { activeLetter = '{{ $char }}'; activeSort = 'name_az'; } showSortMenu = false">
                            <i class="ph-fill ph-text-aa"></i>
                            <span> {{ $char }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Category Filter Pills --}}
        <div class="explore-category-pills">
            <button class="category-pill" :class="activeFilter === 'semua' ? 'active' : ''"
                @click="activeFilter = 'semua'">
                <i class="ph-fill ph-coffee"></i>
                <span>Semua</span>
            </button>
            <button class="category-pill" :class="activeFilter === 'terdekat' ? 'active' : ''"
                @click="activeFilter = 'terdekat'; getLocation()">
                <i class="ph-fill ph-map-pin" x-show="!isLocating"></i>
                <i class="ph ph-circle-notch animate-spin" x-show="isLocating"></i>
                <span>Terdekat</span>
            </button>
            <button class="category-pill pill-open" :class="activeFilter === 'buka' ? 'active' : ''"
                @click="activeFilter = 'buka'">
                <span class="pulse-dot"></span>
                <span>Buka Sekarang</span>
            </button>
        </div>

        {{-- City Filter Pills --}}
        <div class="explore-city-pills">
            <button wire:click="$set('cityId', '')"
                class="city-pill {{ $cityId === '' || $cityId === null ? 'active' : '' }}">
                Semua Kota
            </button>
            @foreach($cities as $city)
                <button wire:click="$set('cityId', '{{ $city['id'] }}')"
                    class="city-pill {{ $cityId == $city['id'] ? 'active' : '' }}">
                    {{ $city['name'] }}
                </button>
            @endforeach
        </div>

        {{-- Results Counter --}}
        <div class="explore-result-counter-wrapper">
            <div class="explore-result-counter" x-show="filteredCafes().length > 0">
                <span class="counter-number" x-text="filteredCafes().length"></span>
                <span class="counter-text">cafe ditemukan</span>
            </div>
            <div wire:loading.delay.shorter class="explore-loading-state">
                <i class="ph ph-circle-notch animate-spin"></i>
                <span>Mencari...</span>
            </div>
        </div>
    </header>

    {{-- Content Spacer for Fixed Header (Static height to prevent jumping) --}}
    <div class="header-spacer-2026"></div>

    {{-- Skeleton Grid (Shown while loading) --}}
    <div class="explore-cafe-grid" wire:loading.delay.shorter>
        <template x-for="i in 4" :key="i">
            <div class="cafe-card-2026 skeleton-card">
                <div class="skeleton-image"></div>
                <div class="p-5 flex flex-col gap-3">
                    <div class="skeleton-title"></div>
                    <div class="skeleton-text w-3/4"></div>
                    <div class="flex gap-2">
                        <div class="skeleton-pill"></div>
                        <div class="skeleton-pill"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Cafe Grid Premium --}}
    <main class="explore-cafe-grid" wire:loading.remove.delay.shorter x-show="filteredCafes().length > 0">
        <template x-for="(cafe, idx) in filteredCafes()" :key="cafe.id || idx">
            <a :href="cafe.url" class="cafe-card-2026 group">
                {{-- Card Image --}}
                <div class="cafe-card-image">
                    <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                        :alt="cafe.name" loading="lazy" class="cafe-card-img">

                    {{-- Gradient Overlay --}}
                    <div class="cafe-card-overlay"></div>

                    {{-- Status Badge --}}
                    <div class="cafe-status-badge" :class="cafe.isOpen ? 'open' : 'closed'">
                        <span class="status-dot"></span>
                        <span x-text="cafe.isOpen ? 'Buka' : 'Tutup'"></span>
                    </div>

                    {{-- Distance Badge --}}
                    <template x-if="userLat && cafe.distance">
                        <div class="cafe-distance-badge">
                            <i class="ph-fill ph-navigation-arrow"></i>
                            <span x-text="formatDistance(cafe.distance)"></span>
                        </div>
                    </template>

                    {{-- Hover Quick Actions --}}
                    <div class="cafe-card-actions">
                        <template x-for="(social, sIdx) in (cafe.socialLinks || []).slice(0, 4)" :key="sIdx">
                            <button @click.prevent.stop="window.open(social.url, '_blank')" class="quick-action-btn">
                                <i :class="{
                                    'ph-bold ph-instagram-logo': social.platform === 'instagram',
                                    'ph-bold ph-tiktok-logo': social.platform === 'tiktok',
                                    'ph-bold ph-facebook-logo': social.platform === 'facebook',
                                    'ph-bold ph-x-logo': social.platform === 'twitter'
                                }"></i>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Card Content --}}
                <div class="cafe-card-content">
                    <h3 class="cafe-card-title" x-text="cafe.name"></h3>
                    <p class="cafe-card-address">
                        <i class="ph-fill ph-map-pin"></i>
                        <span x-text="cafe.city"></span>
                    </p>

                    {{-- Facilities Tags --}}
                    <div class="cafe-card-tags" x-show="(cafe.facilities || []).length > 0">
                        <template x-for="(tag, tIdx) in (cafe.facilities || []).slice(0, 3)" :key="tIdx">
                            <span class="cafe-tag" x-text="tag"></span>
                        </template>
                        <span class="cafe-tag-more" x-show="(cafe.facilities || []).length > 3"
                            x-text="'+' + ((cafe.facilities || []).length - 3)"></span>
                    </div>
                </div>

                {{-- Hover Shine Effect --}}
                <div class="card-shine"></div>
            </a>
        </template>
    </main>

    {{-- Premium Empty State --}}
    <div wire:loading.remove.delay.shorter x-show="filteredCafes().length === 0" x-cloak class="explore-empty-state">
        <div class="empty-state-icon">
            <i class="ph-light ph-coffee"></i>
        </div>
        <h3 class="empty-state-title">Belum Ada Cafe</h3>
        <p class="empty-state-text">Coba ubah filter atau kata kunci pencarian</p>
        <button class="empty-state-btn"
            @click="search = ''; activeFilter = 'semua'; activeLetter = null; activeSort = 'relevance'">
            <i class="ph-bold ph-arrow-counter-clockwise"></i>
            Reset Filter
        </button>
    </div>

    {{-- Premium Explore Styles --}}
    <style>
        /* === EXPLORE PAGE 2026 ULTRA PREMIUM === */

        .explore-hero-2026 {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            max-width: 480px;
            z-index: 1000;
            padding: 40px 24px 14px;
            background: #FFFDFB;
            border-radius: 0 0 32px 32px;
            overflow: visible;
            box-shadow: 0 4px 20px rgba(26, 15, 10, 0.05);
            transition: padding 0.4s ease, background 0.4s ease, box-shadow 0.4s ease;
            will-change: padding, background, box-shadow;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .header-spacer-2026 {
            height: 380px; /* Safe static height to prevent content overlap */
            width: 100%;
        }

        .explore-hero-2026.is-compact {
            padding-top: 35px;
            padding-bottom: 12px;
            gap: 4px;
            background: rgba(255, 253, 251, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(111, 78, 55, 0.1);
            box-shadow: 0 10px 40px rgba(26, 15, 10, 0.12);
        }

        .explore-branding-wrapper {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: left top;
            will-change: transform, opacity;
        }

        .explore-hero-2026.is-compact .explore-branding-wrapper {
            transform: scale(0.92);
            opacity: 0.95;
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.1;
            pointer-events: none;
            will-change: transform;
        }

        .hero-orb-1 {
            width: 200px;
            height: 200px;
            background: var(--color-amber);
            top: -50px;
            right: -50px;
            animation: float-orb 8s ease-in-out infinite;
        }

        .hero-orb-2 {
            width: 150px;
            height: 150px;
            background: var(--color-coffee);
            bottom: 20%;
            left: -30px;
            animation: float-orb 10s ease-in-out infinite reverse;
        }

        .hero-orb-3 {
            width: 100px;
            height: 100px;
            background: var(--color-amber);
            top: 40%;
            right: 10%;
            animation: float-orb 6s ease-in-out infinite 2s;
        }

        @keyframes float-orb {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(20px, -20px) scale(1.1);
            }
        }

        .explore-topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 10;
        }

        .explore-brand {
            font-size: 1.25rem;
            font-weight: 900;
            color: #2C1810;
            letter-spacing: -0.01em;
            line-height: 1.1;
        }

        .explore-brand span {
            color: #F59E0B;
        }

        .explore-tagline {
            font-size: 0.55rem;
            font-weight: 800;
            color: #8B7355;
            letter-spacing: 0.08em;
            margin: 0;
            opacity: 0.8;
        }

        .explore-logo-box {
            width: 36px;
            /* Slightly smaller */
            height: 36px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(26, 15, 10, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .explore-logo-box img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        /* Search Box 2026 */
        .explore-search-2026 {
            display: flex;
            align-items: center;
            gap: 14px;
            background: white;
            padding: 14px 18px;
            border-radius: 22px;
            box-shadow: 0 4px 24px rgba(26, 15, 10, 0.08), inset 0 0 0 1px rgba(26, 15, 10, 0.03);
            position: relative;
            z-index: 100;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .explore-search-2026:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(26, 15, 10, 0.12), inset 0 0 0 2px var(--color-amber);
        }

        .search-icon-pulse {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--color-espresso) 0%, var(--color-coffee) 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .explore-search-input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-espresso);
            background: transparent;
            font-family: var(--font-sans);
        }

        .explore-search-input::placeholder {
            color: var(--color-text-muted);
            opacity: 0.6;
        }

        .explore-sort-btn {
            width: 44px;
            height: 44px;
            background: #F5EFED;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2C1810;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            flex-shrink: 0;
            border: 1px solid rgba(26, 15, 10, 0.08);
            cursor: pointer;
        }

        .explore-sort-btn:hover,
        .explore-sort-btn.active {
            background: #2C1810;
            color: white;
            border-color: #2C1810;
            transform: rotate(90deg);
        }

        /* Sort Dropdown */
        .explore-sort-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 280px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(26, 15, 10, 0.25);
            border: 1px solid rgba(26, 15, 10, 0.08);
            z-index: 9999;
            max-height: 400px;
            overflow-y: auto;
        }

        .sort-dropdown-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 20px;
            background: #F5EFED;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #2C1810;
        }

        .sort-dropdown-body {
            padding: 8px;
        }

        .sort-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #2C1810;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
        }

        .sort-dropdown-item:hover {
            background: #F5EFED;
        }

        .sort-dropdown-item.active {
            background: #2C1810;
            color: white;
        }

        .sort-dropdown-alphabet {
            display: flex;
            flex-direction: column;
            padding: 8px;
            border-top: 1px solid #E6E1DC;
            max-height: 250px;
            overflow-y: auto;
        }

        .alphabet-btn {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 800;
            background: #F5EFED;
            color: #2C1810;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .alphabet-btn:hover {
            background: #8B7355;
            color: white;
        }

        .alphabet-btn.active {
            background: #F59E0B;
            color: white;
            transform: scale(1.1);
        }

        /* Category Pills 2026 */
        .explore-category-pills {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 4px 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .explore-category-pills::-webkit-scrollbar {
            display: none;
        }

        .category-pill {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            background: white;
            border: 1px solid rgba(26, 15, 10, 0.1);
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #2C1810;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 8px rgba(26, 15, 10, 0.06);
        }

        .category-pill i {
            color: #8B7355;
            font-size: 0.95rem;
        }

        .category-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 15, 10, 0.12);
            border-color: rgba(26, 15, 10, 0.15);
        }

        .category-pill.active {
            background: #2C1810;
            color: white;
            border-color: #2C1810;
            box-shadow: 0 8px 24px rgba(26, 15, 10, 0.3);
        }

        .category-pill.active i {
            color: white;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10B981;
            border-radius: 50%;
            animation: pulse-green 2s infinite;
        }

        .category-pill.active .pulse-dot {
            background: white;
        }

        @keyframes pulse-green {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.3);
            }
        }

        /* City Filter Pills */
        .explore-city-pills {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 4px 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .explore-city-pills::-webkit-scrollbar {
            display: none;
        }

        .city-pill {
            padding: 10px 18px;
            background: white;
            border: 1px solid rgba(26, 15, 10, 0.12);
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #5C4A3D;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .city-pill:hover {
            background: #F5EFED;
            border-color: rgba(26, 15, 10, 0.2);
        }

        .city-pill.active {
            background: #2C1810;
            color: white;
            border-color: #2C1810;
        }

        /* Results Counter */
        .explore-result-counter {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: rgba(26, 15, 10, 0.03);
            border-radius: 100px;
            width: fit-content;
        }

        .counter-number {
            font-size: 1.1rem;
            font-weight: 900;
            color: var(--color-amber);
        }

        .counter-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--color-text-muted);
        }

        /* Cafe Grid */
        .explore-cafe-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            padding: 24px 20px 140px;
        }

        @media (max-width: 360px) {
            .explore-cafe-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        /* Cafe Card 2026 */
        .cafe-card-2026 {
            position: relative;
            background: white;
            border-radius: 28px;
            overflow: hidden;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 20px rgba(26, 15, 10, 0.06);
            border: 1px solid rgba(26, 15, 10, 0.03);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            opacity: 1 !important;
            transform: none !important;
        }

        .cafe-card-2026:hover {
            transform: translateY(-8px) !important;
            box-shadow: 0 20px 50px rgba(26, 15, 10, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .cafe-card-2026:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 50px rgba(26, 15, 10, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .cafe-card-2026:active {
            transform: scale(0.98);
        }

        .cafe-card-image {
            position: relative;
            aspect-ratio: 4/5;
            overflow: hidden;
        }

        .cafe-card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .cafe-card-2026:hover .cafe-card-img {
            transform: scale(1.1);
        }

        .cafe-card-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 40%, rgba(26, 15, 10, 0.6) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .cafe-card-2026:hover .cafe-card-overlay {
            opacity: 1;
        }

        /* Status Badge */
        .cafe-status-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 100px;
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .cafe-status-badge.open {
            background: rgba(16, 185, 129, 0.9);
            color: white;
        }

        .cafe-status-badge.closed {
            background: rgba(239, 68, 68, 0.85);
            color: white;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: currentColor;
            border-radius: 50%;
            animation: pulse-dot 1.5s infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* Distance Badge */
        .cafe-distance-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 100px;
            font-size: 0.65rem;
            font-weight: 800;
            color: var(--color-espresso);
        }

        /* Quick Actions */
        .cafe-card-actions {
            position: absolute;
            bottom: 12px;
            right: 12px;
            display: flex;
            gap: 8px;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        .cafe-card-2026:hover .cafe-card-actions {
            opacity: 1;
            transform: translateY(0);
        }

        .quick-action-btn {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-espresso);
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .quick-action-btn:hover {
            background: var(--color-espresso);
            color: white;
            transform: scale(1.1);
        }

        /* Card Content */
        .cafe-card-content {
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .cafe-card-title {
            font-size: 1rem;
            font-weight: 800;
            color: #2C1810;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cafe-card-address {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #8B7355;
        }

        .cafe-card-address i {
            color: #F59E0B;
            font-size: 0.85rem;
        }

        .cafe-card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 4px;
        }

        .cafe-tag {
            padding: 4px 10px;
            background: #F5EFED;
            border-radius: 8px;
            font-size: 0.6rem;
            font-weight: 700;
            color: #6F4E37;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .cafe-tag-more {
            padding: 4px 8px;
            background: var(--color-amber);
            border-radius: 8px;
            font-size: 0.6rem;
            font-weight: 800;
            color: white;
        }

        /* Card Shine Effect */
        .card-shine {
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
            pointer-events: none;
        }

        .cafe-card-2026:hover .card-shine {
            left: 100%;
        }

        /* Empty State Premium */
        .explore-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 40px;
            text-align: center;
            min-height: 50vh;
        }

        .empty-state-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, var(--color-cream-dark) 0%, white 100%);
            border-radius: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            animation: float-icon 3s ease-in-out infinite;
        }

        .empty-state-icon i {
            font-size: 3.5rem;
            color: var(--color-coffee-light);
        }

        @keyframes float-icon {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .empty-state-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--color-espresso);
            margin-bottom: 8px;
        }

        .empty-state-text {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--color-text-muted);
            margin-bottom: 24px;
        }

        .empty-state-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: var(--color-espresso);
            color: white;
            border: none;
            border-radius: 100px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .empty-state-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 32px rgba(26, 15, 10, 0.25);
        }

        /* Responsive Tweaks */
        @media (min-width: 400px) {
            .explore-main-title {
                font-size: 2.8rem;
            }
        }

        /* City Selector 2026 */
        .explore-city-selector {
            margin-bottom: 24px;
            display: flex;
            justify-content: center;
        }

        .city-select-premium {
            width: 100%;
            max-width: 300px;
            padding: 14px 24px;
            background: white;
            border: 2px solid rgba(26, 15, 10, 0.05);
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 800;
            color: #2C1810;
            appearance: none;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 16px rgba(26, 15, 10, 0.04);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 256'%3E%3Cpath fill='%232C1810' d='M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80a8,8,0,0,1,11.32-11.32L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 18px;
        }

        .city-select-premium:focus {
            outline: none;
            border-color: var(--color-amber);
            box-shadow: 0 8px 32px rgba(245, 158, 11, 0.15);
            transform: translateY(-2px);
        }

        .city-select-premium:hover {
            border-color: rgba(26, 15, 10, 0.2);
            background-color: #F8F7F5;
        }

        /* Skeleton Loaders */
        .skeleton-card {
            background: white !important;
            border-color: rgba(26, 15, 10, 0.03) !important;
            pointer-events: none;
        }

        .skeleton-image {
            width: 100%;
            aspect-ratio: 16/10;
            background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%);
            background-size: 200% 100%;
            animation: skeleton-pulse 1.5s infinite;
        }

        .skeleton-title {
            height: 24px;
            background: #F3F4F6;
            border-radius: 8px;
            animation: skeleton-pulse 1.5s infinite;
        }

        .skeleton-text {
            height: 16px;
            background: #F3F4F6;
            border-radius: 6px;
            animation: skeleton-pulse 1.5s infinite;
        }

        .skeleton-pill {
            width: 60px;
            height: 20px;
            background: #F3F4F6;
            border-radius: 100px;
            animation: skeleton-pulse 1.5s infinite;
        }

        @keyframes skeleton-pulse {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .explore-result-counter-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
            padding: 0 4px;
        }

        .explore-result-counter {
            background: rgba(245, 158, 11, 0.05);
            padding: 4px 12px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .counter-number {
            font-size: 0.9rem;
            font-weight: 800;
            color: #F59E0B;
        }

        .counter-text {
            font-size: 0.7rem;
            font-weight: 700;
            color: #8B7355;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .explore-loading-state {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #8B7355;
            opacity: 0.6;
        }

        .explore-loading-state i {
            font-size: 1.1rem;
        }

        .explore-loading-state span {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
    </style>
</div>

@script
<script>
    Alpine.data('exploreLogic', () => ({
        filteredCafes: [],
        search: '',
        isScrolled: false,
        activeFilter: 'semua',
        activeSort: 'relevance',
        activeLetter: null,
        showSortMenu: false,
        isLocating: false,
        userLat: null,
        userLng: null,
        cafes: @js($cafes),
        initComponent() {
            // Catch explicit update event ONLY
            // Ditching the deep $watch($wire.cafes) which causes major lag
            this.$wire.on('cafes-updated', (event) => {
                const data = event.detail?.cafes || event.cafes;
                if (data && typeof data === 'object') {
                    this.cafes = Array.isArray(data) ? [...data] : Object.values(data);
                }
            });
        },

        filteredCafes() {
            if (!this.cafes || !Array.isArray(this.cafes)) return [];

            // Search cache
            const q = (this.search || '').trim().toLowerCase();
            const filter = this.activeFilter;
            const letter = this.activeLetter;

            return this.cafes.filter(c => {
                // Search filter
                if (q !== '') {
                    const name = (c.name || '').toLowerCase();
                    const addr = (c.address || '').toLowerCase();
                    if (!name.includes(q) && !addr.includes(q)) return false;
                }

                // Status filter
                if (filter === 'buka' && !c.isOpen) return false;

                // Alphabet filter
                if (letter) {
                    const name = (c.name || '').trim().toUpperCase();
                    if (!name.startsWith(letter)) return false;
                }

                return true;
            }).sort((a, b) => {
                // Sorting logic
                if (this.activeSort === 'name_az') return (a.name || '').localeCompare(b.name || '');
                if (this.activeSort === 'name_za') return (b.name || '').localeCompare(a.name || '');
                if (filter === 'terdekat' && this.userLat && this.userLng) {
                    return (a.distance || 0) - (b.distance || 0);
                }
                return 0;
            });
        },

        getLocation() {
            if (!navigator.geolocation) return;
            this.isLocating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.userLat = pos.coords.latitude;
                    this.userLng = pos.coords.longitude;
                    this.isLocating = false;
                },
                () => { this.isLocating = false; }
            );
        },

        calculateDistance(lat1, lon1, lat2, lon2) {
            if (!lat2 || !lon2) return null;
            const R = 6371; // km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        },

        formatDistance(km) {
            if (km === null) return '';
            return km < 1 ? Math.round(km * 1000) + 'm' : km.toFixed(1) + 'km';
        }
    }));
</script>
@endscript