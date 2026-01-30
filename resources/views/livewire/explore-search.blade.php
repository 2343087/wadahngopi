{{-- Livewire Explore Search Component --}}
{{-- wire:poll.30s refreshes cafe data every 30 seconds --}}
<div wire:poll.30s="loadCafes" x-data="exploreLogic()" x-init="initComponent()" class="flex flex-col flex-1">
    {{-- Pass Livewire data to Alpine --}}
    <div x-init="cafes = $wire.cafes; $watch('$wire.cafes', value => cafes = value)"></div>

    @if(config('app.debug'))
        <div class="hidden" id="cafe-debug-count">{{ count($cafes) }}</div>
    @endif

    {{-- The rest of the explore UI stays exactly the same --}}
    {{-- Alpine handles search/filter/sort client-side, Livewire handles data refresh --}}

    {{-- Glass Header with Search --}}
    <header class="hero-fancy px-6 pt-24 pb-10 flex flex-col gap-6">
        <div class="hero-header-decoration"></div>
        <div class="flex items-center justify-between relative z-10">
            <h1 class="hero-luxury-title">Jelajahi <span class="italic text-[--color-coffee]">Cafe</span></h1>
            <div
                class="w-12 h-12 bg-white/80 backdrop-blur-md border border-espresso/5 rounded-2xl flex items-center justify-center shadow-premium overflow-hidden">
                <img src="{{ asset('wadahicon.png') }}" alt="Logo" class="w-full h-full object-cover">
            </div>
        </div>

        <div class="search-luxury-box !mt-2 relative z-10" @click.away="showSortMenu = false">
            <i class="ph-bold ph-magnifying-glass text-xl opacity-30"></i>
            <input type="text" x-model="search" placeholder="Cari cafe idamanmu..." @keyup.escape="search = ''">

            {{-- Sort Button --}}
            <button class="sort-luxury-btn" @click="showSortMenu = !showSortMenu">
                <i class="ph-bold ph-sliders-horizontal"></i>
            </button>

            {{-- Sort Dropdown --}}
            <div class="sort-luxury-menu" x-show="showSortMenu" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-cloak>
                <div class="px-4 py-2 border-b border-espresso/5 mb-2">
                    <span class="text-[0.6rem] font-black uppercase tracking-widest opacity-40">Urutkan
                        Berdasarkan</span>
                </div>
                <div class="sort-option-item" :class="activeSort === 'name_az' && !activeLetter ? 'active' : ''"
                    @click="if (activeSort === 'name_az' && !activeLetter) { activeSort = 'relevance'; } else { activeSort = 'name_az'; activeLetter = null; } showSortMenu = false">
                    <i class="ph-fill ph-sort-ascending"></i>
                    Nama (A-Z)
                </div>
                <div class="sort-option-item" :class="activeSort === 'name_za' ? 'active' : ''"
                    @click="if (activeSort === 'name_za') { activeSort = 'relevance'; } else { activeSort = 'name_za'; activeLetter = null; } showSortMenu = false">
                    <i class="ph-fill ph-sort-descending"></i>
                    Nama (Z-A)
                </div>

                <div class="grid grid-cols-4 gap-1 p-1 mt-2">
                    @foreach (range('A', 'Z') as $char)
                        <div class="w-full aspect-square flex items-center justify-center rounded-xl text-[0.7rem] font-bold cursor-pointer transition-all"
                            :class="activeLetter === '{{ $char }}' ? 'bg-espresso text-white shadow-lg' : 'hover:bg-espresso/5'"
                            @click="if (activeLetter === '{{ $char }}') { activeLetter = null; activeSort = 'relevance'; } else { activeLetter = '{{ $char }}'; activeSort = 'name_az'; } showSortMenu = false">
                            {{ $char }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="pills-container-luxury !px-0 relative z-10">
            <button class="pill-luxury" :class="activeFilter === 'semua' ? 'active' : ''"
                @click="activeFilter = 'semua'">Semua</button>
            <button class="pill-luxury" :class="activeFilter === 'terdekat' ? 'active' : ''"
                @click="activeFilter = 'terdekat'; getLocation()">
                <i class="ph-fill ph-map-pin" x-show="!isLocating"></i>
                <i class="ph ph-circle-notch animate-spin" x-show="isLocating"></i>
                Terdekat
            </button>
            <button class="pill-luxury" :class="activeFilter === 'buka' ? 'active' : ''"
                @click="activeFilter = 'buka'">Sedang Buka</button>
        </div>
    </header>

    {{-- Cafe List --}}
    <main class="luxury-grid !pb-32" x-show="filteredCafes().length > 0">
        <template x-for="(cafe, index) in filteredCafes()" :key="cafe.id">
            <a :href="cafe.url" class="luxury-cafe-card animate-up group"
                :style="'animation-delay: ' + (index * 0.04) + 's'">
                <div class="luxury-image-wrapper">
                    <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                        :alt="cafe.name" loading="lazy">

                    {{-- Floating Chips --}}
                    <div class="absolute top-3 inset-x-3 flex justify-between items-start pointer-events-none">
                        {{-- Distance Tag --}}
                        <template x-if="userLat && cafe.distance">
                            <div
                                class="bg-white/90 backdrop-blur-md px-2.5 py-1.5 rounded-xl flex items-center gap-1.5 shadow-premium border border-white/20">
                                <i class="ph-fill ph-navigation-arrow text-espresso text-[0.7rem]"></i>
                                <span class="text-espresso text-[0.65rem] font-black"
                                    x-text="formatDistance(cafe.distance)"></span>
                            </div>
                        </template>

                        {{-- Status Badge --}}
                        <div class="status-badge-glass-v2">
                            <span :class="cafe.isOpen ? 'bg-emerald-400' : 'bg-rose-400'"
                                class="h-1.5 w-1.5 rounded-full inline-block shadow-[0_0_8px_rgba(52,211,153,0.5)]"></span>
                            <span class="text-[0.6rem] font-black uppercase tracking-widest"
                                x-text="cafe.isOpen ? 'Buka' : 'Tutup'"></span>
                        </div>
                    </div>
                </div>

                {{-- Card Info Below Image --}}
                <div class="luxury-card-content">
                    <div class="flex flex-col gap-0.5">
                        <h3 class="luxury-card-title-v2" x-text="cafe.name"></h3>
                        <p class="luxury-card-location-v2">
                            <i class="ph-fill ph-map-pin-line text-[0.8rem] text-amber"></i>
                            <span x-text="(cafe.address || '').split(',')[0]"></span>
                        </p>
                    </div>

                    <div class="flex flex-col gap-4 mt-4">
                        <div class="flex flex-wrap gap-1.5">
                            {{-- Facilities/Tags --}}
                            <template x-for="tag in (cafe.facilities || [])">
                                <span class="luxury-tag-v2" x-text="tag"></span>
                            </template>
                        </div>

                        <div class="flex justify-end gap-1.5 border-t border-espresso/5 pt-3">
                            {{-- Social Media Icons --}}
                            <template x-for="social in (cafe.socialLinks || [])" :key="social.platform">
                                <div @click.prevent.stop="window.open(social.url, '_blank')"
                                    class="luxury-social-btn-v2">
                                    <i :class="{
                                                    'ph-bold ph-instagram-logo': social.platform === 'instagram',
                                                    'ph-bold ph-tiktok-logo': social.platform === 'tiktok',
                                                    'ph-bold ph-facebook-logo': social.platform === 'facebook',
                                                    'ph-bold ph-x-logo': social.platform === 'twitter'
                                                }"></i>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </a>
        </template>
    </main>

    {{-- Empty State --}}
    <div x-show="filteredCafes().length === 0" x-cloak
        style="text-align: center; padding: 120px 40px; color: var(--color-text-muted);">
        <i class="ph ph-coffee-bean text-[4rem] opacity-20 block mb-6 mx-auto"></i>
        <h3 class="text-[1.2rem] font-black text-[--color-espresso] mb-2">Belum Ada Cafe Nih</h3>
        <p class="text-[0.9rem] font-medium opacity-60">Lokasi ini belum ada cafe yang terdaftar. Coba cari lokasi lain!
        </p>
    </div>
