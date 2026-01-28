@extends('layouts.app')

@section('title', $cafe->name . ' - WadahNgopi.Com')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;

    $rawImages = collect([$cafe->image_path])
        ->merge($cafe->images ?? [])
        ->filter()
        ->map(function ($img) {
            return str_starts_with($img, 'http') ? $img : Storage::url($img);
        })
        ->values()
        ->all();

    $galleryImages = empty($rawImages)
        ? ['https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=1200']
        : $rawImages;

    // PRECOMPUTE CATEGORIES
    $activeMenus = $cafe->menus()->where('is_active', true)->get();
    $menuCats = $activeMenus->pluck('type')->unique();
    $activeGalleryImages = collect($cafe->menu_images)->filter(fn($img) => ($img['is_active'] ?? true) === true);
    $galleryCats = $activeGalleryImages->pluck('tag')->unique();
    $allCats = $menuCats->merge($galleryCats)->filter()->unique()->values()->all();
    $defaultTab = !empty($allCats) ? $allCats[0] : '';
@endphp

<style>
    /* EMERGENCY PROTECTIVE STYLES - Ensure these load even if app.css fails */
    .menu-section-v4 {
        margin-bottom: 5rem;
    }
    .modern-grid-v4 {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 16px !important;
        width: 100% !important;
    }
    @media (min-width: 768px) {
        .modern-grid-v4 {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 24px !important;
        }
    }
    .luxury-card-v4 {
        background: #ffffff;
        border-radius: 24px;
        padding: 12px;
        border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .luxury-card-v4:active {
        transform: scale(0.97);
    }
    .card-img-wrapper-v4 {
        width: 100%;
        aspect-ratio: 1/1;
        border-radius: 16px;
        overflow: hidden;
        background: #f8fafc;
        position: relative;
    }
    .card-img-v4 {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .price-badge-v4 {
        font-weight: 900;
        color: #D97706; /* color-amber */
        font-size: 0.95rem;
    }
    .name-text-v4 {
        font-weight: 800;
        color: #2C1810; /* color-espresso */
        font-size: 0.85rem;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.6em;
        margin-bottom: 4px;
    }
    .pill-nav-v4 {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 15px;
        margin-bottom: 20px;
        scrollbar-width: none;
    }
    .pill-nav-v4::-webkit-scrollbar { display: none; }
    
    .pill-v4 {
        padding: 10px 20px;
        border-radius: 100px;
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        color: #2C1810;
        font-weight: 800;
        font-size: 0.8rem;
        white-space: nowrap;
        transition: all 0.3s ease;
    }
    .pill-v4.active {
        background: #2C1810;
        color: #fff;
        border-color: #2C1810;
        box-shadow: 0 4px 15px rgba(44, 24, 16, 0.2);
    }
    .placeholder-v4 {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #cbd5e0;
        background: #f1f5f9;
        font-size: 0.7rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }
</style>

    <div class="detail-wrapper" x-data='cafeDetailComponent({
                                    id: {{ $cafe->id }},
                                    images: {!! json_encode($galleryImages) !!},
                                    allCategories: {!! json_encode($allCats) !!},
                                    defaultTab: {!! json_encode($defaultTab) !!}
                                })'>

        {{-- Hero Slider Section --}}
        <div class="detail-hero-luxury" @touchstart="touchStart($event)" @touchend="touchEnd($event)">
            {{-- Nav Overlay --}}
            <nav class="detail-nav-overlay">
                <a href="javascript:history.back()"
                    class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-xl border border-white/30 flex items-center justify-center text-white shadow-lg transition-all active:scale-90 no-underline">
                    <i class="ph ph-arrow-left text-2xl"></i>
                </a>
                <button
                    class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-xl border border-white/30 flex items-center justify-center text-white shadow-lg transition-all active:scale-90"
                    @click="toggleBookmark">
                    <i :class="isBookmarked ? 'ph-fill ph-bookmark-simple' : 'ph ph-bookmark-simple'"
                        :style="isBookmarked ? 'color: #F59E0B' : ''" class="text-2xl"></i>
                </button>
            </nav>

            {{-- Slider Images --}}
            <template x-for="(img, idx) in images" :key="idx">
                <div x-show="currentSlide === idx" x-transition:enter="transition duration-700 ease-out"
                    x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100"
                    class="absolute inset-0">
                    <img :src="img" class="detail-gallery-img" alt="Cafe">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>
                </div>
            </template>
            <div x-show="images.length === 0" class="absolute inset-0 bg-slate-200 flex items-center justify-center">
                <img src="https://placehold.co/1200x800?text={{ urlencode($cafe->name) }}"
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/20"></div>
            </div>

            {{-- Slider Info Overlay --}}
            <div class="absolute bottom-16 left-6 right-6 z-20">
                <span class="status-label-luxury {{ $cafe->is_open ? 'status-open' : 'status-closed' }} mb-3 inline-block">
                    {{ $cafe->is_open ? 'Buka Sekarang' : 'Tutup' }}
                </span>
                <h1 class="text-4xl font-black text-white leading-tight drop-shadow-lg">{{ $cafe->name }}</h1>
                <div class="flex items-center gap-2 text-white/80 font-bold text-sm mt-3">
                    <i class="ph-fill ph-map-pin text-[--color-amber]"></i>
                    <span class="drop-shadow-sm">{{ $cafe->address }}</span>
                </div>
            </div>

            {{-- Dot Indicators --}}
            <div class="absolute bottom-10 left-6 flex gap-1.5 z-30">
                <template x-for="(_, idx) in images" :key="idx">
                    <div class="h-1 rounded-full transition-all duration-500"
                        :class="currentSlide === idx ? 'w-8 bg-white' : 'w-2 bg-white/40'" @click="currentSlide = idx">
                    </div>
                </template>
            </div>
        </div>

        {{-- Main Detail Content --}}
        <div class="detail-content-luxury animate-up">
            <div class="text-slate-600 leading-[1.8] text-[1rem] font-medium mb-10 opacity-90">
                {{ $cafe->description }}
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 mb-12">
                <a href="{{ $cafe->google_maps_url }}" target="_blank"
                    class="flex-1 btn btn-primary py-4 h-14 text-sm shadow-xl">
                    <i class="ph-fill ph-navigation-arrow text-lg"></i>Maps
                </a>
                <a href="https://wa.me/{{ $cafe->whatsapp_number }}" target="_blank"
                    class="flex-1 btn bg-[#10B981] text-white py-4 h-14 text-sm shadow-xl shadow-emerald-500/20">
                    <i class="ph-fill ph-whatsapp-logo text-xl"></i>WhatsApp
                </a>
            </div>

            {{-- Features Section --}}
            <section class="mb-12">
                <h2 class="text-[1.3rem] font-black text-[#2C1810] mb-5 flex items-center gap-3">
                    <i class="ph-fill ph-sparkle text-[#D97706] text-2xl"></i>
                    Fasilitas
                </h2>
                <div class="flex flex-wrap gap-3">
                    @forelse($cafe->facilities as $f)
                        <div
                            class="bg-white border border-slate-100 px-5 py-3 rounded-2xl flex items-center gap-3 transition-all hover:border-slate-200">
                            <i class="{{ $f->icon ?? 'ph ph-check-circle' }} text-[#2C1810] text-xl"></i>
                            <span class="font-bold text-[#2C1810] text-sm">{{ $f->name }}</span>
                        </div>
                    @empty
                        <span class="text-slate-300 italic text-sm">Informasi belum tersedia.</span>
                    @endforelse
                </div>
            </section>

            {{-- Menu Section --}}
            <section class="menu-section-v4">
                <div class="flex flex-col mb-8">
                    <span class="text-[10px] font-black text-[#D97706] tracking-[0.4em] uppercase mb-2">Exclusive Selection</span>
                    <h2 class="text-[2rem] font-black text-[#2C1810] leading-tight">Daftar Menu & Katalog</h2>
                </div>

                {{-- Horizontal Categories --}}
                <div class="pill-nav-v4" x-show="allCategories.length > 0">
                    <template x-for="cat in allCategories" :key="cat">
                        <button class="pill-v4 transition-all" 
                            :class="currentTab === cat ? 'active' : ''"
                            @click="currentTab = cat" x-text="cat.replace(/-/g, ' ')">
                        </button>
                    </template>
                </div>

                {{-- Unified Grid --}}
                <div class="modern-grid-v4">
                    {{-- Gallery Images First --}}
                    @foreach($activeGalleryImages as $img)
                        <div class="luxury-card-v4 group"
                            x-show="currentTab === '{{ $img['tag'] }}'"
                            @click="openLightbox('{{ Storage::url($img['image']) }}')">
                            
                            <div class="card-img-wrapper-v4">
                                <img src="{{ Storage::url($img['image']) }}" class="card-img-v4" alt="{{ $img['tag'] }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                <div class="placeholder-v4" style="display:none">
                                    <i class="ph-bold ph-image text-2xl mb-1"></i>
                                    <span>Preview</span>
                                </div>
                                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-wider text-[#2C1810]">
                                    {{ $img['tag'] }}
                                </div>
                            </div>
                            <div class="py-1">
                                <h4 class="name-text-v4">Lihat Katalog {{ $img['tag'] }}</h4>
                            </div>
                        </div>
                    @endforeach

                    {{-- Individual Menu Items --}}
                    @foreach($activeMenus as $menu)
                        @php
                            $menuImg = $menu->image_path && str_starts_with($menu->image_path, 'http')
                                ? $menu->image_path
                                : ($menu->image_path ? Storage::url($menu->image_path) : null);
                        @endphp
                        <div class="luxury-card-v4 group"
                            x-show="currentTab === '{{ $menu->type }}'" 
                            @click="openLightbox('{{ $menuImg ?? 'https://placehold.co/600x600?text=Menu' }}')">

                            <div class="card-img-wrapper-v4">
                                @if($menuImg)
                                    <img src="{{ $menuImg }}" class="card-img-v4" alt="{{ $menu->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                                    <div class="placeholder-v4" style="display:none">
                                        <i class="ph-bold ph-coffee text-2xl mb-1"></i>
                                        <span>No Photo</span>
                                    </div>
                                @else
                                    <div class="placeholder-v4">
                                        <i class="ph-bold ph-coffee text-2xl mb-1"></i>
                                        <span>No Photo</span>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-1">
                                <h4 class="name-text-v4">{{ $menu->name }}</h4>
                                <div class="price-badge-v4">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty State --}}
                <div x-show="allCategories.length === 0" class="py-20 text-center bg-white rounded-[32px] border border-dashed border-slate-200">
                    <i class="ph ph-coffee text-5xl text-slate-200 mb-4"></i>
                    <p class="text-slate-400 font-bold">Katalog menu segera hadir.</p>
                </div>

                {{-- Simple Lightbox --}}
                <div x-show="lightboxOpen" 
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm"
                    @click="lightboxOpen = false" x-trap.noscroll="lightboxOpen" x-cloak>
                    <div class="relative max-w-4xl w-full" @click.stop>
                        <img :src="lightboxImg" class="w-full h-auto rounded-3xl shadow-2xl">
                        <button @click="lightboxOpen = false" class="absolute -top-4 -right-4 w-12 h-12 rounded-full bg-white text-black shadow-lg flex items-center justify-center">
                            <i class="ph ph-x ph-bold text-2xl"></i>
                        </button>
                    </div>
                </div>
            </section>

            <div class="h-24"></div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cafeDetailComponent', (props) => ({
                currentSlide: 0, currentTab: '', allCategories: [], lightboxOpen: false, lightboxImg: '', images: [], isBookmarked: false, visitorId: '', tx: 0,
                init() {
                    this.allCategories = props.allCategories || []; this.images = props.images || []; this.currentTab = props.defaultTab || '';
                    if (!this.currentTab && this.allCategories.length > 0) { this.currentTab = this.allCategories[0]; }
                    try {
                        let vid = localStorage.getItem('wadah-visitor-id'); if (!vid) { vid = 'visitor-' + Math.random().toString(36).substr(2, 9) + Date.now(); localStorage.setItem('wadah-visitor-id', vid); } this.visitorId = vid;
                        const saved = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'); this.isBookmarked = saved.includes(props.id);
                    } catch (e) { }
                    setInterval(() => { this.nextSlide(); }, 6000);
                },
                openLightbox(img) { if (img) { this.lightboxImg = img; this.lightboxOpen = true; } },
                nextSlide() { const len = this.images.length; if (len > 0) this.currentSlide = (this.currentSlide + 1) % len; },
                prevSlide() { const len = this.images.length; if (len > 0) this.currentSlide = (this.currentSlide - 1 + len) % len; },
                touchStart(e) { this.tx = e.touches[0].clientX; }, touchEnd(e) { const dx = this.tx - e.changedTouches[0].clientX; if (Math.abs(dx) > 40) { dx > 0 ? this.nextSlide() : this.prevSlide(); } },
                toggleBookmark() { let b = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'); this.isBookmarked ? b = b.filter(id => id !== props.id) : b.push(props.id); localStorage.setItem('wadah-bookmarks', JSON.stringify(b)); this.isBookmarked = !this.isBookmarked; }
            }));
        });
    </script>
@endsection