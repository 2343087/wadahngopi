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
                    class="text-3xl md:text-5xl font-black text-[--color-espresso] leading-[1.1] tracking-tight mb-6 break-words">
                    {{ $information->title }}
                </h1>

                {{-- 4. Meta Data (Date & Author) --}}
                <div class="flex items-center gap-3 mb-10 pb-6 border-b border-slate-50">
                    <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-[--color-espresso]">
                        <i class="ph-fill ph-user-circle text-2xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[--color-espresso] text-[0.8rem] font-black">Tim WadahNgopi</span>
                        <time class="text-slate-400 text-[0.65rem] font-bold uppercase tracking-widest">
                            {{ $information->published_at?->format('d M Y') ?? $information->created_at->format('d M Y') }}
                        </time>
                    </div>
                </div>

                {{-- 5. Main Image (Now below Title/Meta) --}}
                <div class="mb-12">
                    <img src="{{ $information->image_path && str_starts_with($information->image_path, 'http') ? $information->image_path : ($information->image_path ? Storage::url($information->image_path) : 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=1200') }}"
                        class="w-full aspect-video md:aspect-[21/10] object-cover rounded-[32px] shadow-2xl shadow-black/5 border border-slate-100"
                        alt="{{ $information->title }}">
                </div>

                {{-- 6. Executive Summary --}}
                @if($information->summary)
                    <div class="mb-12">
                        <p
                            class="text-slate-700 font-extrabold text-xl md:text-2xl leading-relaxed italic border-l-4 border-[--color-amber] pl-6 py-2 break-words">
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
            color: #1a202c;
            font-size: 1.15rem;
            line-height: 1.85;
        }

        .prose-news p {
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .prose-news h2 {
            font-weight: 950;
            color: var(--color-espresso);
            font-size: 1.8rem;
            margin-top: 4rem;
            margin-bottom: 1.5rem;
            letter-spacing: -0.025em;
        }

        .prose-news img {
            border-radius: 24px;
            margin: 4rem auto;
            display: block;
            width: 100%;
            height: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
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