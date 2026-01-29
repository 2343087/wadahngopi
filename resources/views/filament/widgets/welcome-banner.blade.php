<div class="fi-wi-widget">
    <div class="relative overflow-hidden rounded-[2.5rem] welcome-banner-luxury px-10 py-12 shadow-2xl transition-all">
        {{-- Background Decoration --}}
        <div class="absolute -right-20 -top-20 h-96 w-96 rounded-full bg-white/5 blur-[100px] animate-float"></div>
        <div class="absolute -left-20 -bottom-20 h-80 w-80 rounded-full bg-amber-400/5 blur-[80px] animate-float"
            style="animation-delay: -3s"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-10">
            <div class="text-center lg:text-left">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/10 text-xs font-bold text-amber-100 uppercase tracking-widest mb-6 animate-pulse-soft">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Dashboard Berjalan Lancar
                </div>

                <h2 class="text-4xl font-black text-white tracking-tight mb-4 leading-tight">
                    WadahNgopi <span class="text-amber-200 italic font-medium text-3xl">Control Center</span> ☕️
                </h2>
                <p class="text-amber-50 font-medium text-lg opacity-80 max-w-2xl leading-relaxed">
                    Halo {{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Bos Cafe' }},
                    dashboard sudah di-optimize buat nemenin lo kelola bisnis hari ini. Yuk gaskeun! 🚀
                </p>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('home') }}" target="_blank"
                    class="inline-flex items-center px-8 py-4 rounded-2xl welcome-banner-btn-primary font-black transition-all hover:scale-105 active:scale-95 shadow-xl">
                    <i class="ph-bold ph-arrow-square-out mr-2 text-xl"></i>
                    Lihat Web
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ \App\Filament\Resources\CafeResource::getUrl('index') }}"
                        class="inline-flex items-center px-8 py-4 rounded-2xl welcome-banner-btn-secondary font-black transition-all hover:scale-105 active:scale-95">
                        <i class="ph-bold ph-coffee mr-2 text-xl"></i>
                        Kelola Cafe
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>