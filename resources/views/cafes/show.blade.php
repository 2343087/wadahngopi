@extends('layouts.app')

@section('title', $cafe->name . ' - WadahNgopi.Com')
@section('meta_description', Str::limit(strip_tags($cafe->description), 155) ?: 'Temukan ' . $cafe->name . ' di ' . ($cafe->address ?? 'Kalimantan') . '. Cafe dengan fasilitas lengkap.')
@section('og_title', $cafe->name . ' - WadahNgopi')
@section('og_description', Str::limit(strip_tags($cafe->description), 100) ?: 'Cafe nyaman di ' . ($cafe->address ?? 'Kalimantan'))
@section('og_image', $cafe->image_path ? Storage::url($cafe->image_path) : asset('wadahicon.png'))

@section('content')
    @php
        $galleryImages = $cafe->processed_images;

        // PRECOMPUTE CATEGORIES
        $menuRaw = $cafe->menu_images ?? [];
        $activeGalleryImages = collect($menuRaw)->filter(fn($img) => is_array($img) && ($img['is_active'] ?? true) === true);
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
                                                                                                    menuImages: {{ json_encode($activeGalleryImages->map(fn($img) => ['url' => str_starts_with($img['image'], 'http') ? $img['image'] : '/storage/' . $img['image'], 'tag' => $img['tag']])->values()) }}
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
                <div class="flex items-start gap-2 tphp artisan testext-slate-500 font-medium text-sm">
                    <i class="ph-fill ph-map-pin text-amber-600 text-lg mt-0.5"></i>
                    <span>{{ $cafe->address }}</span>
                </div>
            </div>
            <div class="text-slate-600 leading-[1.8] text-[1rem] font-medium mb-10 opacity-90">
                {!! clean($cafe->description) !!}
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
                    {{-- Action Buttons --}}
                    @if($hasGoogleMaps || $hasWhatsApp)
                        <div class="flex items-center gap-3 mb-5">
                            @if($hasGoogleMaps)
                                <a href="{{ e($cafe->google_maps_url) }}" target="_blank" rel="noopener noreferrer"
                                    class="flex-1 group/btn relative overflow-hidden p-[1px] rounded-2xl transition-all active:scale-[0.98] no-underline shadow-soft hover:shadow-lg hover:shadow-amber-500/20">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-amber-200 via-amber-100 to-transparent opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300">
                                    </div>
                                    <div
                                        class="relative bg-white rounded-2xl p-3 flex items-center gap-3 h-full border border-transparant">
                                        <div
                                            class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-gradient-to-br from-amber-50 to-orange-50 text-amber-600 group-hover/btn:scale-110 transition-transform duration-300">
                                            <i class="ph-fill ph-map-pin text-lg"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span
                                                class="text-espresso text-[0.85rem] font-bold block leading-none mb-1 group-hover/btn:text-amber-700 transition-colors">Lokasi</span>
                                            <span
                                                class="text-slate-400 text-[10px] font-semibold group-hover/btn:text-amber-600/70 transition-colors">Google
                                                Maps</span>
                                        </div>
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
                                    class="flex-1 group/btn relative overflow-hidden p-[1px] rounded-2xl transition-all active:scale-[0.98] no-underline shadow-soft hover:shadow-lg hover:shadow-emerald-500/20">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-br from-emerald-200 via-emerald-100 to-transparent opacity-0 group-hover/btn:opacity-100 transition-opacity duration-300">
                                    </div>
                                    <div
                                        class="relative bg-white rounded-2xl p-3 flex items-center gap-3 h-full border border-transparant">
                                        <div
                                            class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-gradient-to-br from-emerald-50 to-green-50 text-emerald-600 group-hover/btn:scale-110 transition-transform duration-300">
                                            <i class="ph-fill ph-whatsapp-logo text-lg"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span
                                                class="text-espresso text-[0.85rem] font-bold block leading-none mb-1 group-hover/btn:text-emerald-700 transition-colors">Chat</span>
                                            <span
                                                class="text-slate-400 text-[10px] font-semibold group-hover/btn:text-emerald-600/70 transition-colors">WhatsApp</span>
                                        </div>
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
                            class="bg-slate-50 border border-slate-100 px-4 py-2.5 rounded-2xl flex items-center gap-2.5 transition-all shadow-sm hover:shadow-md hover:bg-amber-50/50 hover:border-amber-200/50 group/fac cursor-default">
                            <i
                                class="{{ $f->icon ?? 'ph ph-check-circle' }} text-slate-400 text-lg group-hover/fac:text-amber-500 group-hover/fac:scale-110 transition-all duration-300"></i>
                            <span
                                class="font-bold text-slate-700 text-[0.8rem] group-hover/fac:text-amber-800 transition-colors">{{ $f->name }}</span>
                        </div>
                    @empty
                        <span class="text-slate-300 italic text-sm">Informasi fasilitas belum tersedia.</span>
                    @endforelse
                </div>
            </section>

            {{-- Premium Menu Section --}}
            <section class="relative section-premium-fade mb-16">

                {{-- Section Header (Polished) --}}
                <div class="flex items-center justify-between mb-8 px-2">
                    <div>
                        <h2 class="text-2xl font-extrabold text-[#2C1810] tracking-tight leading-tight">Daftar Menu</h2>
                        <p class="text-xs font-medium text-slate-500 mt-1 tracking-wide">
                            {{ $activeGalleryImages->count() }} Pilihan Menu Tersedia
                        </p>
                    </div>

                    {{-- Animated Badge --}}
                    <div
                        class="group relative overflow-hidden bg-white border border-amber-100 px-4 py-2 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-amber-50 to-orange-50 opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                        </div>
                        <div class="relative flex items-center gap-2">
                            <i
                                class="ph-fill ph-camera text-amber-500 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                            <span
                                class="text-[0.75rem] font-bold text-amber-950 uppercase tracking-widest group-hover:tracking-[0.15em] transition-all duration-300">
                                {{ $activeGalleryImages->count() }} FOTO
                            </span>
                        </div>
                    </div>
                </div>

                @if($activeGalleryImages->count() > 0)
                    {{-- Premium Uniform Grid (Aspect 4:5 + Interaction) --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-1">
                        @foreach($activeGalleryImages as $index => $img)
                            <div class="relative group/menu cursor-pointer transform transition-all duration-500 hover:-translate-y-2"
                                @click="activeLightboxIdx = {{ $index }}"
                                style="animation: fadeInUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) {{ $index * 0.05 }}s both;">

                                {{-- Card Container (Fixed Aspect Ratio 4:5 + Deep Shadow) --}}
                                <div
                                    class="relative aspect-[4/5] w-full rounded-2xl overflow-hidden bg-white shadow-[0_8px_20px_-6px_rgba(0,0,0,0.08)] group-hover/menu:shadow-[0_20px_40px_-12px_rgba(0,0,0,0.15)] transition-all duration-500 ring-1 ring-black/5">

                                    {{-- Image (Smart Crop + Zoom Effect) --}}
                                    <img src="{{ str_starts_with($img['image'], 'http') ? $img['image'] : '/storage/' . $img['image'] }}"
                                        class="w-full h-full object-cover object-center transition-transform duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] group-hover/menu:scale-105"
                                        alt="{{ $img['tag'] }}" loading="lazy">

                                    {{-- Full Gradient Overlay (Cinematic) --}}
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent opacity-0 group-hover/menu:opacity-100 transition-opacity duration-500">
                                    </div>

                                    {{-- Tag Badge (Floating) --}}
                                    @if(!empty($img['tag']))
                                        <div
                                            class="absolute bottom-3 left-3 right-3 opacity-0 group-hover/menu:opacity-100 transform translate-y-4 group-hover/menu:translate-y-0 transition-all duration-500 delay-75">
                                            <span
                                                class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-md px-2.5 py-1.5 rounded-lg shadow-lg ring-1 ring-black/5">
                                                <i class="ph-fill ph-tag text-amber-600 text-[0.65rem]"></i>
                                                <span
                                                    class="text-[0.65rem] font-bold text-slate-800 uppercase tracking-wider truncate max-w-full">
                                                    {{ $img['tag'] }}
                                                </span>
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Zoom Icon (Center Pulse) --}}
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-0 group-hover/menu:opacity-100 transition-opacity duration-500">
                                        <div
                                            class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/30 transform scale-50 opacity-0 group-hover/menu:scale-100 group-hover/menu:opacity-100 transition-all duration-500 delay-100 shadow-xl">
                                            <i class="ph-bold ph-arrows-out-simple text-lg"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                @else
                    {{-- Empty State --}}
                    <div
                        class="py-20 text-center bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-3xl border-2 border-dashed border-slate-200">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                            <i class="ph-fill ph-image-square text-4xl text-amber-400"></i>
                        </div>
                        <p class="text-slate-400 font-bold text-sm">Belum ada menu yang diunggah</p>
                        <p class="text-slate-300 text-xs mt-1">Menu akan tampil di sini setelah ditambahkan</p>
                    </div>
                @endif
            </section>

            <style>
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            </style>

            <div class="h-24"></div>
        </div>

        {{-- LIGHTBOX MUST BE OUTSIDE ANY TRANSFORM PARENT --}}
        {{-- Menu Lightbox (Fixed Position Fix) --}}
        <div x-show="activeLightboxIdx !== null" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] bg-black/95 backdrop-blur-xl flex flex-col items-center justify-center"
            @keydown.escape.window="activeLightboxIdx = null" @keydown.left.window="prevLightbox()"
            @keydown.right.window="nextLightbox()" @wheel.prevent="handleWheel($event)"
            @touchstart="touchStartX = $event.touches[0].clientX"
            @touchend="if (touchStartX - $event.changedTouches[0].clientX > 50) nextLightbox(); if (touchStartX - $event.changedTouches[0].clientX < -50) prevLightbox();"
            x-cloak>

            {{-- Close Button --}}
            <button @click="activeLightboxIdx = null"
                class="absolute top-6 right-6 z-[100000] w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white border border-white/10 transition-all active:scale-90 backdrop-blur-md cursor-pointer">
                <i class="ph-bold ph-x text-lg"></i>
            </button>

            {{-- Image Container --}}
            <div class="w-full h-full flex items-center justify-center relative overflow-hidden pointer-events-none p-0 md:p-8"
                @click.self="activeLightboxIdx = null">
                <template x-for="(m, i) in menuImages" :key="i">
                    <div x-show="activeLightboxIdx === i" x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition duration-200 ease-in"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute inset-0 flex items-center justify-center pointer-events-auto w-full h-full">
                        <img :src="m.url" class="max-w-full max-h-[85vh] object-contain shadow-2xl mx-auto cursor-zoom-in"
                            :alt="m.tag" @click.stop>
                    </div>
                </template>
            </div>

            {{-- Floating Controls --}}
            <div class="absolute bottom-10 left-0 right-0 z-[100000] flex justify-center pointer-events-none">
                <div
                    class="flex items-center gap-4 bg-white/10 backdrop-blur-md px-5 py-2.5 rounded-full border border-white/10 shadow-lg pointer-events-auto">
                    <button @click.stop="prevLightbox()"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-all active:scale-90">
                        <i class="ph-bold ph-caret-left text-2xl"></i>
                    </button>
                    <span class="text-white font-bold text-xs tracking-widest w-12 text-center">
                        <span x-text="activeLightboxIdx + 1"></span>/<span x-text="menuImages.length"></span>
                    </span>
                    <button @click.stop="nextLightbox()"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-all active:scale-90">
                        <i class="ph-bold ph-caret-right text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- LIGHTBOX MUST BE OUTSIDE ANY TRANSFORM PARENT --}}
        {{-- Menu Lightbox (Fixed Position Fix) --}}
        <div x-show="activeLightboxIdx !== null" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] bg-black/95 backdrop-blur-xl flex flex-col items-center justify-center"
            @keydown.escape.window="activeLightboxIdx = null" @keydown.left.window="prevLightbox()"
            @keydown.right.window="nextLightbox()" @wheel.prevent="handleWheel($event)"
            @touchstart="touchStartX = $event.touches[0].clientX"
            @touchend="if (touchStartX - $event.changedTouches[0].clientX > 50) nextLightbox(); if (touchStartX - $event.changedTouches[0].clientX < -50) prevLightbox();"
            x-cloak>

            {{-- Close Button --}}
            <button @click="activeLightboxIdx = null"
                class="absolute top-6 right-6 z-[100000] w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white border border-white/10 transition-all active:scale-90 backdrop-blur-md cursor-pointer">
                <i class="ph-bold ph-x text-lg"></i>
            </button>

            {{-- Image Container --}}
            <div class="w-full h-full flex items-center justify-center relative overflow-hidden pointer-events-none p-0 md:p-8"
                @click.self="activeLightboxIdx = null">
                <template x-for="(m, i) in menuImages" :key="i">
                    <div x-show="activeLightboxIdx === i" x-transition:enter="transition duration-300 ease-out"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition duration-200 ease-in"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute inset-0 flex items-center justify-center pointer-events-auto w-full h-full">
                        <img :src="m.url" class="max-w-full max-h-[85vh] object-contain shadow-2xl mx-auto cursor-zoom-in"
                            :alt="m.tag" @click.stop>
                    </div>
                </template>
            </div>

            {{-- Floating Controls --}}
            <div class="absolute bottom-10 left-0 right-0 z-[100000] flex justify-center pointer-events-none">
                <div
                    class="flex items-center gap-4 bg-white/10 backdrop-blur-md px-5 py-2.5 rounded-full border border-white/10 shadow-lg pointer-events-auto">
                    <button @click.stop="prevLightbox()"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-all active:scale-90">
                        <i class="ph-bold ph-caret-left text-2xl"></i>
                    </button>
                    <span class="text-white font-bold text-xs tracking-widest w-12 text-center">
                        <span x-text="activeLightboxIdx + 1"></span>/<span x-text="menuImages.length"></span>
                    </span>
                    <button @click.stop="nextLightbox()"
                        class="w-10 h-10 rounded-full flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-all active:scale-90">
                        <i class="ph-bold ph-caret-right text-2xl"></i>
                    </button>
                </div>
            </div>
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
                    } catch (e) {
                        console.error('[Cafe Detail] LocalStorage error:', e);
                    }
                    const AUTO_SLIDE_INTERVAL = 6000; // 6 seconds
                    setInterval(() => { if (this.activeHeroLightboxIdx === null) this.nextSlide(); }, AUTO_SLIDE_INTERVAL);
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

                // MENU LIGHTBOX LOGIC (Promoted to Main Component)
                activeLightboxIdx: null,
                lastWheelTime: 0,
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
                    e.deltaY > 0 ? this.nextLightbox() : this.prevLightbox();
                    this.lastWheelTime = now;
                },

                toggleBookmark() {
                    try {
                        let b = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]');
                        this.isBookmarked ? b = b.filter(id => id !== props.id) : b.push(props.id);
                        localStorage.setItem('wadah-bookmarks', JSON.stringify(b));
                        this.isBookmarked = !this.isBookmarked;
                    } catch (e) {
                        console.error('[Cafe Detail] Bookmark error:', e);
                    }
                },
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