<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
    current: { type: String, default: '/' },
    native: Boolean,
})
const emit = defineEmits(['navigate'])

const publicLinks = [
    { key: '/', href: '/', label: 'Beranda', icon: 'home' },
    { key: '/explore', href: '/explore', label: 'Jelajahi', icon: 'explore' },
    { key: '/roastery', href: '/roastery', label: 'Roastery', icon: 'bean' },
    { key: '/saved', href: '/saved', label: 'Simpan', icon: 'saved' },
    { key: '/information', href: '/information', label: 'Kabar', icon: 'news' },
]

function isActive(item) {
    if (item.href === '/') return props.current === '/'
    if (item.href === '/explore' && props.current.startsWith('/cafes/')) return true
    return props.current === item.href || props.current.startsWith(item.href + '/')
}

const activeIndex = computed(() => Math.max(0, publicLinks.findIndex(isActive)))
const dockRef = ref(null)
const isDragging = ref(false)
const dragTargetIndex = ref(null)

// Live visual active index: follows the pill in real-time during drag so contrast never breaks
const visualActiveIndex = computed(() =>
    isDragging.value && dragTargetIndex.value !== null ? dragTargetIndex.value : activeIndex.value
)

// GPU-only pill state: x (px), w (px), scaleX, scaleY, ready
const dockPill = ref({ x: 0, w: 0, sx: 1, sy: 1, ready: false })

let pointerId = null
let dockResizeObserver = null
let startX = 0
let lastX = 0
let lastTime = 0
let dragged = false
let suppressClick = false
let itemRects = []
let navRect = null
let decayRaf = 0
let currentVel = 0

function measureItems() {
    const nav = dockRef.value
    if (!nav) return []
    navRect = nav.getBoundingClientRect()
    itemRects = Array.from(nav.querySelectorAll('a[href]')).map(el => ({
        left: el.offsetLeft,
        width: el.offsetWidth,
        center: el.offsetLeft + el.offsetWidth / 2,
    }))
    return itemRects
}

function updateDockPill(targetIdx = activeIndex.value) {
    nextTick(() => {
        const rects = measureItems()
        const slot = rects[targetIdx] ?? rects[0]
        if (!slot) {
            dockPill.value = { x: 0, w: 0, sx: 1, sy: 1, ready: false }
            return
        }
        dockPill.value = {
            x: slot.left,
            w: slot.width,
            sx: 1,
            sy: 1,
            ready: true,
        }
    })
}

function findClosestIndex(centerX) {
    if (!itemRects.length) return activeIndex.value
    let closest = 0
    let best = Infinity
    for (let i = 0; i < itemRects.length; i++) {
        const dist = Math.abs(centerX - itemRects[i].center)
        if (dist < best) {
            best = dist
            closest = i
        }
    }
    return closest
}

function decayVelocity() {
    if (!isDragging.value) {
        decayRaf = 0
        return
    }
    currentVel *= 0.78
    if (Math.abs(currentVel) < 0.02) {
        currentVel = 0
        dockPill.value = { ...dockPill.value, sx: 1.04, sy: 0.96 }
        decayRaf = 0
        return
    }
    const stretch = Math.min(0.22, Math.abs(currentVel) * 0.14)
    dockPill.value = {
        ...dockPill.value,
        sx: 1.04 + stretch,
        sy: Math.max(0.84, 0.96 - stretch * 0.45),
    }
    decayRaf = requestAnimationFrame(decayVelocity)
}

function onPointerDown(event) {
    if (event.button !== 0 && event.pointerType === 'mouse') return
    const nav = dockRef.value
    if (!nav) return
    measureItems()
    pointerId = event.pointerId
    startX = event.clientX
    lastX = event.clientX
    lastTime = performance.now()
    currentVel = 0
    dragged = false
    dragTargetIndex.value = activeIndex.value
    isDragging.value = false
}

