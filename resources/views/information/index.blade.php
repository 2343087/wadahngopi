@extends('layouts.app')

@section('title', 'Informasi Kopi - WadahNgopi')

@section('content')
    <div x-data="{ 
                activeCategory: 'Semua',
                isScrolled: window.pageYOffset > 50
            }" @scroll.window="isScrolled = window.pageYOffset > 50">
        {{-- Ultra Compact Paku Header --}}
        <header class="explore-hero-2026" :class="{ 'is-compact': isScrolled }">
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

            {{-- Category Pills --}}
            <div class="explore-category-pills">
                <button class="category-pill" :class="activeCategory === 'Semua' ? 'active' : ''"
                    @click="activeCategory = 'Semua'">
                    <i class="ph-fill ph-newspaper-clipping"></i>
                    <span>Semua</span>
                </button>
                @foreach(['Berita', 'Edukasi', 'Lomba'] as $cat)
                    <button class="category-pill" :class="activeCategory === '{{ $cat }}' ? 'active' : ''"
                        @click="activeCategory = '{{ $cat }}'">
                        <i
                            class="ph-fill {{ $cat === 'Berita' ? 'ph-megaphone' : ($cat === 'Edukasi' ? 'ph-student' : 'ph-trophy') }}"></i>
                        <span>{{ $cat }}</span>
                    </button>
                @endforeach
            </div>
        </header>

        {{-- Content Spacer --}}
        <div class="header-spacer-2026"></div>

        <div class="px-6 pb-24">
            {{-- Popular News Section (Horizontal Scroll) --}}
            <div x-show="activeCategory === 'Semua' && {{ $popularInformations->count() }} > 0" class="animate-up mb-10">
                <div class="flex items-center justify-between mb-4 px-1">
                    <h2 class="text-[0.65rem] font-black text-[--color-espresso] opacity-40 uppercase tracking-[0.2em]">
                        Populer Sekarang</h2>
                    <div class="h-px flex-1 bg-[--color-espresso] opacity-5 mx-4"></div>
                </div>

                <div class="flex gap-3 overflow-x-auto pb-4 -mx-6 px-6 no-scrollbar">
                    @foreach($popularInformations as $info)
                        <a href="{{ route('information.show', $info) }}" class="popular-card group">
                            <div
                                class="relative w-[240px] aspect-[16/10] rounded-[2rem] overflow-hidden shadow-lg border border-white/20">
                                <img src="{{ $info->image_path && str_starts_with($info->image_path, 'http') ? $info->image_path : ($info->image_path ? Storage::url($info->image_path) : 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=600') }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    alt="{{ $info->title }}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                                <div class="absolute bottom-5 left-5 right-5 text-white">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span
                                            class="px-2 py-0.5 bg-[--color-amber] text-[0.55rem] font-black uppercase rounded-md shadow-lg">{{ $info->category }}</span>
                                        <div class="flex items-center gap-1 text-[0.6rem] font-bold opacity-80">
                                            <i class="ph-bold ph-eye"></i>
                                            {{ number_format($info->views) }}
                                        </div>
                                    </div>
                                    <h3 class="text-sm font-black leading-tight line-clamp-2">{{ $info->title }}</h3>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Main Feed --}}
            <div class="flex items-center gap-4 mb-6 px-1 animate-up">
                <h2 class="text-[0.65rem] font-black text-[--color-espresso] opacity-40 uppercase tracking-[0.2em]">Terbaru
                </h2>
                <div class="h-px flex-1 bg-[--color-espresso] opacity-5"></div>
            </div>

            <div class="space-y-6">
                @forelse($informations as $info)
                    <a href="{{ route('information.show', $info) }}"
                        x-show="activeCategory === 'Semua' || activeCategory === '{{ $info->category }}'"
                        class="standard-magazine-item animate-up group flex gap-4 items-center bg-white p-3 rounded-[2rem] shadow-sm border border-slate-50 hover:shadow-xl hover:shadow-[--color-espresso]/5 transition-all">

                        <div class="w-[100px] h-[100px] rounded-2xl overflow-hidden shrink-0 shadow-md">
                            <img src="{{ $info->image_path && str_starts_with($info->image_path, 'http') ? $info->image_path : ($info->image_path ? Storage::url($info->image_path) : 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=400') }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                alt="{{ $info->title }}">
                        </div>

                        <div class="flex-1 min-w-0 pr-2">
                            <div class="flex items-center justify-between mb-1.5">
                                <span
                                    class="text-[0.6rem] font-black text-[--color-amber] uppercase tracking-wider">{{ $info->category }}</span>
                                <div class="flex items-center gap-1 text-[0.65rem] font-bold text-slate-400">
                                    <i class="ph-bold ph-eye"></i>
                                    {{ number_format($info->views) }}
                                </div>
                            </div>
                            <h3
                                class="text-[0.95rem] font-black text-[--color-espresso] leading-snug group-hover:text-[--color-amber] transition-colors line-clamp-2 mb-2">
                                {{ $info->title }}
                            </h3>
                            <div class="flex items-center gap-2 text-[0.6rem] font-bold text-slate-300">
                                <span>{{ $info->published_at?->diffForHumans() ?? $info->created_at->diffForHumans() }}</span>
                                @if($info->source_name)
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <span class="text-rose-400 capitalize">{{ $info->source_name }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-20 bg-slate-50/50 rounded-[3rem] border border-dashed border-slate-200">
                        <i class="ph ph-newspaper text-[3.5rem] text-[--color-espresso] opacity-5 mb-4"></i>
                        <h3 class="text-sm font-bold text-[--color-espresso] opacity-40">Belum ada info nih!</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
        <style>
            /* Ultra-Compact Header Styles (Sync with Explore/Saved) */
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
                display: flex;
                flex-direction: column;
                gap: 12px;
                box-shadow: 0 4px 20px rgba(26, 15, 10, 0.05);
                transition: padding 0.4s ease, background 0.4s ease, box-shadow 0.4s ease;
                will-change: padding, background, box-shadow;
            }

            .header-spacer-2026 {
                height: 240px;
                /* Reduced for a tighter Info page feel */
                width: 100%;
            }

            .explore-hero-2026.is-compact {
                padding-top: 35px;
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(25px);
                -webkit-backdrop-filter: blur(25px);
                padding-bottom: 20px;
                box-shadow: 0 10px 30px rgba(26, 15, 10, 0.08);
            }

            .explore-branding-wrapper {
                transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                transform-origin: left center;
            }

            .explore-hero-2026.is-compact .explore-branding-wrapper {
                transform: scale(0.9);
            }

            .explore-topbar {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .explore-logo-box {
                width: 36px;
                height: 36px;
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(26, 15, 10, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .explore-logo-box img {
                width: 28px;
                height: 28px;
                object-fit: contain;
            }

            .explore-brand {
                font-size: 1.25rem;
                font-weight: 900;
                color: #2C1810;
                letter-spacing: -0.01em;
                line-height: 1.1;
            }

            .explore-brand span {
                color: #F59E0B;
            }

            .explore-tagline {
                font-size: 0.55rem;
                font-weight: 800;
                color: #8B7355;
                letter-spacing: 0.08em;
                margin: 0;
                opacity: 0.8;
                text-transform: uppercase;
            }

            /* Category Pills */
            .explore-category-pills {
                display: flex;
                gap: 10px;
                overflow-x: auto;
                padding: 4px 0;
                scrollbar-width: none;
            }

            .explore-category-pills::-webkit-scrollbar {
                display: none;
            }

            .category-pill {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 7px 16px;
                background: white;
                border: 1px solid rgba(26, 15, 10, 0.06);
                border-radius: 100px;
                font-size: 0.7rem;
                font-weight: 700;
                color: #2C1810;
                white-space: nowrap;
                transition: all 0.3s ease;
            }

            .category-pill i {
                color: #8B7355;
                font-size: 0.9rem;
            }

            .category-pill.active {
                background: #2C1810;
                color: white;
                border-color: #2C1810;
                box-shadow: 0 4px 12px rgba(26, 15, 10, 0.2);
            }

            .category-pill.active i {
                color: white;
            }

            /* Popular Section */
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            .popular-card {
                flex-shrink: 0;
                transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            }

            .popular-card:hover {
                transform: scale(1.02);
            }

            .popular-card:active {
                transform: scale(0.98);
            }

            .standard-magazine-item:active {
                transform: scale(0.97);
            }

            .animate-up {
                animation: slideUp 0.8s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
                opacity: 0;
                transform: translateY(20px);
            }

            @keyframes slideUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush
@endsection