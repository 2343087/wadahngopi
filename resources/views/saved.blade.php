@extends('layouts.app')

@section('title', 'Tersimpan - WadahNgopi.Com')

@section('content')
    <div x-data="{ 
            bookmarks: JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'),
            init() {
                const urlParams = new URLSearchParams(window.location.search);
                const urlIds = urlParams.getAll('ids[]');

                if (this.bookmarks.length > 0 && urlIds.length === 0) {
                    let query = this.bookmarks.map(id => `ids[]=${id}`).join('&');
                    window.location.href = `{{ route('saved') }}?${query}`;
                }
            },
            removeBookmark(id) {
                this.bookmarks = this.bookmarks.filter(b => b != id);
                localStorage.setItem('wadah-bookmarks', JSON.stringify(this.bookmarks));

                if (this.bookmarks.length === 0) {
                    window.location.href = `{{ route('saved') }}`;
                } else {
                    let query = this.bookmarks.map(id => `ids[]=${id}`).join('&');
                    window.location.href = `{{ route('saved') }}?${query}`;
                }
            }
        }">
        <div class="px-6 py-12">
            <h1 class="text-3xl font-black text-[--color-espresso] tracking-tight mb-2">Cafe Tersimpan</h1>
            <p x-show="bookmarks.length > 0" class="text-slate-400 font-bold text-sm">
                Daftar cafe favorit yang kamu simpan.
            </p>
        </div>

        <div class="p-6 pt-0">
            @forelse($cafes as $index => $cafe)
                <div class="item-luxury animate-up relative" style="animation-delay: {{ $index * 0.05 }}s">
                    <button @click.stop="removeBookmark({{ $cafe->id }})"
                        class="absolute top-3 right-3 w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-300 hover:bg-red-50 hover:text-red-500 transition-all z-20">
                        <i class="ph ph-x-circle text-xl"></i>
                    </button>

                    <a href="{{ route('cafes.show', $cafe) }}" class="flex gap-4 no-underline color-inherit flex-1">
                        <img src="{{ $cafe->image_path ? Storage::url($cafe->image_path) : 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800' }}"
                            alt="{{ $cafe->name }}" class="item-img-luxury" loading="lazy">

                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <h3 class="font-black text-[--color-espresso] text-lg line-clamp-1 mb-1">{{ $cafe->name }}</h3>
                            <p class="text-slate-400 text-xs font-bold line-clamp-1 italic">{{ $cafe->address }}</p>
                        </div>
                    </a>
                </div>
            @empty
                <div class="text-center py-24 px-6 animate-up">
                    <i class="ph ph-heart-break text-[5rem] opacity-20 block mb-6 mx-auto"></i>
                    <h3 class="text-xl font-black text-[--color-espresso] mb-3">Belum Ada Simpanan</h3>
                    <p class="text-slate-400 font-semibold text-sm mb-10">Jelajahi dan simpan cafe favoritmu!</p>
                    <a href="{{ route('explore') }}" class="btn btn-primary w-full h-14">Mulai Explore</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection