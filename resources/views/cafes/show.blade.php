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
                // Filter active social links - handle various boolean representations
                $activeSocialLinks = collect($cafe->social_links ?? [])->filter(function($link) {
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
                                <a href="https://wa.me/{{ e(preg_replace('/[^0-9]/', '', $cafe->whatsapp_number)) }}" target="_blank"
                                    rel="noopener noreferrer"
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
                                    <i class="{{ $iconClass }} text-sm text-amber-600 group-hover/social:text-white transition-colors"></i>
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
                            <i class="{{ $f->icon ?? 'ph ph-check-circle' }} text-espresso/60 text-lg group-hover/fac:text-amber-600 transition-colors"></i>
                            <span class="font-bold text-espresso text-[0.8rem]">{{ $f->name }}</span>
                        </div>
                    @empty
                        <span class="text-slate-300 italic text-sm">Informasi fasilitas belum tersedia.</span>
                    @endforelse
                </div>
            </section>

            {{-- Menu Section (Clean & Simple) --}}
            <section x-data="{ activeImage: null }">
                {{-- Simple Header --}}
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-black text-[#2C1810]">Daftar Menu</h2>
                    @if($activeGalleryImages->count() > 0)
                        <span class="text-xs font-bold text-slate-400">{{ $activeGalleryImages->count() }} foto</span>
                    @endif
                </div>

                @if($activeGalleryImages->count() > 0)
                    {{-- Simple Grid - 2 columns --}}
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($activeGalleryImages as $index => $img)
                            <div class="cursor-pointer active:scale-95 transition-transform"
                                @click="activeImage = '{{ Storage::url($img['image']) }}'">
                                <img src="{{ Storage::url($img['image']) }}"
                                    class="w-full aspect-square object-cover rounded-2xl shadow-md"
                                    alt="{{ $img['tag'] }}" loading="lazy">
                            </div>
                        @endforeach
                    </div>

                    {{-- Lightbox --}}
                    <div x-show="activeImage" x-transition.opacity
                        class="fixed inset-0 z-[9999] bg-black/95 flex items-center justify-center p-4"
                        @click.self="activeImage = null" x-cloak>
                        <button @click="activeImage = null"
                            class="absolute top-5 right-5 w-10 h-10 bg-white/20 rounded-full flex items-center justify-center text-white">
                            <i class="ph-bold ph-x text-lg"></i>
                        </button>
                        <img :src="activeImage" class="max-w-full max-h-[90vh] rounded-xl object-contain" alt="Menu">
                    </div>
                @else
                    <div class="py-10 text-center bg-slate-50 rounded-2xl">
                        <i class="ph ph-image text-3xl text-slate-200 mb-2 block"></i>
                        <p class="text-slate-400 text-sm">Menu belum tersedia</p>
                    </div>
                @endif
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