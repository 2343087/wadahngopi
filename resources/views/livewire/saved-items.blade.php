{{-- Livewire Saved Items Component --}}
<div wire:poll.30s="loadItems" x-data="savedItemsLogic()" x-init="initFromStorage()" class="min-h-screen">
    <div x-effect="$wire.updateIds(savedCafeIds, savedRoasteryIds)"></div>

    {{-- Saved Items List --}}
    <div class="space-y-4 pb-32" x-show="$wire.items.length > 0">
        <template x-for="item in $wire.items" :key="item.type + '-' + item.id">
            <a :href="item.url"
                class="group block bg-white hover:bg-[#F5EFED] transition-colors border border-[#1a0f0a]/5 rounded-[24px] shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] overflow-hidden relative">

                <div class="flex items-center p-3 gap-4">
                    {{-- Left: Fixed Image --}}
                    <div class="w-20 h-20 shrink-0 rounded-[18px] overflow-hidden bg-gray-100 relative shadow-inner">
                        <img :src="item.image || 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=300'"
                            :alt="item.name"
                            class="absolute inset-0 w-full !h-full object-cover z-10 transition-transform duration-500 group-hover:scale-110">

                        {{-- Status Dot --}}
                        <div
                            class="absolute top-1.5 right-1.5 w-3.5 h-3.5 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-sm z-20">
                            <div :class="item.isOpen ? 'bg-emerald-500' : 'bg-rose-500'"
                                class="w-1.5 h-1.5 rounded-full"></div>
                        </div>
                    </div>

                    {{-- Middle: Content Area --}}
                    <div class="flex-1 min-w-0 flex flex-col justify-center gap-1">
                        <h3 class="text-[1rem] font-black text-[#2C1810] leading-snug truncate group-hover:text-[#F59E0B] transition-colors"
                            x-text="item.name"></h3>

                        <p class="text-[0.7rem] font-bold text-[#8B7355] flex items-center gap-1">
                            <i class="ph-fill ph-map-pin text-[#F59E0B]"></i>
                            <span class="truncate" x-text="(item.address || '').split(',')[0]"></span>
                        </p>

                        <div class="flex flex-wrap gap-1.5 mt-1">
                            {{-- TYPE TAG --}}
                            <span class="px-2 py-0.5 text-[0.6rem] font-bold uppercase tracking-wider rounded-md"
                                :class="item.type === 'roastery' ? 'bg-amber-100 text-amber-800' : 'bg-[#FAF9F6] text-[#8B7355]'"
                                x-text="item.type === 'roastery' ? 'ROASTERY' : 'CAFE'">
                            </span>

                            {{-- OTHER TAGS --}}
                            <template x-for="tag in (item.tags || []).slice(0, 2)">
                                <span
                                    class="px-2 py-0.5 bg-[#FAF9F6] text-[#8B7355] text-[0.6rem] font-bold uppercase tracking-wider rounded-md"
                                    x-text="tag"></span>
                            </template>
                        </div>
                    </div>

                    {{-- Right: Actions --}}
                    <div class="pl-2 border-l border-[#1a0f0a]/5 flex flex-col justify-center">
                        <button @click.prevent.stop="confirmDelete(item)"
                            class="flex items-center justify-center w-10 h-10 bg-[#FEF2F2] text-[#EF4444] rounded-xl hover:bg-[#FEE2E2] active:scale-90 transition-all shadow-sm">
                            <i class="ph-bold ph-trash text-lg"></i>
                        </button>
                    </div>
                </div>
            </a>
        </template>
    </div>

    {{-- Premium Empty State --}}
    <div x-show="$wire.items.length === 0" x-cloak
        class="fixed inset-x-0 top-[180px] bottom-[80px] flex items-center justify-center z-0">
        <div class="empty-state-premium">
            <div class="empty-state-illustration">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="150" rx="55" ry="10" fill="#F5EFED" />
                    <rect x="55" y="30" width="90" height="120" rx="12" fill="white" stroke="#2C1810"
                        stroke-width="2" />
                    <path d="M70 30 L70 95 L100 78 L130 95 L130 30" fill="#F59E0B" stroke="#D97706" stroke-width="1" />
                    <circle cx="100" cy="120" r="8" fill="#F5EFED" stroke="#E6E1DC" stroke-width="1" />
                    <path d="M96 120 L99 123 L104 117" stroke="#2C1810" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" opacity="0.3" />
                    <path d="M85 25 Q100 15 115 25" stroke="#B08968" stroke-width="1.5" stroke-linecap="round"
                        fill="none" opacity="0.3">
                        <animate attributeName="d"
                            values="M85 25 Q100 15 115 25;M85 25 Q100 10 115 25;M85 25 Q100 15 115 25" dur="3s"
                            repeatCount="indefinite" />
                    </path>
                </svg>
            </div>
            <h3>Belum Ada Simpanan</h3>
            <p>Jelajahi cafe dan roastery estetik di sekitarmu dan simpan favoritmu di sini.</p>
            <div class="flex gap-3 mt-2">
                <a href="{{ route('explore') }}" class="empty-state-cta">
                    <i class="ph-bold ph-coffee"></i>
                    Cari Kopi
                </a>
                <a href="{{ route('roastery') }}" class="empty-state-cta" style="background: #F59E0B;">
                    <i class="ph-bold ph-coffee-bean"></i>
                    Cari Beans
                </a>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <template x-teleport="body">
        <div x-show="showDeleteModal" x-cloak
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 16px; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @click.self="showDeleteModal = false">

            <div x-show="showDeleteModal"
                style="background: white; border-radius: 24px; padding: 24px; width: 100%; max-width: 320px; margin: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);"
                x-transition:enter="transition ease-out duration-200 delay-75"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                {{-- Modal Header --}}
                <div
                    style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 20px;">
                    <div
                        style="width: 56px; height: 56px; background: #FEE2E2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                        <i class="ph-bold ph-warning" style="font-size: 24px; color: #EF4444;"></i>
                    </div>
                    <h3 style="font-size: 18px; font-weight: 900; color: #2C1810; margin-bottom: 8px;">Hapus dari
                        Simpanan?</h3>
                    <p style="font-size: 14px; color: #8B7355; line-height: 1.5;">
                        Kamu yakin mau hapus <span style="font-weight: 700; color: #2C1810;"
                            x-text="itemToDelete?.name || 'item ini'"></span> dari daftar simpananmu?
                    </p>
                </div>

                {{-- Modal Actions --}}
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <button @click="executeDelete()"
                        style="width: 100%; padding: 14px 16px; background: #EF4444; color: white; font-weight: 700; font-size: 14px; border-radius: 12px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;"
                        onmouseover="this.style.background='#DC2626'" onmouseout="this.style.background='#EF4444'">
                        <i class="ph-bold ph-trash"></i>
                        Ya, Hapus
                    </button>
                    <button @click="showDeleteModal = false"
                        style="width: 100%; padding: 14px 16px; background: #F5EFED; color: #2C1810; font-weight: 700; font-size: 14px; border-radius: 12px; border: none; cursor: pointer;"
                        onmouseover="this.style.background='#E8E0DC'" onmouseout="this.style.background='#F5EFED'">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

