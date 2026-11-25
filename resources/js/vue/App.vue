<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import Dock from './Dock.vue'
import Icon from './Icon.vue'
import VenueCard from './VenueCard.vue'
import Catalog from './Catalog.vue'
import Detail from './Detail.vue'
import Account from './Account.vue'
import Cms from './Cms.vue'
import Tongkrongan from './Tongkrongan.vue'
import SkeletonList from './components/SkeletonList.vue'
import StateBlock from './components/StateBlock.vue'
import { api, query } from './api'
import { useBookmarks } from './composables/useBookmarks'

const props = defineProps({ user: Number, cms: Boolean, errorPage: String })
const { ensureAccountBookmarks } = useBookmarks()
const readPath = () => location.pathname.replace(/\/$/, '') || '/'
const readKey = () => readPath() + location.search
const path = ref(readPath())
const searchQuery = ref(location.search)
const currentKey = ref(readKey())
const homeSearch = ref('')
const offline = ref(typeof navigator !== 'undefined' ? !navigator.onLine : false)
const initialTitle = document.title
const scrollPositions = new Map()
let pendingRestoreY = null

const titles = {
    '/': 'WadahNgopi | Kopi Samarinda',
    '/explore': 'Cari kedai kopi',
    '/roastery': 'Cari roastery',
    '/saved': 'Tempat tersimpan',
    '/information': 'Kabar kopi',
    '/admin': 'Kelola WadahNgopi',
    '/profile/badges': 'Lencana kopi',
}

const navLinks = [
    { href: '/', label: 'Beranda' },
    { href: '/explore', label: 'Jelajahi' },
    { href: '/roastery', label: 'Roastery' },
    { href: '/saved', label: 'Simpan' },
    { href: '/information', label: 'Kabar' },
]

const section = computed(() => path.value.split('/')[1])
const slug = computed(() => path.value.split('/')[2])
const catalog = computed(() => path.value !== '/' && ['/explore', '/roastery', '/saved', '/information'].includes(path.value))
const detail = computed(() => Boolean(slug.value && ['cafes', 'roastery', 'information'].includes(section.value)))
const isCms = computed(() => path.value === '/admin')
const isAuth = computed(() => path.value === '/admin/login' || path.value === '/admin/register')

const content = ref(null)
const data = ref(null)
const error = ref('')
const featuredIndex = ref(0)
const heroImgLoaded = ref(false)
const heroDragX = ref(0)
const isHeroDragging = ref(false)
const isScrolled = ref(false)
const navRef = ref(null)
const navPill = ref({ x: 0, w: 0, ready: false })
const isNavDragging = ref(false)
const loadedHeroUrls = new Set()
const featured = computed(() => data.value?.items?.[featuredIndex.value] ?? data.value?.items?.[0] ?? null)
const shelfItems = computed(() => {
    if (!data.value?.items?.length) return []
    return data.value.items.slice(0, 6)
})
const random = ref(null)
const finding = ref(false)
const randomError = ref('')
const help = ref(null)
let helpTrigger = null
let reveal
let loadingHome = false
let heroTimer = null
let scrollRaf = 0
let navResizeObserver = null
let touchStartX = 0
let touchStartY = 0
let heroPointerId = null
let heroStartX = 0
let heroDragged = false
let navPointerId = null
let navStartX = 0
let navStartPillX = 0
let navDragged = false
let navSuppressClick = false

function updateNavPill() {
    nextTick(() => {
        const nav = navRef.value
        if (!nav) return
        const active = nav.querySelector('a[aria-current="page"]')
        if (!active) {
            navPill.value = { x: 0, w: 0, ready: false }
            return
        }
        navPill.value = {
            x: active.offsetLeft,
            w: active.offsetWidth,
            ready: true,
        }
    })
}

function onHeaderTransitionEnd(event) {
    if (event.target === event.currentTarget && !isNavDragging.value) {
        updateNavPill()
    }
}

function onScroll() {
    if (scrollRaf) return
    scrollRaf = requestAnimationFrame(() => {
        scrollRaf = 0
        const nextScrolled = window.scrollY > 24
        if (nextScrolled !== isScrolled.value) {
            isScrolled.value = nextScrolled
            updateNavPill()
        }
    })
}

function onMainTouchStart(event) {
    const t = event.touches?.[0]
    if (!t) return
    touchStartX = t.clientX
    touchStartY = t.clientY
}

