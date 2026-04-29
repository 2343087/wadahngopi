@extends('layouts.app')

@section('title', 'WadahNgopi - Premium Portal Cafe & Roastery')

@section('content')
    <div class="bg-noise flex flex-col min-h-screen min-h-[100dvh] relative overflow-hidden"
         style="background:
            radial-gradient(ellipse 80% 60% at 18% -8%, rgba(234,179,8,0.13) 0%, transparent 58%),
            radial-gradient(ellipse 55% 45% at 82% 105%, rgba(202,138,4,0.09) 0%, transparent 52%),
            radial-gradient(ellipse 100% 100% at 50% 50%, #FFFBF0 0%, #FEF3C7 100%);">

        {{-- Ambient Floating Orbs --}}
        <div class="absolute -top-16 -left-12 w-72 h-72 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(251,191,36,0.32) 0%, transparent 70%); animation: ambient-drift 14s ease-in-out infinite;"></div>
        <div class="absolute top-[35%] -right-16 w-60 h-60 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(120,80,50,0.14) 0%, transparent 70%); animation: ambient-drift 18s ease-in-out infinite reverse;"></div>
        <div class="absolute bottom-24 left-1/3 w-80 h-80 rounded-full pointer-events-none"
             style="background: radial-gradient(circle, rgba(255,240,200,0.45) 0%, transparent 70%); animation: ambient-drift 22s ease-in-out infinite 3s;"></div>

        <div class="flex-1 flex flex-col max-w-[430px] mx-auto w-full relative z-10 px-4 pb-24 pt-8">

            {{-- ── BENTO GRID HERO SECTION ────────────────────────────── --}}
            <div class="bento-hero-grid mb-4 animate-fade-up" style="
                display: grid;
                grid-template-columns: 2fr 1fr;
                grid-template-rows: auto auto;
                gap: 10px;
            ">
                {{-- TILE 1: Hero / Brand (large, 2-row span) --}}
                <div class="hero-tile relative overflow-hidden"
                     id="hero-tile"
                     style="grid-column: 1; grid-row: 1 / span 2; padding: 24px 20px; min-height: 200px; transform-style: preserve-3d; cursor: default;"
                     x-data="{}" @mousemove="
                        const r = $el.getBoundingClientRect();
                        const x = (event.clientX - r.left) / r.width - 0.5;
                        const y = (event.clientY - r.top) / r.height - 0.5;
                        $el.style.transform = `perspective(1000px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg) translateZ(10px)`;
                     " @mouseleave="$el.style.transform = 'perspective(1000px) rotateY(0deg) rotateX(0deg) translateZ(0px)'">

                    {{-- Warm coffee radial bg glow --}}
                    <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse 120% 80% at 0% 0%, rgba(234,179,8,0.18) 0%, transparent 60%);"></div>
                    <div class="absolute bottom-0 right-0 w-32 h-32 pointer-events-none" style="background: radial-gradient(circle, rgba(234,179,8,0.12) 0%, transparent 70%);"></div>

                    {{-- Logo --}}
                    <div class="w-10 h-10 rounded-[12px] bg-white/10 border border-white/20 flex items-center justify-center mb-4 relative z-10"
                         style="backdrop-filter: blur(12px);">
                        <img src="{{ asset('wadahngopi.png') }}" alt="WadahNgopi" class="w-6 h-6 object-contain">
                    </div>

                    {{-- Headline --}}
                    <div class="relative z-10">
                        <span class="block text-[0.45rem] font-black tracking-[0.35em] uppercase text-amber-300/60 mb-2">BY AK KREATIF</span>
                        <h1 class="text-[1.6rem] font-black text-white leading-[1.05] tracking-tighter mb-2">
                            <span class="text-shimmer">WadahNgopi</span>
                        </h1>
                        <p class="text-[0.65rem] text-amber-100/60 font-semibold leading-relaxed max-w-[160px]">
                            Satu wadah untuk para penikmat kopi Kalimantan Timur.
                        </p>
                    </div>

                    {{-- CTA Button --}}
                    <a href="{{ route('explore') }}"
                       class="relative z-10 mt-5 inline-flex items-center gap-2 px-4 py-2.5 rounded-[14px] font-black text-[0.78rem] text-[#2A1C15] transition-all active:scale-95"
                       style="background: linear-gradient(135deg, #EAB308, #FBBF24); box-shadow: 0 6px 20px rgba(234,179,8,0.45); margin-top: 20px; display: inline-flex;">
                        <i class="ph-bold ph-compass text-[0.95rem]"></i>
                        Jelajahi Cafe
                        <i class="ph-bold ph-arrow-right text-[0.7rem]"></i>
                    </a>
                </div>

                {{-- TILE 2: Stat — Total Cafe (CLICKABLE) --}}
                <a href="{{ route('explore') }}" 
                   class="stat-tile relative overflow-hidden group"
                   style="padding: 16px 14px; display: flex; flex-direction: column; justify-content: space-between; text-decoration: none;"
                   x-data="{ count: 0, target: {{ \App\Models\Cafe::count() }} }"
                   x-init="
                        const obs = new IntersectionObserver(([e]) => {
                            if (e.isIntersecting) {
                                const speed = 40; 
                                const step = () => {
                                    const diff = target - count;
                                    if (diff > 0) {
                                        count += Math.ceil(diff / 6);
                                        setTimeout(step, speed);
                                    } else { count = target; }
                                };
                                step();
                                obs.disconnect();
                            }
                        }, { threshold: 0.1 });
                        obs.observe($el);
                   ">
                    <div>
                        <div class="w-7 h-7 rounded-[8px] flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"
                             style="background: linear-gradient(135deg, rgba(234,179,8,0.2), rgba(234,179,8,0.08));">
                            <i class="ph-fill ph-storefront text-[1rem] text-amber-500"></i>
                        </div>
                        <div class="stat-number leading-none tracking-tighter" x-text="count">{{ \App\Models\Cafe::count() }}</div>
                        <div class="text-[0.55rem] font-black text-[#8B7355] uppercase tracking-widest mt-0.5">Cafe</div>
                    </div>
                    <div class="text-[0.5rem] text-[#2A1C15]/30 font-bold uppercase tracking-wider">Terdaftar</div>
                </a>

                {{-- TILE 3: Stat — Total Roastery (CLICKABLE) --}}
                <a href="{{ route('roastery') }}" 
                   class="stat-tile relative overflow-hidden group"
                   style="padding: 16px 14px; display: flex; flex-direction: column; justify-content: space-between; text-decoration: none;"
                   x-data="{ count: 0, target: {{ \App\Models\Roastery::count() }} }"
                   x-init="
                        const obs = new IntersectionObserver(([e]) => {
                            if (e.isIntersecting) {
                                const speed = 40;
                                const step = () => {
                                    const diff = target - count;
                                    if (diff > 0) {
                                        count += Math.ceil(diff / 6);
                                        setTimeout(step, speed);
                                    } else { count = target; }
                                };
                                step();
                                obs.disconnect();
                            }
                        }, { threshold: 0.1 });
                        obs.observe($el);
                   ">
                    <div>
                        <div class="w-7 h-7 rounded-[8px] flex items-center justify-center mb-2 group-hover:scale-110 transition-transform"
                             style="background: linear-gradient(135deg, rgba(120,80,50,0.2), rgba(120,80,50,0.08));">
                            <i class="ph-fill ph-coffee-bean text-[1rem] text-[#8B7355]"></i>
                        </div>
                        <div class="stat-number leading-none tracking-tighter" x-text="count">{{ \App\Models\Roastery::count() }}</div>
                        <div class="text-[0.55rem] font-black text-[#8B7355] uppercase tracking-widest mt-0.5">Roastery</div>
                    </div>
                    <div class="text-[0.5rem] text-[#2A1C15]/30 font-bold uppercase tracking-wider">Terdaftar</div>
                </a>
            </div>

            {{-- ── ROW 2: BENTO QUICK ACTIONS ────────────────────────── --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;" class="mb-4 animate-fade-up delay-200">

                {{-- Roulette Tile — 3D float, links to explore (roulette is embedded there) --}}
                <a href="{{ route('explore') }}#roulette"
                   class="glass-bento relative overflow-hidden group"
                   style="padding: 18px 16px; min-height: 110px; display: flex; flex-direction: column; justify-content: space-between; text-decoration: none;"
                   x-data="{}" @mousemove="
                        const r = $el.getBoundingClientRect();
                        const x = (event.clientX - r.left) / r.width - 0.5;
                        const y = (event.clientY - r.top) / r.height - 0.5;
                        $el.style.transform = `perspective(1000px) rotateY(${x * 10}deg) rotateX(${-y * 10}deg) translateY(-4px)`;
                   " @mouseleave="$el.style.transform = ''">
                    <div class="w-9 h-9 rounded-[12px] flex items-center justify-center"
                         style="background: linear-gradient(135deg, rgba(234,179,8,0.18), rgba(234,179,8,0.06)); font-size: 1.2rem;">
                        🎲
                    </div>
                    <div>
                        <div class="text-[0.85rem] font-black text-[#2A1C15] leading-tight">Roulette</div>
                        <div class="text-[0.55rem] font-bold text-[#8B7355] mt-0.5">Random cafe!</div>
                    </div>
                </a>

                {{-- Feature Tile: Informasi --}}
                <a href="{{ route('information') }}"
                   class="glass-bento relative overflow-hidden group"
                   style="padding: 18px 16px; min-height: 110px; display: flex; flex-direction: column; justify-content: space-between; text-decoration: none;">
                    <div class="w-9 h-9 rounded-[12px] flex items-center justify-center"
                         style="background: linear-gradient(135deg, rgba(14,165,233,0.18), rgba(14,165,233,0.06)); font-size: 1.2rem;">
                        <i class="ph-fill ph-newspaper text-sky-500"></i>
                    </div>
                    <div>
                        <div class="text-[0.85rem] font-black text-[#2A1C15] leading-tight">Artikel</div>
                        <div class="text-[0.55rem] font-bold text-[#8B7355] mt-0.5">Tips & info kopi</div>
                    </div>
                </a>
            </div>

            {{-- ── ABOUT GLASS CARD ────────────────────────────────────── --}}
            <div class="glass-bento animate-fade-up delay-300 mb-4" style="padding: 20px;">

                {{-- Section Header --}}
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-1 h-8 rounded-full shrink-0"
                         style="background: linear-gradient(to bottom, #EAB308, #CA8A04);"></div>
                    <div>
                        <span class="block text-[0.48rem] font-black tracking-[0.2em] uppercase text-amber-600/60 mb-0.5">TENTANG KAMI</span>
                        <h2 class="text-[1.15rem] font-black text-[#2A1C15] leading-tight tracking-tight">
                            Lebih dari <span class="text-amber-600/80 italic">Sekadar Kopi</span>
                        </h2>
                    </div>
                </div>

                <p class="text-[0.78rem] text-[#2A1C15]/70 font-semibold leading-relaxed mb-4">
                    Kami hadir untuk mempermudah ekosistem kopi lokal Kalimantan Timur tumbuh dan berkembang bersama di dunia digital.
                </p>

                {{-- Social Widgets --}}
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <a href="https://www.instagram.com/wadahngopi/" target="_blank"
                       class="group aspect-square flex items-center justify-center rounded-xl bg-white/60 border border-black/5 transition-all duration-300"
                       style="backdrop-filter: blur(8px);">
                        <i class="ph-fill ph-instagram-logo text-[1rem] text-[#2A1C15]/40 group-hover:text-[#E1306C] transition-colors"></i>
                    </a>
                    <a href="https://www.tiktok.com/@wadah.ngopi" target="_blank"
                       class="group aspect-square flex items-center justify-center rounded-xl bg-white/60 border border-black/5 hover:bg-black/5 transition-all duration-300"
                       style="backdrop-filter: blur(8px);">
                        <i class="ph-fill ph-tiktok-logo text-[1rem] text-[#2A1C15]/40 group-hover:text-black transition-colors"></i>
                    </a>
                    <a href="https://youtube.com/@wadahngopi" target="_blank"
                       class="group aspect-square flex items-center justify-center rounded-xl bg-white/60 border border-black/5 hover:bg-[#FF0000]/5 transition-all duration-300"
                       style="backdrop-filter: blur(8px);">
                        <i class="ph-fill ph-youtube-logo text-[1rem] text-[#2A1C15]/40 group-hover:text-[#FF0000] transition-colors"></i>
                    </a>
                    <a href="https://wa.me/6282199694350" target="_blank"
                       class="group aspect-square flex items-center justify-center rounded-xl bg-white/60 border border-black/5 hover:bg-[#25D366]/5 transition-all duration-300 active:scale-90"
                       style="backdrop-filter: blur(8px);">
                        <i class="ph-fill ph-whatsapp-logo text-[1rem] text-[#2A1C15]/40 group-hover:text-[#25D366] transition-colors"></i>
                    </a>
                </div>

                {{-- Roastery link --}}
                <a href="{{ route('roastery') }}"
                   class="flex items-center gap-3 p-3 rounded-[16px] border border-black/5 hover:border-amber-400/50 transition-all active:scale-[0.97] bg-white/40"
                   style="text-decoration: none; backdrop-filter: blur(8px);">
                    <div class="w-8 h-8 rounded-[10px] flex items-center justify-center text-amber-600"
                         style="background: linear-gradient(135deg, rgba(234,179,8,0.15), rgba(234,179,8,0.05));">
                        <i class="ph-bold ph-coffee-bean text-[1rem]"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-[0.82rem] font-black text-[#2A1C15]/80 tracking-tight">Jelajahi Roastery</span>
                        <div class="text-[0.55rem] font-bold text-[#8B7355]">Temukan roastery lokal terbaik</div>
                    </div>
                    <i class="ph-bold ph-arrow-right text-[#8B7355] text-[0.75rem]"></i>
                </a>
            </div>

            {{-- ── PARTNER PORTAL ──────────────────────────────────────── --}}
            <div class="glass-bento animate-fade-up delay-400" style="padding: 18px;">
                <p class="text-[0.48rem] font-black text-[#140C08]/45 tracking-[0.22em] text-center mb-3 uppercase">
                    DAFTARKAN CAFE & ROASTERY ANDA
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="/admin/login"
                       class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-[14px] bg-white/50 border border-black/5 hover:bg-white/80 transition-all text-[#140C08] font-bold text-[0.75rem]"
                       style="backdrop-filter: blur(8px); text-decoration: none;">
                        Masuk
                    </a>
                    <a href="/admin/register"
                       class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-[14px] hover:opacity-90 transition-all text-white font-black text-[0.75rem]"
                       style="background: linear-gradient(135deg, #2A1C15, #140C08); box-shadow: 0 8px 16px -4px rgba(20,12,8,0.3); text-decoration: none;">
                        Daftar
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-4">
                <span class="text-[0.42rem] font-black text-amber-600/40 tracking-[0.4em] uppercase">WADAHNGOPI.COM</span>
            </div>

        </div>
    </div>

    <style>
        .text-shimmer {
            background: linear-gradient(110deg, #fff 30%, #FCD34D 48%, #ffffff 55%, #FCD34D 62%, #fff 78%);
            background-size: 260% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: text-shimmer-sweep 3.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        @keyframes text-shimmer-sweep {
            0%   { background-position: 260% center; }
            100% { background-position: -40% center; }
        }
    </style>
@endsection