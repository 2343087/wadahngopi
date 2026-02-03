{{-- Livewire Saved Cafes Component --}}
<div wire:poll.30s="loadCafes" x-data="savedCafesLogic()" x-init="initFromStorage()" class="min-h-screen">
    <div x-effect="$wire.updateIds(savedIds)"></div>

    {{-- Saved Cafes List (Bulletproof Horizontal) --}}
    <div class="px-6 space-y-4 pb-32" x-show="$wire.cafes.length > 0">
        <template x-for="cafe in $wire.cafes" :key="cafe.id">
            <a :href="cafe.url"
                class="block bg-white hover:bg-cream-dark transition-colors border border-espresso/5 border-solid rounded-[28px] shadow-sm overflow-hidden"
                style="display: flex !important; align-items: center; padding: 12px; gap: 16px; text-decoration: none !important;">

                {{-- Left: Fixed Image --}}
                <div
                    style="width: 80px; height: 80px; border-radius: 20px; overflow: hidden; flex-shrink: 0; position: relative; background: #f5efed;">
                    <img :src="cafe.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=300'"
                        :alt="cafe.name" style="width: 100%; height: 100%; object-fit: cover;">

                    {{-- Status Dot --}}
                    <div
                        style="position: absolute; top: 6px; right: 6px; width: 14px; height: 14px; background: rgba(255,255,255,0.9); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">
                        <div :class="cafe.isOpen ? 'bg-emerald-500' : 'bg-rose-500'"
                            style="width: 6px; height: 6px; border-radius: 50%;"></div>
                    </div>
                </div>

                {{-- Middle: Content Area --}}
                <div style="flex-grow: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px;">
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 850; color: #1a0f0a; line-height: 1.2; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                        x-text="cafe.name"></h3>
                    <p
                        style="margin: 0; font-size: 0.75rem; font-weight: 700; color: #8d7b70; display: flex; align-items: center; gap: 4px;">
                        <i class="ph-fill ph-map-pin-line text-amber"></i>
                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                            x-text="(cafe.address || '').split(',')[0]"></span>
                    </p>
                    <div style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px;">
                        <template x-for="tag in (cafe.facilities || [])">
                            <span
                                style="padding: 3px 8px; background: #f5efed; color: #7f5539; font-size: 0.6rem; font-weight: 800; border-radius: 8px; white-space: nowrap;"
                                x-text="tag"></span>
                        </template>
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div
                    style="padding-left: 12px; border-left: 1px solid rgba(26,15,10,0.05); flex-shrink: 0; display: flex; flex-direction: column; gap: 8px;">
                    <button @click.prevent.stop="removeFromSaved(cafe.id)"
                        class="flex items-center justify-center transition-all active:scale-90"
                        style="width: 44px; height: 44px; border: none; background: #FEF2F2; color: #EF4444; border-radius: 16px; cursor: pointer;">
                        <i class="ph-bold ph-trash" style="font-size: 1.2rem;"></i>
                    </button>
                </div>
            </a>
        </template>
    </div>

    {{-- Empty State --}}
    <div x-show="$wire.cafes.length === 0" x-cloak
        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 40px; text-align: center;">
        <div
            style="width: 80px; height: 80px; background: #f5efed; border-radius: 30px; display: flex; align-items: center; justify-content: center; margin-bottom: 24px;">
            <i class="ph-fill ph-coffee" style="font-size: 32px; color: rgba(26,15,10,0.2);"></i>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 900; color: #1a0f0a; margin-bottom: 8px;">Belum Ada Cafe Simpanan
        </h3>
        <p
            style="font-size: 0.9rem; font-weight: 600; color: rgba(26,15,10,0.5); line-height: 1.5; margin-bottom: 32px;">
            Jelajahi cafe estetik di sekitarmu dan simpan ke daftar ini.
        </p>
        <a href="{{ route('explore') }}"
            style="display: inline-flex; align-items: center; gap: 10px; padding: 14px 28px; background: #1a0f0a; color: #fffdfb; border-radius: 20px; font-weight: 900; text-decoration: none; box-shadow: 0 10px 25px rgba(26,15,10,0.2);">
            <i class="ph-bold ph-compass"></i>
            Mulai Cari
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
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Link disalin ke clipboard!', type: 'success' } }));
                });
            }
        }
    }));
</script>
@endscript