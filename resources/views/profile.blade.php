@extends('layouts.app')

@section('title', 'Profil - WadahNgopi.Com')

@section('content')
    <div class="animate-up">
        {{-- Profile Header --}}
        <div class="px-6 py-16 text-center relative overflow-hidden">
            <div
                class="absolute inset-x-0 top-0 h-64 bg-gradient-to-b from-[--color-cream-dark] to-transparent -z-10 opacity-40">
            </div>

            <div
                class="w-32 h-32 bg-gradient-to-br from-[--color-espresso] to-[--color-coffee] text-white rounded-[40px] flex items-center justify-center mx-auto mb-6 shadow-2xl border-4 border-white animate-bounce-slow">
                <i class="ph ph-user-circle-fill text-[4rem]"></i>
            </div>

            <h1 class="text-3xl font-black text-[--color-espresso] mb-1 tracking-tight">Halo, Kopi Mania!</h1>
            <p class="text-[--color-text-muted] font-bold opacity-70">Pecinta Kopi Sejati</p>
        </div>

        {{-- About Card --}}
        <div class="m-6 p-8 bg-white/40 backdrop-blur-xl border border-white/60 rounded-[35px] shadow-sm">
            <h3 class="text-xl font-black text-[--color-espresso] mb-4">Tentang WadahNgopi</h3>
            <p class="text-slate-600 leading-relaxed font-medium text-[0.95rem]">
                WadahNgopi.Com adalah aplikasi pencarian tempat ngopi Kalimantan kekinian yang informatif dan modern.
                Dirancang buat buanmu yang pengen eksplor spot ngopi baru
                dan pengalaman visual yang clean.
            </p>
        </div>

        {{-- App Info --}}
        <div class="px-6 mt-12 text-center">           
            <div class="text-[0.6rem] font-bold text-slate-300 tracking-[0.2em] uppercase">
                MADE WITH ❤️ FOR ANAK KALIMANTAN KREATIF
                <i class="ph ph-rocket-launch"></i> WadahNgopi.Com V1.0
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce-slow {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-bounce-slow {
            animation: bounce-slow 4s ease-in-out infinite;
        }
    </style>
@endsection