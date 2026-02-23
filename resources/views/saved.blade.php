@extends('layouts.app')

@section('title', 'Tersimpan - WadahNgopi.Com')

@section('content')
    <div class="block min-h-screen pb-32 bg-[#FAF9F6]">
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
                        <img src="{{ asset('wadahngopi.png') }}" alt="Logo">

                    </div>
                    <div class="flex flex-col">
                        <h1 class="explore-brand">Wadah<span>Ngopi</span></h1>
                        <p class="explore-tagline">SIMPAN WADAH FAVORITMU</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Spacer for Fixed Header --}}
        <div class="header-spacer-saved"></div>



        <main class="px-6 flex-1 mb-24">
            <livewire:saved-items />
        </main>
    </div>
@endsection