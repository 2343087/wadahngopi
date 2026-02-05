<x-filament::section>
    <div class="relative overflow-hidden rounded-[2.5rem] bg-[#1a0f0a] p-8 lg:p-12 shadow-2xl isolate">
        {{-- Absolute Background Elements --}}
        <div
            class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-[#F59E0B] opacity-20 blur-[80px] animate-pulse">
        </div>
        <div class="absolute -left-10 -bottom-10 h-64 w-64 rounded-full bg-white opacity-5 blur-[80px]"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="text-center lg:text-left space-y-4">


                {{-- Greeting --}}
                <h2 class="text-3xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                    {{ $greeting }}, <br class="hidden lg:block">
                    <span class="text-[#F59E0B] italic font-serif font-light">{{ $user->name }}</span> ☕️
                </h2>

                <p class="text-white/70 text-base lg:text-lg max-w-lg leading-relaxed">
                    Siap pantau performa <span
                        class="text-white font-bold underline decoration-[#F59E0B]">WadahNgopi</span> hari ini?
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <a href="{{ route('home') }}" target="_blank"
                    class="group inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-[#F59E0B] text-[#2C1810] font-black text-sm uppercase tracking-wider hover:bg-[#fbbf24] hover:-translate-y-1 transition-all shadow-[0_10px_20px_rgba(245,158,11,0.3)]">
                    <span>Lihat Website</span>
                    <x-heroicon-m-arrow-top-right-on-square
                        class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                </a>

                @if(auth()->user()->role === 'developer' || auth()->user()->role === 'admin')
                    <a href="{{ \App\Filament\Resources\CafeResource::getUrl('index') }}"
                        class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-white/10 text-white font-bold text-sm uppercase tracking-wider backdrop-blur-md border border-white/10 hover:bg-white/20 transition-all">
                        <span>Kelola Cafe</span>
                        <x-heroicon-m-building-storefront class="w-5 h-5 text-[#F59E0B]" />
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-filament::section>