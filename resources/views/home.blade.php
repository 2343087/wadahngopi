@extends('layouts.app')

@section('title', 'WadahNgopi - Portal Cafe Terbaik di Kalimantan')

@section('content')
    <div class="home-wrapper">
        {{-- Hero --}}
        <header class="hero">
            <div class="logo-box">
                <img src="{{ asset('wadahicon.png') }}" alt="WadahNgopi">
            </div>
            <span class="badge">BY : AK KREATIF</span>
            <h1 class="brand">Wadah<span>Ngopi</span></h1>
            <p class="tagline">untuk merajut silaturahmi dan mendekatkan para pelaku usaha kedai kopi dengan parah peminat
                kopi di samarinda.</p>
        </header>

        {{-- Main Card --}}
        <main class="main-card">
            {{-- Header --}}
            <div class="c-head">
                <div class="c-bar"></div>
                <div class="c-titles">
                    <span>TENTANG KAMI</span>
                    <h2>Lebih dari Sekadar Kopi</h2>
                </div>
            </div>

            {{-- Body --}}
            <p class="c-text">di wadah ngopi ini. para pelaku usaha kedai kopi. dapat memperkenalkan produk kedai kopinya,
                secara lebih mudah.</p>

            {{-- Social Media --}}
            <div class="c-socials">
                <a href="https://www.instagram.com/wadahngopi/" target="_blank" class="soc soc-ig">
                    <i class="ph-fill ph-instagram-logo"></i>
                    <span>INSTAGRAM</span>
                </a>
                <a href="https://www.tiktok.com/@wadah.ngopi?_r=1&_t=ZS-93dSno0Z0Cu
    " class="soc soc-tt">
                    <i class="ph-fill ph-tiktok-logo"></i>
                    <span>TIKTOK</span>
                </a>
                <a href="https://youtube.com/@wadahngopi?si=4MYRQCMn6cjEeAlB" class="soc soc-yt">
                    <i class="ph-fill ph-youtube-logo"></i>
                    <span>YOUTUBE</span>
                </a>
                <a href="https://wa.me/6282199694350" class="soc soc-wa">
                    <i class="ph-fill ph-whatsapp-logo"></i>
                    <span>WHATSAPP</span>
                </a>
            </div>

            {{-- CTA Buttons --}}
            <div class="c-actions">
                <a href="{{ route('explore') }}" class="cta cta-primary">
                    <div class="cta-icon">
                        <i class="ph-bold ph-compass"></i>
                    </div>
                    <div class="cta-text">
                        <strong>JELAJAHI</strong>
                        <span>Ayo Cari Kopi Favoritmu</span>
                    </div>
                    <i class="ph-bold ph-caret-right cta-arrow"></i>
                </a>

                <a href="{{ route('information') }}" class="cta cta-ghost">
                    <div class="cta-icon">
                        <i class="ph-bold ph-newspaper"></i>
                    </div>
                    <div class="cta-text">
                        <strong>INFORMASI</strong>
                        <span>Berita & Event Terbaru</span>
                    </div>
                    <i class="ph-bold ph-caret-right cta-arrow"></i>
                </a>
            </div>

            {{-- Footer --}}
            <div class="c-footer">
                <span>WADAHNGOPI.COM &copy; 2026</span>
            </div>
        </main>
    </div>

    <style>
        /* Clean Premium Homepage */
        .home-wrapper {
            display: flex;
            flex-direction: column;
            min-height: calc(100vh - 100px);
            min-height: calc(100dvh - 100px);
            padding: 16px 20px 20px;
            background: #FFFDFB;
        }

        /* === HERO === */
        .hero {
            text-align: center;
            flex-shrink: 0;
            padding-bottom: 18px;
        }

        .logo-box {
            width: 60px;
            height: 60px;
            margin: 0 auto 12px;
            background: #fff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 30px rgba(44, 24, 16, 0.12);
        }

        .logo-box img {
            width: 42px;
            height: 42px;
        }

        .badge {
            display: inline-block;
            background: #2C1810;
            color: #FFFDFB;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.55rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            margin-bottom: 12px;
        }

        .brand {
            font-size: 2.2rem;
            font-weight: 900;
            color: #2C1810;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .brand span {
            color: #F59E0B;
        }

        .tagline {
            font-size: 0.85rem;
            color: #8B7355;
            font-weight: 600;
            line-height: 1.5;
            max-width: 280px;
            margin: 0 auto;
        }

        /* === MAIN CARD === */
        .main-card {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: linear-gradient(165deg, #3D2518 0%, #241208 100%);
            border-radius: 32px;
            padding: 24px 22px;
            box-shadow: 0 20px 60px rgba(44, 24, 16, 0.25);
        }

        /* Card Header */
        .c-head {
            display: flex;
            gap: 14px;
            margin-bottom: 16px;
        }

        .c-bar {
            width: 4px;
            height: 44px;
            background: linear-gradient(180deg, #F59E0B 0%, #D97706 100%);
            border-radius: 4px;
            flex-shrink: 0;
        }

        .c-titles span {
            display: block;
            font-size: 0.6rem;
            font-weight: 700;
            color: #F59E0B;
            letter-spacing: 0.2em;
            margin-bottom: 6px;
        }

        .c-titles h2 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
        }

        /* Card Text */
        .c-text {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        /* Social Media */
        .c-socials {
            display: flex;
            justify-content: center;
            gap: 28px;
            padding: 18px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .soc {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: transform 0.3s ease;
        }

        .soc:hover {
            transform: translateY(-3px);
        }

        .soc i {
            font-size: 1.6rem;
        }

        .soc-ig i {
            color: #E1306C;
        }

        .soc-tt i {
            color: #fff;
        }

        .soc-yt i {
            color: #FF0000;
        }

        .soc-wa i {
            color: #25D366;
        }

        .soc span {
            font-size: 0.5rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.1em;
        }

        /* CTA Buttons */
        .c-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: auto;
            margin-bottom: 20px;
        }

        .cta {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 18px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .cta-primary {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.18) 0%, rgba(217, 119, 6, 0.08) 100%);
            border: 1px solid rgba(245, 158, 11, 0.35);
        }

        .cta-ghost {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .cta:hover {
            transform: translateX(6px);
        }

        .cta-primary:hover {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.28) 0%, rgba(217, 119, 6, 0.12) 100%);
            border-color: rgba(245, 158, 11, 0.5);
        }

        .cta-ghost:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .cta-icon {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .cta-primary .cta-icon {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: #fff;
        }

        .cta-ghost .cta-icon {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .cta-text {
            flex: 1;
        }

        .cta-text strong {
            display: block;
            font-size: 1rem;
            font-weight: 800;
            color: #fff;
        }

        .cta-text span {
            display: block;
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
            margin-top: 2px;
        }

        .cta-arrow {
            color: rgba(255, 255, 255, 0.3);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .cta:hover .cta-arrow {
            color: #F59E0B;
            transform: translateX(4px);
        }

        /* Card Footer */
        .c-footer {
            text-align: center;
            padding-top: 18px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .c-footer span {
            font-size: 0.55rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.2);
            letter-spacing: 0.2em;
        }

        /* === RESPONSIVE === */
        @media (max-height: 720px) {
            .home-wrapper {
                padding: 12px 16px 16px;
            }

            .logo-box {
                width: 52px;
                height: 52px;
            }

            .logo-box img {
                width: 36px;
                height: 36px;
            }

            .brand {
                font-size: 1.9rem;
            }

            .tagline {
                font-size: 0.78rem;
            }

            .main-card {
                padding: 20px 18px;
            }

            .c-titles h2 {
                font-size: 1.15rem;
            }

            .c-text {
                font-size: 0.78rem;
                margin-bottom: 16px;
            }

            .c-socials {
                padding: 14px 0;
                gap: 22px;
            }

            .soc i {
                font-size: 1.4rem;
            }

            .c-actions {
                gap: 10px;
                margin-bottom: 16px;
            }

            .cta {
                padding: 14px 16px;
            }

            .cta-icon {
                width: 42px;
                height: 42px;
            }
        }
    </style>
@endsection