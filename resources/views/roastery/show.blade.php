@extends('layouts.app')

@section('title', $roastery->name . ' - WadahNgopi.Com')
@section('meta_description', Str::limit($roastery->description, 155) ?: 'Temukan ' . $roastery->name . ' di ' . ($roastery->address ?? 'Kalimantan') . '. Roastery terbaik pilihan WadahNgopi.')
@section('og_title', $roastery->name . ' - WadahNgopi')
@section('og_description', Str::limit($roastery->description, 100) ?: 'Roastery terbaik di ' . ($roastery->address ?? 'Kalimantan'))
@section('og_image', $roastery->image_path ? (str_starts_with($roastery->image_path, 'http') ? $roastery->image_path : '/storage/' . trim($roastery->image_path)) : asset('wadahicon.png'))

@section('content')
    @php
        $galleryImages = $roastery->processed_images;
        $menuImages = $roastery->processed_menu_images;
    @endphp

    <style>
        /* MODERN CLEAN DESIGN SYSTEM (Reused from Cafe Detail) */
        .detail-hero-luxury {
            position: relative;
            height: 50vh;
            border-radius: 0 0 40px 40px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .detail-content-luxury {
            position: relative;
            margin-top: -40px;
            padding: 40px 24px 120px;
            background: #FFFDFB;
            border-radius: 40px 40px 0 0;
            z-index: 20;
        }

        .shadow-soft {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .shadow-glass {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .animate-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    {{--
    FIX: Use single quotes for x-data attribute to avoid conflict with double quotes in JSON.
    We use @json($galleryImages) which outputs a JSON string.
    --}}
    <div class="detail-wrapper"
        x-data='roasteryDetailComponent({
                                                                                                                                id: {{ $roastery->id }},
                                                                                                                                images: @json($galleryImages),
                                                                                                                                menuImages: @json($menuImages)
                                                                                                                            })'>

        {{-- Hero Slider Section --}}
        <div class="detail-hero-luxury" @touchstart="touchStart($event)" @touchend="touchEnd($event)">
            {{-- Nav Overlay --}}
            <nav class="detail-nav-overlay group absolute top-6 left-6 right-6 flex justify-between z-50">
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
                        @click="shareRoastery">
                        <i class="ph ph-share-network text-2xl"></i>
                    </button>
                </div>
            </nav>

            {{-- Slider Images --}}
            <template x-for="(img, idx) in images" :key="idx">
                <div x-show="currentSlide === idx" class="absolute inset-0 z-0 cursor-pointer"
                    x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 scale-105"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-100"
                    @click="activeHeroLightboxIdx = idx">
                    <img :src="img" class="detail-gallery-img w-full h-full object-cover" alt="Roastery"
                        onerror="this.onerror=null; this.src='https://placehold.co/1200x800?text=Image+Not+Found';">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>
                </div>
            </template>

            <div x-show="images.length === 0" class="absolute inset-0 z-0 bg-slate-200 flex items-center justify-center">
                <img src="https://placehold.co/1200x800?text={{ urlencode($roastery->name) }}"
                    class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/20"></div>
            </div>

            {{-- Slider Info Overlay --}}
            <div class="absolute bottom-16 left-6 right-6 z-20">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/90 backdrop-blur-md rounded-lg shadow-soft">
                        <i class="ph-fill ph-coffee-bean text-white text-xs"></i>
                        <span class="text-white text-[0.65rem] font-black uppercase tracking-widest">Premium Roastery</span>
                    </div>

                    {{-- Open Status Badge --}}
                    <livewire:roastery-detail :roastery-id="$roastery->id" />
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
    </div>

    {{-- Main Detail Content --}}
    <div class="detail-content-luxury animate-up">
        <div class="mb-6">
            <h1 class="text-3xl font-black text-[#2C1810] leading-tight mb-2">{{ $roastery->name }}</h1>
            <div class="flex flex-wrap items-center gap-3 text-slate-500 font-medium text-sm">
                <div class="flex items-center gap-2">
                    <i class="ph-fill ph-map-pin text-amber-600 text-lg mt-0.5"></i>
                    <span>{{ $roastery->city?->name ?? 'Kalimantan' }}</span>
                </div>

                @if($todayHours = $roastery->today_hours)
                    <div class="flex items-center gap-2 pl-3 border-l border-slate-300">
                        <i class="ph-bold ph-clock text-amber-600"></i>
                        <span class="text-xs font-bold">{{ $todayHours['open'] ?? '00:00' }} -
                            {{ $todayHours['close'] ?? '00:00' }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Description --}}
        <div class="text-slate-600 leading-[1.8] text-[1rem] font-medium mb-10 opacity-90">
            {!! clean($roastery->description ?: 'Deskripsi roastery belum tersedia.') !!}
        </div>

        {{-- Kontak & Sosmed Section --}}
        @php
            // Filter active social links
            $activeSocialLinks = collect($roastery->social_links ?? [])->filter(function ($link) {
                $isVisible = isset($link['show']) && filter_var($link['show'], FILTER_VALIDATE_BOOLEAN);
                $hasUrl = !empty($link['url']);
                return $isVisible && $hasUrl;
            });
            $hasGoogleMaps = !empty($roastery->google_maps_url);
            $hasWhatsApp = !empty($roastery->whatsapp_number);
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
                            <a href="{{ e($roastery->google_maps_url) }}" target="_blank" rel="noopener noreferrer"
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
                                $rawWa = preg_replace('/[^0-9]/', '', $roastery->whatsapp_number);
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
                                    default => 'Website'
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

        {{-- NOTE: Removed "Alamat Lengkap" Section to match Cafe Detail UI which puts address in Hero --}}



        {{-- Menu Biji Kopi Section --}}
        <section x-data='roasteryMenuComponent(@json($menuImages))' class="relative mb-12 section-premium-fade">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-black text-[#2C1810] tracking-tight mb-1">Daftar Menu Biji Kopi</h2>
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                </div>
                <div
                    class="flex items-center gap-2 bg-white/50 backdrop-blur-sm border border-espresso/5 px-4 py-2 rounded-2xl shadow-soft">
                    <i class="ph ph-coffee-bean text-amber-600 font-bold"></i>
                    <span class="text-[0.7rem] font-bold text-espresso/70 uppercase tracking-widest"
                        x-text="menuImages.length + ' Foto'"></span>
                </div>
            </div>

            <template x-if="menuImages.length > 0">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                    <template x-for="(img, index) in menuImages" :key="index">
                        <div class="relative group/menu cursor-pointer" @click="activeLightboxIdx = index">
                            <div
                                class="relative aspect-[3/4] rounded-[28px] overflow-hidden shadow-soft transition-all duration-500 group-hover/menu:shadow-xl group-hover/menu:-translate-y-2 border border-espresso/5">
                                {{-- Image --}}
                                <img :src="img"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover/menu:scale-110"
                                    loading="lazy">

                                {{-- Gradient Overlay --}}
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover/menu:opacity-100 transition-opacity duration-300">
                                </div>

                                {{-- Icon Overlay --}}
                                <div
                                    class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/menu:opacity-100 transition-opacity">
                                    <div
                                        class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center border border-white/30 text-white shadow-lg">
                                        <i class="ph-bold ph-magnifying-glass-plus text-xl"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Lightbox (Gallery) --}}
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

                {{-- Nav Arrows --}}
                <button x-show="menuImages.length > 1" @click.stop="prevLightbox()"
                    class="hidden md:flex absolute left-8 z-[10001] w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white transition-all active:scale-90 border border-white/10 hover:border-white/30 cursor-pointer">
                    <i class="ph-bold ph-caret-left text-3xl"></i>
                </button>
                <button x-show="menuImages.length > 1" @click.stop="nextLightbox()"
                    class="hidden md:flex absolute right-8 z-[10001] w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white transition-all active:scale-90 border border-white/10 hover:border-white/30 cursor-pointer">
                    <i class="ph-bold ph-caret-right text-3xl"></i>
                </button>

                {{-- Slides Container --}}
                <div class="w-full h-full flex items-center justify-center relative overflow-hidden pointer-events-none"
                    @click.self="activeLightboxIdx = null">
                    <template x-for="(img, i) in menuImages" :key="i">
                        <div x-show="activeLightboxIdx === i" x-transition:enter="transition duration-300 ease-out"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition duration-200 ease-in"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="text-center w-full max-w-5xl h-full flex items-center justify-center p-2 pointer-events-auto">
                            <img :src="img"
                                class="max-w-full max-h-[85vh] md:max-h-[90vh] object-contain rounded-xl md:rounded-[32px] shadow-2xl mx-auto cursor-default"
                                draggable="false" @click.stop>
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
    </div>
    </template>

    <template x-if="menuImages.length === 0">
        <div class="py-16 text-center bg-slate-50/50 rounded-[32px] border-2 border-dashed border-slate-200">
            <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="ph-fill ph-coffee-bean text-3xl text-amber-300"></i>
            </div>
            <p class="text-slate-400 font-bold text-sm">Belum ada menu biji kopi</p>
        </div>
    </template>
    </section>

    <div class="h-24"></div>
    </div>

    {{-- Hero Gallery Lightbox (Premium Slider) --}}
    <div x-show="activeHeroLightboxIdx !== null" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[99999] bg-black/95 backdrop-blur-md flex flex-col items-center justify-center p-4"
        @keydown.escape.window="activeHeroLightboxIdx = null" @keydown.left.window="prevHero()"
        @keydown.right.window="nextHero()" @wheel.prevent="handleHeroWheel($event)"
        @touchstart="touchHeroStartX = $event.touches[0].clientX"
        @touchend="if (touchHeroStartX - $event.changedTouches[0].clientX > 50) nextHero(); if (touchHeroStartX - $event.changedTouches[0].clientX < -50) prevHero();"
        @click.self="activeHeroLightboxIdx = null" x-cloak>

        {{-- Close Button --}}
        <button @click="activeHeroLightboxIdx = null"
            class="absolute top-6 right-6 z-[100000] w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white border border-white/20 transition-all active:scale-95 shadow-lg">
            <i class="ph-bold ph-x text-xl"></i>
        </button>

        {{-- Nav Arrows --}}
        <button x-show="images.length > 1" @click="prevHero()"
            class="hidden sm:flex absolute left-6 z-[100000] w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white transition-all active:scale-95 border border-white/20 shadow-lg">
            <i class="ph-bold ph-caret-left text-2xl"></i>
        </button>
        <button x-show="images.length > 1" @click="nextHero()"
            class="hidden sm:flex absolute right-6 z-[100000] w-14 h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white transition-all active:scale-95 border border-white/20 shadow-lg">
            <i class="ph-bold ph-caret-right text-2xl"></i>
        </button>

        {{-- Slides Container --}}
        <div class="w-full h-full flex items-center justify-center relative pointer-events-none"
            @click.self="activeHeroLightboxIdx = null">
            <template x-for="(img, i) in images" :key="i">
                <div x-show="activeHeroLightboxIdx === i"
                    x-transition:enter="transition duration-500 cubic-bezier(0.34, 1.56, 0.64, 1)"
                    x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="absolute inset-0 flex items-center justify-center pointer-events-auto p-4 sm:p-8">
                    <img :src="img"
                        class="max-w-full max-h-[85vh] w-auto h-auto rounded-[24px] shadow-2xl object-contain border border-white/10 bg-black/50"
                        draggable="false" @click.stop>
                </div>
            </template>
        </div>

        {{-- Counter Indicator --}}
        <div
            class="absolute bottom-10 bg-black/40 backdrop-blur-md px-5 py-2 rounded-full border border-white/10 z-[100000]">
            <span class="text-white/90 font-bold text-xs tracking-widest">
                <span x-text="activeHeroLightboxIdx + 1" class="text-white"></span> / <span x-text="images.length"></span>
            </span>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('roasteryDetailComponent', (props) => ({
                currentSlide: 0,
                images: [],
                menuImages: [], // Initialize menuImages
                isBookmarked: false,
                tx: 0,
                activeHeroLightboxIdx: null,
                touchHeroStartX: 0,
                lastHeroWheelTime: 0,

                init() {
                    this.images = props.images || [];
                    this.menuImages = props.menuImages || []; // Assign from props
                    try {
                        const saved = JSON.parse(localStorage.getItem('wadah-roastery-bookmarks') || '[]');
                        this.isBookmarked = saved.includes(props.id);
                    } catch (e) {
                        console.error('[Roastery Detail] LocalStorage error:', e);
                    }

                    // Auto slide hero
                    const AUTO_SLIDE_INTERVAL = 6000; // 6 seconds
                    setInterval(() => { if (this.activeHeroLightboxIdx === null) this.nextSlide(); }, AUTO_SLIDE_INTERVAL);
                },

                nextSlide() { const len = this.images.length; if (len > 0) this.currentSlide = (this.currentSlide + 1) % len; },
                prevSlide() { const len = this.images.length; if (len > 0) this.currentSlide = (this.currentSlide - 1 + len) % len; },
                touchStart(e) { this.tx = e.touches[0].clientX; },
                touchEnd(e) {
                    const dx = this.tx - e.changedTouches[0].clientX;
                    if (Math.abs(dx) > 40) { dx > 0 ? this.nextSlide() : this.prevSlide(); }
                },

                nextHero() { const len = this.images.length; if (this.activeHeroLightboxIdx !== null) this.activeHeroLightboxIdx = (this.activeHeroLightboxIdx + 1) % len; },
                prevHero() { const len = this.images.length; if (this.activeHeroLightboxIdx !== null) this.activeHeroLightboxIdx = (this.activeHeroLightboxIdx - 1 + len) % len; },
                handleHeroWheel(e) {
                    const now = Date.now();
                    if (now - this.lastHeroWheelTime < 250) return;
                    if (Math.abs(e.deltaY) < 30) return;
                    e.deltaY > 0 ? this.nextHero() : this.prevHero();
                    this.lastHeroWheelTime = now;
                },

                toggleBookmark() {
                    try {
                        let b = JSON.parse(localStorage.getItem('wadah-roastery-bookmarks') || '[]');
                        this.isBookmarked ? b = b.filter(id => id !== props.id) : b.push(props.id);
                        localStorage.setItem('wadah-roastery-bookmarks', JSON.stringify(b));
                        this.isBookmarked = !this.isBookmarked;
                    } catch (e) {
                        console.error('[Roastery Detail] Bookmark error:', e);
                    }
                },

                shareRoastery() {
                    const shareData = {
                        title: '{{ $roastery->name }} - WadahNgopi',
                        text: 'Cek roastery kece ini di WadahNgopi: {{ $roastery->name }}',
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

            Alpine.data('roasteryMenuComponent', (menuImages) => ({
                menuImages: menuImages || [],
                activeMenuIdx: 0,
                activeLightboxIdx: null,
                touchStartX: 0,
                lastWheelTime: 0,

                init() {
                    // No specific init logic needed yet
                },

                scrollTo(idx) {
                    const el = this.$refs.menuSlider;
                    if (!el || !el.children[idx]) return;
                    const item = el.children[idx];
                    el.scrollTo({
                        left: item.offsetLeft - (el.offsetWidth - item.offsetWidth) / 2,
                        behavior: 'smooth'
                    });
                },

                updateIdx() {
                    const el = this.$refs.menuSlider;
                    if (!el) return;
                    const center = el.scrollLeft + el.offsetWidth / 2;
                    let minDiff = Infinity;
                    let closestIdx = 0;
                    Array.from(el.children).forEach((child, i) => {
                        const diff = Math.abs((child.offsetLeft + child.offsetWidth / 2) - center);
                        if (diff < minDiff) {
                            minDiff = diff;
                            closestIdx = i;
                        }
                    });
                    this.activeMenuIdx = closestIdx;
                },

                nextLightbox() {
                    const len = this.menuImages.length;
                    if (this.activeLightboxIdx !== null) this.activeLightboxIdx = (this.activeLightboxIdx + 1) % len;
                },

                prevLightbox() {
                    const len = this.menuImages.length;
                    if (this.activeLightboxIdx !== null) this.activeLightboxIdx = (this.activeLightboxIdx - 1 + len) % len;
                },

                handleWheel(e) {
                    const now = Date.now();
                    if (now - this.lastWheelTime < 250) return;
                    if (Math.abs(e.deltaY) < 30) return;
                    if (e.deltaY > 0) this.nextLightbox();
                    else this.prevLightbox();
                    this.lastWheelTime = now;
                }
            }));
        });
    </script>
@endsection