<div wire:poll.10s="refreshVotes" x-data="tongkronganVoteLogic()">
    {{-- Title & Timer --}}
    <div class="mb-6">
        <h2 class="text-2xl font-black text-[#2C1810] leading-tight">{{ $tongkrongan->title }}</h2>
        <div class="flex items-center gap-2 mt-2">
            <span class="text-[0.65rem] font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg flex items-center gap-1">
                <i class="ph-fill ph-timer"></i>
                {{ $expiresIn }}
            </span>
            <span class="text-[0.65rem] font-bold text-slate-400">
                {{ $tongkrongan->items->sum(fn($i) => $i->votes->count()) }} votes total
            </span>
        </div>
    </div>

    {{-- Cafe Cards with Vote --}}
    <div class="space-y-3">
        @foreach($items as $index => $item)
            @php
                $cafe = $item->cafe;
                $voteCount = $item->votes->count();
                $isWinner = $maxVotes > 0 && $voteCount === $maxVotes;
                $pct = $maxVotes > 0 ? ($voteCount / $maxVotes) * 100 : 0;
            @endphp
            <div class="relative bg-white border {{ $isWinner && $voteCount > 0 ? 'border-amber-300 ring-2 ring-amber-100' : 'border-slate-100' }} rounded-[24px] p-4 shadow-sm transition-all hover:shadow-md">
                {{-- Winner Crown --}}
                @if($isWinner && $voteCount > 0)
                    <div class="absolute -top-3 left-4 bg-amber-500 text-white text-[0.5rem] font-black uppercase tracking-widest px-2.5 py-1 rounded-full shadow-md">
                        👑 Leading
                    </div>
                @endif

                <div class="flex items-center gap-3">
                    {{-- Cafe Image --}}
                    <div class="w-14 h-14 rounded-2xl overflow-hidden bg-slate-100 flex-shrink-0">
                        @if($cafe && $cafe->image_path)
                            <img src="/storage/{{ $cafe->image_path }}" class="w-full h-full object-cover" alt="{{ $cafe->name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="ph-fill ph-coffee text-2xl"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Cafe Info --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-black text-[#2C1810] truncate">{{ $cafe->name ?? 'Cafe' }}</h3>
                        <p class="text-[0.6rem] text-slate-400 font-medium truncate">
                            <i class="ph-fill ph-map-pin text-amber-500"></i>
                            {{ Str::limit($cafe->address ?? '', 40) }}
                        </p>
                        {{-- Vote Bar --}}
                        <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-700 {{ $isWinner && $voteCount > 0 ? 'bg-amber-500' : 'bg-slate-300' }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    {{-- Vote Button --}}
                    <button @click="vote({{ $item->id }})"
                        :class="hasVoted({{ $item->id }}) ? 'bg-amber-500 text-white border-amber-400' : 'bg-white text-[#2C1810] border-slate-200 hover:border-amber-300'"
                        class="flex flex-col items-center justify-center w-14 h-14 rounded-2xl border-2 transition-all active:scale-90 flex-shrink-0">
                        <i :class="hasVoted({{ $item->id }}) ? 'ph-fill ph-heart' : 'ph ph-heart'" class="text-lg"></i>
                        <span class="text-[0.55rem] font-black mt-0.5" x-text="voteCounts[{{ $item->id }}] ?? {{ $voteCount }}">{{ $voteCount }}</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Share Section --}}
    <div class="mt-8 bg-gradient-to-br from-[#1A0F0A] to-[#2D1B12] rounded-[28px] p-6 text-white text-center">
        <p class="text-white/60 text-xs font-medium mb-3">Share biar temen lo ikut vote!</p>
        <div class="flex gap-2">
            <button @click="copyLink('{{ $tongkrongan->share_url }}')" 
                class="flex-1 py-3 bg-white/10 border border-white/10 rounded-xl text-xs font-bold text-white active:scale-95 transition-all">
                <i class="ph-bold ph-copy"></i> Salin Link
            </button>
            <a href="https://wa.me/?text={{ urlencode('Vote cafe buat tongkrongan! 👉 ' . $tongkrongan->share_url) }}" 
               target="_blank"
               class="flex-1 py-3 bg-emerald-500 rounded-xl text-xs font-bold text-white active:scale-95 transition-all no-underline text-center">
                <i class="ph-fill ph-whatsapp-logo"></i> Share WA
            </a>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('tongkronganVoteLogic', () => ({
        votedItems: JSON.parse(localStorage.getItem('tongkrongan-votes-{{ $tongkrongan->uuid }}') || '[]'),
        voteCounts: {},

        getFingerprint() {
            let fp = localStorage.getItem('wadah-visitor-id');
            if (!fp) {
                fp = 'v-' + Math.random().toString(36).substr(2, 9) + Date.now();
                localStorage.setItem('wadah-visitor-id', fp);
            }
            return fp;
        },

        hasVoted(itemId) {
            return this.votedItems.includes(itemId);
        },

        async vote(itemId) {
            try {
                const res = await fetch(`/tongkrongan/{{ $tongkrongan->uuid }}/vote/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ fingerprint: this.getFingerprint() }),
                });

                const data = await res.json();
                if (res.ok) {
                    if (data.action === 'added') {
                        if (!this.votedItems.includes(itemId)) this.votedItems.push(itemId);
                    } else {
                        this.votedItems = this.votedItems.filter(id => id !== itemId);
                    }
                    localStorage.setItem('tongkrongan-votes-{{ $tongkrongan->uuid }}', JSON.stringify(this.votedItems));
                    this.voteCounts[itemId] = data.vote_count;
                    if (window.hapticFeedback) window.hapticFeedback('light');
                    // Refresh Livewire for real-time update
                    this.$wire.refreshVotes();
                }
            } catch (e) {
                console.error('[Vote]', e);
            }
        },

        copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Link disalin!', type: 'success' } }));
            });
        }
    }));
</script>
@endscript
