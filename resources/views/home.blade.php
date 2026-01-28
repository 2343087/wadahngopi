@extends('layouts.app')

@section('title', 'WadahNgopi - Portal Cafe Terbaik di Kalimantan')

@section('content')
    <div class="landing-page min-h-screen bg-[--color-cream] pb-32">
        {{-- High-Contrast Hero --}}
        <header class="relative px-6 pt-24 pb-20 text-center overflow-hidden">
            <div
                class="absolute -top-24 -right-24 w-80 h-80 bg-[--color-amber] rounded-full blur-[120px] opacity-10 animate-pulse-slow">
            </div>
            <div class="absolute top-1/2 -left-24 w-64 h-64 bg-[--color-espresso] rounded-full blur-[100px] opacity-10">
            </div>

            <div class="animate-up relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[--color-espresso] text-[--color-cream] text-[0.65rem] font-black rounded-xl mb-8 tracking-[0.2em] uppercase shadow-lg shadow-black/10">
                    <i class="ph-fill ph-sparkle animate-spin-slow"></i> AKCreatif
                </div>
                <h1 class="text-5xl font-black text-[--color-espresso] leading-[1] tracking-tighter mb-6">
                    Wadah<span class="text-[--color-amber]">Ngopi</span>
                </h1>
                <p class="text-slate-600 font-bold text-[1.05rem] leading-relaxed max-w-[320px] mx-auto mb-10 opacity-90">
                    mempermudah kalian mencari spot ngopi di Kalimantan.
                </p>
            </div>
        </header>

        {{-- Feature Grid with High Contrast --}}
        <section class="px-6 py-8 relative">
            <div class="grid grid-cols-1 gap-5">
                <div class="premium-feature-card animate-up" style="animation-delay: 0.3s">
                    <div class="feature-icon bg-[--color-espresso]/5 text-[--color-espresso]">
                        <i class="ph ph-navigation-arrow"></i>
                    </div>
                    <div>
                        <h3 class="text-[1.1rem] font-black text-[--color-espresso] mb-1">Cari Tanpa Ribet</h3>
                        <p class="text-slate-500 font-bold text-[0.85rem] leading-snug">Cek lokasi terdekat real-time pake
                            Google Maps!</p>
                    </div>
                </div>

                <div class="premium-feature-card animate-up" style="animation-delay: 0.4s">
                    <div class="feature-icon bg-[--color-amber]/10 text-[--color-amber]">
                        <i class="ph ph-info"></i>
                    </div>
                    <div>
                        <h3 class="text-[1.1rem] font-black text-[--color-espresso] mb-1">Info Lengkap & Jelas</h3>
                        <p class="text-slate-500 font-bold text-[0.85rem] leading-snug">Jam operasional, fasilitas, sampe
                            menu andalan ada semua.</p>
                    </div>
                </div>

                <div class="premium-feature-card animate-up" style="animation-delay: 0.5s">
                    <div class="feature-icon bg-[--color-coffee]/10 text-[--color-coffee]">
                        <i class="ph ph-heart"></i>
                    </div>
                    <div>
                        <h3 class="text-[1.1rem] font-black text-[--color-espresso] mb-1">Simpan Favoritmu</h3>
                        <p class="text-slate-500 font-bold text-[0.85rem] leading-snug">Nemu yang pas? Simpen aja dulu biar
                            nanti gampang carinya.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Mission High-Contrast Section --}}
        <section class="px-6 py-12">
            <div class="mission-luxury-card animate-up" style="animation-delay: 0.6s">
                <div class="absolute -right-8 -bottom-8 opacity-10">
                    <i class="ph ph-coffee-bean text-[14rem] rotate-45"></i>
                </div>

                <h2 class="text-3xl font-black mb-5 tracking-tight leading-[1.1] relative z-20 text-white">Dibuat khusus
                    untuk buanmu yang nyari tempat nongkrong.</h2>
                <p class="text-[#C8A68F] text-[1.1rem] font-bold leading-relaxed mb-8 relative z-20">
                    WadahNgopi hadir buat memangkas waktu buanmu keliling-keliling cari tempat yang pas. Cari, liat, terus
                    OTW!
                </p>

                <div class="text-center w-full text-[0.8rem] font-black tracking-[0.3em] uppercase text-[--color-amber]">
                    <div class="h-[2px] w-8 bg-[--color-amber]"></div>
                    <span class="text-[0.8rem] font-black tracking-[0.3em] uppercase text-[--color-amber]">Support UMKM
                        Lokal</span>
                </div>
            </div>
        </section>

        {{-- Luxury Footer --}}
        <div class="px-6 pt-10 pb-32 text-center flex flex-col items-center">

            <div class="animate-up relative z-10 mb-10" style="animation-delay: 0.2s">
                <a href="{{ route('explore') }}" style="background-color: #2C1810; color: #FFFFFF;"
                    class="inline-flex items-center gap-3 px-10 py-5 rounded-[22px] font-black text-[1.1rem] shadow-[0_20px_40px_-12px_rgba(44,24,16,0.5)] hover:scale-[1.03] active:scale-95 transition-all">
                    Ayo Cari Cafe <i class="ph-bold ph-arrow-right text-[1.2rem]"></i>
                </a>
            </div>

            <div class="space-y-8">

                <div class="flex justify-center gap-7">
                    <a href="https://www.instagram.com/chrnlsdlng/" target="_blank" rel="noopener noreferrer"
                        class="w-12 h-12 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm hover:shadow-xl hover:scale-125 hover:-translate-y-1.5 hover:text-white hover:bg-[#E1306C] hover:border-[#E1306C] transition-all duration-300 text-slate-500 no-underline group">
                        <i class="ph-fill ph-instagram-logo text-xl transition-transform group-hover:rotate-[8deg]"></i>
                    </a>

                    <a href="https://www.tiktok.com/@chrnlsdlng" target="_blank" rel="noopener noreferrer"
                        class="w-12 h-12 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm hover:shadow-xl hover:scale-125 hover:-translate-y-1.5 hover:text-white hover:bg-black hover:border-black transition-all duration-300 text-slate-500 no-underline group">
                        <i class="ph-fill ph-tiktok-logo text-xl transition-transform group-hover:rotate-[-8deg]"></i>
                    </a>

                    <a href="https://www.facebook.com/chrnlsdlng" target="_blank" rel="noopener noreferrer"
                        class="w-12 h-12 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm hover:shadow-xl hover:scale-125 hover:-translate-y-1.5 hover:text-white hover:bg-[#1877F2] hover:border-[#1877F2] transition-all duration-300 text-slate-500 no-underline group">
                        <i class="ph-fill ph-facebook-logo text-xl transition-transform group-hover:rotate-[8deg]"></i>
                    </a>
                </div>

                <p class="text-[0.65rem] font-black text-slate-400 tracking-[0.3em] uppercase opacity-50">
                    WadahNgopi.Com &copy; 2026
                </p>

            </div>
        </div>


        <style>
            .premium-feature-card {
                background: white;
                padding: 22px;
                border-radius: 28px;
                border: 1px solid rgba(0, 0, 0, 0.04);
                display: flex;
                align-items: center;
                gap: 18px;
                box-shadow: 0 10px 30px -10px rgba(44, 24, 16, 0.06);
                transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            }

            .premium-feature-card:active {
                transform: scale(0.96);
                background: var(--color-cream-dark);
            }

            .feature-icon {
                width: 56px;
                height: 56px;
                border-radius: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.6rem;
                flex-shrink: 0;
            }

            .mission-luxury-card {
                padding: 40px;
                background: linear-gradient(135deg, #2C1810 0%, #3E2723 100%) !important;
                border-radius: 45px;
                color: white;
                overflow: hidden;
                position: relative;
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
            }

            .animate-spin-slow {
                animation: spin 8s linear infinite;
            }

            @keyframes spin {
                from {
                    transform: rotate(0deg);
                }

                to {
                    transform: rotate(360deg);
                }
            }

            .animate-pulse-slow {
                animation: pulse-slow 4s ease-in-out infinite;
            }

            @keyframes pulse-slow {

                0%,
                100% {
                    opacity: 0.1;
                    transform: scale(1);
                }

                50% {
                    opacity: 0.2;
                    transform: scale(1.1);
                }
            }
        </style>
@endsection