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

        {{-- City Filter Pills --}}
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
    <main class="explore-cafe-grid" wire:loading.remove.delay.shorter>
        @forelse($cafes as $cafe)
            <a href="{{ route('cafes.show', $cafe) }}" class="cafe-card-2026 group">
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
                              
                        {{-- Hover Quick Actions (Moved Inside Image) --}}
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

                    {{-- Gradient Overlay --}}
                    <div class="cafe-card-overlay"></div>

                    {{-- Smart Status Badge 2026 --}}
                    @php
                        $isOpen = $cafe->is_open;
                        $timeText = '';
                        $todayHours = $cafe->today_hours;

                        if ($cafe->is_24_hours) {
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




                    {{-- Distance Badge (Only when location is available) --}}
                    @if(isset($cafe->distance))
                        <div class="cafe-distance-badge">
                            <i class="ph-fill ph-navigation-arrow text-amber-500"></i>
                            <span>{{ number_format($cafe->distance, 1) }} km</span>
                        </div>
                    @endif

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
            {{-- Premium Empty State --}}
            <div class="col-span-full explore-empty-state">
                <div class="empty-state-icon">
                    <i class="ph-light ph-coffee"></i>
                </div>
                <h3 class="empty-state-title">Belum Ada Cafe</h3>
                <p class="empty-state-text">Coba ubah filter atau kata kunci pencarian</p>
                <button class="empty-state-btn" wire:click="resetAllFilters">
                    <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    Reset Filter
                </button>
            </div>
        @endforelse
    </main>

    <div class="px-6 pb-20">
        {{ $cafes->links() }}
    </div>

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