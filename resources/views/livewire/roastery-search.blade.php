{{-- Livewire Roastery Search Component --}}
{{-- Premium Redesign 2026 - Ultra Modern & Responsive (Matched to Explore) --}}
<div x-data="roasteryLogic()" x-init="initComponent()" class="block min-h-screen">
    {{-- Livewire data is handled in Alpine x-data script --}}

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
                    <p class="explore-tagline">CARI BIJI KOPI FAVORITMU</p>
                </div>
            </div>
        </div>

        {{-- Premium Search Box --}}
        <div class="explore-search-2026" @click.away="showSortMenu = false">
            <div class="search-icon-pulse">
                <i class="ph-bold ph-magnifying-glass"></i>
            </div>
            <input type="text" id="roastery-search" name="search" wire:model.live.debounce.500ms="search" placeholder="Cari roastery atau beans..." 
                class="explore-search-input">
            <button class="explore-sort-btn" @click="showSortMenu = !showSortMenu"
                :class="showSortMenu ? 'active' : ''">
                <i class="ph-bold ph-sliders-horizontal"></i>
            </button>

            {{-- Sort Dropdown Premium --}}
            <div class="explore-sort-dropdown" x-show="showSortMenu" x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95">
                <div class="sort-dropdown-header">
                    <i class="ph-fill ph-funnel text-amber"></i>
                    <span>Urutkan</span>
                </div>
                <div class="sort-dropdown-body custom-scrollbar relative">
                    <button class="sort-dropdown-item"
                        wire:click="setSort('name_az')"
                        :class="$wire.sort === 'name_az' ? 'active' : ''"
                        @click="showSortMenu = false">
                        <i class="ph-fill ph-sort-ascending"></i>
                        <span> (A-Z)</span>
                    </button>
                    <button class="sort-dropdown-item" 
                        wire:click="setSort('name_za')"
                        :class="$wire.sort === 'name_za' && !$wire.activeLetter ? 'active' : ''"
                        @click="showSortMenu = false">
                        <i class="ph-fill ph-sort-descending"></i>
                        <span> (Z-A)</span>
                    </button>
                    
                    {{-- Unified Alphabet List items --}}
                     @foreach (range('A', 'Z') as $char)
                        <button class="sort-dropdown-item" 
                            wire:click="setLetter('{{ $char }}')"
                            :class="$wire.activeLetter === '{{ $char }}' ? 'active' : ''"
                            @click="showSortMenu = false">
                            <i class="ph-bold ph-text-aa"></i>
                            <span> ({{ $char }})</span>
                        </button>
                    @endforeach
                    
                    @if($activeLetter || $search || $filter !== 'semua' || $cityId || $sort !== 'relevance')
                        <div class="sticky bottom-0 left-0 right-0 p-2 bg-white/95 backdrop-blur-sm border-t border-[#F5EFED]">
                            <button wire:click="resetAllFilters" @click="showSortMenu = false"
                                class="w-full py-2 flex items-center justify-center gap-2 text-xs font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                <i class="ph-bold ph-x"></i>
                                Reset Filter
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Category Filter Pills --}}
        <div class="explore-category-pills">
            <button class="category-pill" :class="$wire.filter === 'semua' ? 'active' : ''"
                wire:click="$set('filter', 'semua')">
                <i class="ph-fill ph-coffee-bean"></i>
                <span>Semua</span>
            </button>
            <button class="category-pill" :class="$wire.filter === 'terdekat' ? 'active' : ''"
                @click="getLocation()">
                <i class="ph-fill ph-map-pin" x-show="!isLocating"></i>
                <i class="ph ph-circle-notch animate-spin" x-show="isLocating"></i>
                <span>Terdekat</span>
            </button>
            <button class="category-pill pill-open" :class="$wire.filter === 'buka' ? 'active' : ''"
                wire:click="$set('filter', 'buka')">
                <span class="pulse-dot"></span>
                <span>Sedang Buka</span>
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
    </header>

    {{-- Content Spacer for Fixed Header --}}
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

    {{-- Roastery Grid Premium --}}
    <main class="explore-cafe-grid" wire:loading.remove.delay.shorter>
        @forelse($roasteries as $roastery)
            <a href="{{ route('roastery.show', $roastery) }}" class="cafe-card-2026 group">
                    {{-- Fixed Height Image Container --}}
                    @php
                        $featuredImage = $roastery->processed_images[0] ?? 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?auto=format&fit=crop&q=80&w=800';
                    @endphp
                    <div class="cafe-card-image">
                         <img src="{{ $featuredImage }}"
                              alt="{{ $roastery->name }}" loading="lazy" 
                              class="cafe-card-img object-cover">
                              
                        {{-- Hover Quick Actions --}}
                        <div class="cafe-card-actions">
                            @php
                                $socials = collect($roastery->social_links ?? [])
                                    ->filter(fn($s) => filter_var($s['show'] ?? false, FILTER_VALIDATE_BOOLEAN) && !empty($s['url']));
                                $visibleSocials = $socials->take(3);
                            @endphp
                            
                            @foreach ($visibleSocials as $social)
                                <button onclick="event.preventDefault(); window.open('{{ $social['url'] }}', '_blank')" class="quick-action-btn" title="{{ ucfirst($social['platform']) }}">
                                    <i class="ph-bold ph-@if($social['platform'] === 'twitter')x-logo @else{{ $social['platform'] }}-logo @endif"></i>
                                </button>
                            @endforeach
                        </div>
                     </div>

                    {{-- Gradient Overlay --}}
                    <div class="cafe-card-overlay"></div>

                    {{-- Simple Roastery Badge --}}
                    {{-- Smart Status Badge 2026 --}}
                    @php
                        $isOpen = $roastery->is_open;
                        $timeText = '';
                        $todayHours = $roastery->today_hours;

                        if ($roastery->is_24_hours) {
                            $timeText = '• 24 Jam';
                        } elseif ($isOpen && isset($todayHours['close'])) {
                            $timeText = '• Sampai ' . \Carbon\Carbon::parse($todayHours['close'])->format('H:i');
                        } elseif (!$isOpen && isset($todayHours['open'])) {
                            $timeText = '• Buka ' . \Carbon\Carbon::parse($todayHours['open'])->format('H:i');
                        }
                    @endphp
                    <div class="cafe-status-badge-smart {{ $isOpen ? 'open' : 'closed' }}">
                        <div class="flex items-center gap-1.5">
                            <span class="status-indicator">
                                <span class="status-ping"></span>
                                <span class="status-dot"></span>
                            </span>
                            <span class="font-black text-[0.65rem] tracking-wider uppercase">
                                {{ $isOpen ? 'Buka' : 'Tutup' }}
                            </span>
                        </div>
                        @if($timeText)
                            <span class="text-[0.6rem] font-bold opacity-90 ml-1 border-l border-white/20 pl-1.5 leading-none">
                                {{ $timeText }}
                            </span>
                        @endif
                    </div>

                    {{-- Distance Badge --}}
                    @if(isset($roastery->distance))
                        <div class="cafe-distance-badge">
                            <i class="ph-fill ph-navigation-arrow text-amber-500"></i>
                            <span>{{ number_format($roastery->distance, 1) }} km</span>
                        </div>
                    @endif

                {{-- Card Content --}}
                <div class="cafe-card-content">
                    <h3 class="cafe-card-title">{{ $roastery->name }}</h3>
                    <p class="cafe-card-address">
                        <i class="ph-fill ph-map-pin"></i>
                        <span class="drop-shadow-sm">{{ $roastery->city?->name ?? 'Kalimantan' }}</span>    
                    </p>
                    
                    {{-- Spacer --}}
                    <div class="mt-auto"></div> 
                </div>

                {{-- Hover Shine Effect --}}
                <div class="card-shine"></div>
            </a>
        @empty
            {{-- Premium Empty State --}}
            <div class="col-span-full explore-empty-state">
                <div class="empty-state-icon">
                    <i class="ph-light ph-coffee-bean"></i>
                </div>
                <h3 class="empty-state-title">Belum Ada Roastery</h3>
                <p class="empty-state-text">Coba ubah filter atau kata kunci pencarian</p>
                <button class="empty-state-btn" wire:click="resetAllFilters">
                    <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    Reset Filter
                </button>
            </div>
        @endforelse
    </main>

    <div class="px-6 pb-20">
        {{ $roasteries->links() }}
    </div>

    {{-- Premium Explore Styles --}}
    <style>
        /* === EXPLORE PAGE 2026 ULTRA PREMIUM (COPIED FOR ROASTERY) === */

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
            padding: 32px 20px 12px;
            background: #FFFDFB;
            border-radius: 0 0 32px 32px;
            overflow: visible;
            box-shadow: 0 4px 20px rgba(26, 15, 10, 0.05);
            transition: padding 0.4s ease, background 0.4s ease, box-shadow 0.4s ease;
            will-change: padding, background, box-shadow;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .header-spacer-2026 {
            height: 330px; /* Reduced to match smaller header */
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

        .explore-topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 10;
        }

        .explore-brand {
            font-size: 1.15rem;
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
            width: 32px;
            height: 32px;
            background: white;
            border-radius: 9px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(26, 15, 10, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .explore-logo-box img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        /* Search Box 2026 */
        .explore-search-2026 {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            padding: 10px 16px;
            border-radius: 20px;
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
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--color-espresso) 0%, var(--color-coffee) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .explore-search-input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 0.95rem;
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
            width: 38px;
            height: 38px;
            background: #F5EFED;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2C1810;
            font-size: 1.1rem;
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
            gap: 6px;
            padding: 8px 14px;
            background: white;
            border: 1px solid rgba(26, 15, 10, 0.1);
            border-radius: 100px;
            font-size: 0.75rem;
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
            padding: 8px 14px;
            background: white;
            border: 1px solid rgba(26, 15, 10, 0.12);
            border-radius: 100px;
            font-size: 0.75rem;
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

        /* Cafe Grid */
        .explore-cafe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); /* Allow smaller cards */
            gap: 16px;
            padding: 20px 16px 140px;
        }

        @media (max-width: 640px) {
            .explore-cafe-grid {
                grid-template-columns: repeat(2, 1fr); /* Force 2 Columns on mobile */
                gap: 12px;
                padding: 16px 12px 140px;
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
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 20px 50px rgba(26, 15, 10, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .cafe-card-2026:active {
            transform: scale(0.98);
        }

        .cafe-card-image {
            position: relative;
            width: 100%;
            aspect-ratio: 4/5; /* Fixed Uniform Size */
            overflow: hidden;
            background-color: #F5EFED;
        }

        .cafe-card-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Fill the card, crop excess */
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

        /* Smart Status Badge 2026 */
        .cafe-status-badge-smart {
            position: absolute;
            top: 12px;
            right: 12px;
            display: flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 12px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            z-index: 20;
            color: white;
            line-height: normal;
        }

        .cafe-status-badge-smart.open {
            background: rgba(16, 185, 129, 0.85); /* Emerald Premium */
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .cafe-status-badge-smart.closed {
            background: rgba(239, 68, 68, 0.85); /* Rose Premium */
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .status-indicator {
            position: relative;
            width: 8px;
            height: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-dot {
            width: 5px;
            height: 5px;
            background: white;
            border-radius: 50%;
            z-index: 2;
            box-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        .status-ping {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: white;
            opacity: 0.6;
            animation: status-ping 1.5s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        @keyframes status-ping {
            50% {
                opacity: 0.4;
            }
            75%, 100% {
                transform: scale(2.5);
                opacity: 0;
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
            gap: 6px;
            z-index: 50;
        }

        .quick-action-btn {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2C1810;
            font-size: 1rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .quick-action-btn:hover {
            transform: scale(1.1);
            background: #2C1810;
            color: #F59E0B;
        }

        /* Card Content */
        .cafe-card-content {
            padding: 12px 14px; /* Compact Padding */
            display: flex;
            flex-direction: column;
            gap: 6px; /* Tighter gap */
            flex: 1;
        }

        .cafe-card-title {
            font-size: 0.9rem; /* Smaller Title */
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
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
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
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</div>

@script
<script>
    Alpine.data('roasteryLogic', () => ({
        isScrolled: false,
        showSortMenu: false,
        isLocating: false,
        
        initComponent() {
            // Keep header scroll effect
            this.isScrolled = window.pageYOffset > 50;
        },

        getLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }
            this.isLocating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    // Send to backend
                    this.$wire.setUserLocation(pos.coords.latitude, pos.coords.longitude);
                    this.$wire.set('filter', 'terdekat');
                    this.isLocating = false;
                },
                (err) => { 
                    console.error('Geolocation Error:', err);
                    this.isLocating = false; 
                    
                    if (err.code === 1) { // PERMISSION_DENIED
                        if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                            alert('⚠️ Fitur "Terdekat" butuh koneksi aman (HTTPS). Gunakan "herd secure" atau akses via localhost.');
                        } else {
                            alert('Gagal: Izin lokasi ditolak. Cek pengaturan browser lo.');
                        }
                    } else if (err.code === 2) { // POSITION_UNAVAILABLE
                        alert('Gagal: Lokasi gak ketemu. Cek GPS atau sinyal.');
                    } else {
                        alert('Gagal mendapatkan lokasi. Masalah teknis.');
                    }
                }
            );
        }
    }));
</script>
@endscript