function onMainTouchEnd(event) {
    const t = event.changedTouches?.[0]
    if (!t || props.errorPage) return
    if (event.target?.closest?.('.hero-media-stage, input, textarea, select')) return
    const dx = t.clientX - touchStartX
    const dy = t.clientY - touchStartY
    if (Math.abs(dx) < 65 || Math.abs(dx) < Math.abs(dy) * 1.4) return
    const idx = navLinks.findIndex(link => link.href === path.value)
    if (idx === -1) return
    const nextIdx = dx < 0 ? idx + 1 : idx - 1
    if (nextIdx >= 0 && nextIdx < navLinks.length) {
        navigate(navLinks[nextIdx].href)
    }
}

function onHeroPointerDown(event) {
    if (event.button !== 0 && event.pointerType === 'mouse') return
    if ((data.value?.items?.length ?? 0) <= 1) return
    heroPointerId = event.pointerId
    heroStartX = event.clientX
    heroDragged = false
    isHeroDragging.value = true
    heroDragX.value = 0
}

function onHeroPointerMove(event) {
    if (!isHeroDragging.value || event.pointerId !== heroPointerId) return
    const dx = event.clientX - heroStartX
    if (!heroDragged && Math.abs(dx) > 6) {
        heroDragged = true
        event.currentTarget.setPointerCapture?.(event.pointerId)
    }
    if (heroDragged) heroDragX.value = dx * 0.85
}

function onHeroPointerUp(event) {
    if (!isHeroDragging.value || event.pointerId !== heroPointerId) return
    const dx = event.clientX - heroStartX
    isHeroDragging.value = false
    heroPointerId = null
    heroDragX.value = 0
    const len = data.value?.items?.length ?? 0
    if (len > 1 && Math.abs(dx) >= 45) {
        const nextIdx = dx < 0 ? (featuredIndex.value + 1) % len : (featuredIndex.value - 1 + len) % len
        setHeroSlide(nextIdx)
    }
}

function onNavPointerDown(event) {
    if (event.button !== 0 && event.pointerType === 'mouse') return
    const nav = navRef.value
    if (!nav) return
    navPointerId = event.pointerId
    navStartX = event.clientX
    navStartPillX = navPill.value.x
    navDragged = false
    isNavDragging.value = true
}

function onNavPointerMove(event) {
    if (!isNavDragging.value || event.pointerId !== navPointerId) return
    const nav = navRef.value
    if (!nav) return
    const dx = event.clientX - navStartX
    if (!navDragged && Math.abs(dx) > 6) {
        navDragged = true
        nav.setPointerCapture?.(event.pointerId)
    }
    if (!navDragged) return
    const maxX = Math.max(0, nav.clientWidth - navPill.value.w)
    const clampedX = Math.min(maxX, Math.max(0, navStartPillX + dx))
    navPill.value = { ...navPill.value, x: clampedX, ready: true }
}

function onNavPointerUp(event) {
    if (!isNavDragging.value || event.pointerId !== navPointerId) return
    const nav = navRef.value
    const wasDragged = navDragged
    isNavDragging.value = false
    navPointerId = null
    navDragged = false
    if (!nav || !wasDragged) {
        updateNavPill()
        return
    }
    navSuppressClick = true
    setTimeout(() => { navSuppressClick = false }, 50)
    const center = navPill.value.x + navPill.value.w / 2
    const links = Array.from(nav.querySelectorAll('a[href]'))
    let closest = links[0]
    let bestDist = Infinity
    for (const a of links) {
        const aCenter = a.offsetLeft + a.offsetWidth / 2
        const dist = Math.abs(center - aCenter)
        if (dist < bestDist) {
            bestDist = dist
            closest = a
        }
    }
    if (closest) {
        const href = closest.getAttribute('href')
        if (href && href !== path.value) {
            navigate(href)
        } else {
            updateNavPill()
        }
    } else {
        updateNavPill()
    }
}

function onNavClick(href, event) {
    if (navSuppressClick) {
        event.preventDefault()
        return
    }
    navigate(href, event)
}

function preloadHeroImage(idx) {
    const items = data.value?.items
    if (!items?.length) return
    const nextUrl = items[(idx + 1) % items.length]?.image
    if (!nextUrl || loadedHeroUrls.has(nextUrl)) return
    const img = new Image()
    img.onload = () => loadedHeroUrls.add(nextUrl)
    img.src = nextUrl
}

