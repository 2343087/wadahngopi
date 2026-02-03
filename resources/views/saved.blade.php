@extends('layouts.app')

@section('title', 'Tersimpan - WadahNgopi.Com')

@section('content')
    <header class="explore-hero-2026" x-data="{ isScrolled: false }" :class="{ 'is-compact': isScrolled }"
        @scroll.window="isScrolled = window.pageYOffset > 50">
        {{-- Premium Background Orbs --}}
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>

        {{-- Branding Section --}}
        <div class="explore-branding-wrapper">
            <div class="explore-topbar">
                <div class="explore-logo-box">
                    <img src="{{ asset('wadahicon.png') }}" alt="Logo">

                </div>
                <div class="flex flex-col">
                    <h1 class="explore-brand">Wadah<span>Ngopi</span></h1>
                    <p class="explore-tagline">SIMPAN WADAH FAVORITMU</p>
                </div>
            </div>
        </div>
    </header>

    {{-- Content Spacer for Fixed Header (Static height to prevent jumping) --}}
    <div class="header-spacer-2026"></div>

    <style>
        .explore-hero-2026 {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            max-width: 480px;
            z-index: 1000;
            padding: 40px 24px 14px;
            background: #FFFDFB;
            border-radius: 0 0 32px 32px;
            overflow: visible;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(26, 15, 10, 0.05);
            transition: padding 0.4s ease, background 0.4s ease, box-shadow 0.4s ease;
            will-change: padding, background, box-shadow;
        }

        .header-spacer-2026 {
            height: 160px;
            /* Precise visual buffer for Saved page */
            width: 100%;
        }

        .explore-hero-2026.is-compact {
            padding-top: 35px;
            padding-bottom: 12px;
            background: rgba(255, 253, 251, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(111, 78, 55, 0.1);
            box-shadow: 0 10px 40px rgba(26, 15, 10, 0.12);
        }

        .explore-branding-wrapper {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: left top;
            will-change: transform, opacity;
        }

        .explore-hero-2026.is-compact .explore-branding-wrapper {
            transform: scale(0.92);
            opacity: 0.95;
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.1;
            pointer-events: none;
            will-change: transform;
        }

        .hero-orb-1 {
            width: 200px;
            height: 200px;
            background: #F59E0B;
            top: -50px;
            right: -50px;
            animation: float-orb 8s ease-in-out infinite;
        }

        .hero-orb-2 {
            width: 150px;
            height: 150px;
            background: #6F4E37;
            bottom: 20%;
            left: -30px;
            animation: float-orb 10s ease-in-out infinite reverse;
        }

        .hero-orb-3 {
            width: 100px;
            height: 100px;
            background: #F59E0B;
            top: 40%;
            right: 10%;
            animation: float-orb 6s ease-in-out infinite 2s;
        }

        @keyframes float-orb {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(20px, -20px) scale(1.1);
            }
        }

        .explore-topbar {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 10;
        }

        .explore-brand {
            font-size: 1.4rem;
            font-weight: 900;
            color: #2C1810;
            letter-spacing: -0.02em;
        }

        .explore-brand span {
            color: #F59E0B;
        }

        .explore-tagline {
            font-size: 0.65rem;
            font-weight: 700;
            color: #8B7355;
            letter-spacing: 0.12em;
            margin: 0;
            /* Clear and clean */
            text-transform: uppercase;
        }

        .explore-logo-box {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(26, 15, 10, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .explore-logo-box img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }
    </style>

    <main class="px-6 py-4 flex-1 mb-24">
        <livewire:saved-cafes />
    </main>
@endsection