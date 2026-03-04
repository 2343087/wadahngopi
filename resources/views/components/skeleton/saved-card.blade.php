{{-- Skeleton Saved Card — pixel-perfect match with saved-items.blade.php lines 17-69 --}}
<div class="group block bg-white border border-[#1a0f0a]/5 rounded-[24px] shadow-sm overflow-hidden relative"
    aria-hidden="true" style="pointer-events:none;">
    <div class="flex items-center p-3 gap-4">
        {{-- Left: Fixed Image (w-20 h-20 shrink-0 rounded-[18px]) --}}
        <div class="w-20 h-20 shrink-0 rounded-[18px] overflow-hidden bg-gray-100 relative shadow-inner">
            <div class="absolute inset-0 skeleton-shimmer"></div>
            {{-- Status Dot --}}
            <div
                class="absolute top-1.5 right-1.5 w-3.5 h-3.5 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-sm z-20">
                <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
            </div>
        </div>

        {{-- Middle: Content Area (flex-1 min-w-0 flex-col justify-center gap-1) --}}
        <div class="flex-1 min-w-0 flex flex-col justify-center gap-1">
            {{-- Title --}}
            <div class="h-4 w-4/5 bg-gray-200 rounded skeleton"></div>

            {{-- Address --}}
            <div class="flex items-center gap-1">
                <div class="h-3 w-3 shrink-0 bg-[#F59E0B]/30 rounded-full"></div>
                <div class="h-3 w-3/5 bg-gray-200 rounded skeleton"></div>
            </div>

            {{-- Tags --}}
            <div class="flex flex-wrap gap-1.5 mt-1">
                <div class="px-2 py-0.5 w-12 h-[18px] bg-[#FAF9F6] rounded-md skeleton"></div>
                <div class="px-2 py-0.5 w-14 h-[18px] bg-[#FAF9F6] rounded-md skeleton"></div>
            </div>
        </div>

        {{-- Right: Actions (pl-2 border-l) --}}
        <div class="pl-2 border-l border-[#1a0f0a]/5 flex flex-col justify-center">
            <div class="flex items-center justify-center w-10 h-10 bg-[#FEF2F2] rounded-xl">
                <div class="w-5 h-5 bg-[#EF4444]/15 rounded skeleton"></div>
            </div>
        </div>
    </div>
</div>