function setHeroSlide(idx) {
    const items = data.value?.items
    if (!items?.length) return
    const nextIdx = idx % items.length
    const nextUrl = items[nextIdx]?.image
    heroImgLoaded.value = Boolean(nextUrl && loadedHeroUrls.has(nextUrl))
    featuredIndex.value = nextIdx
    preloadHeroImage(nextIdx)
    startHeroRotation()
}

function onHeroImgLoad(url) {
    if (url) loadedHeroUrls.add(url)
    heroImgLoaded.value = true
}

function stopHeroRotation() {
    if (heroTimer) {
        clearInterval(heroTimer)
        heroTimer = null
    }
}

function startHeroRotation() {
    stopHeroRotation()
    if (path.value !== '/' || (data.value?.items?.length ?? 0) <= 1) return
    preloadHeroImage(featuredIndex.value)
    heroTimer = setInterval(() => {
        const len = data.value?.items?.length ?? 0
        if (len <= 1 || path.value !== '/') return
        const nextIdx = (featuredIndex.value + 1) % len
        const nextUrl = data.value.items[nextIdx]?.image
        heroImgLoaded.value = Boolean(nextUrl && loadedHeroUrls.has(nextUrl))
        featuredIndex.value = nextIdx
        preloadHeroImage(nextIdx)
    }, 4500)
}

function isSpaPath(pathname) {
    const normalized = pathname.replace(/\/$/, '') || '/'
    if (normalized.startsWith('/admin') || normalized === '/logout' || normalized === '/profile/badges') return false
    if (['/', '/explore', '/roastery', '/saved', '/information'].includes(normalized)) return true
    const parts = normalized.split('/')
    return Boolean(parts[2] && ['cafes', 'roastery', 'information', 'tongkrongan'].includes(parts[1]))
}

function isNavActive(href) {
    if (href === '/') return path.value === '/'
    if (href === '/explore' && path.value.startsWith('/cafes/')) return true
    return path.value === href || path.value.startsWith(href + '/')
}

function syncLocation({ restoreScroll = false } = {}) {
    const nextPath = readPath()
    const nextSearch = location.search
    const nextKey = nextPath + nextSearch
    if (nextKey === currentKey.value) return
    path.value = nextPath
    searchQuery.value = nextSearch
    currentKey.value = nextKey
    document.title = titles[nextPath] ?? initialTitle
    if (restoreScroll && scrollPositions.has(nextKey)) {
        pendingRestoreY = scrollPositions.get(nextKey)
        nextTick(() => {
            requestAnimationFrame(() => {
                if (pendingRestoreY !== null && document.documentElement.scrollHeight >= window.innerHeight + pendingRestoreY) {
                    window.scrollTo({ top: pendingRestoreY, behavior: 'auto' })
                    pendingRestoreY = null
                }
            })
            content.value?.focus({ preventScroll: true })
        })
    } else {
        pendingRestoreY = null
        window.scrollTo({ top: 0, behavior: 'auto' })
        nextTick(() => content.value?.focus({ preventScroll: true }))
    }
}

function restorePendingScroll() {
    if (pendingRestoreY === null) return
    const targetY = pendingRestoreY
    pendingRestoreY = null
    nextTick(() => {
        requestAnimationFrame(() => window.scrollTo({ top: targetY, behavior: 'auto' }))
    })
}

function onUrlSynced() {
    searchQuery.value = location.search
    currentKey.value = readKey()
}

function navigate(href, event) {
    if (event && (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey)) return
    let url
    try {
        url = new URL(href, location.origin)
    } catch {
        return
    }
    if (url.origin !== location.origin || !isSpaPath(url.pathname)) return
    if (event) event.preventDefault()
    const fromKey = readKey()
    const targetKey = (url.pathname.replace(/\/$/, '') || '/') + url.search
    if (targetKey === fromKey) return
    scrollPositions.set(fromKey, window.scrollY)
    history.pushState(null, '', url.pathname + url.search)
    syncLocation({ restoreScroll: false })
}

function handleMainClick(event) {
    if (props.errorPage || event.defaultPrevented) return
    const anchor = event.target?.closest?.('a[href]')
    if (!anchor) return
    if (anchor.target && anchor.target !== '_self') return
    if (anchor.hasAttribute('download')) return
    const href = anchor.getAttribute('href')
    if (!href || href.startsWith('#')) return
    navigate(href, event)
}