@script
<script>
    Alpine.data('savedItemsLogic', () => ({
        savedCafeIds: [],
        savedRoasteryIds: [],
        showDeleteModal: false,
        itemToDelete: null,

        initFromStorage() {
            try {
                // Read Cafes
                const storedCafes = localStorage.getItem('wadah-bookmarks');
                this.savedCafeIds = storedCafes ? JSON.parse(storedCafes) : [];

                // Read Roasteries
                const storedRoasteries = localStorage.getItem('wadah-roastery-bookmarks');
                this.savedRoasteryIds = storedRoasteries ? JSON.parse(storedRoasteries) : [];
            } catch (e) {
                this.savedCafeIds = [];
                this.savedRoasteryIds = [];
            }
        },

        confirmDelete(item) {
            this.itemToDelete = item;
            this.showDeleteModal = true;
        },

        executeDelete() {
            if (!this.itemToDelete) return;

            if (this.itemToDelete.type === 'cafe') {
                this.savedCafeIds = this.savedCafeIds.filter(i => i != this.itemToDelete.id);
                localStorage.setItem('wadah-bookmarks', JSON.stringify(this.savedCafeIds));
            } else if (this.itemToDelete.type === 'roastery') {
                this.savedRoasteryIds = this.savedRoasteryIds.filter(i => i != this.itemToDelete.id);
                localStorage.setItem('wadah-roastery-bookmarks', JSON.stringify(this.savedRoasteryIds));
            }

            // Haptic feedback
            if (navigator.vibrate) navigator.vibrate(50);

            // Close modal and reset
            this.showDeleteModal = false;
            this.itemToDelete = null;
        }
    }));
</script>
@endscript