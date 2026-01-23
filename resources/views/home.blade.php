@extends('layouts.app')

@section('title', 'WadahNgopi.Com ☕')

@section('content')
    <div x-data="homeLogic()" x-init="initComponent()">
        <div class="hero-fancy">
            <h1 class="hero-luxury-title">WadahNgopi.Com</h1>
            <p class="text-[--color-text-muted] text-[1.1rem] font-semibold opacity-80">Temukan Tempat Nongkrong Terbaik Buanmu.</p>

            <div class="search-luxury-container">
                <div class="search-luxury-box">
                    <i class="ph ph-magnifying-glass text-xl opacity-40"></i>
                    <input type="text" x-model="search" placeholder="Cari nama cafe atau lokasi..."
                        @keyup.escape="search = ''">
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center px-6 pt-10 pb-4">
            <h2 class="text-[1.3rem] font-black text-[--color-espresso]">Rekomendasi</h2>
            <a href="{{ route('explore') }}"
                class="text-[0.8rem] font-bold text-[--color-coffee] bg-[#fff] px-4 py-2 rounded-xl shadow-sm border border-black/5 no-underline transition-all active:scale-95">Lihat
                Semua</a>
        </div>

        <div class="luxury-grid" x-show="filteredCafes().length > 0">
            <template x-for="(cafe, index) in filteredCafes()" :key="cafe.id">
                <a :href="cafe.url" class="luxury-cafe-card animate-up" :style="'animation-delay: ' + (index * 0.1) + 's'">
                    <div class="luxury-image-wrapper">
                        <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'"
                            :alt="cafe.name" loading="lazy">
                        <div class="luxury-badge">
                            <span x-text="cafe.isOpen ? 'BUKA' : 'TUTUP'"></span>
                        </div>
                    </div>
                    <div class="luxury-card-info">
                        <h3 x-text="cafe.name"></h3>
                        <div class="luxury-meta-row">
                            <div class="energy-tag">
                                <i class="ph-fill ph-bolt"></i> <span x-text="formatNumber(cafe.energy || 0)"></span>
                            </div>
                            <span class="opacity-40">•</span>
                            <span x-text="cafe.address.split(',')[0]"></span>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        <div x-show="filteredCafes().length === 0" x-cloak
            style="text-align: center; padding: 100px 40px; color: var(--color-text-muted);">
            <i class="ph ph-mask-sad text-[4rem] opacity-20 block mb-6 mx-auto"></i>
            <h3 class="text-[1.2rem] font-black text-[--color-espresso] mb-2">Waduh, Gak Ketemu!</h3>
            <p class="text-[0.9rem] font-medium opacity-60">Coba cari dengan kata kunci lain atau cek lokasi sekitarmu.</p>
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
        'image' => $c->image_path,
        'url' => route('cafes.show', $c)
    ])->toJson() !!};

        document.addEventListener('alpine:init', () => {
            Alpine.data('homeLogic', () => ({
                search: '',
                cafes: [],

                initComponent() {
                    this.cafes = window.cafesData || [];
                },

                filteredCafes() {
                    if (!this.search) return this.cafes;
                    const s = this.search.toLowerCase();
                    return this.cafes.filter(c =>
                        c.name.toLowerCase().includes(s) ||
                        c.address.toLowerCase().includes(s)
                    );
                },

                formatNumber(n) {
                    if (n >= 1000) return (n / 1000).toFixed(1) + 'k';
                    return n;
                }
            }));
        });
    </script>
@endpush