function submitHomeSearch() {
    navigate(query('/explore', { search: homeSearch.value }))
}

function openHelp(event) {
    helpTrigger = event?.currentTarget || document.activeElement
    help.value?.showModal()
}

function restoreHelpFocus() {
    helpTrigger?.focus?.()
    helpTrigger = null
}

function onPopstate() {
    syncLocation({ restoreScroll: true })
}

function onOffline() {
    offline.value = true
}

function onOnline() {
    offline.value = false
}

function startReveals() {
    const elements = document.querySelectorAll('[data-reveal]')
    if (!elements.length) return
    if (!window.IntersectionObserver || !matchMedia('(prefers-reduced-motion: no-preference)').matches) {
        elements.forEach(el => el.classList.add('entered'))
        return
    }
    reveal ??= new IntersectionObserver(entries => {
        for (const entry of entries) {
            if (!entry.isIntersecting) continue
            entry.target.classList.add('entered')
            reveal.unobserve(entry.target)
        }
    }, { rootMargin: '0px 0px -6% 0px', threshold: 0.08 })
    elements.forEach(element => {
        if (!element.classList.contains('entered')) reveal.observe(element)
    })
}

async function home() {
    error.value = ''
    try {
        data.value = await api('/')
        featuredIndex.value = 0
        await nextTick()
        if (path.value === '/') {
            startReveals()
            startHeroRotation()
        }
    } catch (e) {
        error.value = e.message
    }
}

async function roulette() {
    finding.value = true
    randomError.value = ''
    try {
        random.value = (await api('/api/cafe-roulette')).item
        if (!random.value) randomError.value = 'Belum ada kedai yang sedang buka saat ini.'
    } catch (e) {
        randomError.value = e.message
    } finally {
        finding.value = false
    }
}

function formatDate(iso) {
    if (!iso) return ''
    try {
        return new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
    } catch {
        return iso
    }
}

function showHome() {
    nextTick(startReveals)
    if (path.value !== '/' || props.errorPage) {
        stopHeroRotation()
        return
    }
    if (!data.value && !loadingHome) {
        loadingHome = true
        void home().finally(() => { loadingHome = false })
    } else if (data.value?.items?.length > 1) {
        startHeroRotation()
    }
}

watch(path, () => {
    reveal?.disconnect()
    showHome()
    updateNavPill()
}, { flush: 'post' })

onMounted(() => {
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual'
    window.addEventListener('popstate', onPopstate)
    window.addEventListener('online', onOnline)
    window.addEventListener('offline', onOffline)
    window.addEventListener('scroll', onScroll, { passive: true })
    window.addEventListener('resize', updateNavPill, { passive: true })
    onScroll()
    updateNavPill()
    if (typeof ResizeObserver !== 'undefined' && navRef.value) {
        navResizeObserver = new ResizeObserver(() => {
            if (!isNavDragging.value) updateNavPill()
        })
        navResizeObserver.observe(navRef.value)
    }
    if (document.fonts?.ready) {
        document.fonts.ready.then(() => updateNavPill())
    }
    if (props.user) void ensureAccountBookmarks(true)
    showHome()
})

onBeforeUnmount(() => {
    stopHeroRotation()
    navResizeObserver?.disconnect()
    if (scrollRaf) cancelAnimationFrame(scrollRaf)
    window.removeEventListener('popstate', onPopstate)
    window.removeEventListener('online', onOnline)
    window.removeEventListener('offline', onOffline)
    window.removeEventListener('scroll', onScroll)
    window.removeEventListener('resize', updateNavPill)
    reveal?.disconnect()
})
</script>

