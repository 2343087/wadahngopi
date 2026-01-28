<div class="fi-wi-widget">
    <div class="relative overflow-hidden rounded-3xl welcome-banner-luxury px-8 py-10 shadow-2xl transition-all">
        {{-- Background Decoration --}}
        <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -left-10 -bottom-10 h-48 w-48 rounded-full bg-amber-400/10 blur-2xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-black text-white tracking-tight mb-2">
                    Yo, Selamat Datang di WadahNgopi! ☕️
                </h2>
                <p class="text-amber-50 font-medium text-lg opacity-90 max-w-xl">
                    Halo {{ auth()->user()->role === 'admin' ? 'Super Admin' : 'Bos Cafe' }},
                    siap buat bikin WadahNgopi makin asik and sat-set hari ini? Yuk kelola data mu dengan gaya! 🚀
                </p>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" target="_blank"
                    class="inline-flex items-center px-6 py-3 rounded-2xl welcome-banner-btn-primary font-bold transition-all hover:scale-105 active:scale-95">
                    <i class="ph-bold ph-browser mr-2"></i>
                    Lihat Web
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ \App\Filament\Resources\CafeResource::getUrl('index') }}"
                        class="inline-flex items-center px-6 py-3 rounded-2xl welcome-banner-btn-secondary font-bold transition-all hover:scale-105 active:scale-95">
                        <i class="ph-bold ph-storefront mr-2"></i>
                        Kelola Cafe
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>