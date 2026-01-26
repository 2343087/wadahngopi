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
                <a :href="cafe.url" class="luxury-cafe-card animate-up" :style="'animation-delay: ' + (index * 0.05) + 's'">
                    <div class="luxury-image-wrapper">
                        <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                            :alt="cafe.name" loading="lazy">
                        <div class="luxury-badge">
                            <span x-text="cafe.isOpen ? 'BUKA' : 'TUTUP'"></span>
                        </div>
                    </div>

                    <div class="luxury-card-info">
                        <h3 x-text="cafe.name"></h3>
                        <p x-text="cafe.address.split(',')[0]"></p>

                        <div class="luxury-meta-row">
                            <template x-if="userLat && activeFilter === 'terdekat'">
                                <span
                                    class="bg-blue-50 text-blue-600 text-[8px] font-extrabold px-1.5 py-0.5 rounded-md flex items-center gap-0.5 border border-blue-100">
                                    <i class="ph-fill ph-navigation-arrow text-[9px]"></i>
                                    <span x-text="formatDistance(getDistance(cafe.lat, cafe.lng))"></span>
                                </span>
                            </template>
                            <template x-for="facility in (cafe.facilities || []).slice(0, 2)" :key="facility">
                                <span
                                    class="bg-slate-50 text-slate-500 text-[8px] font-extrabold px-1.5 py-0.5 rounded-md border border-slate-100"
                                    x-text="facility"></span>
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