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

    <div class="detail-wrapper" x-data='cafeDetailComponent({
                                                                                                id: {{ $cafe->id }},
                                                                                                images: {!! json_encode($galleryImages) !!},
                                                                                                allCategories: {!! json_encode($allCats) !!},
                                                                                                defaultTab: {!! json_encode($defaultTab) !!},
                                                                                                menuImages: {!! json_encode($activeGalleryImages->map(fn($img) => ["url" => Storage::url($img["image"]), "tag" => $img["tag"]])->values()) !!}
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
                <livewire:cafe-detail :cafe-id="$cafe->id" />
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

            {{-- Kontak & Sosmed Section --}}
            @php
                // Filter active social links using the correct 'show' field
                $activeSocialLinks = collect($cafe->social_links ?? [])->filter(fn($link) => !empty($link['show']) && !empty($link['url']));
                $hasGoogleMaps = !empty($cafe->google_maps_url);
                $hasWhatsApp = !empty($cafe->whatsapp_number);
            @endphp

            @if($hasGoogleMaps || $hasWhatsApp || $activeSocialLinks->isNotEmpty())
                <section class="mb-8">
                    {{-- Section Title --}}
                    <div class="flex items-center gap-2 mb-4">
                        <i class="ph-bold ph-phone text-amber-600 text-base"></i>
                        <h3 class="text-espresso text-sm font-bold">Hubungi & Follow</h3>
                    </div>

                    {{-- Action Buttons --}}
                    @if($hasGoogleMaps || $hasWhatsApp)
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            @if($hasGoogleMaps)
                                <a href="{{ e($cafe->google_maps_url) }}" target="_blank" rel="noopener noreferrer"
                                    class="flex items-center gap-3 p-3.5 rounded-2xl border border-espresso/10 bg-espresso/5 active:scale-[0.98] transition-transform no-underline">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-amber-500">
                                        <i class="ph-bold ph-map-pin text-white text-base"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-espresso text-sm font-semibold block">Lokasi</span>
                                        <span class="text-espresso/50 text-[10px]">Google Maps</span>
                                    </div>
                                </a>
                            @endif

                            @if($hasWhatsApp)
                                <a href="https://wa.me/{{ e(preg_replace('/[^0-9]/', '', $cafe->whatsapp_number)) }}" target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center gap-3 p-3.5 rounded-2xl border border-espresso/10 bg-espresso/5 active:scale-[0.98] transition-transform no-underline">
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-green-500">
                                        <i class="ph-bold ph-whatsapp-logo text-white text-base"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-espresso text-sm font-semibold block">Chat</span>
                                        <span class="text-espresso/50 text-[10px]">WhatsApp</span>
                                    </div>
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Social Media Links --}}
                    @if($activeSocialLinks->isNotEmpty())
                        <div class="flex flex-wrap gap-2">
                            @foreach($activeSocialLinks as $link)
                                @php
                                    $platform = strtolower($link['platform'] ?? '');
                                    $iconClass = match ($platform) {
                                        'instagram' => 'ph-instagram-logo',
                                        'tiktok' => 'ph-tiktok-logo',
                                        'facebook' => 'ph-facebook-logo',
                                        'x', 'twitter' => 'ph-x-logo',
                                        'youtube' => 'ph-youtube-logo',
                                        default => 'ph-globe'
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
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-espresso/10 bg-espresso/5 text-espresso active:scale-95 transition-transform no-underline">
                                    <i class="ph-bold {{ $iconClass }} text-sm"></i>
                                    <span class="text-xs font-medium">{{ $label }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

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

            {{-- Menu Section (Refined Masonry) --}}
            <section class="menu-section-v4" x-data="{ showAllMenu: false }">
                <div class="section-header-v5 flex items-end justify-between mb-8">
                    <div>
                        <h2 class="section-title-v5">Daftar Menu</h2>
                        <p class="text-slate-400 text-[11px] font-black mt-2 uppercase tracking-[0.25em]">Cita rasa dalam
                            setiap pilihan</p>
                    </div>
                </div>

                {{-- Premium Grid (Masonry) --}}
                <div class="columns-2 md:columns-3 gap-4 space-y-4">
                    @foreach($activeGalleryImages as $index => $img)
                        <div class="menu-card-v10 group break-inside-avoid"
                            x-show="showAllMenu || {{ $index < 6 ? 'true' : 'false' }}"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-8"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <div
                                class="relative overflow-hidden rounded-[2.5rem] bg-white border border-slate-100 p-2 transition-all duration-500 group-hover:border-amber-200 group-hover:shadow-2xl group-hover:shadow-amber-900/10 active:scale-95">
                                <img src="{{ Storage::url($img['image']) }}"
                                    class="w-full h-auto rounded-[2rem] transition-transform duration-1000 group-hover:scale-110"
                                    alt="{{ $img['tag'] }}">

                                <div
                                    class="absolute bottom-3.5 left-3.5 right-3.5 bg-black/40 backdrop-blur-xl border border-white/20 px-3 py-2 rounded-2xl shadow-xl transform transition-all duration-500 group-hover:bg-white group-hover:border-amber-100">
                                    <span
                                        class="text-[0.6rem] font-black uppercase tracking-widest text-white group-hover:text-[#2C1810] block text-center truncate">
                                        {{ $img['tag'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- "Show More" Button --}}
                @if($activeGalleryImages->count() > 6)
                    <div class="flex justify-center mt-12" x-show="!showAllMenu">
                        <button @click="showAllMenu = true"
                            class="px-8 py-4 bg-white border border-slate-100 text-[#2C1810] font-black text-xs uppercase tracking-widest rounded-3xl shadow-lg hover:shadow-2xl transition-all active:scale-90 flex items-center gap-3">
                            <i class="ph-bold ph-plus text-amber-500"></i>Lihat Menu Lengkap
                        </button>
                    </div>
                @endif

                {{-- Empty State --}}
                <div x-show="menuImages.length === 0"
                    class="py-20 text-center bg-white rounded-[40px] border border-dashed border-slate-200">
                    <i class="ph-bold ph-book-open text-4xl text-slate-100 mb-4 block"></i>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-[0.7rem]">Menu belum tersedia</p>
                </div>
            </section>

            <div class="h-24"></div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cafeDetailComponent', (props) => ({
                currentSlide: 0, currentTab: '', allCategories: [], images: [], isBookmarked: false, visitorId: '', tx: 0,
                menuImages: [], touchStartX: 0,
                init() {
                    this.allCategories = props.allCategories || []; this.images = props.images || []; this.currentTab = props.defaultTab || '';
                    this.menuImages = props.menuImages || [];
                    if (!this.currentTab && this.allCategories.length > 0) { this.currentTab = this.allCategories[0]; }
                    try {
                        let vid = localStorage.getItem('wadah-visitor-id'); if (!vid) { vid = 'visitor-' + Math.random().toString(36).substr(2, 9) + Date.now(); localStorage.setItem('wadah-visitor-id', vid); } this.visitorId = vid;
                        const saved = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'); this.isBookmarked = saved.includes(props.id);
                    } catch (e) { }
                    setInterval(() => { this.nextSlide(); }, 6000);
                },
                nextSlide() { const len = this.images.length; if (len > 0) this.currentSlide = (this.currentSlide + 1) % len; },
                prevSlide() { const len = this.images.length; if (len > 0) this.currentSlide = (this.currentSlide - 1 + len) % len; },
                touchStart(e) { this.tx = e.touches[0].clientX; }, touchEnd(e) { const dx = this.tx - e.changedTouches[0].clientX; if (Math.abs(dx) > 40) { dx > 0 ? this.nextSlide() : this.prevSlide(); } },
                toggleBookmark() { let b = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'); this.isBookmarked ? b = b.filter(id => id !== props.id) : b.push(props.id); localStorage.setItem('wadah-bookmarks', JSON.stringify(b)); this.isBookmarked = !this.isBookmarked; }
            }));
        });
    </script>
@endsection