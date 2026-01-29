@extends('layouts.app')

@section('title', 'WadahNgopi - Portal Cafe Terbaik di Kalimantan')

@section('content')
    <div class="landing-page min-h-screen bg-cream pb-32">
        {{-- High-Contrast Hero --}}
        <header class="relative px-6 pt-24 pb-16 text-center overflow-hidden">
            <div
                class="absolute -top-24 -right-24 w-80 h-80 bg-amber rounded-full blur-[120px] opacity-10 animate-pulse-slow">
            </div>
            <div class="absolute top-1/2 -left-24 w-64 h-64 bg-espresso rounded-full blur-[100px] opacity-10">
            </div>

            <div class="animate-up relative z-10 flex flex-col items-center">
                {{-- Developer Logo Slot --}}
                <div class="group relative mb-8">
                    <div
                        class="absolute inset-0 bg-espresso rounded-2xl blur-lg opacity-20 group-hover:opacity-40 transition-opacity">
                    </div>
                    <div
                        class="relative w-16 h-16 bg-white border-2 border-espresso/5 rounded-2xl flex items-center justify-center shadow-xl transform group-hover:rotate-6 transition-transform">
                        <img
                            src="{{ asset('wadahicon.jpg') }}"
                            alt="Logo WadahNgopi"
                            class="w-12 h-12 object-contain select-none"
                            loading="eager"
                            draggable="false"
                        >
                    </div>
                    <div
                        class="mt-3 bg-espresso text-cream px-3 py-1 rounded-full text-[0.6rem] font-black tracking-widest uppercase shadow-lg">
                        BY : AK KREATIF
                    </div>
                </div>

                <h1 class="text-5xl font-black text-espresso leading-[1] tracking-tighter mb-4">
                    Wadah<span class="text-amber">Ngopi</span>
                </h1>
                <p class="text-slate-600 font-bold text-[1.1rem] leading-relaxed max-w-[320px] mx-auto opacity-90">
                    Mempermudah buanmu mencari spot ngopi nyaman di Kalimantan.
                </p>
            </div>
        </header>

        {{-- About Section (Tentang Kami) --}}
        <section class="px-6 py-10 relative">
            <div class="mission-luxury-card animate-up">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1.5 h-8 bg-amber rounded-full shadow-[0_0_15px_rgba(217,119,6,0.5)]"></div>
                    <div>
                        <span class="text-[0.65rem] font-bold text-amber uppercase tracking-[0.3em]">Tentang
                            Kami</span>
                        <h2 class="text-2xl font-black text-white tracking-tight">Lebih dari Sekadar Kopi</h2>
                    </div>
                </div>

                <div class="space-y-6">
                    <p class="text-cream-dark text-[1.05rem] font-semibold leading-relaxed opacity-90">
                        Di WadahNgopi, secangkir kopi adalah awal kolaborasi. Kami bukan sekadar tempat singgah, melainkan
                        ruang untuk merajut silaturahmi dan menyatukan ide para pelaku usaha.
                    </p>
                    <p class="text-cream-dark text-[1.05rem] font-semibold leading-relaxed opacity-90">
                        Setiap meja adalah peluang bagi buanmu untuk tumbuh dan maju bersama.
                    </p>
                </div>

                <div class="mt-10 pt-8 border-t border-white/10">
                    <h3 class="text-lg font-black text-amber mb-3">Harmoni Komunitas</h3>
                    <p class="text-white/70 text-[0.95rem] font-bold leading-relaxed mb-6">
                        Setiap "Wadah" punya cerita. Kami hadir untuk memastikan setiap cerita lokal di Kalimantan terdengar
                        dan diapresiasi oleh buanmu semua.
                    </p>

                    <div class="flex items-center gap-3 py-4 bg-white/5 rounded-2xl px-5 border border-white/5">
                        <i class="ph-bold ph-paint-brush-broad text-amber text-2xl"></i>
                        <div>
                            <h4 class="text-white text-xs font-black uppercase tracking-wider mb-0.5">Kearifan Lokal</h4>
                            <p class="text-white/60 text-[0.8rem] font-bold">Menghangatkan buanmu dengan spot kopi terbaik.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Admin Social Grid --}}
        <section class="px-6 py-12 text-center">
            <div class="animate-up mb-8">
                <span
                    class="inline-block px-4 py-1.5 bg-espresso/5 text-espresso text-[0.6rem] font-black uppercase tracking-[0.3em] rounded-full border border-espresso/10">
                    Sapa Developer Kami
                </span>
            </div>

            <div class="grid grid-cols-2 gap-4 animate-up">
                <a href="https://www.instagram.com/ak_kreatif/" target="_blank" class="social-admin-card group">
                    <div
                        class="admin-icon-box bg-[#E1306C]/10 text-[#E1306C] group-hover:bg-[#E1306C] group-hover:text-white transition-all shadow-sm">
                        <i class="ph-bold ph-instagram-logo"></i>
                    </div>
                    <span class="text-[0.7rem] font-black text-espresso uppercase tracking-widest">Instagram</span>
                </a>
                <a href="https://www.instagram.com/ak_kreatif/" target="_blank" class="social-admin-card group">
                    <div
                        class="admin-icon-box bg-black/10 text-black group-hover:bg-black group-hover:text-white transition-all shadow-sm">
                        <i class="ph-bold ph-tiktok-logo"></i>
                    </div>
                    <span class="text-[0.7rem] font-black text-espresso uppercase tracking-widest">TikTok</span>
                </a>
                <a href="https://www.instagram.com/ak_kreatif/" target="_blank" class="social-admin-card group">
                    <div
                        class="admin-icon-box bg-[#FF0000]/10 text-[#FF0000] group-hover:bg-[#FF0000] group-hover:text-white transition-all shadow-sm">
                        <i class="ph-bold ph-youtube-logo"></i>
                    </div>
                    <span class="text-[0.7rem] font-black text-espresso uppercase tracking-widest">YouTube</span>
                </a>
                <a href="https://www.instagram.com/ak_kreatif/" target="_blank" class="social-admin-card group">
                    <div
                        class="admin-icon-box bg-[#25D366]/10 text-[#25D366] group-hover:bg-[#25D366] group-hover:text-white transition-all shadow-sm">
                        <i class="ph-bold ph-whatsapp-logo"></i>
                    </div>
                    <span class="text-[0.7rem] font-black text-espresso uppercase tracking-widest">WhatsApp</span>
                </a>
            </div>
        </section>

        {{-- Featured Features (Fitur Unggulan) --}}
        <section class="px-6 py-10 relative">
            <div class="flex items-center gap-3 mb-8 animate-up">
                <div class="w-1.5 h-7 bg-espresso rounded-full"></div>
                <h2 class="text-2xl font-black text-espresso tracking-tight">Fitur Unggulan</h2>
            </div>

            <div class="grid grid-cols-1 gap-5">
                <div class="premium-feature-card-v2 animate-up" style="animation-delay: 0.2s">
                    <div class="feature-icon-v2 bg-espresso/5 text-espresso">
                        <i class="ph-bold ph-navigation-arrow"></i>
                    </div>
                    <div>
                        <h3 class="text-[1.1rem] font-black text-espresso mb-1">Cari Tempat Nongkrong Anti Bingung</h3>
                        <p class="text-slate-500 font-bold text-[0.85rem] leading-snug">Cek lokasi Dan Jarak Wadah Tanpa Bingung
                            pake Google Maps!</p>
                    </div>
                </div>

                <div class="premium-feature-card-v2 animate-up" style="animation-delay: 0.3s">
                    <div class="feature-icon-v2 bg-amber/10 text-amber">
                        <i class="ph-bold ph-info"></i>
                    </div>
                    <div>
                        <h3 class="text-[1.1rem] font-black text-espresso mb-1">Info Jelas & Lengkap</h3>
                        <p class="text-slate-500 font-bold text-[0.85rem] leading-snug">Fasilitas, menu, sampai jam buka ada
                            barataan.</p>
                    </div>
                </div>

                <div class="premium-feature-card-v2 animate-up" style="animation-delay: 0.4s">
                    <div class="feature-icon-v2 bg-coffee/10 text-coffee">
                        <i class="ph-bold ph-heart"></i>
                    </div>
                    <div>
                        <h3 class="text-[1.1rem] font-black text-espresso mb-1">Simpan Wadah Favorit</h3>
                        <p class="text-slate-500 font-bold text-[0.85rem] leading-snug">Simpan yang buanmu suka biar gampang
                            dicari lagi.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <div class="px-6 pt-16 pb-24 text-center">
            <div class="animate-up relative z-10">
                <a href="{{ route('explore') }}"
                    class="group inline-flex items-center gap-4 px-12 py-5 bg-espresso text-white rounded-[24px] font-black text-[1.1rem] shadow-[0_20px_50px_-15px_rgba(44,24,16,0.6)] hover:shadow-[0_25px_60px_-15px_rgba(44,24,16,0.7)] hover:scale-[1.05] active:scale-95 transition-all">
                    JELAJAH <span class="flex items-center gap-1 opacity-70 group-hover:opacity-100 transition-opacity">Ayo
                        Cari Cafe <i class="ph-bold ph-arrow-right"></i></span>
                </a>
            </div>

            <div class="mt-16 animate-up">
                <p class="text-[0.65rem] font-black text-slate-400 tracking-[0.4em] uppercase opacity-60">
                    WADAHNGOPI.COM &copy; 2026
                </p>
            </div>
        </div>
    </div>

    <style>
        .mission-luxury-card {
            padding: 40px;
            background: linear-gradient(135deg, #2C1810 0%, #3E2723 100%) !important;
            border-radius: 45px;
            color: white;
            overflow: hidden;
            position: relative;
            box-shadow: 0 25px 60px -15px rgba(44, 24, 16, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .social-admin-card {
            background: white;
            padding: 20px 15px;
            border-radius: 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(44, 24, 16, 0.04);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .social-admin-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
            border-color: var(--color-espresso);
        }

        .admin-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .premium-feature-card-v2 {
            background: white;
            padding: 24px;
            border-radius: 32px;
            border: 1px solid rgba(44, 24, 16, 0.03);
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 12px 35px -12px rgba(44, 24, 16, 0.08);
            transition: all 0.4s ease;
        }

        .premium-feature-card-v2:hover {
            transform: scale(1.02);
            border-color: var(--color-amber);
            box-shadow: 0 20px 45px -15px rgba(217, 119, 6, 0.15);
        }

        .feature-icon-v2 {
            width: 60px;
            height: 60px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            flex-shrink: 0;
        }

        .animate-pulse-slow {
            animation: pulse-slow 6s ease-in-out infinite;
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 0.1;
                transform: scale(1);
            }

            50% {
                opacity: 0.2;
                transform: scale(1.15);
            }
        }
    </style>
@endsection