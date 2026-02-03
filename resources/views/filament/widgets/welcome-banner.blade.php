<div class="fi-wi-widget">
    <div class="relative overflow-hidden rounded-[2.5rem] welcome-banner-luxury p-8 lg:p-12 transition-all">
        {{-- Absolute Background Elements for "Lux" Feel --}}
        <div
            class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-amber-500/10 blur-[80px] animate-float shadow-2xl">
        </div>
        <div class="absolute -left-10 -bottom-10 h-64 w-64 rounded-full bg-white/5 blur-[80px] animate-float shadow-2xl"
            style="animation-delay: -3s"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="text-center lg:text-left">
                @php
                    $hour = now()->hour;
                    $greeting = 'Selamat Malam';
                    if ($hour < 12)
                        $greeting = 'Selamat Pagi';
                    elseif ($hour < 15)
                        $greeting = 'Selamat Siang';
                    elseif ($hour < 19)
                        $greeting = 'Selamat Sore';
                @endphp

                <div
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white/10 border border-white/20 mb-8 backdrop-blur-md">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    <span class="text-[10px] font-black text-amber-200 uppercase tracking-[0.3em] !m-0">AI Dashboard
                        Hub</span>
                </div>

                <h2 class="text-4xl lg:text-6xl font-black text-white tracking-tighter mb-4 leading-none">
                    {{ $greeting }}, <span
                        class="text-amber-400 font-serif italic font-light tracking-tight">{{ auth()->user()->name }}</span>
                    ☕️
                </h2>

                <p class="text-white font-medium text-lg lg:text-xl max-w-xl leading-relaxed opacity-80">
                    Siap gaspol <span
                        class="text-white font-bold italic underline decoration-amber-500 underline-offset-8">WadahNgopi</span>
                    hari ini?
                    Yuk kelola bisnis lo dengan gaya sultan! 🚀
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-6">
                <a href="{{ route('home') }}" target="_blank"
                    class="group relative inline-flex items-center justify-center px-10 py-5 rounded-2xl bg-[#D97706] text-[#1A0F0A] font-black text-lg transition-all hover:scale-105 active:scale-95 shadow-[0_20px_40px_-15px_rgba(217,119,6,0.5)] !no-underline">
                    <span class="mr-3">Lihat Web</span>
                    <svg class="w-6 h-6 transform transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'developer')
                    <a href="{{ \App\Filament\Resources\CafeResource::getUrl('index') }}"
                        class="inline-flex items-center justify-center px-10 py-5 rounded-2xl bg-white/10 text-white border border-white/20 backdrop-blur-xl font-bold text-lg transition-all hover:bg-white/15 hover:scale-105 active:scale-95 !no-underline">
                        <span class="mr-3">Kelola Cafe</span>
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>