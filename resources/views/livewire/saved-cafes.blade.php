{{-- Livewire Saved Cafes Component --}}
<div wire:poll.30s="loadCafes" x-data="savedCafesLogic()" x-init="initFromStorage()" class="min-h-screen">
    <div x-effect="$wire.updateIds(savedIds)"></div>

    {{-- Saved Cafes List --}}
    <div class="space-y-4 pb-32" x-show="$wire.cafes.length > 0">
        <template x-for="cafe in $wire.cafes" :key="cafe.id">
            <a :href="cafe.url"
                class="group block bg-white hover:bg-[#F5EFED] transition-colors border border-[#1a0f0a]/5 rounded-[24px] shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] overflow-hidden relative">

                <div class="flex items-center p-3 gap-4">
                    {{-- Left: Fixed Image --}}
                    <div class="w-20 h-20 shrink-0 rounded-[18px] overflow-hidden bg-gray-100 relative shadow-inner">
                        <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=300'"
                            :alt="cafe.name"
                            class="absolute inset-0 w-full h-full object-cover z-10 transition-transform duration-500 group-hover:scale-110">

                        {{-- Status Dot --}}
                        <div
                            class="absolute top-1.5 right-1.5 w-3.5 h-3.5 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-sm">
                            <div :class="cafe.isOpen ? 'bg-emerald-500' : 'bg-rose-500'"
                                class="w-1.5 h-1.5 rounded-full"></div>
                        </div>
                    </div>

                    {{-- Middle: Content Area --}}
                    <div class="flex-1 min-w-0 flex flex-col justify-center gap-1">
                        <h3 class="text-[1rem] font-black text-[#2C1810] leading-snug truncate group-hover:text-[#F59E0B] transition-colors"
                            x-text="cafe.name"></h3>

                        <p class="text-[0.7rem] font-bold text-[#8B7355] flex items-center gap-1">
                            <i class="ph-fill ph-map-pin text-[#F59E0B]"></i>
                            <span class="truncate" x-text="(cafe.address || '').split(',')[0]"></span>
                        </p>

                        <div class="flex flex-wrap gap-1.5 mt-1">
                            <template x-for="tag in (cafe.facilities || []).slice(0, 2)">
                                <span
                                    class="px-2 py-0.5 bg-[#FAF9F6] text-[#8B7355] text-[0.6rem] font-bold uppercase tracking-wider rounded-md"
                                    x-text="tag"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="pl-2 border-l border-[#1a0f0a]/5 flex flex-col justify-center">
                        <button @click.prevent.stop="removeFromSaved(cafe.id)"
                            class="flex items-center justify-center w-10 h-10 bg-[#FEF2F2] text-[#EF4444] rounded-xl hover:bg-[#FEE2E2] active:scale-90 transition-all shadow-sm">
                            <i class="ph-bold ph-trash text-lg"></i>
                        </button>
                    </div>
                </div>
            </a>
        </template>
    </div>

    {{-- Premium Empty State --}}
    <div x-show="$wire.cafes.length === 0" x-cloak class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-20 h-20 bg-[#F5EFED] rounded-full flex items-center justify-center mb-6 animate-pulse">
            <i class="ph-fill ph-bookmark-simple text-3xl text-[#8B7355]"></i>
        </div>
        <h3 class="text-lg font-black text-[#2C1810] mb-2">Belum Ada Cafe Tersimpan</h3>
        <p class="text-sm text-[#8B7355] max-w-[240px] leading-relaxed mb-8">
            Jelajahi cafe estetik di sekitarmu dan simpan favoritmu di sini.
        </p>
        <a href="{{ route('explore') }}"
            class="flex items-center gap-2 px-8 py-3.5 bg-[#2C1810] text-[#FFFDFB] rounded-2xl font-bold text-sm shadow-lg shadow-[#2C1810]/20 hover:bg-[#4A2C20] hover:-translate-y-1 transition-all">
            <i class="ph-bold ph-compass text-lg"></i>
            Mulai Menjelajah
        </a>
    </div>
</div>

@script
<script>
    Alpine.data('savedCafesLogic', () => ({
        savedIds: [],
        initFromStorage() {
            try {
                const stored = localStorage.getItem('wadah-bookmarks');
                this.savedIds = stored ? JSON.parse(stored) : [];
            } catch (e) { this.savedIds = []; }
        },
        removeFromSaved(id) {
            this.savedIds = this.savedIds.filter(i => i != id);
            localStorage.setItem('wadah-bookmarks', JSON.stringify(this.savedIds));

            // Dispatch subtle toast or vibration pattern if needed
            if (navigator.vibrate) navigator.vibrate(50);
        },
        shareCafe(cafe) {
            const shareData = {
                title: cafe.name + ' - WadahNgopi',
                text: 'Cek cafe estetik ini di WadahNgopi: ' + cafe.name,
                url: cafe.url.startsWith('http') ? cafe.url : window.location.origin + cafe.url
            };
            if (navigator.share) {
                navigator.share(shareData).catch(err => console.log('Error sharing:', err));
            } else {
                navigator.clipboard.writeText(shareData.url).then(() => {
                    // Optional: Custom toast dispatch
                });
            }
        }
    }));
</script>
@endscript