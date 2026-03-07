@extends('layouts.app')

@section('title', 'WadahNgopi - Premium Portal Cafe & Roastery')

@section('content')
    <div
        class="bg-noise flex flex-col min-h-screen min-h-[100dvh] px-4 md:px-5 pb-20 relative z-10 overflow-hidden bg-[#fffdfb]">

        {{-- Vibrant Lighting Blobs --}}
        <div
            class="absolute -top-10 -left-10 w-64 h-64 bg-amber-400/25 blur-[100px] rounded-full animate-pulse pointer-events-none">
        </div>
        <div
            class="absolute top-[30%] -right-10 w-56 h-56 bg-sky-400/15 blur-[80px] rounded-full animate-float pointer-events-none">
        </div>
        <div
            class="absolute bottom-20 left-1/2 -translate-x-1/2 w-72 h-72 bg-orange-400/10 blur-[120px] rounded-full pointer-events-none">
        </div>

        <div class="flex-1 flex flex-col max-w-[430px] mx-auto w-full relative z-20">

            {{-- Hero Section --}}
            <header class="text-center shrink-0 py-6 px-5 relative z-20">
                <div class="relative inline-block mb-3 animate-fade-up scale-90 md:scale-100">
                    {{-- Refined Logo Widget --}}
                    <div
                        class="w-[52px] h-[52px] mx-auto bg-white rounded-[15px] flex items-center justify-center shadow-[0_10px_25px_-5px_rgba(42,28,21,0.08)] ring-1 ring-black/5 relative active:scale-95 transition-transform duration-300">
                        <img src="{{ asset('wadahngopi.png') }}" alt="WadahNgopi" class="w-7.5 h-7.5 object-contain">
                    </div>
                </div>

                <div class="flex flex-col items-center gap-1 mb-2.5 animate-fade-up delay-100 scale-90 md:scale-100">
                    <span
                        class="inline-block px-3 py-0.5 rounded-full text-[0.45rem] font-black tracking-[0.4em] uppercase text-amber-900/30 bg-amber-900/5 border border-amber-900/5">
                        BY : AK KREATIF
                    </span>
                </div>

                <h1
                    class="text-[1.8rem] md:text-[2.2rem] font-black text-[#2A1C15] tracking-tighter leading-none mb-1.5 animate-fade-up delay-200 relative inline-block group">
                    <span class="text-shimmer">WadahNgopi</span>
                </h1>

                <p
                    class="text-[0.68rem] md:text-[0.75rem] text-[#8D7B70/80] font-bold leading-tight max-w-[200px] mx-auto tracking-tight animate-fade-up delay-300 opacity-70">
                    Satu wadah untuk mendekatkan kedai kopi dengan para penikmatnya.
                </p>
            </header>

            {{-- Responsive Premium Glass Card --}}
            <main
                class="fresh-glass flex-1 flex flex-col rounded-[40px] shadow-[0_30px_60px_-15px_rgba(135,100,80,0.12)] relative z-20 animate-fade-up delay-400 border border-white/80 pb-36">

                <div class="flex-1 flex flex-col rounded-[40px] px-6 py-6 relative overflow-hidden">
                    {{-- Inner Glass Texture --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-white/95 via-white/70 to-white/40 backdrop-blur-[40px]">
                    </div>
                    <div class="absolute inset-0 bg-noise opacity-[0.015] pointer-events-none"></div>

                    {{-- Section Header --}}
                    <div class="flex items-start gap-3.5 mb-5 relative z-10">
                        <div class="w-1 h-9 bg-gradient-to-b from-amber-400 to-amber-600 rounded-full shrink-0 shadow-sm">
                        </div>
                        <div class="pt-0.5">
                            <span
                                class="text-amber-600/60 text-[0.5rem] font-black tracking-[0.2em] uppercase mb-0.5 block">TENTANG
                                KAMI</span>
                            <h2 class="text-[1.25rem] font-black text-[#2A1C15] leading-[1] tracking-tight">
                                Lebih dari<br><span class="text-amber-600/80 font-extrabold italic bg-amber-500/5">Sekadar
                                    Kopi</span>
                            </h2>
                        </div>
                    </div>

                    <p
                        class="text-[0.8rem] text-[#2A1C15]/70 font-bold leading-relaxed mb-4.5 tracking-tight relative z-10">
                        Kami hadir untuk mempermudah ekosistem kopi lokal Kalimantan Timur tumbuh dan berkembang bersama di
                        dunia digital.
                    </p>

                    {{-- jewellery-style Social Widgets --}}
                    <div class="grid grid-cols-4 gap-2 mb-5 relative z-10">
                        <a href="https://www.instagram.com/wadahngopi/" target="_blank" class="group">
                            <div
                                class="w-full aspect-square flex items-center justify-center rounded-xl bg-white shadow-sm border border-black/5 group-hover:bg-[#E1306C]/5 transition-all duration-300">
                                <i
                                    class="ph-fill ph-instagram-logo text-[1rem] text-[#2A1C15]/40 group-hover:text-[#E1306C] transition-colors"></i>
                            </div>
                        </a>
                        <a href="https://www.tiktok.com/@wadah.ngopi?_r=1&_t=ZS-93dSno0Z0Cu" target="_blank" class="group">
                            <div
                                class="w-full aspect-square flex items-center justify-center rounded-xl bg-white shadow-sm border border-black/5 group-hover:bg-black/5 transition-all duration-300">
                                <i
                                    class="ph-fill ph-tiktok-logo text-[1rem] text-[#2A1C15]/40 group-hover:text-black transition-colors"></i>
                            </div>
                        </a>
                        <a href="https://youtube.com/@wadahngopi?si=4MYRQCMn6cjEeAlB" target="_blank" class="group">
                            <div
                                class="w-full aspect-square flex items-center justify-center rounded-xl bg-white shadow-sm border border-black/5 group-hover:bg-[#FF0000]/5 transition-all duration-300">
                                <i
                                    class="ph-fill ph-youtube-logo text-[1rem] text-[#2A1C15]/40 group-hover:text-[#FF0000] transition-colors"></i>
                            </div>
                        </a>
                        <a href="https://wa.me/6282199694350" target="_blank" class="group">
                            <div
                                class="w-full aspect-square flex items-center justify-center rounded-xl bg-white shadow-sm border border-black/5 group-hover:bg-[#25D366]/5 transition-all duration-300 active:scale-90">
                                <i
                                    class="ph-fill ph-whatsapp-logo text-[1rem] text-[#2A1C15]/40 group-hover:text-[#25D366] transition-colors"></i>
                            </div>
                        </a>
                    </div>

                    {{-- Vibrant CTA Stack --}}
                    <div class="flex flex-col gap-3 relative z-10 mb-6">
                        {{-- Explore --}}
                        <a href="{{ route('explore') }}"
                            class="group flex items-center gap-3.5 p-3.5 rounded-[24px] bg-gradient-to-br from-amber-400 to-amber-500 shadow-[0_12px_24px_-6px_rgba(245,158,11,0.35)] hover:shadow-[0_16px_32px_-6px_rgba(245,158,11,0.45)] transition-all duration-400 active:scale-[0.97] ring-1 ring-amber-300/50">
                            <div
                                class="w-10 h-10 rounded-[12px] flex items-center justify-center text-[1.1rem] shrink-0 bg-white shadow-sm text-amber-600 transition-transform group-hover:scale-105">
                                <i class="ph-bold ph-compass"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-[0.9rem] font-black text-[#2A1C15] tracking-tight leading-none mb-0.5">
                                    JELAJAHI
                                    CAFE</h3>
                                <p
                                    class="text-[0.55rem] text-amber-900/60 font-black uppercase tracking-widest leading-none">
                                    Cari Cafe Kesukaan Kamu</p>
                            </div>
                            <div
                                class="w-6 h-6 rounded-lg bg-black/5 flex items-center justify-center group-hover:bg-white transition-all">
                                <i class="ph-bold ph-arrow-right text-[0.7rem]"></i>
                            </div>
                        </a>

                        {{-- Split Row --}}
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('roastery') }}"
                                class="group flex items-center gap-2 p-2.5 rounded-[16px] bg-white border border-black/5 hover:border-amber-400 transition-all active:scale-[0.96] shadow-sm">
                                <div
                                    class="w-7 h-7 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-all">
                                    <i class="ph-bold ph-coffee-bean text-[0.9rem]"></i>
                                </div>
                                <span class="text-[0.8rem] font-black text-[#2A1C15]/80 tracking-tight">Roastery</span>
                            </a>
                            <a href="{{ route('information') }}"
                                class="group flex items-center gap-2 p-2.5 rounded-[16px] bg-white border border-black/5 hover:border-sky-400 transition-all active:scale-[0.96] shadow-sm">
                                <div
                                    class="w-7 h-7 rounded-lg bg-sky-500/10 flex items-center justify-center text-sky-600 group-hover:bg-sky-500 group-hover:text-white transition-all">
                                    <i class="ph-bold ph-newspaper text-[0.9rem]"></i>
                                </div>
                                <span class="text-[0.8rem] font-black text-[#2A1C15]/80 tracking-tight">Informasi</span>
                            </a>
                        </div>
                    </div>

                    {{-- Clean Partner Portal --}}
                    <div class="mt-auto pt-5 border-t border-black/5 relative z-10">
                        <p
                            class="text-[0.5rem] font-black text-[#140C08]/50 tracking-[0.25em] text-center mb-4 uppercase leading-relaxed">
                            MASUK DAN DAFTAR CAFE SERTA ROASTERY ANDA SEKARANG JUGA</p>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="/admin/login"
                                class="flex items-center justify-center gap-2 px-4 py-3 rounded-[16px] bg-black/5 border border-transparent hover:bg-black/10 transition-all text-[#140C08] font-bold text-[0.75rem]">
                                Masuk
                            </a>
                            <a href="/admin/register"
                                class="flex items-center justify-center gap-2 px-4 py-3 rounded-[16px] bg-gradient-to-b from-[#2A1C15] to-[#140C08] hover:opacity-95 transition-all text-white font-black text-[0.75rem] shadow-[0_8px_16px_-4px_rgba(20,12,8,0.3)] ring-1 ring-white/10">
                                Daftar
                            </a>
                        </div>
                    </div>

                    {{-- Minimal Footer --}}
                    <div class="text-center mt-5">
                        <span
                            class="text-[0.45rem] font-black text-amber-600/50 tracking-[0.4em] uppercase">WADAHNGOPI.COM</span>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <style>
        .fresh-glass {
            position: relative;
            background: rgba(255, 255, 255, 0.4);
            overflow: hidden;
        }

        .text-shimmer {
            background: linear-gradient(110deg, #2A1C15 35%, #F59E0B 45%, #FFFFFF 50%, #F59E0B 55%, #2A1C15 65%);
            background-size: 250% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: text-shimmer-sweep 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        @keyframes text-shimmer-sweep {
            0% {
                background-position: 250% center;
            }

            100% {
                background-position: -50% center;
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0);
            }

            50% {
                transform: translateY(-10px) rotate(2deg);
            }
        }

        .animate-float {
            animation: float 10s ease-in-out infinite;
        }
    </style>
@endsection