@extends('layouts.app')

@section('title', 'Tersimpan - WadahNgopi.Com')

@section('content')
    <div x-data="{ 
                bookmarks: JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'),
                cafes: window.savedCafesData || [],
                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const urlIds = urlParams.getAll('ids[]');

                    if (this.bookmarks.length > 0 && urlIds.length === 0) {
                        let query = this.bookmarks.map(id => `ids[]=${id}`).join('&');
                        window.location.href = `{{ route('saved') }}?${query}`;
                    }
                },
                removeBookmark(id) {
                    // Update LocalStorage
                    this.bookmarks = this.bookmarks.filter(b => b != id);
                    localStorage.setItem('wadah-bookmarks', JSON.stringify(this.bookmarks));

                    // Update Local State (Realtime No Reload!)
                    this.cafes = this.cafes.filter(c => c.id != id);

                    // Update URL silently without reload
                    let newUrl = '{{ route('saved') }}';
                    if(this.bookmarks.length > 0) {
                        let query = this.bookmarks.map(bid => `ids[]=${bid}`).join('&');
                        newUrl += '?' + query;
                    }
                    window.history.replaceState({}, '', newUrl);
                }
            }">
        <div class="px-6 py-12">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-1.5 h-8 bg-[--color-amber] rounded-full shadow-[0_0_10px_var(--color-amber)]"></div>
                <h1 class="hero-luxury-title !mt-0">Saved <span class="italic text-[--color-coffee]">Cafes</span></h1>
            </div>
            <p x-show="cafes.length > 0" class="mt-2 text-[--color-text-muted] font-semibold opacity-70">
                Koleksi cafe pilihan kamu yang siap dikunjungi.
            </p>
        </div>

        <div class="saved-list-container mb-10">
            <template x-for="(cafe, index) in cafes" :key="cafe.id">
                <div class="relative transition-all duration-500 hover:z-10"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

                    <button @click.stop="removeBookmark(cafe.id)"
                        class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/80 backdrop-blur-xl flex items-center justify-center text-red-500 shadow-xl hover:scale-110 active:scale-90 transition-all z-20 border border-white/50">
                        <i class="ph-bold ph-trash text-lg"></i>
                    </button>

                    <a :href="cafe.url" class="saved-item-card" :style="'animation-delay: ' + (index * 0.05) + 's'">
                        <div class="saved-img-wrapper">
                            <img :src="cafe.image" :alt="cafe.name" loading="lazy">
                        </div>
                        <div class="saved-info">
                            <div class="luxury-badge !static !inline-block !p-0 !bg-transparent !shadow-none !mb-1">
                                <span class="text-[0.6rem] px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-600 font-black"
                                    x-show="cafe.isOpen">BUKA</span>
                                <span class="text-[0.6rem] px-2 py-0.5 rounded-lg bg-red-50 text-red-600 font-black"
                                    x-show="!cafe.isOpen">TUTUP</span>
                            </div>
                            <h3 x-text="cafe.name"></h3>
                            <p class="opacity-60" x-text="cafe.address.split(',')[0]"></p>
                        </div>
                    </a>
                </div>
            </template>

            <template x-if="cafes.length === 0">
                <div class="text-center py-24 px-6 animate-up">
                    <div
                        class="w-24 h-24 bg-white/50 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-soft border border-white">
                        <i class="ph ph-heart-break text-5xl opacity-30 text-[--color-coffee]"></i>
                    </div>
                    <h3 class="text-xl font-black text-[--color-espresso] mb-3">Belum Ada Simpanan</h3>
                    <p class="text-[--color-text-muted] font-semibold text-sm mb-10">Jelajahi dan simpan cafe favoritmu!</p>
                    <a href="{{ route('explore') }}" class="btn btn-primary w-full h-14">Mulai Explore</a>
                </div>
            </template>
        </div>
    </div>

    @push('scripts')
        <script>
            window.savedCafesData = {{ Js::from($cafes->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'address' => $c->address,
            'isOpen' => $c->is_open,
            'image' => $c->image_path && str_starts_with($c->image_path, 'http') ? $c->image_path : ($c->image_path ? Storage::url($c->image_path) : 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'),
            'url' => route('cafes.show', $c)
        ])) }};
        </script>
    @endpush
@endsection