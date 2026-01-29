@extends('layouts.app')

@section('title', 'Explore - WadahNgopi.Com')

@section('content')
    <div x-data="exploreLogic()" x-init="initComponent()" class="flex flex-col flex-1">
        {{-- Glass Header with Search --}}
        <header class="px-6 pt-10 pb-6 flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <h1 class="hero-luxury-title">Explore <span class="italic text-[--color-coffee]">Cafe</span></h1>
                <div class="w-10 h-10 bg-white border border-espresso/5 rounded-xl flex items-center justify-center shadow-lg overflow-hidden">
                    <img src="{{ asset('wadahicon.jpg') }}" alt="Logo" class="w-full h-full object-cover">
                </div>
            </div>

            <div class="search-luxury-box !mt-0 relative" @click.away="showSortMenu = false">
                <i class="ph-bold ph-magnifying-glass text-xl opacity-30"></i>
                <input type="text" x-model="search" placeholder="Cari cafe idamanmu..." @keyup.escape="search = ''">

                {{-- Sort Button --}}
                <button class="sort-luxury-btn" @click="showSortMenu = !showSortMenu">
                    <i class="ph-bold ph-sliders-horizontal"></i>
                </button>

                {{-- Sort Dropdown --}}
                <div class="sort-luxury-menu" x-show="showSortMenu" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                    x-cloak>

                    <div class="sort-option-item" :class="activeSort === 'name_az' && !activeLetter ? 'active' : ''"
                        @click="if (activeSort === 'name_az' && !activeLetter) { activeSort = 'relevance'; } else { activeSort = 'name_az'; activeLetter = null; } showSortMenu = false">
                        <i class="ph ph-sort-ascending"></i>
                        (A-Z)
                    </div>
                    <div class="sort-option-item" :class="activeSort === 'name_za' ? 'active' : ''"
                        @click="if (activeSort === 'name_za') { activeSort = 'relevance'; } else { activeSort = 'name_za'; activeLetter = null; } showSortMenu = false">
                        <i class="ph ph-sort-descending"></i>
                        (Z-A)
                    </div>

                    @foreach(range('A', 'Z') as $char)
                        <div class="sort-option-item" :class="activeLetter === '{{ $char }}' ? 'active' : ''"
                            @click="if (activeLetter === '{{ $char }}') { activeLetter = null; activeSort = 'relevance'; } else { activeLetter = '{{ $char }}'; activeSort = 'name_az'; } showSortMenu = false">
                            <i class="ph ph-text-aa"></i>
                            ({{ $char }})
                        </div>
                    @endforeach


                </div>
            </div>

            <div class="pills-container-luxury !px-0">
                <button class="pill-luxury" :class="activeFilter === 'semua' ? 'active' : ''"
                    @click="activeFilter = 'semua'">Semua</button>
                <button class="pill-luxury" :class="activeFilter === 'terdekat' ? 'active' : ''"
                    @click="activeFilter = 'terdekat'; getLocation()">
                    <i class="ph-fill ph-map-pin" x-show="!isLocating"></i>
                    <i class="ph ph-circle-notch animate-spin" x-show="isLocating"></i>
                    Terdekat
                </button>
                <button class="pill-luxury" :class="activeFilter === 'buka' ? 'active' : ''"
                    @click="activeFilter = 'buka'">Buka Sekarang</button>
            </div>
        </header>

        {{-- Cafe List --}}
        <main class="luxury-grid" x-show="filteredCafes().length > 0">
            <template x-for="(cafe, index) in filteredCafes()" :key="cafe.id">
                <a :href="cafe.url" class="luxury-cafe-card animate-up group"
                    :style="'animation-delay: ' + (index * 0.05) + 's'">
                    <div class="luxury-image-wrapper">
                        <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                            :alt="cafe.name" loading="lazy">

                        {{-- Floating Chips --}}
                        <div class="absolute top-2.5 inset-x-2.5 flex justify-between items-start pointer-events-none">
                            {{-- Distance Tag --}}
                            <template x-if="userLat && cafe.distance">
                                <div class="distance-tag-glass-v2">
                                    <i class="ph-fill ph-navigation-arrow"></i>
                                    <span x-text="formatDistance(cafe.distance)"></span>
                                </div>
                            </template>

                            {{-- Status Badge --}}
                            <div class="status-badge-glass-v2">
                                <span :class="cafe.isOpen ? 'bg-emerald-400' : 'bg-rose-400'"
                                    class="h-1.5 w-1.5 rounded-full inline-block"></span>
                                <span class="text-[0.6rem] font-bold uppercase tracking-wider"
                                    x-text="cafe.isOpen ? 'Buka' : 'Tutup'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Card Info Below Image --}}
                    <div class="luxury-card-content">
                        <div class="flex flex-col gap-0.5">
                            <h3 class="luxury-card-title-v2" x-text="cafe.name"></h3>
                            <p class="luxury-card-location-v2">
                                <i class="ph ph-map-pin"></i>
                                <span x-text="cafe.address.split(',')[0]"></span>
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-1 mt-2.5">
                            {{-- Social Media Icons --}}
                            <template x-for="social in (cafe.socialLinks || [])" :key="social.platform">
                                <a :href="social.url" target="_blank" rel="noopener noreferrer" @click.stop
                                    class="luxury-social-btn-v2">
                                    <i :class="{
                                                'ph-bold ph-instagram-logo': social.platform === 'instagram',
                                                'ph-bold ph-tiktok-logo': social.platform === 'tiktok',
                                                'ph-bold ph-facebook-logo': social.platform === 'facebook',
                                                'ph-bold ph-x-logo': social.platform === 'twitter'
                                            }"></i>
                                </a>
                            </template>

                            {{-- Facilities/Tags --}}
                            <template x-for="tag in (cafe.facilities || []).slice(0, 2)">
                                <span class="luxury-tag-v2" x-text="tag"></span>
                            </template>
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
            <p class="text-[0.9rem] font-medium opacity-60">Lokasi ini belum ada cafe yang terdaftar. Coba cari lokasi
                lain!
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.cafesData = {{ Js::from($cafes->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'address' => $c->address,
        'isOpen' => $c->is_open,
        'lat' => $c->latitude,
        'lng' => $c->longitude,
        'facilities' => $c->facilities->pluck('name'),
        'socialLinks' => collect($c->social_links ?? [])->filter(fn($s) => ($s['show'] ?? false) && !empty($s['url']))->values(),
        'image' => $c->image_path ? Storage::url($c->image_path) : null,
        'url' => route('cafes.show', $c)
    ])) }};

        document.addEventListener('alpine:init', () => {
            Alpine.data('exploreLogic', () => ({
                search: '',
                activeFilter: 'semua',
                activeSort: 'relevance',
                showSortMenu: false,
                cafes: [],
                userLat: null,
                userLng: null,
                isLocating: false,
                distanceCache: {},
                activeLetter: null,

                initComponent() {
                    this.cafes = window.cafesData || [];

                    // Coba ambil lokasi otomatis kalau user sebelumnya udah ngizinin
                    if (navigator.permissions) {
                        navigator.permissions.query({ name: 'geolocation' }).then(result => {
                            if (result.state === 'granted') {
                                this.getLocation();
                            }
                        });
                    }
                },

                getLocation() {
                    if (!navigator.geolocation) {
                        alert('Geolocation tidak didukung oleh browser kamu.');
                        return;
                    }

                    this.isLocating = true;
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            if (this.userLat !== position.coords.latitude || this.userLng !== position.coords.longitude) {
                                this.distanceCache = {}; // Clear cache if moved
                            }
                            this.userLat = position.coords.latitude;
                            this.userLng = position.coords.longitude;
                            this.isLocating = false;
                        },
                        (error) => {
                            console.error('Error getting location:', error);
                            this.isLocating = false;
                            this.activeFilter = 'semua';
                            alert('Gagal mengambil lokasi. Pastikan GPS aktif ya!');
                        }, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                    );
                },

                getDistance(lat2, lon2) {
                    if (!this.userLat || !this.userLng || !lat2 || !lon2) return null;

                    const cacheKey = `${lat2},${lon2}`;
                    if (this.distanceCache[cacheKey] !== undefined) return this.distanceCache[cacheKey];

                    try {
                        const R = 6371; // Radius bumi dalam KM
                        const phi1 = this.userLat * Math.PI / 180;
                        const phi2 = lat2 * Math.PI / 180;
                        const deltaLambda = (lon2 - this.userLng) * Math.PI / 180;

                        // Spherical Law of Cosines
                        let cosVal = Math.sin(phi1) * Math.sin(phi2) +
                            Math.cos(phi1) * Math.cos(phi2) * Math.cos(deltaLambda);

                        // Bound cosVal between -1 and 1
                        cosVal = Math.max(-1, Math.min(1, cosVal));

                        const d = Math.acos(cosVal) * R;
                        this.distanceCache[cacheKey] = d;
                        return d;
                    } catch (e) {
                        return null;
                    }
                },

                formatDistance(d) {
                    if (d === null) return '';
                    if (d < 1) return Math.round(d * 1000) + ' m';
                    return d.toFixed(1) + ' km';
                },

                filteredCafes() {
                    let results = [...this.cafes];

                    if (this.search) {
                        const s = this.search.toLowerCase();
                        results = results.filter(c =>
                            c.name.toLowerCase().includes(s) ||
                            c.address.toLowerCase().includes(s)
                        );
                    }

                    if (this.activeFilter === 'buka') {
                        results = results.filter(c => c.isOpen);
                    }

                    if (this.activeLetter) {
                        results = results.filter(c => c.name.charAt(0).toUpperCase() === this.activeLetter);
                    }

                    // Pre-calculate distance
                    if (this.userLat) {
                        results.forEach(c => {
                            c.distance = this.getDistance(c.lat, c.lng);
                        });
                    }

                    if (this.activeFilter === 'terdekat' && this.userLat) {
                        results = results.filter(c => c.distance !== null);
                    }

                    // Determine Final Sorting
                    const sortMode = this.activeSort === 'relevance' && this.activeFilter === 'terdekat' ? 'terdekat' : this.activeSort;

                    if (sortMode === 'terdekat' && this.userLat) {
                        results.sort((a, b) => (a.distance || 0) - (b.distance || 0));
                    } else if (sortMode === 'name_az') {
                        results.sort((a, b) => a.name.localeCompare(b.name));
                    } else if (sortMode === 'name_za') {
                        results.sort((a, b) => b.name.localeCompare(a.name));
                    }

                    return results;
                },

                formatNumber(n) {
                    if (n >= 1000) return (n / 1000).toFixed(1) + 'k';
                    return n;
                }
            }));
        });
    </script>
@endpush