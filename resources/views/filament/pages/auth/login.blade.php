<div
    class="fi-login-wrapper relative min-h-screen grid place-items-center bg-[#0B0B0B] font-['Outfit'] overflow-hidden selection:bg-amber-500/30">

    {{-- Ambient Background --}}
    <div class="fixed inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0A0A0A] via-[#111111] to-[#0A0A0A]"></div>

        {{-- Dynamic Glow --}}
        <div class="absolute -top-32 -left-32 w-[520px] h-[520px] bg-amber-500/10 rounded-full blur-[160px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[420px] h-[420px] bg-orange-900/20 rounded-full blur-[180px]"></div>
    </div>

    <div class="relative z-10 w-full max-w-[420px] px-6 py-12 animate-in fade-in duration-1000">

        {{-- Brand --}}
        <div class="flex flex-col items-center mb-14">
            <div
                class="mb-5 p-3 rounded-2xl bg-white/[0.03] border border-white/[0.05] shadow-lg transition-transform duration-500 hover:scale-105">
                <img src="{{ asset('wadahicon.png') }}" class="w-10 h-10 brightness-125" alt="WadahNgopi Logo">
            </div>

            <h1 class="text-[2.1rem] font-black text-white tracking-[-0.04em] leading-none">
                Wadah<span class="text-amber-500 italic">Ngopi</span>
            </h1>
            <p class="mt-2 text-white/25 text-[10px] font-bold uppercase tracking-[0.45em]">
                Admin Management Hub
            </p>
        </div>

        {{-- Login Card --}}
        <div
            class="relative bg-[#141414]/80 backdrop-blur-2xl border border-white/[0.06]
                   rounded-[2.2rem] p-8 sm:p-10
                   shadow-[0_40px_80px_-20px_rgba(0,0,0,0.8)]">

            {{-- Subtle Inner Highlight --}}
            <div class="absolute inset-0 rounded-[2.2rem] ring-1 ring-white/[0.03] pointer-events-none"></div>

            <div class="relative mb-10">
                <h2 class="text-xl font-bold text-white mb-1 tracking-tight">
                    Authentikasi
                </h2>
            </div>

            <x-filament-panels::form wire:submit="authenticate" class="space-y-6 relative">
                {{ $this->form }}

                <div class="pt-4">
                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()" />
                </div>
            </x-filament-panels::form>
        </div>

        {{-- Footer --}}
        <div class="mt-16 text-center">
            <a href="/"
               class="inline-flex items-center gap-2 text-white/30 hover:text-amber-500
                      transition-all text-[10px] font-bold uppercase tracking-[0.2em] no-underline">
                <span class="text-lg leading-none">‹</span> Kembali ke Website
            </a>

            <div class="mt-8 pt-8 border-t border-white/[0.04]">
                <p class="text-white/10 text-[9px] font-semibold tracking-[0.15em] uppercase">
                    © 2026 WadahNgopi Professional Admin
                </p>
            </div>
        </div>
    </div>

    <style>
        /* ===== Filament Cleanup ===== */
        .fi-simple-main {
            display: none !important;
        }

        /* ===== Input Wrapper ===== */
        .fi-input-wrp {
            background: linear-gradient(180deg, #0A0A0A, #0E0E0E) !important;
            border: 1px solid #262626 !important;
            border-radius: 1.1rem !important;
            padding: 2px !important;
            transition: all .25s ease !important;
        }

        .fi-input-wrp:focus-within {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.12) !important;
        }

        input {
            background: transparent !important;
            color: #e5e5e5 !important;
            font-size: .95rem !important;
            padding: .8rem .9rem !important;
        }

        input::placeholder {
            color: #555 !important;
        }

        /* ===== Labels ===== */
        label {
            color: #737373 !important;
            font-size: .65rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: .14em !important;
            margin-bottom: .6rem !important;
        }

        /* ===== Submit Button ===== */
        button[type="submit"] {
            background: linear-gradient(135deg, #f59e0b, #fbbf24) !important;
            color: #0B0B0B !important;
            font-weight: 900 !important;
            border-radius: 1.1rem !important;
            padding: .95rem !important;
            width: 100% !important;
            border: none !important;
            text-transform: uppercase !important;
            letter-spacing: .18em !important;
            font-size: .75rem !important;
            box-shadow: 0 15px 35px rgba(245, 158, 11, .35) !important;
            transition: all .25s ease !important;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 45px rgba(245, 158, 11, .45) !important;
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        /* ===== Checkbox ===== */
        .fi-fo-checkbox input {
            background-color: #0B0B0B !important;
            border: 1px solid #333 !important;
            border-radius: 5px !important;
            color: #f59e0b !important;
        }

        .fi-fo-checkbox span {
            color: #666 !important;
            font-size: .8rem !important;
        }

        /* ===== Error Message ===== */
        .fi-fo-field-wrp-error-message {
            color: #ef4444 !important;
            font-weight: 700 !important;
            font-size: .7rem !important;
            margin-top: .5rem !important;
        }

        /* ===== Scroll Control ===== */
        body {
            overflow: hidden !important;
        }

        @media (max-height: 720px) {
            body {
                overflow-y: auto !important;
            }
        }
    </style>
</div>
