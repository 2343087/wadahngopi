@extends('layouts.app')

@section('title', 'Tersimpan - WadahNgopi.Com')

@section('content')
    <header class="hero-fancy px-8 pt-24 pb-12 flex flex-col gap-2">
        <div class="hero-header-decoration"></div>
        <div class="flex items-center justify-between relative z-10">
            <div>
                <h1 class="hero-luxury-title">Simpan <span class="italic text-coffee">Cafe</span></h1>
                <p class="mt-2 text-text-muted font-bold opacity-70 text-[0.9rem]">
                    Koleksi cafe pilihan kamu yang siap dikunjungi.
                </p>
            </div>
            <div
                class="w-12 h-12 bg-white/80 backdrop-blur-md border border-espresso/5 rounded-2xl flex items-center justify-center shadow-premium overflow-hidden">
                <img src="{{ asset('wadahicon.png') }}" alt="Logo" class="w-full h-full object-cover">
            </div>
        </div>
    </header>

    <main class="px-6 py-4 flex-1 mb-24">
        <livewire:saved-cafes />
    </main>
@endsection