@extends('layouts.app')

@section('title', $information->title . ' - WadahNgopi')

@section('content')
    <div x-data="{ 
                        scrolled: 0,
                        progress: 0,
                        init() {
                            window.addEventListener('scroll', () => {
                                let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                                let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                                this.progress = (winScroll / height) * 100;
                                this.scrolled = winScroll;
                            });
                        },
                        share() {
                            if (navigator.share) {
                                navigator.share({
                                    title: '{{ addslashes($information->title) }}',
                                    text: '{{ addslashes($information->summary ?? "Baca berita terbaru tentang kopi di WadahNgopi!") }}',
                                    url: window.location.href,
                                }).catch(console.error);
                            } else {
                                navigator.clipboard.writeText(window.location.href);
                                alert('Link disalin ke clipboard!');
                            }
                        }
                    }" class="min-h-screen bg-white pb-40 font-['Plus_Jakarta_Sans'] antialiased">

        {{-- Top Reading Bar --}}
        <div class="fixed top-0 left-0 w-full h-[3px] z-[70] pointer-events-none">
            <div class="h-full bg-[--color-espresso] transition-all duration-200 ease-out"
                :style="`width: ${progress}%` font-weight: 800;"></div>
        </div>

        {{-- Main Article Container --}}
        <main class="relative pt-8 px-5 md:px-6 max-w-2xl mx-auto">

            {{-- 1. Breadcrumb / Back Navigation --}}
            <div class="mb-8">
                <a href="{{ route('information') }}"
                    class="inline-flex items-center gap-2 text-[--color-espresso]/60 hover:text-[--color-espresso] no-underline transition-colors">
                    <i class="ph-bold ph-arrow-left text-xs"></i>
                    <span class="text-[0.7rem] font-bold uppercase tracking-widest">Kembali ke Daftar Berita</span>
                </a>
            </div>

            <article class="animate-up">
                {{-- 2. Category Label --}}
                <div class="mb-4">
                    <span
                        class="text-[--color-amber] text-[0.7rem] font-black uppercase tracking-[0.25em] px-3.5 py-1.5 bg-[--color-amber]/5 rounded-lg border border-[--color-amber]/10">
                        {{ $information->category }}
                    </span>
                </div>

                {{-- 3. Main Title --}}
                <h1
                    class="text-2xl md:text-4xl font-black text-[--color-espresso] leading-[1.2] tracking-tight mb-6 break-words">
                    {{ $information->title }}
                </h1>

                {{-- 4. Meta Data (Date, Author & Views) --}}
                <div class="flex items-center gap-4 mb-10 pb-6 border-b border-slate-50">
                    <div
                        class="w-12 h-12 rounded-2xl bg-[--color-espresso]/5 flex items-center justify-center text-[--color-espresso] shadow-inner">
                        <i class="ph-fill ph-user-circle text-3xl opacity-80"></i>
                    </div>
                    <div class="flex flex-col flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[--color-espresso] text-[0.85rem] font-black">Tim WadahNgopi</span>
                            <div
                                class="flex items-center gap-1.5 px-3 py-1 bg-slate-50 rounded-full border border-slate-100">
                                <i class="ph-bold ph-eye text-[--color-amber] text-xs"></i>
                                <span
                                    class="text-[0.65rem] font-black text-slate-500 uppercase tracking-tighter">{{ number_format($information->views) }}
                                    Views</span>
                            </div>
                        </div>
                        <time class="text-slate-400 text-[0.65rem] font-bold uppercase tracking-[0.15em] mt-0.5">
                            {{ $information->published_at?->format('d M Y') ?? $information->created_at->format('d M Y') }}
                        </time>
                    </div>
                </div>

                {{-- 5. Main Image (Premium Blur + Contain) --}}
                <div
                    class="mb-12 relative w-full aspect-video md:aspect-[21/10] rounded-[32px] overflow-hidden shadow-2xl shadow-black/5 border border-slate-100 bg-[#F5EFED] isolate">
                    @php
                        $imageSrc = $information->image_path && str_starts_with($information->image_path, 'http') ? $information->image_path : ($information->image_path ? Storage::url($information->image_path) : 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=1200');
                    @endphp

                    {{-- 1. Blurred Background (Fill Space) --}}
                    <div class="absolute inset-0 bg-cover bg-center blur-2xl scale-110 opacity-60"
                        style="background-image: url('{{ $imageSrc }}');">
                    </div>

                    {{-- 2. Main Image (Contain - No Crop) --}}
                    <img src="{{ $imageSrc }}" class="absolute inset-0 w-full h-full object-contain z-10"
                        alt="{{ $information->title }}">

                    {{-- 3. Subtle Overlay --}}
                    <div class="absolute inset-0 bg-black/5 z-20 pointer-events-none"></div>
                </div>

                {{-- 6. Executive Summary --}}
                @if($information->summary)
                    <div class="mb-10">
                        <p
                            class="text-slate-600 font-bold text-lg leading-relaxed italic border-l-4 border-[--color-amber] pl-6 py-1 break-words">
                            {{ $information->summary }}
                        </p>
                    </div>
                @endif

                {{-- 7. Body Content --}}
                <div class="prose prose-news max-w-none break-words">
                    {!! $information->content !!}
                </div>

                {{-- Separator to prevent overlapping bottom nav --}}
                <div class="h-20"></div>
            </article>
        </main>
    </div>

    <style>
        .prose-news {
            color: #2C1810;
            font-size: 1.05rem;
            line-height: 1.75;
        }

        .prose-news p {
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .prose-news h2 {
            font-weight: 900;
            color: var(--color-espresso);
            font-size: 1.5rem;
            margin-top: 3rem;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .prose-news img {
            border-radius: 20px;
            margin: 2.5rem auto;
            display: block;
            width: 100%;
            max-width: 100%;
            height: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .prose-news table {
            width: 100%;
            margin: 3rem 0;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 1rem;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
            display: block;
            overflow-x: auto;
        }

        .prose-news th {
            background: #f8fafc;
            padding: 16px 20px;
            text-align: left;
            font-weight: 800;
            color: var(--color-espresso);
        }

        .prose-news td {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.02);
            color: #475569;
        }

        .animate-up {
            animation: slideUpNews 0.8s cubic-bezier(0.19, 1, 0.22, 1) both;
        }

        @keyframes slideUpNews {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection