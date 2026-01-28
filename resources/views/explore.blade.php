@extends('layouts.app')

@section('title', 'Explore - WadahNgopi.Com')

@section('content')
    <div x-data="exploreLogic()" x-init="initComponent()" class="flex flex-col flex-1">
        {{-- Glass Header with Search --}}
        <header class="px-6 pt-10 pb-6 flex flex-col gap-6">
            <h1 class="hero-luxury-title">Explore <span class="italic text-[--color-coffee]">Cafe</span></h1>

            <div class="search-luxury-box !mt-0">
                <i class="ph-bold ph-magnifying-glass text-xl opacity-30"></i>
                <input type="text" x-model="search" placeholder="Cari cafe idamanmu..." @keyup.escape="search = ''">
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
                    <div class="luxury-image-wrapper relative overflow-hidden rounded-[24px]">
                        <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                            :alt="cafe.name" loading="lazy"
                            class="w-full h-[220px] object-cover transition-transform duration-700 group-hover:scale-110">

                        {{-- Top Right Status Badge (Glassmorphism) --}}
                        <div class="absolute top-4 right-4 z-20">
                            <div
                                class="px-3 py-1.5 bg-black/40 backdrop-blur-md border border-white/20 rounded-full flex items-center gap-2 shadow-lg">
                                {{-- Pulsing Dot Indicator --}}
                                <div class="relative flex h-2 w-2">
                                    <span :class="cafe.isOpen ? 'bg-emerald-400' : 'bg-rose-400'"
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"></span>
                                    <span :class="cafe.isOpen ? 'bg-emerald-500' : 'bg-rose-500'"
                                        class="relative inline-flex rounded-full h-2 w-2"></span>
                                </div>
                                <span class="text-white text-[0.65rem] font-black tracking-widest uppercase"
                                    x-text="cafe.isOpen ? 'Buka' : 'Tutup'"></span>
                            </div>
                        </div>

                        {{-- Cinematic Floating Info: No More Boxes --}}
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent flex flex-col justify-end p-5 h-full">
                            <div class="flex flex-col gap-2">
                                <div>
                                    <h3 class="text-white text-[1.15rem] font-black leading-tight mb-1 drop-shadow-md"
                                        x-text="cafe.name"></h3>
                                    <p
                                        class="text-gray-300/90 font-bold text-[0.7rem] flex items-center gap-1 drop-shadow-sm">
                                        <i class="ph-bold ph-map-pin text-[--color-amber]"></i>
                                        <span x-text="cafe.address.split(',')[0]"></span>
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    <template x-if="userLat && activeFilter === 'terdekat'">
                                        <span
                                            class="bg-white/10 backdrop-blur-md text-white text-[8px] font-black px-2 py-0.5 rounded-md border border-white/10 flex items-center gap-1">
                                            <i class="ph-fill ph-navigation-arrow"></i>
                                            <span x-text="formatDistance(getDistance(cafe.lat, cafe.lng))"></span>
                                        </span>
                                    </template>
                                    <template x-for="facility in (cafe.facilities || []).slice(0, 2)" :key="facility">
                                        <span
                                            class="bg-white/5 backdrop-blur-md text-gray-200 text-[8px] font-black px-2 py-0.5 rounded-md border border-white/10"
                                            x-text="facility"></span>
                                    </template>
                                </div>

                                {{-- Social Media Icons --}}
                                <div class="flex gap-2 mt-2" x-show="cafe.socialLinks && cafe.socialLinks.length > 0">
                                    <template x-for="social in (cafe.socialLinks || [])" :key="social.platform">
                                        <a :href="social.url" target="_blank" rel="noopener noreferrer"
                                            @click.stop
                                            class="w-6 h-6 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center hover:bg-white/40 hover:scale-110 transition-all"
                                            :title="social.platform">
                                            <i class="text-white text-[0.7rem]"
                                                :class="{
                                                    'ph-fill ph-instagram-logo': social.platform === 'instagram',
                                                    'ph-fill ph-tiktok-logo': social.platform === 'tiktok',
                                                    'ph-fill ph-facebook-logo': social.platform === 'facebook',
                                                    'ph-fill ph-x-logo': social.platform === 'twitter'
                                                }"></i>
                                        </a>
                                    </template>
                                </div>
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
                cafes: [],
                userLat: null,
                userLng: null,
                isLocating: false,

                initComponent() {
                    this.cafes = window.cafesData || [];
                },

                getLocation() {
                    if (!navigator.geolocation) {
                        alert('Geolocation tidak didukung oleh browser kamu.');
                        return;
                    }

                    this.isLocating = true;
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
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

                    const R = 6371; // Radius bumi dalam KM
                    const phi1 = this.userLat * Math.PI / 180;
                    const phi2 = lat2 * Math.PI / 180;
                    const deltaLambda = (lon2 - this.userLng) * Math.PI / 180;

                    // Spherical Law of Cosines (Consistent with Backend)
                    const d = Math.acos(Math.sin(phi1) * Math.sin(phi2) +
                        Math.cos(phi1) * Math.cos(phi2) * Math.cos(deltaLambda)) * R;

                    return d; // Hasil dalam KM
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

                    // Handling Distance Sorting
                    if (this.activeFilter === 'terdekat' && this.userLat) {
                        results.forEach(c => {
                            c.distance = this.getDistance(c.lat, c.lng);
                        });
                        results = results.filter(c => c.distance !== null);
                        results.sort((a, b) => a.distance - b.distance);
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