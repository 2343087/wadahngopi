@extends('layouts.app')

@section('title', 'WadahNgopi - Portal Cafe Terbaik di Kalimantan')

@section('content')
    <div class="bg-noise flex flex-col min-h-[calc(100vh-100px)] min-h-[calc(100dvh-100px)] px-5 py-4 pb-5 relative z-10">
        {{-- Hero --}}
        <header class="text-center shrink-0 pb-[20px] relative z-20">
            <div
                class="w-[60px] h-[60px] mx-auto mb-3 bg-white rounded-[22px] flex items-center justify-center shadow-[0_10px_30px_rgba(42,28,21,0.08)] ring-1 ring-black/5 animate-fade-up">
                <img src="{{ asset('wadahicon.png') }}" alt="WadahNgopi"
                    class="w-[42px] h-[42px] object-contain drop-shadow-sm">
            </div>
            <span
                class="glass-badge-dark inline-block px-4 py-1 rounded-full text-[0.55rem] font-black tracking-widest mb-3 uppercase animate-fade-up delay-100">
                BY : AK KREATIF
            </span>
            <h1
                class="text-[2.2rem] font-black text-[#2A1C15] tracking-tighter leading-[0.9] mb-1.5 drop-shadow-sm animate-fade-up delay-100">
                Wadah<span class="text-shimmer">Ngopi</span>
            </h1>
            <p
                class="text-[0.85rem] text-[#8D7B70] font-medium leading-relaxed max-w-[280px] mx-auto tracking-tight animate-fade-up delay-200 hover-float cursor-default">
                untuk merajut silaturahmi dan mendekatkan para pelaku usaha kedai kopi dengan para penikmat kopi di
                Samarinda.
            </p>
        </header>

        {{-- Main Card --}}
        <main
            class="card-premium-gradient flex-1 flex flex-col rounded-[32px] px-[20px] py-5 shadow-[0_25px_60px_-10px_rgba(42,28,21,0.35)] border border-white/5 relative z-20 animate-fade-up delay-300">

            {{-- Header --}}
            <div class="flex gap-3 mb-4 relative z-10">
                <div
                    class="w-1 h-10 bg-gradient-to-b from-[#F59E0B] to-[#D97706] rounded-full shrink-0 shadow-[0_0_12px_rgba(245,158,11,0.4)]">
                </div>
                <div>
                    <span
                        class="glass-badge inline-block px-2 py-0.5 rounded-md text-[0.5rem] font-black tracking-[0.2em] mb-1 border-white/10 uppercase">
                        TENTANG KAMI
                    </span>
                    <h2
                        class="text-[1.25rem] font-black text-white leading-[1.1] tracking-tight hover-float cursor-default">
                        Lebih dari<br>Sekadar Kopi
                    </h2>
                </div>
            </div>

            {{-- Body --}}
            <p
                class="text-[0.85rem] text-white/90 font-medium leading-relaxed mb-5 tracking-wide drop-shadow-md hover-float cursor-default">
                di wadah ngopi ini. para pelaku usaha kedai kopi. dapat memperkenalkan produk kedai kopinya, secara lebih
                mudah.
            </p>

            {{-- Social Media --}}
            <div class="flex justify-center gap-6 py-[16px] border-y border-white/10 mb-5 relative z-10">
                <a href="https://www.instagram.com/wadahngopi/" target="_blank"
                    class="btn-premium group flex flex-col items-center gap-1.5 no-underline">
                    <div
                        class="p-1.5 bg-white/5 rounded-xl border border-white/10 group-hover:bg-white/10 transition-colors">
                        <i class="ph-fill ph-instagram-logo text-[1.4rem] text-[#E1306C] drop-shadow-lg"></i>
                    </div>
                    <span
                        class="text-[0.5rem] font-bold text-white/60 tracking-[0.1em] group-hover:text-white transition-colors">INSTAGRAM</span>
                </a>
                <a href="https://www.tiktok.com/@wadah.ngopi?_r=1&_t=ZS-93dSno0Z0Cu"
                    class="btn-premium group flex flex-col items-center gap-1.5 no-underline">
                    <div
                        class="p-1.5 bg-white/5 rounded-xl border border-white/10 group-hover:bg-white/10 transition-colors">
                        <i class="ph-fill ph-tiktok-logo text-[1.4rem] text-white drop-shadow-lg"></i>
                    </div>
                    <span
                        class="text-[0.5rem] font-bold text-white/60 tracking-[0.1em] group-hover:text-white transition-colors">TIKTOK</span>
                </a>
                <a href="https://youtube.com/@wadahngopi?si=4MYRQCMn6cjEeAlB"
                    class="btn-premium group flex flex-col items-center gap-1.5 no-underline">
                    <div
                        class="p-1.5 bg-white/5 rounded-xl border border-white/10 group-hover:bg-white/10 transition-colors">
                        <i class="ph-fill ph-youtube-logo text-[1.4rem] text-[#FF0000] drop-shadow-lg"></i>
                    </div>
                    <span
                        class="text-[0.5rem] font-bold text-white/60 tracking-[0.1em] group-hover:text-white transition-colors">YOUTUBE</span>
                </a>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col gap-3 mt-auto mb-1 relative z-10">
                {{-- Explore --}}
                <a href="{{ route('explore') }}"
                    class="btn-premium group flex items-center gap-3 px-[16px] py-3 rounded-[20px] no-underline bg-gradient-to-r from-[#F59E0B]/20 to-[#D97706]/10 border border-[#F59E0B]/40 hover:from-[#F59E0B]/30 hover:border-[#F59E0B]/60 shadow-[0_4px_15px_rgba(245,158,11,0.1)]">
                    <div
                        class="w-[42px] h-[42px] rounded-[14px] flex items-center justify-center text-[1.2rem] shrink-0 bg-gradient-to-br from-[#F59E0B] to-[#D97706] text-white shadow-lg shadow-orange-500/30 ring-2 ring-white/10">
                        <i class="ph-bold ph-compass"></i>
                    </div>
                    <div class="flex-1">
                        <strong class="block text-[0.95rem] font-black text-white tracking-tight">JELAJAHI CAFE</strong>
                        <span class="block text-[0.7rem] text-white/60 font-semibold mt-0.5 tracking-wide">Ayo Cari Cafe
                            Favoritmu</span>
                    </div>
                    <div
                        class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-[#F59E0B] group-hover:text-white transition-all">
                        <i class="ph-bold ph-caret-right text-[1rem] text-white/40 group-hover:text-white"></i>
                    </div>
                </a>

                {{-- Information --}}
                <a href="{{ route('information') }}"
                    class="btn-premium group flex items-center gap-3 px-[16px] py-3 rounded-[20px] no-underline bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 shadow-sm">
                    <div
                        class="w-[42px] h-[42px] rounded-[14px] flex items-center justify-center text-[1.2rem] shrink-0 bg-white/10 text-white border border-white/10">
                        <i class="ph-bold ph-newspaper"></i>
                    </div>
                    <div class="flex-1">
                        <strong class="block text-[0.95rem] font-black text-white tracking-tight">INFORMASI</strong>
                        <span class="block text-[0.7rem] text-white/60 font-semibold mt-0.5 tracking-wide">Berita & Event
                            Terbaru</span>
                    </div>
                    <div
                        class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-white/20 transition-all">
                        <i class="ph-bold ph-caret-right text-[1rem] text-white/40 group-hover:text-white"></i>
                    </div>
                </a>

                {{-- Roastery --}}
                <a href="{{ route('roastery') }}"
                    class="btn-premium group flex items-center gap-3 px-[16px] py-3 rounded-[20px] no-underline bg-gradient-to-r from-[#D97706]/20 to-[#B45309]/10 border border-[#D97706]/40 hover:from-[#D97706]/30 hover:border-[#D97706]/60 shadow-[0_4px_15px_rgba(217,119,6,0.1)]">
                    <div
                        class="w-[42px] h-[42px] rounded-[14px] flex items-center justify-center text-[1.2rem] shrink-0 bg-gradient-to-br from-[#D97706] to-[#B45309] text-white shadow-lg shadow-amber-700/30 ring-2 ring-white/10">
                        <i class="ph-bold ph-coffee-bean"></i>
                    </div>
                    <div class="flex-1">
                        <strong class="block text-[0.95rem] font-black text-white tracking-tight">ROASTERY</strong>
                        <span class="block text-[0.7rem] text-white/60 font-semibold mt-0.5 tracking-wide">Cari Beans Kopi
                            Favoritmu</span>
                    </div>
                    <div
                        class="w-7 h-7 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-[#D97706] group-hover:text-white transition-all">
                        <i class="ph-bold ph-caret-right text-[1rem] text-white/40 group-hover:text-white"></i>
                    </div>
                </a>
            </div>

            {{-- Footer --}}
            <div class="text-center pt-[16px] border-t border-white/5 mt-2">
                <span
                    class="text-[0.55rem] font-bold text-white/20 tracking-[0.25em] uppercase hover:text-white/40 transition-colors cursor-default">WADAHNGOPI.COM
                    &copy; 2026</span>
            </div>
        </main>
    </div>

@endsection