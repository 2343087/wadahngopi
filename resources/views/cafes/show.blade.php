@extends('layouts.app')

@section('title', $cafe->name . ' - WadahNgopi.Com')
@section('meta_description', Str::limit($cafe->description, 155) ?: 'Temukan ' . $cafe->name . ' di ' . ($cafe->address ?? 'Kalimantan') . '. Cafe dengan fasilitas lengkap.')
@section('og_title', $cafe->name . ' - WadahNgopi')
@section('og_description', Str::limit($cafe->description, 100) ?: 'Cafe nyaman di ' . ($cafe->address ?? 'Kalimantan'))
@section('og_image', $cafe->image_path ? Storage::url($cafe->image_path) : asset('wadahicon.png'))

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
        $menuImages = $cafe->menu_images ?? [];
        $activeGalleryImages = collect($menuImages)->filter(fn($img) => is_array($img) && ($img['is_active'] ?? true) === true);
        $allCats = $activeGalleryImages->pluck('tag')->unique()->filter()->values()->all();
        $defaultTab = !empty($allCats) ? $allCats[0] : '';
    @endphp

    <style>
        /* MODERN CLEAN DESIGN SYSTEM */
        .menu-section-v4 {
            margin-bottom: 5rem;
            padding-top: 2rem;
        }

        .section-header-v5 {
            margin-bottom: 2.5rem;
            position: relative;
        }

        .section-title-v5 {
            font-size: 2.25rem;
            font-weight: 900;
            color: #2C1810;
            letter-spacing: -0.02em;
            line-height: 1.1;
        }

        /* Utils */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="detail-wrapper" x-data="cafeDetailComponent({
                                                    id: {{ $cafe->id }},
                                                    images: {{ json_encode($galleryImages) }},
                                                    allCategories: {{ json_encode($allCats) }},
                                                    defaultTab: {{ json_encode($defaultTab) }},
                                                    menuImages: {{ json_encode($activeGalleryImages->map(fn($img) => ['url' => Storage::url($img['image']), 'tag' => $img['tag']])->values()) }}
                                                })">

        {{-- Hero Slider Section --}}
        <div class="detail-hero-luxury" @touchstart="touchStart($event)" @touchend="touchEnd($event)">
            {{-- Nav Overlay --}}
            <nav class="detail-nav-overlay group">
                <a href="javascript:history.back()"
                    class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-xl border border-white/20 flex items-center justify-center text-white shadow-glass transition-all active:scale-90 no-underline hover:bg-white/30">
                    <i class="ph ph-arrow-left text-2xl"></i>
                </a>

                <div class="flex items-center gap-2">
                    <button
                        class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-xl border border-white/20 flex items-center justify-center text-white shadow-glass transition-all active:scale-90 hover:bg-white/30"
                        @click="toggleBookmark">
                        <i :class="isBookmarked ? 'ph-fill ph-bookmark-simple' : 'ph ph-bookmark-simple'"
                            :style="isBookmarked ? 'color: #F59E0B' : ''" class="text-2xl"></i>
                    </button>
                    <button
                        class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-xl border border-white/20 flex items-center justify-center text-white shadow-glass transition-all active:scale-90 hover:bg-white/30"
                        @click="shareCafe">
                        <i class="ph ph-share-network text-2xl"></i>
                    </button>
                </div>
            </nav>

            {{-- Slider Images --}}
            <template x-for="(img, idx) in images" :key="idx">
                <div x-show="currentSlide === idx" class="absolute inset-0 z-0">
                    <img :src="img" class="detail-gallery-img w-full h-full object-cover" alt="Cafe">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>
                </div>
            </template>
            <div x-show="images.length === 0" class="absolute inset-0 z-0 bg-slate-200 flex items-center justify-center">
                <img src="https://placehold.co/1200x800?text={{ urlencode($cafe->name) }}"
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/20"></div>
            </div>

            {{-- Slider Info Overlay --}}
            <div class="absolute bottom-16 left-6 right-6 z-20">
                <livewire:cafe-detail :cafe-id="$cafe->id" />
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
            <div class="mb-6">
                <h1 class="text-3xl font-black text-[#2C1810] leading-tight mb-2">{{ $cafe->name }}</h1>
                <div class="flex items-start gap-2 text-slate-500 font-medium text-sm">
                    <i class="ph-fill ph-map-pin text-amber-600 text-lg mt-0.5"></i>
                    <span>{{ $cafe->address }}</span>
                </div>
            </div>
            <div class="text-slate-600 leading-[1.8] text-[1rem] font-medium mb-10 opacity-90">
                {!! $cafe->description !!}
            </div>

            {{-- Kontak & Sosmed Section --}}
            @php
                // Filter active social links - handle various boolean representations
                $activeSocialLinks = collect($cafe->social_links ?? [])->filter(function ($link) {
                    $isVisible = isset($link['show']) && filter_var($link['show'], FILTER_VALIDATE_BOOLEAN);
                    $hasUrl = !empty($link['url']);
                    return $isVisible && $hasUrl;
                });
                $hasGoogleMaps = !empty($cafe->google_maps_url);
                $hasWhatsApp = !empty($cafe->whatsapp_number);
            @endphp

            @if($hasGoogleMaps || $hasWhatsApp || $activeSocialLinks->isNotEmpty())
                <section class="mb-8">
                    {{-- Section Title --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <i class="ph-bold ph-chats-circle text-amber-600 text-lg"></i>
                            <h3 class="text-espresso text-sm font-bold uppercase tracking-wider">Hubungi & Follow</h3>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    @if($hasGoogleMaps || $hasWhatsApp)
                        <div class="flex items-center gap-3 mb-5">
                            @if($hasGoogleMaps)
                                <a href="{{ e($cafe->google_maps_url) }}" target="_blank" rel="noopener noreferrer"
                                    class="flex-1 flex items-center gap-3 p-3 rounded-2xl border border-espresso/5 bg-white shadow-soft active:scale-[0.98] transition-all no-underline hover:border-amber/30">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-amber-50 rounded-xl">
                                        <i class="ph-fill ph-map-pin text-amber-600 text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-espresso text-[0.85rem] font-bold block leading-none mb-1">Lokasi</span>
                                        <span class="text-text-muted text-[10px] font-semibold">Google Maps</span>
                                    </div>
                                </a>
                            @endif

                            @if($hasWhatsApp)
                                @php
                                    $rawWa = preg_replace('/[^0-9]/', '', $cafe->whatsapp_number);
                                    if (str_starts_with($rawWa, '08')) {
                                        $rawWa = '62' . substr($rawWa, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $rawWa }}" target="_blank" rel="noopener noreferrer"
                                    class="flex-1 flex items-center gap-3 p-3 rounded-2xl border border-espresso/5 bg-white shadow-soft active:scale-[0.98] transition-all no-underline hover:border-green-500/30">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-green-50 rounded-xl">
                                        <i class="ph-fill ph-whatsapp-logo text-green-600 text-lg"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-espresso text-[0.85rem] font-bold block leading-none mb-1">Chat</span>
                                        <span class="text-text-muted text-[10px] font-semibold">WhatsApp</span>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Social Media Links --}}
                    @if($activeSocialLinks->isNotEmpty())
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($activeSocialLinks as $link)
                                @php
                                    $platform = strtolower($link['platform'] ?? '');
                                    $iconClass = match ($platform) {
                                        'instagram' => 'ph-fill ph-instagram-logo',
                                        'tiktok' => 'ph-fill ph-tiktok-logo',
                                        'facebook' => 'ph-fill ph-facebook-logo',
                                        'x', 'twitter' => 'ph-fill ph-x-logo',
                                        'youtube' => 'ph-fill ph-youtube-logo',
                                        default => 'ph-fill ph-globe'
                                    };
                                    $label = match ($platform) {
                                        'instagram' => 'Instagram',
                                        'tiktok' => 'TikTok',
                                        'facebook' => 'Facebook',
                                        'x', 'twitter' => 'X',
                                        'youtube' => 'YouTube',
                                        default => 'Web'
                                    };
                                @endphp
                                <a href="{{ e($link['url']) }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-espresso/5 bg-white shadow-soft text-espresso active:scale-95 transition-all no-underline hover:bg-espresso hover:text-white group/social">
                                    <i
                                        class="{{ $iconClass }} text-sm text-amber-600 group-hover/social:text-white transition-colors"></i>
                                    <span class="text-[0.7rem] font-bold uppercase tracking-wider">{{ $label }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            {{-- Features Section --}}
            <section class="mb-12">
                <h2 class="text-sm font-bold text-espresso uppercase tracking-widest mb-5 flex items-center gap-2.5">
                    <i class="ph-fill ph-sparkle text-amber-600 text-lg"></i>
                    Fasilitas Tersedia
                </h2>
                <div class="flex flex-wrap gap-2.5">
                    @forelse($cafe->facilities as $f)
                        <div
                            class="bg-white border border-espresso/5 px-4 py-2.5 rounded-xl flex items-center gap-2.5 transition-all shadow-soft group/fac">
                            <i
                                class="{{ $f->icon ?? 'ph ph-check-circle' }} text-espresso/60 text-lg group-hover/fac:text-amber-600 transition-colors"></i>
                            <span class="font-bold text-espresso text-[0.8rem]">{{ $f->name }}</span>
                        </div>
                    @empty
                        <span class="text-slate-300 italic text-sm">Informasi fasilitas belum tersedia.</span>
                    @endforelse
                </div>
            </section>

            {{-- Hero-Style Menu Section --}}
            <section x-data="{ 
                                                                                            activeMenuIdx: 0,
                                                                                            activeLightboxIdx: null,
                                                                                            touchStartX: 0,
                                                                                            lastWheelTime: 0,

                                                                                            scrollTo(idx) {
                                                                                                const el = this.$refs.menuSlider;
                                                                                                const item = el.children[idx];
                                                                                                el.scrollTo({ left: item.offsetLeft - (el.offsetWidth - item.offsetWidth) / 2, behavior: 'smooth' });
                                                                                            },
                                                                                            updateIdx() {
                                                                                                const el = this.$refs.menuSlider;
                                                                                                const center = el.scrollLeft + el.offsetWidth / 2;
                                                                                                let minDiff = Infinity;
                                                                                                let closestIdx = 0;
                                                                                                Array.from(el.children).forEach((child, i) => {
                                                                                                    const diff = Math.abs((child.offsetLeft + child.offsetWidth / 2) - center);
                                                                                                    if(diff < minDiff) { minDiff = diff; closestIdx = i; }
                                                                                                });
                                                                                                this.activeMenuIdx = closestIdx;
                                                                                            },
                                                                                            nextLightbox() {
                                                                                                const len = this.menuImages.length;
                                                                                                if(this.activeLightboxIdx !== null) this.activeLightboxIdx = (this.activeLightboxIdx + 1) % len;
                                                                                            },
                                                                                            prevLightbox() {
                                                                                                const len = this.menuImages.length;
                                                                                                if(this.activeLightboxIdx !== null) this.activeLightboxIdx = (this.activeLightboxIdx - 1 + len) % len;
                                                                                            },
                                                                                            handleWheel(e) {
                                                                                                const now = Date.now();
                                                                                                if (now - this.lastWheelTime < 250) return; // Cooldown biar gak lompat-lompat
                                                                                                if (Math.abs(e.deltaY) < 30) return; // Abaikan scroll halus

                                                                                                if (e.deltaY > 0) this.nextLightbox();
                                                                                                else this.prevLightbox();

                                                                                                this.lastWheelTime = now;
                                                                                            }
                                                                                        }" class="relative">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-black text-[#2C1810] tracking-tight">Daftar Menu</h2>
                    <div class="flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-full">
                        <i class="ph ph-image text-slate-400"></i>
                        <span
                            class="text-[0.7rem] font-black text-slate-500 uppercase tracking-tighter">{{ $activeGalleryImages->count() }}
                            Foto</span>
                    </div>
                </div>

                @if($activeGalleryImages->count() > 0)
                    {{-- Large Hero-Style Menu Slider --}}
                    <div class="relative group">
                        <div x-ref="menuSlider" @scroll.debounce.50ms="updateIdx()"
                            class="flex gap-4 overflow-x-auto pb-8 scrollbar-hide snap-x snap-mandatory -mx-6 px-6 md:grid md:grid-cols-3 lg:grid-cols-4 md:gap-6 md:overflow-visible md:pb-0 md:px-0 md:mx-0">
                            @foreach($activeGalleryImages as $index => $img)
                                <div class="flex-none w-[80%] md:w-auto snap-center cursor-pointer group/item"
                                    @click="activeLightboxIdx = {{ $index }}">
                                    <div
                                        class="relative aspect-[3/4] rounded-[24px] overflow-hidden shadow-lg border border-white/20 bg-white/5 backdrop-blur-sm transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">

                                        {{-- Image --}}
                                        <img src="{{ Storage::url($img['image']) }}"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover/item:scale-110"
                                            alt="{{ $img['tag'] }}" loading="lazy">

                                        {{-- Glass Overlay --}}
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-80 transition-opacity duration-300 group-hover/item:opacity-60">
                                        </div>

                                        {{-- Floating Tag Badge --}}
                                        @if(!empty($img['tag']))
                                            <div class="absolute bottom-4 left-4">
                                                <div
                                                    class="inline-flex items-center gap-1.5 bg-white/10 backdrop-blur-md border border-white/20 px-3 py-1.5 rounded-xl shadow-sm">
                                                    <i class="ph-fill ph-tag text-amber-400 text-xs"></i>
                                                    <span
                                                        class="text-[0.65rem] font-bold text-white uppercase tracking-wider">{{ $img['tag'] }}</span>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- View Icon (Hover State) --}}
                                        <div
                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/item:opacity-100 transition-opacity duration-300">
                                            <div
                                                class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center border border-white/30 text-white shadow-lg transform scale-75 group-hover/item:scale-100 transition-transform">
                                                <i class="ph-bold ph-arrows-out-simple text-xl"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Lightbox (Hero Style with Slider) --}}
                    <div x-show="activeLightboxIdx !== null" x-transition.opacity.duration.300ms
                        class="fixed inset-0 z-[10000] bg-black/95 backdrop-blur-xl flex flex-col items-center justify-center p-4 md:p-8"
                        @keydown.escape.window="activeLightboxIdx = null" @keydown.left.window="prevLightbox()"
                        @keydown.right.window="nextLightbox()" @wheel.prevent="handleWheel($event)"
                        @touchstart="touchStartX = $event.touches[0].clientX"
                        @touchend="if (touchStartX - $event.changedTouches[0].clientX > 50) nextLightbox(); if (touchStartX - $event.changedTouches[0].clientX < -50) prevLightbox();"
                        @click="activeLightboxIdx = null" x-cloak>

                        {{-- Close Button --}}
                        <button @click.stop="activeLightboxIdx = null"
                            class="absolute top-4 right-4 md:top-8 md:right-8 z-[10001] w-10 h-10 md:w-12 md:h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white border border-white/10 transition-all active:scale-90 cursor-pointer">
                            <i class="ph-bold ph-x text-xl"></i>
                        </button>

                        {{-- Nav Arrows (Desktop Focus) --}}
                        <button x-show="menuImages.length > 1" @click.stop="prevLightbox()"
                            class="hidden md:flex absolute left-8 z-[10001] w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white transition-all active:scale-90 border border-white/10 hover:border-white/30 cursor-pointer">
                            <i class="ph-bold ph-caret-left text-3xl"></i>
                        </button>
                        <button x-show="menuImages.length > 1" @click.stop="nextLightbox()"
                            class="hidden md:flex absolute right-8 z-[10001] w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white transition-all active:scale-90 border border-white/10 hover:border-white/30 cursor-pointer">
                            <i class="ph-bold ph-caret-right text-3xl"></i>
                        </button>

                        {{-- Slides Container --}}
                        <div
                            class="w-full h-full flex items-center justify-center relative overflow-hidden pointer-events-none">
                            <template x-for="(m, i) in menuImages" :key="i">
                                <div x-show="activeLightboxIdx === i" x-transition:enter="transition duration-300 ease-out"
                                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition duration-200 ease-in"
                                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                    class="text-center w-full max-w-5xl h-full flex items-center justify-center p-2 pointer-events-auto">
                                    <img :src="m.url"
                                        class="max-w-full max-h-[85vh] md:max-h-[90vh] object-contain rounded-xl md:rounded-[32px] shadow-2xl mx-auto cursor-default"
                                        :alt="m.tag" @click.stop>
                                </div>
                            </template>
                        </div>

                        {{-- Counter Indicator --}}
                        <div class="absolute bottom-10 bg-white/10 backdrop-blur-md px-5 py-2 rounded-full border border-white/10"
                            @click.stop>
                            <span class="text-white font-black text-xs tracking-[0.2em]">
                                <span x-text="activeLightboxIdx + 1"></span> / <span x-text="menuImages.length"></span>
                            </span>
                        </div>
                    </div>
                @else
                    <div class="py-16 text-center bg-slate-50/50 rounded-[32px] border-2 border-dashed border-slate-200">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ph ph-image-square text-3xl text-slate-300"></i>
                        </div>
                        <p class="text-slate-400 font-bold text-sm">Belum ada menu yang diunggah</p>
                    </div>
                @endif
            </section>

            <div class="h-24"></div>
        </div>

        {{-- Hero Gallery Lightbox (Premium Slider) --}}
        <div x-show="activeHeroLightboxIdx !== null" x-transition.opacity
            class="fixed inset-0 z-[20000] bg-black/98 backdrop-blur-xl flex flex-col items-center justify-center p-0"
            @keydown.escape.window="activeHeroLightboxIdx = null" @keydown.left.window="prevHero()"
            @keydown.right.window="nextHero()" @wheel.prevent="handleHeroWheel($event)"
            @touchstart="touchHeroStartX = $event.touches[0].clientX"
            @touchend="if (touchHeroStartX - $event.changedTouches[0].clientX > 50) nextHero(); if (touchHeroStartX - $event.changedTouches[0].clientX < -50) prevHero();"
            @click="activeHeroLightboxIdx = null" x-cloak>

            {{-- Close Button --}}
            <button @click.stop="activeHeroLightboxIdx = null"
                class="absolute top-6 right-6 z-[20001] w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white border border-white/10 transition-all active:scale-90 cursor-pointer">
                <i class="ph-bold ph-x text-xl"></i>
            </button>

            {{-- Nav Arrows --}}
            <button x-show="images.length > 1" @click.stop="prevHero()"
                class="hidden sm:flex absolute left-8 z-[20001] w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white transition-all active:scale-90 border border-white/10 cursor-pointer">
                <i class="ph-bold ph-caret-left text-3xl"></i>
            </button>
            <button x-show="images.length > 1" @click.stop="nextHero()"
                class="hidden sm:flex absolute right-8 z-[20001] w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white transition-all active:scale-90 border border-white/10 cursor-pointer">
                <i class="ph-bold ph-caret-right text-3xl"></i>
            </button>

            {{-- Slides Container --}}
            <div class="w-full h-full flex items-center justify-center relative overflow-hidden pointer-events-none">
                <template x-for="(img, i) in images" :key="i">
                    <div x-show="activeHeroLightboxIdx === i" x-transition:enter="transition duration-500 ease-out"
                        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
                        class="px-4 text-center max-w-[95%] pointer-events-auto">
                        <img :src="img"
                            class="max-w-full max-h-[85vh] rounded-[32px] shadow-3xl object-contain mx-auto border-4 border-white/10 cursor-default"
                            @click.stop>
                    </div>
                </template>
            </div>

            {{-- Counter Indicator --}}
            <div class="absolute bottom-10 bg-white/10 backdrop-blur-md px-5 py-2 rounded-full border border-white/10"
                @click.stop>
                <span class="text-white font-black text-xs tracking-[0.2em]">
                    <span x-text="activeHeroLightboxIdx + 1"></span> / <span x-text="images.length"></span>
                </span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cafeDetailComponent', (props) => ({
                currentSlide: 0, currentTab: '', allCategories: [], images: [], isBookmarked: false, visitorId: '', tx: 0,
                menuImages: [], touchStartX: 0,
                activeHeroLightboxIdx: null, touchHeroStartX: 0, lastHeroWheelTime: 0,

                init() {
                    this.allCategories = props.allCategories || []; this.images = props.images || []; this.currentTab = props.defaultTab || '';
                    this.menuImages = props.menuImages || [];
                    if (!this.currentTab && this.allCategories.length > 0) { this.currentTab = this.allCategories[0]; }
                    try {
                        let vid = localStorage.getItem('wadah-visitor-id'); if (!vid) { vid = 'visitor-' + Math.random().toString(36).substr(2, 9) + Date.now(); localStorage.setItem('wadah-visitor-id', vid); } this.visitorId = vid;
                        const saved = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'); this.isBookmarked = saved.includes(props.id);
                    } catch (e) { }
                    setInterval(() => { if (this.activeHeroLightboxIdx === null) this.nextSlide(); }, 6000);
                },
                nextSlide() { const len = this.images.length; if (len > 0) this.currentSlide = (this.currentSlide + 1) % len; },
                prevSlide() { const len = this.images.length; if (len > 0) this.currentSlide = (this.currentSlide - 1 + len) % len; },
                touchStart(e) { this.tx = e.touches[0].clientX; }, touchEnd(e) { const dx = this.tx - e.changedTouches[0].clientX; if (Math.abs(dx) > 40) { dx > 0 ? this.nextSlide() : this.prevSlide(); } },

                nextHero() { const len = this.images.length; if (this.activeHeroLightboxIdx !== null) this.activeHeroLightboxIdx = (this.activeHeroLightboxIdx + 1) % len; },
                prevHero() { const len = this.images.length; if (this.activeHeroLightboxIdx !== null) this.activeHeroLightboxIdx = (this.activeHeroLightboxIdx - 1 + len) % len; },
                handleHeroWheel(e) {
                    const now = Date.now();
                    if (now - this.lastHeroWheelTime < 250) return;
                    if (Math.abs(e.deltaY) < 30) return;
                    e.deltaY > 0 ? this.nextHero() : this.prevHero();
                    this.lastHeroWheelTime = now;
                },

                toggleBookmark() { let b = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'); this.isBookmarked ? b = b.filter(id => id !== props.id) : b.push(props.id); localStorage.setItem('wadah-bookmarks', JSON.stringify(b)); this.isBookmarked = !this.isBookmarked; },
                shareCafe() {
                    const shareData = {
                        title: '{{ $cafe->name }} - WadahNgopi',
                        text: 'Cek cafe estetik ini di WadahNgopi: {{ $cafe->name }}',
                        url: window.location.href
                    };
                    if (navigator.share) {
                        navigator.share(shareData).catch(err => console.log('Error sharing:', err));
                    } else {
                        navigator.clipboard.writeText(shareData.url).then(() => {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Link disalin ke clipboard!', type: 'success' } }));
                        });
                    }
                }
            }));
        });
    </script>
@endsection