function onPointerMove(event) {
    if (pointerId === null || event.pointerId !== pointerId) return
    const nav = dockRef.value
    if (!nav || !navRect || !itemRects.length) return

    const dxFromStart = event.clientX - startX
    if (!dragged && Math.abs(dxFromStart) > 5) {
        dragged = true
        isDragging.value = true
        nav.setPointerCapture?.(event.pointerId)
    }
    if (!dragged) return

    const now = performance.now()
    const dt = Math.max(8, now - lastTime)
    const vx = (event.clientX - lastX) / dt // px per ms
    currentVel = currentVel * 0.45 + vx * 0.55
    lastX = event.clientX
    lastTime = now

    // Center pill smoothly on pointer position inside the dock
    const pointerLocalX = event.clientX - navRect.left
    const closestIdx = findClosestIndex(pointerLocalX)
    dragTargetIndex.value = closestIdx

    const slotW = itemRects[closestIdx]?.width || dockPill.value.w
    const maxX = Math.max(0, nav.clientWidth - slotW)
    const rawX = pointerLocalX - slotW / 2

    // Soft rubber-band resistance past left/right boundaries (liquid clay feel)
    let targetX = rawX
    let edgeSquish = 0
    if (rawX < 0) {
        targetX = rawX * 0.18
        edgeSquish = Math.min(0.12, Math.abs(rawX) / 140)
    } else if (rawX > maxX) {
        targetX = maxX + (rawX - maxX) * 0.18
        edgeSquish = Math.min(0.12, (rawX - maxX) / 140)
    }

    const velStretch = Math.min(0.24, Math.abs(currentVel) * 0.15)
    const sx = edgeSquish > 0 ? Math.max(0.88, 1.02 - edgeSquish) : 1.04 + velStretch
    const sy = edgeSquish > 0 ? 1 + edgeSquish * 0.45 : Math.max(0.84, 0.96 - velStretch * 0.45)

    dockPill.value = {
        x: targetX,
        w: slotW,
        sx,
        sy,
        ready: true,
    }

    if (!decayRaf) {
        decayRaf = requestAnimationFrame(decayVelocity)
    }
}

function onPointerUp(event) {
    if (pointerId === null || event.pointerId !== pointerId) return
    const wasDragged = dragged
    const targetIdx = dragTargetIndex.value ?? activeIndex.value

    if (decayRaf) {
        cancelAnimationFrame(decayRaf)
        decayRaf = 0
    }
    pointerId = null
    dragged = false
    isDragging.value = false
    dragTargetIndex.value = null

    if (!wasDragged) {
        updateDockPill()
        return
    }

    suppressClick = true
    setTimeout(() => { suppressClick = false }, 60)

    // Spring-settle onto chosen tab immediately
    updateDockPill(targetIdx)

    const target = publicLinks[targetIdx]
    if (target && targetIdx !== activeIndex.value) {
        if (props.native) {
            window.location.href = target.href
        } else {
            emit('navigate', target.href)
        }
    }
}

function activate(event, item) {
    if (suppressClick) {
        event.preventDefault()
        return
    }
    if (props.native) return
    if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return
    event.preventDefault()
    emit('navigate', item.href)
}

watch(() => props.current, () => updateDockPill(), { flush: 'post' })

const onResize = () => updateDockPill()

onMounted(() => {
    updateDockPill()
    if (typeof ResizeObserver !== 'undefined' && dockRef.value) {
        dockResizeObserver = new ResizeObserver(() => {
            if (!isDragging.value) updateDockPill()
        })
        dockResizeObserver.observe(dockRef.value)
    }
    if (document.fonts?.ready) {
        document.fonts.ready.then(() => updateDockPill())
    }
    window.addEventListener('resize', onResize, { passive: true })
})

onBeforeUnmount(() => {
    if (decayRaf) cancelAnimationFrame(decayRaf)
    dockResizeObserver?.disconnect()
    window.removeEventListener('resize', onResize)
})
</script>

<template>
    <div class="dock-tray">
        <nav
            ref="dockRef"
            :class="['quiet-dock', { 'is-dragging': isDragging }]"
            aria-label="Navigasi utama"
            @pointerdown="onPointerDown"
            @pointermove="onPointerMove"
            @pointerup="onPointerUp"
            @pointercancel="onPointerUp"
        >
            <span
                class="quiet-dock-pill"
                aria-hidden="true"
                :style="{
                    transform: `translate3d(${dockPill.x}px, 0, 0) scale(${dockPill.sx}, ${dockPill.sy})`,
                    width: `${dockPill.w}px`,
                    opacity: dockPill.ready ? 1 : 0,
                }"
            ></span>
            <a
                v-for="(item, index) in publicLinks"
                :key="item.key"
                :href="item.href"
                class="quiet-dock-item"
                :class="{ 'is-active': visualActiveIndex === index }"
                :aria-current="activeIndex === index ? 'page' : undefined"
                draggable="false"
                @click="activate($event, item)"
            >
                <Icon class="dock-icon" :name="item.icon" />
                <span>{{ item.label }}</span>
            </a>
        </nav>
    </div>
</template>
