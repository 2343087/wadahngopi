{{-- Livewire Explore Search Component --}}
{{-- Premium Redesign 2026 - Ultra Modern & Responsive --}}
<div x-data="exploreLogic()" x-init="initComponent()" class="block min-h-screen">
    {{-- Livewire data is handled in Alpine x-data script --}}

    @if(config('app.debug'))
        <div class="hidden" id="cafe-debug-count">{{ $cafes->count() }}</div>
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
                <i class="ph-bold ph-magnifying-glass" wire:loading.remove wire:target="search"></i>
                <i class="ph-bold ph-spinner animate-spin text-amber-500" wire:loading wire:target="search"></i>
            </div>
            <input type="text" id="cafe-search" name="search" wire:model.live.debounce.500ms="search" placeholder="Cari cafe favoritmu..." 
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
                    <i class="ph-bold ph-spinner animate-spin ml-auto text-amber-500" wire:loading wire:target="setSort, setLetter, resetAllFilters"></i>
                </div>
                <div class="sort-dropdown-body custom-scrollbar relative">
                    <button class="sort-dropdown-item"
                        wire:click="setSort('name_az')"
                        :class="$wire.sort === 'name_az' && !$wire.activeLetter ? 'active' : ''"
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
                                wire:loading.attr="disabled"
                                class="w-full py-2 flex items-center justify-center gap-2 text-xs font-bold text-red-500 bg-red-50 hover:bg-red-100 rounded-lg transition-colors disabled:opacity-50">
                                <i class="ph-bold ph-x" wire:loading.remove wire:target="resetAllFilters"></i>
                                <i class="ph-bold ph-spinner animate-spin" wire:loading wire:target="resetAllFilters"></i>
                                Reset Filter
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Category Filter Pills --}}
        <div class="filter-scroll-wrapper">
            <div class="explore-category-pills">
                <button class="category-pill disabled:opacity-50" :class="$wire.filter === 'semua' ? 'active' : ''"
                    wire:click="$set('filter', 'semua')" wire:loading.attr="disabled">
                    <i class="ph-fill ph-coffee" wire:loading.remove wire:target="$set('filter', 'semua')"></i>
                    <i class="ph-bold ph-spinner animate-spin" wire:loading wire:target="$set('filter', 'semua')"></i>
                    <span>Semua</span>
                </button>
                <button class="category-pill" :class="$wire.filter === 'terdekat' ? 'active' : ''"
                    @click="getLocation()">
                    <i class="ph-fill ph-map-pin" x-show="!isLocating" wire:loading.remove wire:target="setUserLocation"></i>
                    <i class="ph ph-circle-notch animate-spin" x-show="isLocating" wire:loading wire:target="setUserLocation"></i>
                    <span>Terdekat</span>
                </button>
                <button class="category-pill pill-open disabled:opacity-50" :class="$wire.filter === 'buka' ? 'active' : ''"
                    wire:click="$set('filter', 'buka')" wire:loading.attr="disabled">
                    <span class="pulse-dot" wire:loading.remove wire:target="$set('filter', 'buka')"></span>
                    <i class="ph-bold ph-spinner animate-spin" wire:loading wire:target="$set('filter', 'buka')"></i>
                    <span>Sedang Buka</span>
                </button>
            </div>
        </div>

        {{-- City Filter Pills --}}
        <div class="filter-scroll-wrapper">
            <div class="explore-city-pills">
                <button wire:click="$set('cityId', '')" wire:loading.attr="disabled"
                    class="city-pill {{ $cityId === '' || $cityId === null ? 'active' : '' }} disabled:opacity-50">
                    <span wire:loading.remove wire:target="$set('cityId', '')">Semua Kota</span>
                    <span wire:loading wire:target="$set('cityId', '')"><i class="ph-bold ph-spinner animate-spin"></i></span>
                </button>
                @foreach($cities as $city)
                    <button wire:click="$set('cityId', '{{ $city['id'] }}')" wire:loading.attr="disabled"
                        class="city-pill {{ $cityId == $city['id'] ? 'active' : '' }} disabled:opacity-50">
                        <span wire:loading.remove wire:target="$set('cityId', '{{ $city['id'] }}')">{{ $city['name'] }}</span>
                        <span wire:loading wire:target="$set('cityId', '{{ $city['id'] }}')"><i class="ph-bold ph-spinner animate-spin"></i></span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Sengaja Gw Hide Dulu Jangan Di Rubah Jangan Di Aktifkan Kalau Gw Gak nyuruh --}}
        <!-- <div class="explore-result-counter-wrapper">
            <div class="explore-result-counter">
                <span class="counter-number">{{ $cafes->total() }}</span>
                <span class="counter-text">cafe ditemukan</span>
            </div>
            <div wire:loading.delay.shorter class="explore-loading-state">
                <i class="ph ph-circle-notch animate-spin"></i>
                <span>Mencari...</span>
            </div>
        </div> -->
    </header>

    {{-- Content Spacer for Fixed Header (Static height to prevent jumping) --}}
    <div class="header-spacer-2026"></div>

    {{-- Premium Skeleton Shimmer (Shown while loading) --}}
    <div class="explore-cafe-grid" wire:loading.delay.shorter>
        <template x-for="i in 6" :key="'skel-'+i">
            <div class="cafe-card-2026 skeleton-card">
                <div class="skeleton-image skeleton-shimmer"></div>
                <div class="cafe-card-content">
                    <div class="skeleton-title skeleton-shimmer"></div>
                    <div class="skeleton-text skeleton-shimmer" style="width: 65%"></div>
                    <div class="flex gap-2 mt-auto">
                        <div class="skeleton-pill skeleton-shimmer"></div>
                        <div class="skeleton-pill skeleton-shimmer"></div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Cafe Grid Premium --}}
    <main class="explore-cafe-grid" wire:loading.remove.delay.shorter>
        @forelse($cafes as $cafe)
            <a href="{{ route('cafes.show', $cafe) }}" class="cafe-card-2026 group card-stagger" wire:key="cafe-{{ $cafe->id }}">
                    {{-- Fixed Height Image Container --}}
                    @php
                        $image = $cafe->image_path ? (str_starts_with($cafe->image_path, 'http') ? $cafe->image_path : Storage::url($cafe->image_path)) : 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800';
                    @endphp
                    <div class="cafe-card-image">
                        @php
                            $images = $cafe->processed_images;
                            $firstImage = count($images) > 0 ? $images[0] : 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800';
                        @endphp
                         <img src="{{ $firstImage }}"
                              alt="{{ $cafe->name }}" loading="lazy" 
                              class="cafe-card-img object-cover">
                              
                        {{-- Gradient Overlay --}}
                        <div class="cafe-card-overlay"></div>

                        {{-- Smart Status Badge --}}
                        @php
                            $isOpen = $cafe->is_open;
                            $timeText = '';
                            $todayHours = $cafe->today_hours;

                            if ($cafe->is_24_hours) {
                                $timeText = '24 Jam';
                            } elseif ($isOpen && isset($todayHours['close'])) {
                                $timeText = 'Sampai ' . \Carbon\Carbon::parse($todayHours['close'])->format('H:i');
                            } elseif (!$isOpen && isset($todayHours['open'])) {
                                $timeText = 'Buka ' . \Carbon\Carbon::parse($todayHours['open'])->format('H:i');
                            }
                        @endphp
                        <div class="cafe-status-badge-smart {{ $isOpen ? 'open' : 'closed' }}">
                            <div class="flex items-center gap-1">
                                <span class="status-indicator">
                                    <span class="status-ping"></span>
                                    <span class="status-dot"></span>
                                </span>
                                <span class="font-black tracking-wider uppercase">
                                    {{ $isOpen ? 'Buka' : 'Tutup' }}
                                </span>
                            </div>
                            @if($timeText)
                                <span class="text-[0.5rem] sm:text-[0.55rem] font-bold opacity-90 ml-1 border-l border-white/20 pl-1 leading-none whitespace-nowrap">
                                    {{ $timeText }}
                                </span>
                            @endif
                        </div>

                        {{-- Distance Badge --}}
                        @if(isset($cafe->distance))
                            <div class="cafe-distance-badge">
                                <i class="ph-fill ph-navigation-arrow text-amber-400"></i>
                                <span>{{ number_format($cafe->distance, 1) }} km</span>
                            </div>
                        @endif

                        {{-- Hover Quick Actions --}}
                        <div class="cafe-card-actions">
                            @php
                                $socials = collect($cafe->social_links ?? [])
                                    ->filter(fn($s) => filter_var($s['show'] ?? false, FILTER_VALIDATE_BOOLEAN) && !empty($s['url']));
                                $visibleSocials = $socials->take(3);
                                $moreSocialsCount = $socials->count() - 3;
                            @endphp
                            
                            @foreach ($visibleSocials as $social)
                                <button onclick="event.preventDefault(); window.open('{{ $social['url'] }}', '_blank')" class="quick-action-btn" title="{{ ucfirst($social['platform']) }}">
                                    <i class="ph-bold ph-@if($social['platform'] === 'twitter')x-logo @else{{ $social['platform'] }}-logo @endif"></i>
                                </button>
                            @endforeach
                            
                            @if($moreSocialsCount > 0)
                                <div class="quick-action-more">+{{ $moreSocialsCount }}</div>
                            @endif
                        </div>
                     </div>

                {{-- Card Content --}}
                <div class="cafe-card-content">
                    <h3 class="cafe-card-title">{{ $cafe->name }}</h3>
                    <p class="cafe-card-address">
                        <i class="ph-fill ph-map-pin"></i>
                        <span class="drop-shadow-sm">{{ $cafe->address }}</span>    
                    </p>

                    {{-- Facilities Tags --}}
                    @if($cafe->facilities->isNotEmpty())
                        <div class="cafe-card-tags">
                            @foreach($cafe->facilities->take(3) as $facility)
                                <span class="cafe-tag">
                                    {{ Str::limit($facility->name, 10) }}
                                </span>
                            @endforeach
                            @if($cafe->facilities->count() > 3)
                                <span class="cafe-tag-more">+{{ $cafe->facilities->count() - 3 }}</span>
                            @endif
                        </div>
                    @else 
                        {{-- Placeholder separation if no facilities --}}
                        <div class="mt-auto"></div> 
                    @endif
                </div>

                {{-- Hover Shine Effect --}}
                <div class="card-shine"></div>
            </a>
        @empty
            {{-- Premium Empty State with SVG --}}
            <div class="col-span-full empty-state-premium">
                <div class="empty-state-illustration">
                    <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="100" cy="130" rx="60" ry="12" fill="#F5EFED"/>
                        <path d="M60 70 C60 50, 80 35, 100 35 C120 35, 140 50, 140 70 L140 110 C140 120, 130 130, 120 130 L80 130 C70 130, 60 120, 60 110 Z" fill="#2C1810" stroke="#1A0F0A" stroke-width="2"/>
                        <path d="M140 80 C155 80, 165 90, 165 100 C165 110, 155 120, 140 115" stroke="#2C1810" stroke-width="4" fill="none" stroke-linecap="round"/>
                        <ellipse cx="100" cy="60" rx="25" ry="6" fill="#3E2723" opacity="0.3"/>
                        <path d="M85 30 C85 20, 90 15, 90 10" stroke="#B08968" stroke-width="2" stroke-linecap="round" opacity="0.5">
                            <animate attributeName="d" values="M85 30 C85 20, 90 15, 90 10;M85 30 C83 18, 88 12, 92 5;M85 30 C85 20, 90 15, 90 10" dur="2.5s" repeatCount="indefinite"/>
                        </path>
                        <path d="M100 28 C100 16, 105 10, 105 2" stroke="#B08968" stroke-width="2" stroke-linecap="round" opacity="0.4">
                            <animate attributeName="d" values="M100 28 C100 16, 105 10, 105 2;M100 28 C98 14, 103 8, 107 0;M100 28 C100 16, 105 10, 105 2" dur="3s" repeatCount="indefinite"/>
                        </path>
                        <path d="M115 30 C115 22, 118 18, 118 12" stroke="#B08968" stroke-width="2" stroke-linecap="round" opacity="0.3">
                            <animate attributeName="d" values="M115 30 C115 22, 118 18, 118 12;M115 30 C113 20, 116 15, 120 8;M115 30 C115 22, 118 18, 118 12" dur="2s" repeatCount="indefinite"/>
                        </path>
                    </svg>
                </div>
                <h3>Cafe Tidak Ditemukan</h3>
                <p>Hmm, belum ada cafe yang cocok. Coba ubah filter atau kata kunci pencarianmu.</p>
                <button class="empty-state-cta" wire:click="resetAllFilters">
                    <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    Reset Pencarian
                </button>
            </div>
        @endforelse
    </main>

    {{-- Infinite Scroll Sentinel --}}
    @if($cafes->hasMorePages())
        <div class="scroll-sentinel" 
            x-data="{ observer: null }"
            x-init="
                observer = new IntersectionObserver((entries) => {
                    entries.forEach(e => { if (e.isIntersecting) $wire.loadMore(); });
                }, { rootMargin: '200px' });
                observer.observe($el);
            "
            x-destroy="observer?.disconnect()">
            <div class="loading-more-spinner" wire:loading.delay wire:target="loadMore"></div>
            <span class="text-xs font-bold text-[#8B7355]/60 uppercase tracking-widest" wire:loading.delay wire:target="loadMore">Memuat...</span>
        </div>
    @else
        @if($cafes->count() > 0)
            <div class="scroll-sentinel">
                <div class="end-of-results">
                    <i class="ph-fill ph-check-circle"></i>
                    <span>Semua cafe sudah ditampilkan</span>
                </div>
            </div>
        @endif
    @endif

    {{-- Styles moved to resources/css/app.css --}}
</div>

@script
<script>
    Alpine.data('exploreLogic', () => ({
        isScrolled: false,
        showSortMenu: false,
        isLocating: false,
        
        initComponent() {
            // Keep header scroll effect
            this.isScrolled = window.pageYOffset > 50;
        },

        getLocation() {
            if (!navigator.geolocation) {
                this.$dispatch('toast', { message: 'Browser tidak mendukung Geolocation', type: 'error' });
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
                            this.$dispatch('toast', { message: 'Fitur Terdekat butuh HTTPS/Secure connection', type: 'error' });
                        } else {
                            this.$dispatch('toast', { message: 'Izin lokasi ditolak. Cek pengaturan browser.', type: 'error' });
                        }
                    } else if (err.code === 2) { // POSITION_UNAVAILABLE
                        this.$dispatch('toast', { message: 'Lokasi tidak ditemukan. Cek GPS.', type: 'error' });
                    } else {
                        this.$dispatch('toast', { message: 'Gagal mendapatkan lokasi.', type: 'error' });
                    }
                }
            );
        }
    }));
</script>
@endscript