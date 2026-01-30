@extends('layouts.app')

@section('title', 'Informasi Kopi - WadahNgopi')

@section('content')
    <div x-data="{ activeCategory: 'Semua' }">
        {{-- Header Section --}}
        <div class="hero-fancy !pb-4">
            <h1 class="hero-luxury-title">Info Kopi</h1>
            <p class="text-[--color-text-muted] text-[1.1rem] font-semibold opacity-80 mt-1">
                Informasi dan edukasi kopi.
            </p>

            <div class="pills-container-luxury mt-8">
                <template x-for="cat in ['Semua', 'Berita', 'Edukasi', 'Lomba']">
                    <button class="pill-luxury" :class="activeCategory === cat ? 'active' : ''"
                        @click="activeCategory = cat" x-text="cat"></button>
                </template>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="px-6 py-10 space-y-12">
            @forelse($informations as $info)
                @if($loop->first)
                    {{-- FEATURED HEADLINE CARD --}}
                    <div x-show="activeCategory === 'Semua' || activeCategory === '{{ $info->category }}'" class="animate-up">
                        <a href="{{ route('information.show', $info) }}" class="featured-magazine-card-v2 group block">
                            <div class="relative aspect-[16/9] overflow-hidden rounded-[2.5rem] shadow-xl">
                                <img src="{{ $info->image_path && str_starts_with($info->image_path, 'http') ? $info->image_path : ($info->image_path ? Storage::url($info->image_path) : 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=800') }}"
                                    class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110"
                                    alt="{{ $info->title }}">

                                {{-- Floating Category --}}
                                <div class="absolute top-5 left-5 z-20">
                                    <span
                                        class="px-4 py-1.5 bg-white/90 backdrop-blur-md text-[--color-espresso] text-[0.6rem] font-black uppercase tracking-widest rounded-xl shadow-lg border border-white">
                                        {{ $info->category }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-6 px-2">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-1.5 h-1.5 bg-[--color-amber] rounded-full animate-pulse"></span>
                                    <span
                                        class="text-[0.7rem] font-black text-[--color-text-muted] tracking-[0.2em] uppercase">Berita
                                        Utama</span>
                                </div>
                                <h2
                                    class="text-[--color-espresso] text-[1.4rem] font-black leading-tight mb-4 group-hover:text-[--color-amber] transition-colors">
                                    {{ $info->title }}
                                </h2>
                                <div class="flex items-center gap-3 text-[--color-text-muted] text-[0.75rem] font-bold opacity-60">
                                    <i class="ph ph-calendar-blank"></i>
                                    {{ $info->published_at?->format('d M Y') ?? $info->created_at->format('d M Y') }}
                                    @if($info->source_name)
                                        <span class="w-1 h-1 bg-current rounded-full"></span>
                                        <span class="text-rose-600 uppercase">{{ $info->source_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>

                    {{-- Section Divider --}}
                    <div x-show="activeCategory === 'Semua'" class="flex items-center gap-4 py-2 animate-up">
                        <span class="text-[0.65rem] font-black tracking-[0.2em] text-[--color-espresso] opacity-40 uppercase">Edisi
                            Lainnya</span>
                        <div class="flex-1 h-px bg-[--color-espresso] opacity-10"></div>
                    </div>
                @else
                    {{-- STANDARD MAGAZINE LIST --}}
                    <a href="{{ route('information.show', $info) }}"
                        x-show="activeCategory === 'Semua' || activeCategory === '{{ $info->category }}'"
                        class="standard-magazine-item animate-up group flex gap-5 items-center">
                        {{-- 4:3 Image Container --}}
                        <div
                            class="w-[120px] h-[90px] rounded-2xl overflow-hidden shrink-0 shadow-lg transition-all duration-500 group-hover:shadow-[0_10px_20px_-5px_rgba(44,24,16,0.2)]">
                            <img src="{{ $info->image_path && str_starts_with($info->image_path, 'http') ? $info->image_path : ($info->image_path ? Storage::url($info->image_path) : 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=800') }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                alt="{{ $info->title }}">
                        </div>

                        <div class="flex-1 min-w-0 py-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span
                                    class="text-[0.6rem] font-black text-[--color-amber] uppercase tracking-wider">{{ $info->category }}</span>
                                @if($info->source_name)
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <span
                                        class="text-[0.6rem] font-black text-rose-600 bg-rose-50 px-1.5 rounded uppercase">{{ $info->source_name }}</span>
                                @endif
                                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                <span
                                    class="text-[0.6rem] font-bold text-slate-400 capitalize">{{ $info->published_at?->diffForHumans() ?? $info->created_at->diffForHumans() }}</span>
                            </div>
                            <h3
                                class="text-[1rem] font-black text-[--color-espresso] leading-snug group-hover:text-[--color-amber] transition-colors line-clamp-2">
                                {{ $info->title }}
                            </h3>
                        </div>
                    </a>
                @endif
            @empty
                <div class="text-center py-24">
                    <div
                        class="w-24 h-24 bg-[--color-cream-dark] rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="ph ph-newspaper-clipping text-[3.5rem] text-[--color-espresso] opacity-10"></i>
                    </div>
                    <h3 class="text-[1.2rem] font-black text-[--color-espresso] mb-2">Belum ada info nih!</h3>
                    <p class="text-[0.85rem] font-medium text-slate-500 max-w-[200px] mx-auto leading-relaxed">Admin lagi nulis
                        berita seru buat kamu. Ditunggu ya!</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
        <style>
            .standard-magazine-item {
                transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            }

            .standard-magazine-item:active {
                transform: scale(0.97);
            }
        </style>
    @endpush
@endsection