</div>

@script
<script>
    Alpine.data('exploreLogic', () => ({
        search: '',
        activeFilter: 'semua',
        activeSort: 'relevance',
        activeLetter: null,
        showSortMenu: false,
        isLocating: false,
        userLat: null,
        userLng: null,
        cafes: @js($cafes),

        initComponent() {
            this.cafes = this.$wire.cafes;
            this.$wire.on('cafes-updated', (event) => {
                this.cafes = event.cafes;
            });
        },

        filteredCafes() {
            let results = [...this.cafes];

            // Filter by search
            if (this.search) {
                const q = this.search.toLowerCase();
                results = results.filter(c =>
                    c.name.toLowerCase().includes(q) ||
                    c.address.toLowerCase().includes(q)
                );
            }

            // Filter by "Buka Sekarang"
            if (this.activeFilter === 'buka') {
                results = results.filter(c => c.isOpen);
            }

            // Alphabetical Scroll Filter
            if (this.activeLetter) {
                results = results.filter(c => c.name.trim().toUpperCase().startsWith(this.activeLetter));
            }

            // Distance calculation
            if (this.userLat && this.userLng) {
                results = results.map(c => ({
                    ...c,
                    distance: this.calculateDistance(this.userLat, this.userLng, c.lat, c.lng)
                }));

                // Auto-sort by distance if "Terdekat" filter is active
                if (this.activeFilter === 'terdekat') {
                    results.sort((a, b) => (a.distance || 0) - (b.distance || 0));
                }
            }

            // Sorting
            if (this.activeSort === 'name_az') {
                results.sort((a, b) => a.name.localeCompare(b.name));
            } else if (this.activeSort === 'name_za') {
                results.sort((a, b) => b.name.localeCompare(a.name));
            }

            return results;
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