@extends('layouts.app')

@section('title', 'WadahNgopi - Portal Cafe Terbaik di Kalimantan')

@section('content')
    <div class="flex flex-col min-h-[calc(100vh-100px)] min-h-[calc(100dvh-100px)] px-5 py-4 pb-5 bg-[#FFFDFB]">
        {{-- Hero --}}
        <header class="text-center shrink-0 pb-[18px]">
            <div
                class="w-[60px] h-[60px] mx-auto mb-3 bg-white rounded-[20px] flex items-center justify-center shadow-[0_8px_30px_rgba(44,24,16,0.12)]">
                <img src="{{ asset('wadahicon.png') }}" alt="WadahNgopi" class="w-[42px] h-[42px]">
            </div>
            <span
                class="inline-block bg-[#2C1810] text-[#FFFDFB] px-4 py-1.5 rounded-full text-[0.55rem] font-extrabold tracking-[0.12em] mb-3">BY
                : AK KREATIF</span>
            <h1 class="text-[2.2rem] font-black text-[#2C1810] tracking-tight leading-none mb-1.5">Wadah<span
                    class="text-[#F59E0B]">Ngopi</span></h1>
            <p class="text-[0.85rem] text-[#8B7355] font-semibold leading-relaxed max-w-[280px] mx-auto">untuk merajut
                silaturahmi dan mendekatkan para pelaku usaha kedai kopi dengan para penikmat
                kopi di Samarinda.</p>
        </header>

        {{-- Main Card --}}
        <main
            class="flex-1 flex flex-col bg-gradient-to-br from-[#3D2518] to-[#241208] rounded-[32px] px-[22px] py-6 shadow-[0_20px_60px_rgba(44,24,16,0.25)]">
            {{-- Header --}}
            <div class="flex gap-3.5 mb-4">
                <div class="w-1 h-11 bg-gradient-to-b from-[#F59E0B] to-[#D97706] rounded shrink-0"></div>
                <div>
                    <span class="block text-[0.6rem] font-bold text-[#F59E0B] tracking-[0.2em] mb-1.5">TENTANG KAMI</span>
                    <h2 class="text-[1.3rem] font-extrabold text-white leading-tight">Lebih dari Sekadar Kopi</h2>
                </div>
            </div>

            {{-- Body --}}
            <p class="text-[0.85rem] text-white/80 font-medium leading-relaxed mb-5">di wadah ngopi ini. para pelaku usaha
                kedai kopi. dapat memperkenalkan produk kedai kopinya,
                secara lebih mudah.</p>

            {{-- Social Media --}}
            <div class="flex justify-center gap-7 py-[18px] border-y border-white/10 mb-5">
                <a href="https://www.instagram.com/wadahngopi/" target="_blank"
                    class="group flex flex-col items-center gap-1.5 no-underline transition-transform duration-300 hover:-translate-y-1">
                    <i class="ph-fill ph-instagram-logo text-[1.6rem] text-[#E1306C]"></i>
                    <span class="text-[0.5rem] font-bold text-white/50 tracking-[0.1em]">INSTAGRAM</span>
                </a>
                <a href="https://www.tiktok.com/@wadah.ngopi?_r=1&_t=ZS-93dSno0Z0Cu"
                    class="group flex flex-col items-center gap-1.5 no-underline transition-transform duration-300 hover:-translate-y-1">
                    <i class="ph-fill ph-tiktok-logo text-[1.6rem] text-white"></i>
                    <span class="text-[0.5rem] font-bold text-white/50 tracking-[0.1em]">TIKTOK</span>
                </a>
                <a href="https://youtube.com/@wadahngopi?si=4MYRQCMn6cjEeAlB"
                    class="group flex flex-col items-center gap-1.5 no-underline transition-transform duration-300 hover:-translate-y-1">
                    <i class="ph-fill ph-youtube-logo text-[1.6rem] text-[#FF0000]"></i>
                    <span class="text-[0.5rem] font-bold text-white/50 tracking-[0.1em]">YOUTUBE</span>
                </a>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col gap-3 mt-auto mb-5">
                <a href="{{ route('explore') }}"
                    class="group flex items-center gap-4 px-[18px] py-4 rounded-[20px] no-underline transition-all duration-300 hover:translate-x-1.5 bg-gradient-to-br from-[#F59E0B]/18 to-[#D97706]/8 border border-[#F59E0B]/35 hover:from-[#F59E0B]/28 hover:to-[#D97706]/12 hover:border-[#F59E0B]/50">
                    <div
                        class="w-[46px] h-[46px] rounded-2xl flex items-center justify-center text-[1.2rem] shrink-0 bg-gradient-to-br from-[#F59E0B] to-[#D97706] text-white">
                        <i class="ph-bold ph-compass"></i>
                    </div>
                    <div class="flex-1">
                        <strong class="block text-base font-extrabold text-white">JELAJAHI CAFE</strong>
                        <span class="block text-[0.72rem] text-white/50 font-semibold mt-0.5">Ayo Cari Cafe
                            Favoritmu</span>
                    </div>
                    <i
                        class="ph-bold ph-caret-right text-[1.1rem] text-white/30 transition-all duration-300 group-hover:text-[#F59E0B] group-hover:translate-x-1"></i>
                </a>

                <a href="{{ route('information') }}"
                    class="group flex items-center gap-4 px-[18px] py-4 rounded-[20px] no-underline transition-all duration-300 hover:translate-x-1.5 bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20">
                    <div
                        class="w-[46px] h-[46px] rounded-2xl flex items-center justify-center text-[1.2rem] shrink-0 bg-white/10 text-white">
                        <i class="ph-bold ph-newspaper"></i>
                    </div>
                    <div class="flex-1">
                        <strong class="block text-base font-extrabold text-white">INFORMASI</strong>
                        <span class="block text-[0.72rem] text-white/50 font-semibold mt-0.5">Berita & Event
                            Terbaru</span>
                    </div>
                    <i
                        class="ph-bold ph-caret-right text-[1.1rem] text-white/30 transition-all duration-300 group-hover:text-[#F59E0B] group-hover:translate-x-1"></i>
                </a>

                <a href="{{ route('roastery') }}"
                    class="group flex items-center gap-4 px-[18px] py-4 rounded-[20px] no-underline transition-all duration-300 hover:translate-x-1.5 bg-gradient-to-br from-[#D97706]/15 to-[#B45309]/8 border border-[#D97706]/35 hover:from-[#D97706]/25 hover:to-[#B45309]/12 hover:border-[#D97706]/50">
                    <div
                        class="w-[46px] h-[46px] rounded-2xl flex items-center justify-center text-[1.2rem] shrink-0 bg-gradient-to-br from-[#D97706] to-[#B45309] text-white">
                        <i class="ph-bold ph-coffee-bean"></i>
                    </div>
                    <div class="flex-1">
                        <strong class="block text-base font-extrabold text-white">ROASTERY</strong>
                        <span class="block text-[0.72rem] text-white/50 font-semibold mt-0.5">Cari Beans Kopi
                            Favoritmu</span>
                    </div>
                    <i
                        class="ph-bold ph-caret-right text-[1.1rem] text-white/30 transition-all duration-300 group-hover:text-[#F59E0B] group-hover:translate-x-1"></i>
                </a>

            </div>

            {{-- Footer --}}
            <div class="text-center pt-[18px] border-t border-white/5">
                <span class="text-[0.55rem] font-bold text-white/20 tracking-[0.2em]">WADAHNGOPI.COM &copy; 2026</span>
            </div>
        </main>
    </div>
@endsection