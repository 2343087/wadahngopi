<div x-data="tongkronganCreateLogic()" class="space-y-6">
    {{-- Title Input --}}
    <div>
        <label class="block text-xs font-bold text-[#2C1810] uppercase tracking-widest mb-2">
            <i class="ph-fill ph-pen-nib text-amber-600"></i> Judul Tongkrongan
        </label>
        <input wire:model="title" type="text" maxlength="100" 
            placeholder="Contoh: Opsi Jumat Malam, Cafe Buat Nugas Bareng..."
            class="w-full px-4 py-3.5 rounded-2xl border border-slate-200 bg-white text-sm font-medium text-[#2C1810] placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
    </div>

    {{-- Cafe Search --}}
    <div>
        <label class="block text-xs font-bold text-[#2C1810] uppercase tracking-widest mb-2">
            <i class="ph-fill ph-magnifying-glass text-amber-600"></i> Cari & Pilih Cafe (Min 2, Max 5)
        </label>
        <input wire:model.live.debounce.300ms="search" type="text" 
            placeholder="Ketik nama cafe..."
            class="w-full px-4 py-3.5 rounded-2xl border border-slate-200 bg-white text-sm font-medium text-[#2C1810] placeholder:text-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500/30 focus:border-amber-500 transition-all">
        
        {{-- Search Results Dropdown --}}
        @if(count($searchResults) > 0)
            <div class="mt-2 bg-white rounded-2xl border border-slate-100 shadow-lg overflow-hidden divide-y divide-slate-50">
                @foreach($searchResults as $cafe)
                    <button wire:click="addCafe({{ $cafe['id'] }})"
                        class="w-full flex items-center gap-3 p-3 hover:bg-amber-50 transition-all text-left">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0">
                            @if($cafe['image_path'])
                                <img src="/storage/{{ $cafe['image_path'] }}" class="w-full h-full object-cover" alt="">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i class="ph-fill ph-coffee text-lg"></i>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-[#2C1810] truncate">{{ $cafe['name'] }}</p>
                            <p class="text-[0.6rem] text-slate-400 truncate">{{ $cafe['address'] }}</p>
                        </div>
                        <i class="ph-bold ph-plus-circle text-amber-500 text-lg ml-auto flex-shrink-0"></i>
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Selected Cafes --}}
    @if(count($selectedCafes) > 0)
        <div class="space-y-2">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ count($selectedCafes) }}/5 Cafe Dipilih</p>
            @foreach($selectedCafes as $i => $cafe)
                <div class="flex items-center gap-3 bg-white border border-slate-100 p-3 rounded-2xl shadow-sm">
                    <span class="w-7 h-7 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 text-xs font-black flex-shrink-0">
                        {{ $i + 1 }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-[#2C1810] truncate">{{ $cafe['name'] }}</p>
                    </div>
                    <button wire:click="removeCafe({{ $cafe['id'] }})"
                        class="w-8 h-8 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-100 active:scale-90 transition-all flex-shrink-0">
                        <i class="ph-bold ph-x text-sm"></i>
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Submit Button --}}
    <button @click="submitTongkrongan()" 
        :disabled="isSubmitting || {{ count($selectedCafes) }} < 2"
        class="w-full py-4 rounded-2xl font-bold text-sm transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
        :class="isSubmitting ? 'bg-slate-200 text-slate-400' : 'bg-gradient-to-r from-amber-500 to-orange-500 text-white hover:shadow-xl active:scale-[0.98]'">
        <span x-show="!isSubmitting" class="flex items-center justify-center gap-2">
            <i class="ph-fill ph-share-network"></i>
            Buat & Share ke Grup!
        </span>
        <span x-show="isSubmitting">Membuat...</span>
    </button>

    {{-- Share Result --}}
    <div x-show="shareUrl" x-cloak class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 text-center">
        <i class="ph-fill ph-check-circle text-emerald-500 text-3xl mb-2"></i>
        <p class="text-sm font-bold text-emerald-700 mb-3">Tongkrongan berhasil dibuat!</p>
        <div class="flex gap-2">
            <button @click="copyLink()" class="flex-1 py-3 bg-white border border-emerald-200 rounded-xl text-xs font-bold text-emerald-600 active:scale-95 transition-all">
                <i class="ph-bold ph-copy"></i> Salin Link
            </button>
            <a :href="'https://wa.me/?text=' + encodeURIComponent('Vote cafe buat tongkrongan! 👉 ' + shareUrl)" 
               target="_blank" 
               class="flex-1 py-3 bg-emerald-500 rounded-xl text-xs font-bold text-white active:scale-95 transition-all no-underline text-center">
                <i class="ph-fill ph-whatsapp-logo"></i> Share WA
            </a>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('tongkronganCreateLogic', () => ({
        isSubmitting: false,
        shareUrl: '',

        getFingerprint() {
            let fp = localStorage.getItem('wadah-visitor-id');
            if (!fp) {
                fp = 'v-' + Math.random().toString(36).substr(2, 9) + Date.now();
                localStorage.setItem('wadah-visitor-id', fp);
            }
            return fp;
        },

        async submitTongkrongan() {
            this.isSubmitting = true;
            try {
                const title = this.$wire.title;
                const cafeIds = this.$wire.selectedCafes.map(c => c.id);
                
                const res = await fetch('/tongkrongan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        title: this.$wire.title,
                        cafe_ids: cafeIds,
                        fingerprint: this.getFingerprint(),
                    }),
                });

                const data = await res.json();
                if (res.ok) {
                    this.shareUrl = data.share_url;
                    if (window.hapticFeedback) window.hapticFeedback('medium');
                } else {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Gagal membuat list.', type: 'error' } }));
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.isSubmitting = false;
            }
        },

        copyLink() {
            navigator.clipboard.writeText(this.shareUrl).then(() => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Link disalin!', type: 'success' } }));
            });
        }
    }));
</script>
@endscript
