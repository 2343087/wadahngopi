@extends('layouts.app')

@section('title', 'Explore - WadahNgopi.Com')

@section('content')
    <div x-data="exploreLogic()" x-init="initComponent()">
        {{-- Glass Header with Search --}}
        <header class="explore-header-luxury">
            <div class="search-luxury-box">
                <i class="ph ph-magnifying-glass text-xl opacity-40"></i>
                <input type="text" x-model="search" placeholder="Cari cafe idamanmu..." @keyup.escape="search = ''">
            </div>

            <div class="pills-container-luxury">
                <button class="pill-luxury" :class="activeFilter === 'semua' ? 'active' : ''"
                    @click="activeFilter = 'semua'">Semua</button>
                <button class="pill-luxury" :class="activeFilter === 'terdekat' ? 'active' : ''"
                    @click="activeFilter = 'terdekat'; getLocation()">
                    <i class="ph ph-map-pin" x-show="!isLocating"></i>
                    <i class="ph ph-circle-notch animate-spin" x-show="isLocating"></i>
                    Terdekat
                </button>
                <button class="pill-luxury" :class="activeFilter === 'buka' ? 'active' : ''"
                    @click="activeFilter = 'buka'">Buka Sekarang</button>
            </div>
        </header>

        {{-- Cafe List --}}
        <main class="list-luxury" x-show="filteredCafes().length > 0">
            <template x-for="(cafe, index) in filteredCafes()" :key="cafe.id">
                <a :href="cafe.url" class="item-luxury animate-up" :style="'animation-delay: ' + (index * 0.05) + 's'">
                    <div class="relative w-[90px] h-[90px] shrink-0">
                        <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                            :alt="cafe.name" class="item-img-luxury" loading="lazy">
                        <span class="absolute top-1.5 right-1.5 px-2 py-0.5 rounded-lg text-[8px] font-black uppercase"
                            :class="cafe.isOpen ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                            x-text="cafe.isOpen ? 'OPEN' : 'CLOSED'">
                        </span>
                    </div>

                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                        <h3 class="font-black text-[--color-espresso] text-lg line-clamp-1 mb-1" x-text="cafe.name"></h3>
                        <p class="text-slate-400 text-[0.8rem] font-bold line-clamp-1 mb-2.5" x-text="cafe.address"></p>

                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1 font-black text-[--color-amber] text-xs">
                                <i class="ph-fill ph-bolt"></i>
                                <span x-text="formatNumber(cafe.energy || 0)"></span>
                            </span>
                            <div class="flex gap-1.5 overflow-hidden">
                                <template x-if="userLat && activeFilter === 'terdekat'">
                                    <span class="bg-blue-100 text-blue-600 text-[9px] font-black px-2 py-0.5 rounded-md uppercase whitespace-nowrap">
                                        <i class="ph-fill ph-navigation-arrow"></i>
                                        <span x-text="formatDistance(getDistance(cafe.lat, cafe.lng))"></span>
                                    </span>
                                </template>
                                <template x-for="facility in (cafe.facilities || []).slice(0, 2)" :key="facility">
                                    <span
                                        class="bg-slate-100 text-slate-500 text-[9px] font-black px-2 py-0.5 rounded-md uppercase whitespace-nowrap"
                                        x-text="facility"></span>
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
@endsection

@push('scripts')
    <script>
        window.cafesData = {!! $cafes->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'address' => $c->address,
        'energy' => $c->total_energy,
        'isOpen' => $c->is_open,
        'lat' => $c->latitude,
        'lng' => $c->longitude,
        'facilities' => $c->facilities->pluck('name'),
        'image' => $c->image_path,
        'url' => route('cafes.show', $c)
    ])->toJson() !!};

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
                    const dLat = (lat2 - this.userLat) * Math.PI / 180;
                    const dLon = (lon2 - this.userLng) * Math.PI / 180;
                    const a =
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(this.userLat * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c; // Hasil dalam KM
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