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
        </main>

        {{-- Undo Toast Notification (High Contrast Fix) --}}
        <div x-show="showUndoToast" x-cloak
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-20 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-20 opacity-0"
             class="fixed bottom-28 left-1/2 -translate-x-1/2 z-[100] w-[calc(100%-48px)] max-w-[400px]">
            <div class="bg-[#2C1810] text-white px-5 py-4 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center justify-between border border-white/10 ring-1 ring-white/20">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-rose-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(251,113,133,0.8)]"></div>
                    <span class="text-[0.85rem] font-black tracking-tight text-white">Kafe dihapus</span>
                </div>
                <button @click="undoRemove()" class="text-[0.75rem] font-black text-[#D97706] tracking-widest uppercase active:scale-90 transition-transform hover:brightness-125">
                    BATALKAN
                </button>
            </div>
        </div>
    </header>

    <main class="px-6 py-4 flex-1 mb-24">
        <livewire:saved-cafes />
    </main>
@endsection