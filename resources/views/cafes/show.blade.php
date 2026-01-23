@extends('layouts.app')

@section('title', $cafe->name . ' - WadahNgopi.Com')

@php
    use Illuminate\Support\Facades\Storage;

    $rawImages = collect([$cafe->image_path])
        ->merge($cafe->images ?? [])
        ->filter()
        ->map(function ($img) {
            return str_starts_with($img, 'http') ? $img : Storage::url($img);
        })
        ->values()
        ->all();

    $galleryImages = empty($rawImages)
        ? ['https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=1200']
        : $rawImages;
@endphp

@section('content')
    <div class="detail-wrapper" x-data="cafeDetailComponent({
        id: {{ $cafe->id }},
        totalEnergy: {{ $cafe->total_energy }},
        images: {{ json_encode($galleryImages) }}
    })">

        {{-- Hero Slider Section --}}
        <div class="detail-hero-luxury" @touchstart="touchStart($event)" @touchend="touchEnd($event)">
            {{-- Nav Overlay --}}
            <nav class="detail-nav-overlay">
                <a href="javascript:history.back()" class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-xl border border-white/30 flex items-center justify-center text-white shadow-lg transition-all active:scale-90 no-underline">
                    <i class="ph ph-arrow-left text-2xl"></i>
                </a>
                <button class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-xl border border-white/30 flex items-center justify-center text-white shadow-lg transition-all active:scale-90" @click="toggleBookmark">
                    <i :class="isBookmarked ? 'ph-fill ph-bookmark-simple' : 'ph ph-bookmark-simple'"
                        :style="isBookmarked ? 'color: #F59E0B' : ''" class="text-2xl"></i>
                </button>
            </nav>

            {{-- Slider Images --}}
            <template x-for="(img, idx) in images" :key="idx">
                <div x-show="currentSlide === idx" x-transition:enter="transition duration-700 ease-out"
                    x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100"
                    class="absolute inset-0">
                    <img :src="img" class="detail-gallery-img" alt="Cafe">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20"></div>
                </div>
            </template>

            {{-- Slider Info Overlay --}}
            <div class="absolute bottom-16 left-6 right-6 z-20">
                <span class="status-label-luxury {{ $cafe->is_open ? 'status-open' : 'status-closed' }} mb-3 inline-block">
                    {{ $cafe->is_open ? 'Buka Sekarang' : 'Tutup' }}
                </span>
                <h1 class="text-4xl font-black text-white leading-tight drop-shadow-lg">{{ $cafe->name }}</h1>
                <div class="flex items-center gap-2 text-white/80 font-bold text-sm mt-3">
                    <i class="ph-fill ph-map-pin text-[--color-amber]"></i>
                    <span class="drop-shadow-sm">{{ $cafe->address }}</span>
                </div>
            </div>

            {{-- Dot Indicators --}}
            <div class="absolute bottom-10 left-6 flex gap-1.5 z-30">
                <template x-for="(_, idx) in images" :key="idx">
                    <div class="h-1 rounded-full transition-all duration-500" 
                        :class="currentSlide === idx ? 'w-8 bg-white' : 'w-2 bg-white/40'"
                        @click="currentSlide = idx"></div>
                </template>
            </div>
        </div>

        {{-- Main Detail Content --}}
        <div class="detail-content-luxury animate-up">
            {{-- Action Buttons --}}
            <div class="flex gap-4 mb-10">
                <a href="{{ $cafe->google_maps_url }}" target="_blank" class="flex-1 btn btn-primary py-4 h-16">
                    <i class="ph-fill ph-navigation-arrow text-xl"></i>Petunjuk Arah
                </a>
                <a href="https://wa.me/{{ $cafe->whatsapp_number }}" target="_blank" class="flex-1 btn bg-[#10B981] text-white py-4 h-16 shadow-lg shadow-emerald-500/20">
                    <i class="ph-fill ph-whatsapp-logo text-2xl"></i>Chat Admin
                </a>
            </div>

            <div class="text-slate-600 leading-[1.8] text-[1.05rem] font-medium mb-10 opacity-90">
                {{ $cafe->description }}
            </div>

            {{-- Features Section --}}
            <section class="mb-12">
                <h2 class="text-[1.3rem] font-black text-[--color-espresso] mb-5 flex items-center gap-3">
                    <i class="ph-fill ph-sparkle text-[--color-amber] text-2xl"></i>
                    Fasilitas
                </h2>
                <div class="flex flex-wrap gap-3">
                    @forelse($cafe->facilities as $f)
                        <div class="bg-slate-50 border border-slate-100 px-5 py-3 rounded-2xl flex items-center gap-3 transition-all hover:border-[--color-coffee-light]">
                            <i class="{{ $f->icon ?? 'ph ph-check-circle' }} text-[--color-coffee-dark] text-xl"></i>
                            <span class="font-extrabold text-[--color-espresso] text-sm">{{ $f->name }}</span>
                        </div>
                    @empty
                        <span class="text-slate-300 italic text-sm">Informasi belum tersedia.</span>
                    @endforelse
                </div>
            </section>

            {{-- Menu Section --}}
            <section class="mb-12">
                <h2 class="text-[1.3rem] font-black text-[--color-espresso] mb-6 flex items-center gap-3">
                    <i class="ph-fill ph-coffee text-[--color-coffee-light] text-2xl"></i>
                    Menu Favorit
                </h2>

                {{-- Tabs --}}
                <div class="flex gap-3 overflow-x-auto pb-4 mb-4 scrollbar-none">
                    <template x-for="t in ['coffee', 'non-coffee', 'food']">
                        <button class="pill-luxury shrink-0"
                            :class="currentTab === t ? 'active' : ''"
                            @click="currentTab = t"
                            x-text="t === 'non-coffee' ? 'Non Coffee' : (t.charAt(0).toUpperCase() + t.slice(1))">
                        </button>
                    </template>
                </div>

                {{-- Menu List --}}
                <div class="grid gap-4">
                    @forelse($cafe->menus as $menu)
                        <div class="flex items-center gap-4 p-4 bg-white rounded-3xl border border-slate-100 shadow-sm transition-all active:scale-95" 
                            x-show="currentTab === '{{ $menu->type }}'"
                            x-transition:enter="transition duration-300"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            
                            <img src="{{ $menu->image_path && str_starts_with($menu->image_path, 'http') ? $menu->image_path : ($menu->image_path ? Storage::url($menu->image_path) : 'https://images.unsplash.com/photo-1541167760496-162955ed8a9f?w=200') }}" 
                                class="w-[80px] h-[80px] rounded-2xl object-cover shadow-sm" 
                                alt="{{ $menu->name }}">

                            <div class="flex-1 min-w-0">
                                <h4 class="font-black text-[--color-espresso] text-[1.1rem] mb-1 truncate">{{ $menu->name }}</h4>
                                <div class="font-black text-[--color-amber] text-base">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                            </div>

                            <button class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
                                <i class="ph-bold ph-caret-right"></i>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                            <span class="text-slate-400 font-bold">Belum ada menu tersedia.</span>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Interactive Energy Center --}}
            <section class="mb-12">
                <div class="energy-box-luxury">
                    <h3 class="text-2xl font-black text-[--color-espresso] mb-2">Kirim Energi! ⚡️</h3>
                    <p class="text-slate-400 font-bold text-[0.9rem] mb-8 px-6">Bantu cafe ini makin populer dengan sekali ketuk!</p>

                    <div class="relative inline-block">
                        <button @click="addEnergy" class="energy-btn-luxury" :class="isBouncing ? 'animate-bounce' : ''">
                            <i class="ph-fill ph-coffee text-[--color-coffee-dark]"></i>
                        </button>
                        
                        {{-- Particles --}}
                        <template x-for="p in particles" :key="p.id">
                            <span class="floating-particle" 
                                :style="'left: ' + p.x + 'px; bottom: 80px;'"
                                x-text="p.icon"></span>
                        </template>
                    </div>

                    <div class="mt-6 flex flex-col items-center">
                        <div class="px-6 py-2 bg-white rounded-full border border-slate-100 shadow-sm font-black text-[1.2rem] text-[--color-espresso]">
                            <span x-text="formatNumber(totalEnergy)"></span> ⚡️
                        </div>
                        <div x-show="pendingSync > 0" x-cloak class="mt-3 text-[0.7rem] font-black text-slate-300 uppercase tracking-widest">
                            Menyimpan <span x-text="pendingSync"></span> energi...
                        </div>
                    </div>
                </div>
            </section>

            {{-- Footer Spacer --}}
            <div class="h-20"></div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cafeDetailComponent', (props) => ({
                currentSlide: 0,
                currentTab: 'coffee',
                images: props.images,
                isBookmarked: false,
                totalEnergy: props.totalEnergy,
                pendingSync: 0,
                isBouncing: false,
                particles: [],
                visitorId: '',
                syncTimer: null,
                tx: 0,

                init() {
                    let vid = localStorage.getItem('wadah-visitor-id');
                    if (!vid) {
                        vid = 'visitor-' + Math.random().toString(36).substr(2, 9) + Date.now();
                        localStorage.setItem('wadah-visitor-id', vid);
                    }
                    this.visitorId = vid;

                    const saved = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]');
                    this.isBookmarked = saved.includes(props.id);
                    
                    // Auto slide
                    setInterval(() => { this.nextSlide(); }, 6000);

                    // Final Sync
                    window.addEventListener('beforeunload', () => {
                        if (this.pendingSync > 0) this.syncEnergy();
                    });
                },

                nextSlide() { this.currentSlide = (this.currentSlide + 1) % this.images.length; },
                prevSlide() { this.currentSlide = (this.currentSlide - 1 + this.images.length) % this.images.length; },

                touchStart(e) { this.tx = e.touches[0].clientX; },
                touchEnd(e) {
                    const dx = this.tx - e.changedTouches[0].clientX;
                    if (Math.abs(dx) > 40) { dx > 0 ? this.nextSlide() : this.prevSlide(); }
                },

                toggleBookmark() {
                    let b = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]');
                    this.isBookmarked ? b = b.filter(id => id !== props.id) : b.push(props.id);
                    localStorage.setItem('wadah-bookmarks', JSON.stringify(b));
                    this.isBookmarked = !this.isBookmarked;
                },

                addEnergy() {
                    this.totalEnergy++;
                    this.pendingSync++;
                    this.isBouncing = true;
                    setTimeout(() => { this.isBouncing = false; }, 200);

                    const icons = ['☕️', '⚡️', '🔥', '✨'];
                    const id = Date.now() + Math.random();
                    this.particles.push({
                        id,
                        icon: icons[Math.floor(Math.random() * icons.length)],
                        x: (Math.random() * 60) - 30
                    });

                    setTimeout(() => {
                        this.particles = this.particles.filter(p => p.id !== id);
                    }, 1200);

                    clearTimeout(this.syncTimer);
                    this.syncTimer = setTimeout(() => { this.syncEnergy(); }, 1500);
                },

                async syncEnergy() {
                    if (this.pendingSync === 0) return;
                    const count = this.pendingSync;
                    this.pendingSync = 0;

                    try {
                        const r = await fetch('{{ route('cafes.energy', $cafe) }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ energy_count: count, visitor_id: this.visitorId })
                        });
                        const d = await r.json();
                        if (d.new_total > this.totalEnergy) this.totalEnergy = d.new_total;
                    } catch (e) {
                        this.pendingSync += count;
                    }
                },

                formatNumber(n) {
                    return new Intl.NumberFormat('id-ID').format(n);
                }
            }));
        });
    </script>
@endsection