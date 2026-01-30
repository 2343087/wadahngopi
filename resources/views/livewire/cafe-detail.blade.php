{{-- Livewire Cafe Detail Component --}}
{{-- wire:poll.60s refreshes open/close status every 60 seconds --}}
<div wire:poll.60s="refreshStatus">
    @if($hasCafe)
        {{-- Status badge that updates in realtime --}}
        <div class="realtime-status" x-data="{ isOpen: @js($isOpen) }">
            @if($isOpen)
                <span
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-lg">
                    <span class="h-2 w-2 bg-white rounded-full animate-pulse"></span>
                    BUKA SEKARANG
                </span>
            @else
                <span
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold bg-rose-500 text-white shadow-lg">
                    <span class="h-2 w-2 bg-white rounded-full"></span>
                    TUTUP
                </span>
            @endif
        </div>
    @endif
</div>