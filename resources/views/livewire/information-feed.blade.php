<div class="block min-h-screen pb-32 bg-[#FAF9F6]">
    {{-- Premium Header (Matches Explore & Saved) --}}
    <header class="explore-hero-2026" x-data="{ isScrolled: false }" :class="{ 'is-compact': isScrolled }"
        @scroll.window="isScrolled = window.pageYOffset > 50">
        {{-- Premium Background Orbs --}}
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>

        {{-- Top Bar Section --}}
        <div class="explore-branding-wrapper">
            <div class="explore-topbar">
                <div class="explore-logo-box">
                    <img src="{{ asset('wadahicon.png') }}" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <h1 class="explore-brand">Wadah<span>Ngopi</span></h1>
                    <p class="explore-tagline">INFO & EDUKASI KOPI</p>
                </div>
            </div>
        </div>

        {{-- Category Pills (Matches Explore Style) --}}
        <div class="explore-category-pills">
            <button class="category-pill" :class="$wire.activeCategory === 'Semua' ? 'active' : ''"
                wire:click="setCategory('Semua')">
                <i class="ph-fill ph-check-square" x-show="$wire.activeCategory === 'Semua'"></i>
                <span>Semua</span>
            </button>
            @foreach (['Berita', 'Edukasi', 'Lomba', 'Promo'] as $cat)
                <button class="category-pill" :class="$wire.activeCategory === '{{ $cat }}' ? 'active' : ''"
                    wire:click="setCategory('{{ $cat }}')">
                    @if($cat === 'Berita') <i class="ph-fill ph-newspaper"></i>
                    @elseif($cat === 'Edukasi') <i class="ph-fill ph-book-open"></i>
                    @elseif($cat === 'Lomba') <i class="ph-fill ph-trophy"></i>
                    @elseif($cat === 'Promo') <i class="ph-fill ph-tag"></i>
                    @endif
                    <span>{{ $cat }}</span>
                </button>
            @endforeach
        </div>
    </header>

    {{-- Content Spacer --}}
    <div class="header-spacer-2026"></div>

    {{-- Main Content Window --}}
    <div class="px-6 space-y-10">
        {{-- Populer Section --}}
        @if($activeCategory === 'Semua' && $popularInformations->isNotEmpty())
            <div class="overflow-hidden" wire:transition.fade>
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-1 h-4 bg-[#F59E0B] rounded-full"></div>
                        <h2 class="text-xs font-black text-[#2C1810] uppercase tracking-widest">Populer Saat Ini</h2>
                    </div>
                </div>

                <div class="flex gap-5 overflow-x-auto no-scrollbar -mx-6 px-6 pb-6 snap-x snap-mandatory">
                    @foreach ($popularInformations as $info)
                        <a href="{{ route('information.show', $info) }}"
                            class="group flex-shrink-0 snap-center relative transition-transform active:scale-95">
                            {{-- Premium Vertical Card --}}
                            <div
                                class="w-[280px] aspect-[4/3] rounded-[24px] overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.08)] relative isolate border border-white/50 bg-[#F5EFED] group-hover:shadow-[0_20px_40px_rgb(0,0,0,0.12)] transition-shadow duration-500">

                                {{-- Image --}}
                                @php
                                    $image = $info->image_path ? (str_starts_with($info->image_path, 'http') ? $info->image_path : Storage::url($info->image_path)) : null;
                                @endphp

                                @if ($image)
                                    {{-- Main Image (Full Bleed) --}}
                                    <img src="{{ $image }}"
                                        class="absolute inset-0 w-full h-full object-cover z-10 transition-transform duration-700 group-hover:scale-105"
                                        alt="{{ $info->title }}">
                                @else
                                    <div class="absolute inset-0 bg-[#F5EFED] flex items-center justify-center">
                                        <i class="ph-fill ph-newspaper text-5xl text-[#1a0f0a]/10"></i>
                                    </div>
                                @endif

                                {{-- Gradient Overlay --}}
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-[#1A0F0A] via-[#1A0F0A]/40 to-transparent opacity-90 z-20 pointer-events-none">
                                </div>

                                {{-- Floating View Count --}}
                                <div
                                    class="absolute top-4 right-4 px-3 py-1.5 bg-white/10 backdrop-blur-md border border-white/20 rounded-full flex items-center gap-1.5 text-white/90 text-[0.65rem] font-bold shadow-sm z-30">
                                    <i class="ph-fill ph-eye text-[#F59E0B]"></i>
                                    <span>{{ number_format($info->views) }}</span>
                                </div>

                                {{-- Content --}}
                                <div
                                    class="absolute bottom-0 left-0 right-0 p-6 pt-12 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300 z-30">
                                    <span
                                        class="inline-block px-3 py-1 mb-3 text-[0.6rem] font-black text-[#2C1810] uppercase bg-[#F59E0B] rounded-lg shadow-lg shadow-orange-500/20">
                                        {{ $info->category }}
                                    </span>
                                    <h3
                                        class="text-white text-lg font-bold leading-tight line-clamp-2 mb-2 group-hover:text-[#F59E0B] transition-colors">
                                        {{ $info->title }}
                                    </h3>
                                    <p class="text-white/70 text-xs font-medium line-clamp-1">
                                        {{ Str::limit(strip_tags($info->content), 50) }}
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Terbaru Feed --}}
        <div wire:key="feed-{{ $activeCategory }}">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-4 bg-[#2C1810] rounded-full"></div>
                    <h2 class="text-xs font-black text-[#2C1810] uppercase tracking-widest">
                        {{ $activeCategory === 'Semua' ? 'Terbaru' : 'Artikel ' . $activeCategory }}
                    </h2>
                </div>

                {{-- Loading Indicator --}}
                <div wire:loading class="text-[#F59E0B]">
                    <i class="ph ph-circle-notch animate-spin text-lg"></i>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                @forelse($informations as $info)
                    <a href="{{ route('information.show', $info) }}"
                        class="group relative flex gap-4 p-3 bg-white hover:bg-white rounded-[24px] border border-[#1a0f0a]/5 shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] transition-all duration-300 active:scale-[0.99] overflow-hidden">

                        {{-- Hover Effect Glow --}}
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-[#F59E0B]/0 via-[#F59E0B]/5 to-[#F59E0B]/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000">
                        </div>

                        {{-- Thumbnail --}}
                        <div
                            class="w-[100px] h-[100px] shrink-0 rounded-[20px] overflow-hidden bg-gray-100 relative shadow-inner isolate">
                            @php
                                $thumb = $info->image_path ? (str_starts_with($info->image_path, 'http') ? $info->image_path : Storage::url($info->image_path)) : null;
                            @endphp
                            @if($thumb)
                                {{-- Main Image (Cover) --}}
                                <img src="{{ $thumb }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                    alt="{{ $info->title }}" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-[#F5EFED] text-[#D7CCC8]">
                                    <i class="ph-fill ph-image text-3xl"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Text Content --}}
                        <div class="flex-1 min-w-0 py-1.5 flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="text-[0.6rem] font-black text-[#F59E0B] uppercase tracking-wider px-2 py-0.5 bg-[#FFF8E1] rounded-md">
                                    {{ $info->category }}
                                </span>
                                <span class="text-[0.65rem] font-bold text-[#1a0f0a]/40 bg-gray-50 px-2 py-0.5 rounded-md">
                                    {{ $info->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <h3
                                class="text-[1rem] font-bold text-[#2C1810] leading-snug line-clamp-2 mb-2 group-hover:text-[#F59E0B] transition-colors">
                                {{ $info->title }}
                            </h3>

                            <div class="flex items-center gap-3 mt-auto">
                                <div class="flex items-center gap-1.5 text-[0.7rem] font-bold text-[#8B7355]">
                                    <i class="ph-fill ph-eye text-[#F59E0B]"></i>
                                    {{ number_format($info->views) }} <span
                                        class="font-medium text-[#1a0f0a]/40">Views</span>
                                </div>
                            </div>
                        </div>

                        {{-- Arrow Indicator --}}
                        <div
                            class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0 transition-all duration-300">
                            <i class="ph-bold ph-caret-right text-[#F59E0B]"></i>
                        </div>
                    </a>
                @empty
                    <div
                        class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-[32px] border border-dashed border-gray-200">
                        <div
                            class="w-20 h-20 bg-[#F5EFED] rounded-full flex items-center justify-center mb-5 animate-pulse">
                            <i class="ph-fill ph-magnifying-glass text-3xl text-[#8B7355]"></i>
                        </div>
                        <h3 class="text-base font-black text-[#2C1810] mb-2">Belum ada konten</h3>
                        <p class="text-sm text-[#8B7355] max-w-[200px] leading-relaxed">Kategori <span
                                class="font-bold text-[#F59E0B]">{{ $activeCategory }}</span> belum memiliki artikel.</p>
                        <button wire:click="setCategory('Semua')"
                            class="mt-6 px-6 py-2.5 bg-[#2C1810] text-white text-xs font-bold rounded-xl hover:bg-[#4A2C20] transition-colors shadow-lg shadow-[#2C1810]/20">
                            Kembali ke Semua
                        </button>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Load More --}}
            @if($informations->hasMorePages())
                <div class="mt-10 mb-8 text-center">
                    <button wire:click="loadMore"
                        class="px-8 py-3.5 bg-white border border-[#F5EFED] text-[#2C1810] text-xs font-bold uppercase tracking-widest rounded-2xl hover:bg-[#2C1810] hover:text-white transition-all shadow-sm hover:shadow-xl hover:-translate-y-1">
                        <span wire:loading.remove>Muat Lebih Banyak</span>
                        <span wire:loading><i class="ph-bold ph-spinner animate-spin"></i> Memuat...</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Shared Explore/Saved Styles --}}
    <style>
        /* Font Family - Must be at top */
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;900&display=swap');

        /* === PREMIUM HEADER / EXPLORE STYLE === */
        body {
            background-color: #FAF9F6;
            /* Premium Off-white background */
        }

        .explore-hero-2026 {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            max-width: 480px;
            z-index: 1000;
            padding: 40px 24px 14px;
            background: #FFFDFB;
            border-radius: 0 0 32px 32px;
            overflow: visible;
            box-shadow: 0 4px 20px rgba(26, 15, 10, 0.03);
            transition: padding 0.4s ease, background 0.4s ease, box-shadow 0.4s ease;
            will-change: padding, background, box-shadow;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .header-spacer-2026 {
            height: 190px;
            width: 100%;
        }

        .explore-hero-2026.is-compact {
            padding-top: 35px;
            padding-bottom: 12px;
            gap: 8px;
            background: rgba(255, 253, 251, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(111, 78, 55, 0.05);
            box-shadow: 0 10px 40px rgba(26, 15, 10, 0.08);
        }

        /* Branding */
        .explore-branding-wrapper {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: left top;
            will-change: transform, opacity;
        }

        .explore-hero-2026.is-compact .explore-branding-wrapper {
            transform: scale(0.92);
            opacity: 0.95;
        }

        .explore-topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 10;
        }

        .explore-brand {
            font-size: 1.35rem;
            font-weight: 900;
            color: #2C1810;
            letter-spacing: -0.02em;
            line-height: 1;
            font-family: 'Outfit', sans-serif;
        }

        .explore-brand span {
            color: #F59E0B;
        }

        .explore-tagline {
            font-size: 0.6rem;
            font-weight: 800;
            color: #8B7355;
            letter-spacing: 0.12em;
            margin-top: 2px;
            opacity: 0.8;
        }

        .explore-logo-box {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(26, 15, 10, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(26, 15, 10, 0.03);
        }

        .explore-logo-box img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        /* Orbs */
        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(50px);
            opacity: 0.15;
            pointer-events: none;
            will-change: transform;
        }

        .hero-orb-1 {
            width: 200px;
            height: 200px;
            background: var(--color-amber, #F59E0B);
            top: -50px;
            right: -50px;
            animation: float-orb 8s ease-in-out infinite;
        }

        .hero-orb-2 {
            width: 150px;
            height: 150px;
            background: var(--color-coffee, #6F4E37);
            bottom: 20%;
            left: -30px;
            animation: float-orb 10s ease-in-out infinite reverse;
        }

        .hero-orb-3 {
            width: 120px;
            height: 120px;
            background: var(--color-amber, #F59E0B);
            top: 40%;
            right: 10%;
            animation: float-orb 6s ease-in-out infinite 2s;
        }

        @keyframes float-orb {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(20px, -20px) scale(1.1);
            }
        }

        /* Category Pills */
        .explore-category-pills {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 4px 4px 8px 4px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .category-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            background: white;
            border: 1px solid rgba(26, 15, 10, 0.06);
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #8B7355;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 2px 4px rgba(26, 15, 10, 0.02);
        }

        .category-pill i {
            font-size: 1rem;
            color: #D7CCC8;
            transition: color 0.3s ease;
        }

        .category-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(26, 15, 10, 0.08);
            border-color: rgba(26, 15, 10, 0.15);
            color: #2C1810;
        }

        .category-pill:hover i {
            color: #F59E0B;
        }

        .category-pill.active {
            background: #2C1810;
            color: white;
            border-color: #2C1810;
            box-shadow: 0 8px 20px rgba(44, 24, 16, 0.25);
            transform: scale(1.02);
        }

        .category-pill.active i {
            color: #F59E0B;
        }

        /* Utilities */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</div>