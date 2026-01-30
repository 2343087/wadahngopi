@extends('layouts.app')

@section('title', 'Tersimpan - WadahNgopi.Com')

@section('content')
    <div x-data="savedLogic()" x-init="initComponent()">
        {{-- Header Section --}}
        <div class="px-6 pt-12 pb-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-1.5 h-8 bg-[--color-amber] rounded-full shadow-[0_0_10px_var(--color-amber)]"></div>
                <h1 class="hero-luxury-title !mt-0 text-[1.8rem]">Saved <span
                        class="italic text-[--color-coffee]">Cafes</span></h1>
            </div>
            <p x-show="savedCafes.length > 0" class="mt-2 text-[--color-text-muted] font-semibold opacity-70 text-sm">
                Koleksi cafe pilihan kamu yang siap dikunjungi.
            </p>
        </div>

        {{-- Saved Cafes List --}}
        <main class="px-6 py-4 flex-1 mb-24">
            <div class="space-y-5" x-show="savedCafes.length > 0">
                <template x-for="(cafe, index) in savedCafes" :key="cafe.id">
                    <div class="horizontal-luxury-card animate-up group cursor-pointer relative"
                        @click="window.location.href = cafe.url" :style="'animation-delay: ' + (index * 0.05) + 's'">

                        {{-- Image Section (40% width) --}}
                        <div class="w-[40%] h-[110px] relative overflow-hidden shrink-0">
                            <img :src="cafe.image" :alt="cafe.name"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                            {{-- Top Left Status Dot --}}
                            <div
                                class="absolute top-2 left-2 px-2 py-0.5 bg-black/40 backdrop-blur-md rounded-full flex items-center gap-1.5 border border-white/10">
                                <span :class="cafe.isOpen ? 'bg-emerald-500' : 'bg-rose-500'"
                                    class="w-1.5 h-1.5 rounded-full"></span>
                                <span class="text-white text-[0.6rem] font-bold uppercase tracking-tighter"
                                    x-text="cafe.isOpen ? 'Buka' : 'Tutup'"></span>
                            </div>
                        </div>

                        {{-- Info Section --}}
                        <div class="flex-1 p-3.5 flex flex-col justify-center relative min-w-0">
                            <div>
                                <h3 class="text-[--color-espresso] text-[1rem] font-black leading-tight mb-0.5 truncate group-hover:text-[--color-amber] transition-colors"
                                    x-text="cafe.name"></h3>
                                <p class="text-slate-400 text-[0.65rem] font-bold flex items-center gap-1 truncate">
                                    <i class="ph-bold ph-map-pin text-[--color-amber]"></i>
                                    <span x-text="cafe.address.split(',')[0]"></span>
                                </p>
                            </div>

                            {{-- Subtle Delete Action - Isolated from Card Click --}}
                            <button @click.stop="removeWithUndo(cafe.id)"
                                class="absolute bottom-2 right-2 w-8 h-8 rounded-full flex items-center justify-center text-slate-300 hover:text-rose-500 hover:bg-rose-50 transition-all duration-300 z-30">
                                <i class="ph-bold ph-trash text-base"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Empty State (The Emotional One) --}}
            <div x-show="savedCafes.length === 0" x-cloak
                class="flex flex-col items-center justify-center py-20 px-4 text-center animate-up">
                <div class="w-40 h-40 bg-[--color-cream-dark] rounded-full flex items-center justify-center mb-8 relative">
                    <i class="ph ph-coffee text-[5rem] text-[--color-espresso] opacity-20"></i>
                    <div class="absolute -right-1 -bottom-1 bg-white p-3 rounded-2xl shadow-xl transform rotate-12">
                        <i class="ph-fill ph-heart text-rose-400 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-[1.2rem] font-black text-[--color-espresso] mb-2">Belum ada koleksi nih!</h3>
                <p class="text-slate-500 font-medium text-[0.85rem] mb-10 leading-relaxed">
                    Yuk cari tempat nongkrong asik di Kalimantan buat masukin ke daftar favorit kamu.
                </p>
                <a href="{{ route('explore') }}"
                    class="inline-flex items-center gap-2 px-8 py-3.5 bg-[#2C1810] text-white rounded-2xl font-black text-[0.9rem] shadow-xl hover:scale-[1.03] active:scale-95 transition-all"
                    style="background-color: #2C1810 !important; color: white !important;">
                    Ayo Cari Cafe <i class="ph-bold ph-arrow-right"></i>
                </a>
            </div>
        </main>

        {{-- Undo Toast Notification (High Contrast Fix) --}}
        <div x-show="showUndoToast" x-cloak x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-20 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-20 opacity-0"
            class="fixed bottom-28 left-1/2 -translate-x-1/2 z-[100] w-[calc(100%-48px)] max-w-[400px]">
            <div
                class="bg-[#2C1810] text-white px-5 py-4 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)] flex items-center justify-between border border-white/10 ring-1 ring-white/20">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 bg-rose-400 rounded-full animate-pulse shadow-[0_0_8px_rgba(251,113,133,0.8)]">
                    </div>
                    <span class="text-[0.85rem] font-black tracking-tight text-white">Kafe dihapus</span>
                </div>
                <button @click="undoRemove()"
                    class="text-[0.75rem] font-black text-[#D97706] tracking-widest uppercase active:scale-90 transition-transform hover:brightness-125">
                    BATALKAN
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            .horizontal-luxury-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                display: flex;
                border: 1px solid rgba(0, 0, 0, 0.04);
                box-shadow: 0 8px 24px -10px rgba(44, 24, 16, 0.06);
                transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            }

            .horizontal-luxury-card:hover {
                transform: translateY(-3px) scale(1.01);
                box-shadow: 0 16px 32px -12px rgba(44, 24, 16, 0.08);
                border-color: var(--color-amber-light);
            }
        </style>

        <script>
            window.savedCafesData = {{ Js::from($cafes->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'address' => $c->address,
            'isOpen' => $c->is_open,
            'image' => $c->image_path && str_starts_with($c->image_path, 'http') ? $c->image_path : ($c->image_path ? Storage::url($c->image_path) : 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800'),
            'url' => route('cafes.show', $c)
        ])) }};

            document.addEventListener('alpine:init', () => {
                Alpine.data('savedLogic', () => ({
                    savedCafes: [],
                    tempRemovedCafe: null,
                    showUndoToast: false,
                    undoTimeout: null,

                    initComponent() {
                        const bookmarks = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]');
                        const urlParams = new URLSearchParams(window.location.search);
                        const urlIds = urlParams.getAll('ids[]');

                        // CRITICAL: Redirect if localStorage has items but URL doesn't
                        if (bookmarks.length > 0 && urlIds.length === 0) {
                            let query = bookmarks.map(id => `ids[]=${id}`).join('&');
                            window.location.href = `{{ route('saved') }}?${query}`;
                            return;
                        }

                        this.savedCafes = window.savedCafesData || [];
                    },

                    removeWithUndo(id) {
                        const index = this.savedCafes.findIndex(c => c.id == id);
                        if (index === -1) return;

                        this.tempRemovedCafe = {
                            data: this.savedCafes[index],
                            index: index
                        };

                        this.savedCafes.splice(index, 1);
                        this.showUndoToast = true;

                        // Update LocalStorage immediately
                        const bookmarks = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]');
                        const newBookmarks = bookmarks.filter(bid => bid != id);
                        localStorage.setItem('wadah-bookmarks', JSON.stringify(newBookmarks));

                        this.updateUrl(newBookmarks);

                        if (this.undoTimeout) clearTimeout(this.undoTimeout);
                        this.undoTimeout = setTimeout(() => {
                            this.showUndoToast = false;
                            this.tempRemovedCafe = null;
                        }, 4000);
                    },

                    undoRemove() {
                        if (!this.tempRemovedCafe) return;

                        this.savedCafes.splice(this.tempRemovedCafe.index, 0, this.tempRemovedCafe.data);

                        const bookmarks = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]');
                        bookmarks.push(this.tempRemovedCafe.data.id.toString());
                        localStorage.setItem('wadah-bookmarks', JSON.stringify(bookmarks));

                        this.updateUrl(bookmarks);

                        this.tempRemovedCafe = null;
                        this.showUndoToast = false;
                        if (this.undoTimeout) clearTimeout(this.undoTimeout);
                    },

                    updateUrl(ids) {
                        let newUrl = '{{ route('saved') }}';
                        if (ids.length > 0) {
                            let query = ids.map(bid => `ids[]=${bid}`).join('&');
                            newUrl += '?' + query;
                        }
                        window.history.replaceState({}, '', newUrl);
                    }
                }));
            });
        </script>
    @endpush
@endsection