<template>
    <a class="skip-link" href="#content">Lewati ke konten</a>

    <!-- Dedicated CMS Workspace Shell -->
    <template v-if="isCms">
        <Cms />
    </template>

    <!-- Public & Account Shell -->
    <div v-else class="public-shell">
        <header
            :class="['site-header', { 'is-scrolled': isScrolled }]"
            @transitionend="onHeaderTransitionEnd"
        >
            <div class="site-header-inner">
                <a class="wordmark" href="/" @click="navigate('/', $event)">
                    <img src="/wadahngopi.png" alt="" width="32" height="32">
                    <span>WadahNgopi</span>
                </a>

                <nav
                    ref="navRef"
                    :class="['desktop-nav', { 'is-dragging': isNavDragging }]"
                    aria-label="Navigasi utama"
                    @pointerdown="onNavPointerDown"
                    @pointermove="onNavPointerMove"
                    @pointerup="onNavPointerUp"
                    @pointercancel="onNavPointerUp"
                >
                    <span
                        class="desktop-nav-pill"
                        aria-hidden="true"
                        :style="{
                            transform: `translate3d(${navPill.x}px, 0, 0)`,
                            width: `${navPill.w}px`,
                            opacity: navPill.ready ? 1 : 0,
                        }"
                    ></span>
                    <a
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        :aria-current="isNavActive(link.href) ? 'page' : undefined"
                        draggable="false"
                        @click="onNavClick(link.href, $event)"
                    >
                        {{ link.label }}
                    </a>
                </nav>

                <div class="header-actions">
                    <a
                        class="header-account"
                        :href="cms ? '/admin' : user ? '/profile/badges' : '/admin/login'"
                    >
                        <Icon :name="cms ? 'store' : user ? 'award' : 'user'" />
                        <span>{{ cms ? 'Kelola konten' : user ? 'Lencana' : 'Masuk' }}</span>
                    </a>
                </div>
            </div>
        </header>

        <div v-if="offline" class="offline-banner" role="status">
            <span>Koneksi internet terputus. Halaman yang belum tersimpan mungkin gagal dimuat.</span>
        </div>

        <main
            id="content"
            ref="content"
            class="public-main"
            tabindex="-1"
            @click="handleMainClick"
            @touchstart.passive="onMainTouchStart"
            @touchend.passive="onMainTouchEnd"
        >
            <section v-if="errorPage" class="page-section">
                <StateBlock
                    variant="error"
                    :title="errorPage"
                    description="Periksa kembali alamat halaman atau kembali ke direktori utama."
                    action-label="Kembali ke beranda"
                    action-href="/"
                    icon="explore"
                />
            </section>

            <!-- Beranda -->
            <template v-else-if="path === '/'">
                <section class="coffee-hero" aria-labelledby="home-title">
                    <div class="hero-copy">
                        <h1 id="home-title" data-reveal>Ngopi di Samarinda.</h1>
                        <p class="hero-lead" data-reveal>
                            Direktori kedai kopi dan roastery lokal untuk mencari tempat singgah, kerja sambil ngopi, atau membeli biji kopi segar.
                        </p>
                        <form class="hero-search" action="/explore" method="get" data-reveal @submit.prevent="submitHomeSearch">
                            <label for="home-search">Cari kedai kopi</label>
                            <div class="search-row">
                                <input id="home-search" name="search" v-model.trim="homeSearch" type="search" maxlength="100" placeholder="Nama kedai atau alamat jalan">
                                <button type="submit"><Icon name="search" /> Cari kedai</button>
                            </div>
                        </form>
                        <div class="quick-links" aria-label="Pintasan utama" data-reveal>
                            <a class="text-link" href="/explore?open_now=1">Sedang buka sekarang</a>
                            <a class="text-link" href="/roastery">Daftar roastery</a>
                            <a class="text-link" href="/tongkrongan/buat">Buat voting tongkrongan</a>
                        </div>
                    </div>

                    <figure v-if="featured?.image" class="hero-photo" data-reveal>
                        <div
                            :class="['hero-media-stage', { 'is-dragging': isHeroDragging }]"
                            @pointerdown="onHeroPointerDown"
                            @pointermove="onHeroPointerMove"
                            @pointerup="onHeroPointerUp"
                            @pointercancel="onHeroPointerUp"
                        >
                            <div class="hero-progress" aria-hidden="true">
                                <span :key="featured.id" class="hero-progress-fill"></span>
                            </div>
                            <div
                                v-if="!heroImgLoaded"
                                class="hero-media-skeleton skeleton-media skeleton-pulse"
                                aria-hidden="true"
                            ></div>
                            <Transition name="hero-slide" mode="out-in">
                                <img
                                    :key="featured.id"
                                    :src="featured.image"
                                    :alt="featured.image_illustration ? '' : featured.name"
                                    width="800"
                                    height="640"
                                    fetchpriority="high"
                                    draggable="false"
                                    :style="{
                                        transform: heroDragX ? `translate3d(${heroDragX}px, 0, 0)` : '',
                                        transition: isHeroDragging ? 'none' : 'transform 0.3s cubic-bezier(0.22, 1, 0.36, 1)',
                                    }"
                                    @load="onHeroImgLoad(featured.image)"
                                    @error="heroImgLoaded = true; $event.currentTarget.hidden = true"
                                >
                            </Transition>
                        </div>
                        <figcaption>
                            <a :href="featured.url">{{ featured.name }}</a>
                            <span>{{ featured.image_illustration ? 'Foto ilustrasi · Unsplash' : featured.city }}</span>
                        </figcaption>
                    </figure>
                    <div v-else-if="!data && !error" class="hero-photo skeleton-media skeleton-pulse" aria-hidden="true"></div>
                </section>

                <section class="home-shelf" aria-labelledby="shelf-title">
                    <div class="section-header" data-reveal>
                        <div>
                            <h2 id="shelf-title">Kedai untuk dikunjungi</h2>
                            <p>Pilihan kedai kopi aktif di Samarinda yang siap dikunjungi.</p>
                        </div>
                        <a class="text-link" href="/explore">Lihat semua kedai <Icon name="arrow" /></a>
                    </div>

                    <StateBlock
                        v-if="error"
                        variant="error"
                        title="Daftar kedai belum bisa dimuat"
                        :description="error"
                        action-label="Muat ulang daftar"
                        @action="home"
                    />
                    <SkeletonList v-else-if="!data" variant="grid" :count="6" />
                    <StateBlock
                        v-else-if="!data.items.length"
                        variant="empty"
                        title="Belum ada kedai yang ditayangkan"
                        description="Data kedai kopi akan tampil setelah ditayangkan oleh pengelola."
                    />
                    <div v-else class="venue-grid">
                        <VenueCard
                            v-for="item in shelfItems"
                            :key="item.id"
                            :item="{ ...item, heading: 'h3' }"
                            :signed-in="Boolean(user)"
                            data-reveal
                        />
                    </div>
                </section>

                <section class="coffee-break-band" aria-labelledby="roulette-title">
                    <div class="coffee-break-inner">
                        <div class="coffee-break-copy" data-reveal>
                            <h2 id="roulette-title">Bingung mau ngopi di mana?</h2>
                            <p>Ambil satu rekomendasi acak dari daftar kedai yang benar-benar sedang buka saat ini.</p>
                        </div>
                        <div class="coffee-break-stage" data-reveal>
                            <button class="button" type="button" :disabled="finding" @click="roulette">
                                <Icon name="dice" /> {{ finding ? 'Memilih kedai…' : random ? 'Pilihkan kedai lain' : 'Pilihkan satu kedai' }}
                            </button>
                            <p v-if="randomError" role="alert">{{ randomError }}</p>
                            <div v-else-if="random" class="roulette-card" role="status">
                                <a class="roulette-card-title" :href="random.url">{{ random.name }}</a>
                                <p v-if="random.address || random.city">{{ [random.address, random.city].filter(Boolean).join(' · ') }}</p>
                                <small>
                                    Sedang buka{{ random.hours?.label ? ' · ' + random.hours.label : random.hours?.open ? ' · ' + random.hours.open + '–' + random.hours.close : '' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </section>

                <section v-if="data?.news?.length" aria-labelledby="kabar-title" class="home-shelf">
                    <div class="section-header" data-reveal>
                        <div>
                            <h2 id="kabar-title">Kabar kopi</h2>
                            <p>Berita, acara komunitas, dan catatan dari skena kopi lokal.</p>
                        </div>
                        <a class="text-link" href="/information">Semua kabar <Icon name="arrow" /></a>
                    </div>
                    <div class="editorial-article-grid">
                        <a
                            v-for="article in data.news"
                            :key="article.id"
                            :href="article.url"
                            class="editorial-article-card"
                            data-reveal
                        >
                            <span class="status-pill is-closed is-neutral">{{ article.category }}</span>
                            <strong>{{ article.name }}</strong>
                            <p v-if="article.summary">{{ article.summary }}</p>
                            <small v-if="article.date" class="helper-text">{{ formatDate(article.date) }}</small>
                        </a>
                    </div>
                </section>

                <section aria-labelledby="pathway-title" class="home-shelf">
                    <div class="section-header" data-reveal>
                        <div>
                            <h2 id="pathway-title">Cara lain menjelajah</h2>
                            <p>Pilih jalur sesuai rencana nongkrong atau kebutuhan seduh Anda.</p>
                        </div>
                    </div>
                    <div class="pathway-grid" data-reveal>
                        <article class="pathway-panel">
                            <div>
                                <h3>Voting tongkrongan bareng teman</h3>
                                <p>Pilih 2 sampai 5 kedai kandidat, buat tautan singkat yang aktif selama 24 jam, lalu kumpulkan suara teman tanpa perlu login.</p>
                            </div>
                            <a class="button" href="/tongkrongan/buat">Buat daftar voting</a>
                        </article>
                        <article class="pathway-panel pathway-panel--alt">
                            <div>
                                <h3>Roastery dan biji kopi lokal</h3>
                                <p>Temukan roastery aktif beserta jam operasional, lokasi peta, dan kontak langsung untuk stok biji kopi rumahan.</p>
                            </div>
                            <a class="button secondary" href="/roastery">Lihat daftar roastery</a>
                        </article>
                    </div>
                </section>
            </template>

            <Catalog
                v-else-if="catalog"
                :key="path"
                :path="path"
                :search="searchQuery"
                :title="titles[path]"
                :signed-in="Boolean(user)"
                @url-synced="onUrlSynced"
                @loaded="restorePendingScroll"
            />
            <Detail v-else-if="detail" :key="path" :path="path" :signed-in="Boolean(user)" @loaded="restorePendingScroll" />
            <Account v-else-if="section === 'admin' || path === '/profile/badges'" :key="path" :path="path" :cms="cms" />
            <Tongkrongan v-else-if="section === 'tongkrongan'" :key="path" :path="path" @loaded="restorePendingScroll" />
            <section v-else class="page-section">
                <StateBlock
                    variant="empty"
                    title="Halaman tidak ditemukan"
                    description="Tautan yang Anda buka tidak tersedia di direktori WadahNgopi."
                    action-label="Kembali ke beranda"
                    action-href="/"
                    icon="explore"
                />
            </section>
        </main>

        <footer class="site-footer" @click="handleMainClick">
            <div class="site-footer-inner">
                <div class="footer-brand">
                    <strong>WadahNgopi</strong>
                    <p>Direktori kedai kopi, roastery, dan kabar kopi untuk Samarinda dan sekitarnya.</p>
                </div>
                <div class="footer-links">
                    <a href="/explore">Jelajahi</a>
                    <a href="/roastery">Roastery</a>
                    <a href="/tongkrongan/buat">Voting Tongkrongan</a>
                    <a href="/information">Kabar</a>
                    <a href="https://www.instagram.com/wadahngopi/" rel="noopener">Instagram</a>
                    <a href="https://www.tiktok.com/@wadah.ngopi" rel="noopener">TikTok</a>
                    <a href="https://youtube.com/@wadahngopi" rel="noopener">YouTube</a>
                    <button class="plain-button" type="button" @click="openHelp">Cara pakai</button>
                </div>
                <div class="footer-meta">
                    <span>WadahNgopi · Samarinda</span>
                    <span>Informasi jam buka dan fasilitas mengikuti pembaruan pemilik kedai.</span>
                </div>
            </div>
        </footer>
        <Dock v-if="!isAuth" :current="path" :native="Boolean(errorPage)" @navigate="navigate" />
    </div>

    <dialog ref="help" aria-labelledby="help-title" @close="restoreHelpFocus">
        <h2 id="help-title">Panduan WadahNgopi</h2>
        <ul class="help-list">
            <li><strong>Cari tempat:</strong> buka Jelajahi atau Roastery untuk menyaring kota, fasilitas, tempat terdekat, atau yang sedang buka.</li>
            <li><strong>Simpan daftar:</strong> tekan Simpan tempat di halaman detail agar mudah dibuka lagi di halaman Simpan.</li>
            <li><strong>Nongkrong bareng:</strong> buat tautan voting 2 sampai 5 kedai di Tongkrongan agar teman bisa ikut memilih.</li>
            <li><strong>Check-in dan kelola:</strong> masuk lewat menu Masuk untuk check-in, memberi ulasan kerja, atau mengelola profil kedai di CMS.</li>
        </ul>
        <form method="dialog"><button type="submit">Tutup panduan</button></form>
    </dialog>
</template>
