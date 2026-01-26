@extends('layouts.app')

@section('title', $information->title . ' - WadahNgopi')

@section('content')
    <div class="animate-up pb-20">
        {{-- Header Area with Image --}}
        <div class="relative h-[45vh] w-full overflow-hidden">
            <img src="{{ $information->image_path && str_starts_with($information->image_path, 'http') ? $information->image_path : ($information->image_path ? Storage::url($information->image_path) : 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&q=80&w=1200') }}"
                class="w-full h-full object-cover" alt="{{ $information->title }}">
            <div class="absolute inset-0 bg-gradient-to-t from-[--color-cream] via-transparent to-black/20"></div>

            <a href="{{ route('information') }}"
                class="absolute top-8 left-6 w-12 h-12 rounded-full bg-white/20 backdrop-blur-xl border border-white/30 flex items-center justify-center text-white shadow-lg transition-all active:scale-90 no-underline">
                <i class="ph ph-arrow-left text-2xl"></i>
            </a>
        </div>

        {{-- Content Area --}}
        <div class="relative px-6 -mt-20">
            <div class="bg-white/40 backdrop-blur-3xl border border-white/60 rounded-[45px] p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <span
                        class="bg-[--color-espresso] text-white text-[0.7rem] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">{{ $information->category }}</span>
                    <span
                        class="text-slate-400 text-xs font-bold">{{ $information->published_at?->format('d M Y') ?? $information->created_at->format('d M Y') }}</span>
                </div>

                <h1 class="text-3xl font-black text-[--color-espresso] leading-tight mb-6">{{ $information->title }}</h1>

                <div
                    class="prose prose-slate max-w-none prose-p:font-medium prose-p:leading-relaxed prose-headings:font-black prose-headings:text-[--color-espresso] prose-img:rounded-[30px] prose-a:text-[--color-coffee-dark]">
                    {!! $information->content !!}
                </div>

                <div class="mt-12 pt-8 border-t border-black/5">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-full bg-[--color-espresso] flex items-center justify-center text-white">
                            <i class="ph ph-user-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-[0.7rem] font-black text-slate-400 uppercase tracking-widest">Penulis</p>
                            <p class="text-[1rem] font-black text-[--color-espresso]">Tim WadahNgopi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection