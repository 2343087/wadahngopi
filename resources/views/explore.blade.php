@extends('layouts.app')

@section('title', 'Explore - WadahNgopi.Com')

@section('content')
    <style>
        .explore-header {
            position: sticky;
            top: 0;
            background: rgba(253, 251, 249, 0.92);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            z-index: 100;
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(111, 78, 55, 0.05);
        }

        .search-box {
            background: white;
            padding: 12px 18px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 25px rgba(111, 78, 55, 0.06);
            border: 1px solid rgba(111, 78, 55, 0.05);
            margin-bottom: 14px;
            transition: var(--transition-smooth);
        }

        .search-box:focus-within {
            box-shadow: 0 12px 30px rgba(111, 78, 55, 0.1);
            border-color: var(--color-coffee-light);
        }

        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 0.95rem;
            font-family: inherit;
            background: transparent;
            color: var(--color-text);
            font-weight: 500;
        }

        .filter-pills {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 5px;
            scrollbar-width: none;
        }

        .filter-pills::-webkit-scrollbar {
            display: none;
        }

        .pill {
            padding: 8px 18px;
            border-radius: 14px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            border: 1px solid rgba(111, 78, 55, 0.08);
            background: white;
            color: var(--color-coffee-dark);
            transition: var(--transition-smooth);
        }

        .pill.active {
            background: var(--color-coffee-dark);
            color: white;
            border-color: var(--color-coffee-dark);
            box-shadow: 0 6px 15px rgba(62, 39, 35, 0.15);
        }

        .cafe-list {
            padding: 18px 20px 40px;
        }

        .cafe-item {
            display: flex;
            gap: 16px;
            padding: 14px;
            background: white;
            border-radius: 26px;
            margin-bottom: 14px;
            text-decoration: none;
            color: inherit;
            border: 1px solid rgba(0, 0, 0, 0.02);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .cafe-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: rgba(111, 78, 55, 0.06);
        }

        .cafe-item:active {
            transform: scale(0.97);
        }

        .cafe-item-image {
            width: 90px;
            height: 90px;
            border-radius: 18px;
            object-fit: cover;
        }

        .cafe-item-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .cafe-item-info h3 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 4px;
        }

        .cafe-item-info p {
            font-size: 0.8rem;
            color: var(--color-text-muted);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .badge-row {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .wifi-tag {
            font-size: 0.6rem;
            font-weight: 800;
            color: var(--color-earth-green);
            background: rgba(74, 93, 35, 0.08);
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .rating-tag {
            font-size: 0.85rem;
            font-weight: 800;
            color: #F59E0B;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .distance-tag {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--color-coffee);
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 4px;
            opacity: 0.8;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div x-data="{ 
                search: '',
                activeFilter: 'semua',
                userLocation: null,
                sortByDistance: false,
                cafes: {{ $cafes->map(fn($c) => [
        'id' => $c->id,
        'name' => $c->name,
        'address' => $c->address,
        'rating' => $c->rating,
        'has_wifi' => $c->has_wifi,
        'image' => $c->image_path,
        'url' => route('cafes.show', $c),
        'lat' => $c->latitude,
        'lng' => $c->longitude
    ])->toJson() }},

                get filteredCafes() {
                    let filtered = this.cafes.filter(c => {
                        const matchesSearch = c.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                            c.address.toLowerCase().includes(this.search.toLowerCase());
                        const matchesWifi = this.activeFilter !== 'wifi' || c.has_wifi;
                        return matchesSearch && matchesWifi;
                    });

                    if (this.sortByDistance && this.userLocation) {
                        return filtered.sort((a, b) => {
                            const distA = this.calculateDistance(this.userLocation.lat, this.userLocation.lng, a.lat, a.lng);
                            const distB = this.calculateDistance(this.userLocation.lat, this.userLocation.lng, b.lat, b.lng);
                            return distA - distB;
                        });
                    }
                    return filtered;
                },

                toggleNearby() {
                    if (!this.sortByDistance) {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    this.userLocation = {
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude
                                    };
                                    this.sortByDistance = true;
                                    this.activeFilter = 'terdekat';
                                },
                                (error) => {
                                    alert('Gagal mendapatkan lokasi. Pastikan izin lokasi aktif.');
                                }
                            );
                        } else {
                            alert('Browser Anda tidak mendukung geolocation.');
                        }
                    } else {
                        this.sortByDistance = false;
                        this.activeFilter = 'semua';
                    }
                },

                calculateDistance(lat1, lon1, lat2, lon2) {
                    if (!lat1 || !lon1 || !lat2 || !lon2) return Infinity;
                    const R = 6371;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                            Math.sin(dLon/2) * Math.sin(dLon/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return R * c;
                },

                formattedDistance(cafe) {
                    if (!this.userLocation || !cafe.lat || !cafe.lng) return null;
                    const dist = this.calculateDistance(this.userLocation.lat, this.userLocation.lng, cafe.lat, cafe.lng);
                    return dist < 1 ? Math.round(dist * 1000) + ' m' : dist.toFixed(1) + ' km';
                }
            }">
        <div class="explore-header">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" x-model="search" placeholder="Cari cafe idamanmu...">
            </div>

            <div class="filter-pills">
                <div class="pill" :class="activeFilter === 'semua' ? 'active' : ''"
                    @click="activeFilter = 'semua'; sortByDistance = false">Semua</div>
                <div class="pill" :class="activeFilter === 'terdekat' ? 'active' : ''" @click="toggleNearby">Terdekat</div>
                <div class="pill" :class="activeFilter === 'wifi' ? 'active' : ''"
                    @click="activeFilter = 'wifi'; sortByDistance = false">
                    <i class="bi bi-wifi"></i> WiFi Tersedia
                </div>
            </div>
        </div>

        <div class="cafe-list" x-show="filteredCafes.length > 0">
            <template x-for="(cafe, index) in filteredCafes" :key="cafe.id">
                <a :href="cafe.url" class="cafe-item animate-up" :style="'animation-delay: ' + (index * 0.05) + 's'">
                    <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                        :alt="cafe.name" class="cafe-item-image" loading="lazy">
                    <div class="cafe-item-info">
                        <h3 class="line-clamp-1" x-text="cafe.name"></h3>
                        <p class="line-clamp-1" x-text="cafe.address"></p>
                        <div class="badge-row">
                            <span class="rating-tag">
                                <i class="bi bi-star-fill"></i> <span x-text="cafe.rating || '0.0'"></span>
                            </span>
                            <template x-if="cafe.has_wifi">
                                <span class="wifi-tag">Free WiFi</span>
                            </template>
                            <template x-if="userLocation">
                                <span class="distance-tag">
                                    <i class="bi bi-geo-alt"></i> <span x-text="formattedDistance(cafe)"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        <div x-show="filteredCafes.length === 0" x-cloak
            style="text-align: center; padding: 100px 20px; color: var(--color-text-muted);">
            <i class="bi bi-cup-hot" style="font-size: 4rem; opacity: 0.1;"></i>
            <p style="margin-top: 15px; font-weight: 500;">Oopps! Cafe tidak ditemukan.</p>
            <button @click="search = ''; filterWifi = false" class="btn"
                style="margin-top: 15px; color: var(--color-coffee);">Reset Filter</button>
        </div>
    </div>
@endsection