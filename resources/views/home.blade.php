@extends('layouts.app')

@section('title', 'WadahNgopi.Com ☕')

@section('content')
    <style>
        .hero {
            padding: 50px 20px 40px;
            text-align: center;
            background: radial-gradient(circle at top, var(--color-cream-dark) 0%, var(--color-cream) 100%);
            border-bottom-left-radius: 45px;
            border-bottom-right-radius: 45px;
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 0%, rgba(111, 78, 55, 0.08), transparent 70%);
            pointer-events: none;
        }

        .hero h1 {
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 8px;
            letter-spacing: -1.5px;
            background: linear-gradient(135deg, var(--color-coffee-dark), var(--color-coffee));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            color: var(--color-text-muted);
            font-size: 0.95rem;
            margin-bottom: 28px;
            font-weight: 500;
            opacity: 0.8;
        }

        .search-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .search-box {
            background: white;
            padding: 14px 20px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 12px 35px rgba(111, 78, 55, 0.08);
            border: 1px solid rgba(111, 78, 55, 0.05);
            transition: var(--transition-smooth);
        }

        .search-box:focus-within {
            box-shadow: 0 15px 45px rgba(111, 78, 55, 0.15);
            border-color: var(--color-coffee-light);
            transform: translateY(-2px);
        }

        .search-box i {
            color: var(--color-coffee);
            font-size: 1.1rem;
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

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 40px 20px 18px;
        }

        .section-title h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            letter-spacing: -0.5px;
        }

        .see-all {
            color: var(--color-coffee);
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            padding: 7px 16px;
            background: rgba(111, 78, 55, 0.06);
            border-radius: 14px;
            transition: var(--transition-smooth);
        }

        .see-all:active {
            transform: scale(0.95);
            background: rgba(111, 78, 55, 0.1);
        }

        .cafe-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            padding: 0 18px 40px;
        }

        @media (max-width: 360px) {
            .cafe-grid {
                gap: 10px;
                padding: 0 15px 40px;
            }
        }

        .cafe-card {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 26px;
            padding: 9px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0, 0, 0, 0.02);
            transition: var(--transition-smooth);
            height: 100%;
            position: relative;
        }

        .cafe-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
            border-color: rgba(111, 78, 55, 0.08);
        }

        .cafe-card:active {
            transform: scale(0.96);
        }

        .cafe-image-container {
            position: relative;
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .cafe-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cafe-card:hover .cafe-image {
            transform: scale(1.1);
        }

        .cafe-info {
            padding: 4px 6px 8px;
        }

        .cafe-info h3 {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 5px;
        }

        .cafe-meta {
            display: flex;
            flex-direction: column;
            gap: 3px;
            font-size: 0.75rem;
            color: var(--color-text-muted);
            font-weight: 500;
        }

        .rating {
            color: #F59E0B;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .wifi-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.6rem;
            font-weight: 800;
            color: var(--color-earth-green);
            text-transform: uppercase;
            z-index: 3;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div x-data="{ 
                        search: '',
                        cafes: {{ $cafes->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'address' => $c->address, 'rating' => $c->rating, 'has_wifi' => $c->has_wifi, 'image' => $c->image_path, 'url' => route('cafes.show', $c)])->toJson() }},
                        get filteredCafes() {
                            if (!this.search) return this.cafes;
                            return this.cafes.filter(c => 
                                c.name.toLowerCase().includes(this.search.toLowerCase()) || 
                                c.address.toLowerCase().includes(this.search.toLowerCase())
                            );
                        }
                    }">
        <div class="hero">
            <h1>WadahNgopi.Com</h1>
            <p>Temukan cafe terbaik untuk harimu.</p>

            <div class="search-container">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" x-model="search" placeholder="Cari nama atau lokasi cafe..."
                        @keyup.escape="search = ''">
                </div>
            </div>
        </div>

        <div class="section-title">
            <h2 x-text="search ? 'Hasil Pencarian' : 'Rekomendasi Cafe'"></h2>
            <a href="{{ route('explore') }}" class="see-all">Lihat Semua</a>
        </div>

        <div class="cafe-grid" x-show="filteredCafes.length > 0">
            <template x-for="(cafe, index) in filteredCafes" :key="cafe.id">
                <a :href="cafe.url" class="cafe-card animate-up" :style="'animation-delay: ' + (index * 0.05) + 's'">
                    <div class="cafe-image-container">
                        <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                            :alt="cafe.name" class="cafe-image" loading="lazy">
                        <template x-if="cafe.has_wifi">
                            <span class="wifi-badge">WiFi</span>
                        </template>
                    </div>
                    <div class="cafe-info">
                        <h3 class="line-clamp-1" x-text="cafe.name"></h3>
                        <div class="cafe-meta">
                            <div class="rating">
                                <i class="bi bi-star-fill"></i> <span x-text="cafe.rating || '0.0'"></span>
                            </div>
                            <span class="address line-clamp-1" x-text="cafe.address.split(',')[0]"></span>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        <div x-show="filteredCafes.length === 0" x-cloak
            style="text-align: center; padding: 80px 20px; color: var(--color-text-muted);">
            <i class="bi bi-search" style="font-size: 3.5rem; opacity: 0.1; margin-bottom: 20px; display: block;"></i>
            <h3 style="color: var(--color-coffee-dark); font-weight: 800; font-size: 1.1rem; margin-bottom: 8px;">Cafe Tidak
                Ditemukan</h3>
            <p style="font-size: 0.9rem; opacity: 0.7;">Coba cari dengan kata kunci lain.</p>
        </div>
    </div>
@endsection