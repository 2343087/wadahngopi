@extends('layouts.app')

@section('title', 'Informasi Kopi - WadahNgopi')

@section('content')
    <div x-data="{ activeCategory: 'Semua' }">
        <div class="hero-fancy">
            <h1 class="hero-luxury-title">Info Kopi</h1>
            <p class="text-[--color-text-muted] text-[1.1rem] font-semibold opacity-80">Update terbaru seputar berita,
                lomba, dan edukasi kopi.</p>

            <div class="pills-container-luxury mt-6">
                <template x-for="cat in ['Semua', 'Berita', 'Edukasi', 'Lomba', ]">
                    <button class="pill-luxury" :class="activeCategory === cat ? 'active' : ''"
                        @click="activeCategory = cat" x-text="cat"></button>
                </template>
            </div>
        </div>

        <div class="info-list-container py-8">
            @forelse($informations as $info)
                <a href="{{ route('information.show', $info) }}"
                    x-show="activeCategory === 'Semua' || activeCategory === '{{ $info->category }}'"
                    class="info-item-row animate-up">
                    <div class="info-img-compact">
                        <img src="{{ $info->image_path && str_starts_with($info->image_path, 'http') ? $info->image_path : ($info->image_path ? Storage::url($info->image_path) : 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=800') }}"
                            alt="{{ $info->title }}">
                    </div>

                    <div class="info-text-box">
                        <span class="info-cat-pill">{{ $info->category }}</span>
                        <h3 class="info-headline">{{ $info->title }}</h3>
                        <div class="info-meta-small">
                            <i class="ph-fill ph-calendar-blank"></i>
                            {{ $info->published_at?->format('d M Y') ?? $info->created_at->format('d M Y') }}
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-20">
                    <i class="ph ph-newspaper-clipping text-[4rem] opacity-20 block mb-6 mx-auto"></i>
                    <h3 class="text-[1.2rem] font-black text-[--color-espresso] mb-2">Belum ada info nih!</h3>
                    <p class="text-[0.9rem] font-medium opacity-60">Admin lagi nulis berita seru buat kamu. Ditunggu ya!</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection