{{-- Skeleton Info Card — matches actual information-feed card structure --}}
<div class="group relative flex gap-4 p-3 bg-white rounded-[24px] border border-[#1a0f0a]/5 shadow-sm overflow-hidden skeleton-card"
    aria-hidden="true">
    {{-- Thumbnail --}}
    <div class="w-[100px] h-[100px] shrink-0 rounded-[20px] skeleton-shimmer overflow-hidden"></div>

    {{-- Text Content --}}
    <div class="flex-1 min-w-0 py-1.5 flex flex-col justify-center gap-2">
        {{-- Category & Date --}}
        <div class="flex items-center gap-2 mb-1">
            <div class="h-4 w-14 bg-gray-100 skeleton-shimmer rounded-md"></div>
            <div class="h-4 w-16 bg-gray-100 skeleton-shimmer rounded-md"></div>
        </div>

        {{-- Title (2 lines) --}}
        <div class="h-4 w-full bg-gray-100 skeleton-shimmer rounded-md"></div>
        <div class="h-4 w-3/4 bg-gray-100 skeleton-shimmer rounded-md"></div>

        {{-- Views --}}
        <div class="mt-auto flex items-center gap-1.5">
            <div class="h-3 w-3 bg-gray-100 skeleton-shimmer rounded-full"></div>
            <div class="h-3 w-12 bg-gray-100 skeleton-shimmer rounded-md"></div>
        </div>
    </div>
</div>