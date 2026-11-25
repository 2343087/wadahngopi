<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import { api, locate, query } from './api'
import { localBookmarks } from './bookmarks'
import { useBookmarks } from './composables/useBookmarks'
import Icon from './Icon.vue'
import VenueCard from './VenueCard.vue'
import SkeletonList from './components/SkeletonList.vue'
import StateBlock from './components/StateBlock.vue'

const props = defineProps({
    path: String,
    search: { type: String, default: '' },
    title: String,
    signedIn: Boolean,
})
const emit = defineEmits(['url-synced', 'loaded'])

function parseFilters(searchString) {
    const params = new URLSearchParams(searchString)
    return {
        search: params.get('search') || '',
        open_now: params.get('open_now') === '1',
        city: params.get('city') || '',
        facility: params.get('facility') || '',
        category: params.get('category') || '',
        sort: params.get('sort') || '',
    }
}

const filters = reactive(parseFilters(props.search || location.search))

const coords = ref(null)
const data = ref(null)
const items = ref([])
const page = ref(1)
const failedPage = ref(1)
const loadingMode = ref('initial') // 'initial' | 'filter' | 'paginate' | null
const locating = ref(false)
const locationError = ref('')
const error = ref('')
const syncError = ref('')
const bookmarkNotice = ref('')
const showAdvanced = ref(Boolean(filters.city || filters.facility || filters.category || filters.sort))
const isStuck = ref(false)
const isScrollingDown = ref(false)
const mobileCompact = computed(() => isStuck.value && isScrollingDown.value && !showAdvanced.value)
const activeQuickCount = computed(() =>
    advancedCount.value + (filters.open_now ? 1 : 0) + (coords.value ? 1 : 0)
)

const { ensureAccountBookmarks, syncGuestBookmarks } = useBookmarks()

const saved = computed(() => props.path === '/saved')
const news = computed(() => props.path === '/information')
const roastery = computed(() => props.path === '/roastery')

const advancedCount = computed(() => {
    let count = 0
    if (filters.city) count++
    if (filters.facility) count++
    if (filters.category) count++
    if (filters.sort) count++
    return count
})

const sortLabels = {
    popular: 'Paling banyak dibaca',
}

const activeFilterChips = computed(() => {
    const chips = []
    if (filters.open_now) chips.push({ key: 'open_now', label: 'Sedang buka' })
    if (coords.value) chips.push({ key: 'coords', label: 'Terdekat aktif' })
    if (filters.city) {
        const cityObj = (data.value?.cities || []).find(c => String(c.id) === String(filters.city))
        chips.push({ key: 'city', label: `Kota: ${cityObj ? cityObj.name : filters.city}` })
    }
    if (filters.facility) chips.push({ key: 'facility', label: `Fasilitas: ${filters.facility}` })
    if (filters.category) chips.push({ key: 'category', label: `Kategori: ${filters.category}` })
    if (filters.sort) chips.push({ key: 'sort', label: `Urut: ${sortLabels[filters.sort] || filters.sort}` })
    return chips
})

const hasActiveFilters = computed(() => Boolean(filters.search) || activeFilterChips.value.length > 0)

let request
let requestSeq = 0
let geoSeq = 0
let mounted = true
let scrollRaf = 0
let lastScrollY = 0
let searchDebounce = null

function onSearchInput() {
    if (searchDebounce) clearTimeout(searchDebounce)
    searchDebounce = setTimeout(() => {
        searchDebounce = null
        void load(1)
    }, 350)
}

function clearSearch() {
    if (searchDebounce) {
        clearTimeout(searchDebounce)
        searchDebounce = null
    }
    filters.search = ''
    void load(1)
}

function onWindowScroll() {
    if (scrollRaf) return
    scrollRaf = requestAnimationFrame(() => {
        scrollRaf = 0
        const y = window.scrollY || 0
        isStuck.value = y > 130
        if (y <= 130) {
            isScrollingDown.value = false
        } else if (y - lastScrollY > 10) {
            isScrollingDown.value = true
        } else if (lastScrollY - y > 8) {
            isScrollingDown.value = false
        }
        lastScrollY = y
    })
}

async function load(next = 1, { syncUrl = true } = {}) {
    if (!mounted) return
    const seq = ++requestSeq
    request?.abort()
    const controller = new AbortController()
    request = controller

    loadingMode.value = next > 1 ? 'paginate' : (items.value.length ? 'filter' : 'initial')
    error.value = ''
    bookmarkNotice.value = ''

    const params = saved.value
        ? { cafes: localBookmarks('cafe'), roasteries: localBookmarks('roastery') }
        : { ...filters, ...(coords.value ? { sort: 'nearest', ...coords.value } : {}), page: next }

    if (!saved.value && next === 1 && syncUrl) {
        history.replaceState(null, '', query(props.path, filters))
        emit('url-synced')
    }

    try {
        const result = await api(query(props.path, params), { signal: controller.signal })
        if (!mounted || controller.signal.aborted || seq !== requestSeq) return
        data.value = result
        const incoming = result.items.data ?? result.items
        if (next === 1) {
            items.value = incoming
        } else {
            const seen = new Set(items.value.map(item => `${item.kind || 'news'}-${item.id}`))
            const fresh = incoming.filter(item => !seen.has(`${item.kind || 'news'}-${item.id}`))
            items.value = [...items.value, ...fresh]
        }
        page.value = next
    } catch (e) {
        if (!mounted || controller.signal.aborted || seq !== requestSeq) return
        failedPage.value = next
        error.value = e.message
        if (next === 1) items.value = []
    } finally {
        if (mounted && !controller.signal.aborted && seq === requestSeq) {
            loadingMode.value = null
            void Promise.resolve().then(() => emit('loaded'))
        }
    }
}

function toggleOpenNow() {
    filters.open_now = !filters.open_now
    void load(1)
}

function clearChip(key) {
    if (key === 'coords') coords.value = null
    else if (key === 'open_now') filters.open_now = false
    else if (key in filters) filters[key] = ''
    void load(1)
}

function resetFilters() {
    geoSeq++
    filters.search = ''
    filters.open_now = false
    filters.city = ''
    filters.facility = ''
    filters.category = ''
    filters.sort = ''
    coords.value = null
    locating.value = false
    locationError.value = ''
    return load(1)
}

async function nearby() {
    if (coords.value) {
        geoSeq++
        coords.value = null
        return load(1)
    }
    const currentGeo = ++geoSeq
    locating.value = true
    locationError.value = ''
    try {
        const pos = await locate()
        if (!mounted || currentGeo !== geoSeq) return
        coords.value = pos
        await load(1)
    } catch (e) {
        if (mounted && currentGeo === geoSeq) locationError.value = e.message
    } finally {
        if (mounted && currentGeo === geoSeq) locating.value = false
    }
}

function onBookmarkToggled({ kind, id, saved: isNowSaved }) {
    if (saved.value && !isNowSaved) {
        items.value = items.value.filter(item => !(item.kind === kind && item.id === id))
        bookmarkNotice.value = 'Tempat dihapus dari daftar simpan.'
    } else {
        bookmarkNotice.value = isNowSaved ? 'Tempat ditambahkan ke daftar simpan.' : 'Tempat dihapus dari daftar simpan.'
    }
}

watch(() => props.search, (nextSearch) => {
    if (saved.value) return
    const parsed = parseFilters(nextSearch || '')
    const changed = Object.keys(parsed).some(k => filters[k] !== parsed[k])
    if (!changed) return
    Object.assign(filters, parsed)
    if (filters.city || filters.facility || filters.category || filters.sort) showAdvanced.value = true
    void load(1, { syncUrl: false })
})

onMounted(async () => {
    lastScrollY = window.scrollY || 0
    window.addEventListener('scroll', onWindowScroll, { passive: true })
    onWindowScroll()
    const hasGuestBookmarks = saved.value && (localBookmarks('cafe').length > 0 || localBookmarks('roastery').length > 0)
    await Promise.all([load(1), ensureAccountBookmarks(props.signedIn)])
    if (mounted && saved.value && props.signedIn && hasGuestBookmarks) {
        try {
            await syncGuestBookmarks(true)
            await load(1)
        } catch {
            syncError.value = 'Simpanan di perangkat ini masih tersedia. Sinkronisasi akun bisa dicoba saat membuka halaman ini lagi.'
        }
    }
})

onBeforeUnmount(() => {
    mounted = false
    if (scrollRaf) cancelAnimationFrame(scrollRaf)
    if (searchDebounce) clearTimeout(searchDebounce)
    window.removeEventListener('scroll', onWindowScroll)
    request?.abort()
})
</script>

<template>
    <section class="page-section">
        <div class="section-header">
            <header class="page-header page-header--functional">
                <h1>{{ title }}</h1>
                <p v-if="saved">Kedai dan roastery yang Anda simpan untuk dikunjungi nanti.</p>
                <p v-else-if="news">Berita, acara komunitas, dan catatan seputar kopi.</p>
                <p v-else>
                    {{ roastery
                        ? 'Temukan roastery lokal di Samarinda beserta jam buka dan informasi kontak.'
                        : 'Saring kedai kopi di Samarinda berdasarkan lokasi terdekat, fasilitas kerja, dan status buka.' }}
                </p>
            </header>

            <a v-if="path === '/explore'" class="text-link" href="/tongkrongan/buat">
                Voting tongkrongan teman <Icon name="arrow" />
            </a>
        </div>

        <div v-if="saved" class="hint-box hint-box--accent">
            <p v-if="signedIn">Simpanan tersinkron dengan akun Anda agar tetap aman saat berganti perangkat.</p>
            <p v-else>
                Simpanan tersimpan di browser perangkat ini (maks. 50 tempat).
                <a href="/admin/login">Masuk</a> untuk menyinkronkannya ke akun.
            </p>
        </div>

        <!-- Search-first & Progressive Disclosure Toolbar -->
        <form
            v-if="!saved"
            :class="['catalog-toolbar', { 'is-stuck': isStuck, 'is-compact': mobileCompact }]"
            @submit.prevent="load(1)"
        >
            <label class="catalog-search-label" for="catalog-search">{{ news ? 'Cari judul kabar' : 'Cari nama tempat atau alamat' }}</label>
            <div class="search-row">
                <input
                    id="catalog-search"
                    name="search"
                    v-model.trim="filters.search"
                    type="search"
                    maxlength="100"
                    :aria-label="news ? 'Cari judul kabar' : 'Cari nama tempat atau alamat'"
                    :placeholder="news ? 'Judul berita atau promo' : 'Nama kedai, roastery, atau jalan'"
                    @input="onSearchInput"
                >
                <button
                    v-if="filters.search"
                    type="button"
                    class="search-clear-btn"
                    aria-label="Hapus pencarian"
                    @click="clearSearch"
                >
                    <Icon name="close" />
                </button>
                <button
                    type="button"
                    class="mobile-filter-toggle"
                    :aria-expanded="showAdvanced"
                    :aria-label="showAdvanced ? 'Tutup filter' : 'Buka filter'"
                    @click="showAdvanced = !showAdvanced"
                >
                    <Icon name="filter" />
                    <span v-if="activeQuickCount" class="mobile-filter-badge">{{ activeQuickCount }}</span>
                </button>
                <button type="submit" class="catalog-search-submit" :disabled="loadingMode === 'initial' || loadingMode === 'filter'">
                    <Icon name="search" /> <span>Cari</span>
                </button>
            </div>

            <div class="catalog-quick-row">
                <button
                    v-if="!news"
                    type="button"
                    class="secondary"
                    :aria-pressed="filters.open_now"
                    @click="toggleOpenNow"
                >
                    <Icon name="clock" /> Sedang buka
                </button>

                <button
                    v-if="!news"
                    type="button"
                    class="secondary"
                    :aria-pressed="Boolean(coords)"
                    :disabled="locating"
                    @click="nearby"
                >
                    <Icon name="pin" />
                    {{ locating ? 'Mencari…' : coords ? 'Terdekat aktif' : 'Terdekat' }}
                </button>

                <button
                    type="button"
                    class="secondary catalog-advanced-btn"
                    :aria-expanded="showAdvanced"
                    aria-controls="catalog-advanced-panel"
                    @click="showAdvanced = !showAdvanced"
                >
                    <Icon name="filter" />
                    Filter{{ advancedCount ? ' (' + advancedCount + ')' : '' }}
                </button>

                <button
                    v-if="hasActiveFilters && !activeFilterChips.length"
                    type="button"
                    class="filter-reset-pill"
                    @click="resetFilters"
                >
                    Reset
                </button>
            </div>

            <div v-if="activeFilterChips.length" class="active-filters" aria-label="Filter aktif">
                <button
                    v-for="chip in activeFilterChips"
                    :key="chip.key"
                    type="button"
                    class="filter-chip"
                    :aria-label="'Hapus filter ' + chip.label"
                    @click="clearChip(chip.key)"
                >
                    <span>{{ chip.label }}</span>
                    <Icon name="close" />
                </button>
                <button type="button" class="filter-reset-pill" @click="resetFilters">
                    Reset
                </button>
            </div>

            <div v-show="showAdvanced" id="catalog-advanced-panel" class="catalog-advanced">
                <label v-if="data?.cities?.length" for="filter-city">
                    Kota
                    <select id="filter-city" name="city" v-model="filters.city" @change="load(1)">
                        <option value="">Semua kota</option>
                        <option v-for="city in data.cities" :key="city.id" :value="city.id">{{ city.name }}</option>
                    </select>
                </label>

                <label v-if="data?.facilities?.length" for="filter-facility">
                    Fasilitas
                    <select id="filter-facility" name="facility" v-model="filters.facility" @change="load(1)">
                        <option value="">Semua fasilitas</option>
                        <option v-for="name in data.facilities" :key="name" :value="name">{{ name }}</option>
                    </select>
                </label>

                <label v-if="data?.categories?.length" for="filter-category">
                    Kategori
                    <select id="filter-category" name="category" v-model="filters.category" @change="load(1)">
                        <option value="">Semua kategori</option>
                        <option v-for="category in data.categories" :key="category" :value="category">{{ category }}</option>
                    </select>
                </label>

                <label v-if="news" for="filter-sort">
                    Urutkan
                    <select id="filter-sort" name="sort" v-model="filters.sort" @change="coords = null; load(1)">
                        <option value="">Terbaru</option>
                        <option value="popular">Paling banyak dibaca</option>
                    </select>
                </label>
            </div>

            <p v-if="locationError" role="alert">{{ locationError }}</p>
        </form>

        <p v-if="syncError" class="hint-box" role="status">{{ syncError }}</p>
        <p v-if="bookmarkNotice" class="notice-banner" role="status">{{ bookmarkNotice }}</p>

        <StateBlock
            v-if="error && !items.length"
            variant="error"
            title="Gagal memuat daftar"
            :description="error"
            action-label="Coba lagi"
            @action="load(failedPage)"
        />

        <SkeletonList v-else-if="loadingMode === 'initial' && !items.length" variant="grid" :count="6" />

        <StateBlock
            v-else-if="!loadingMode && !error && !items.length"
            variant="empty"
            :title="saved
                ? 'Belum ada tempat yang disimpan'
                : hasActiveFilters
                    ? 'Tidak ada hasil yang cocok'
                    : news
                        ? 'Belum ada kabar kopi'
                        : roastery
                            ? 'Belum ada roastery yang ditayangkan'
                            : 'Belum ada kedai yang ditayangkan'"
            :description="saved
                ? 'Buka halaman detail atau tekan ikon simpan pada kartu kedai untuk menyimpannya di sini.'
                : hasActiveFilters
                    ? 'Coba ubah kata kunci pencarian atau reset filter yang sedang aktif.'
                    : news
                        ? 'Artikel, acara komunitas, dan catatan kopi akan tampil di sini setelah diterbitkan.'
                        : roastery
                            ? 'Daftar roastery lokal akan tampil di sini setelah ditayangkan oleh pengelola.'
                            : 'Daftar kedai kopi akan tampil di sini setelah ditayangkan oleh pengelola.'"
            :action-label="saved ? 'Cari kedai kopi' : hasActiveFilters ? 'Reset semua filter' : news ? 'Jelajahi kedai kopi' : ''"
            :action-href="saved || (news && !hasActiveFilters) ? '/explore' : ''"
            :icon="saved ? 'saved' : hasActiveFilters ? 'search' : news ? 'news' : 'store'"
            @action="hasActiveFilters && !saved && resetFilters()"
        />

        <template v-else-if="items.length">
            <div class="catalog-meta-row" aria-live="polite">
                <span class="helper-text tabular">
                    {{ saved ? items.length + ' tempat tersimpan' : 'Menampilkan ' + items.length + (news ? ' artikel' : ' tempat') }}
                </span>
                <span v-if="loadingMode === 'filter'" class="helper-text">Memperbarui daftar…</span>
            </div>

            <div
                class="venue-grid"
                :class="{ 'is-filtering': loadingMode === 'filter' }"
                :aria-busy="Boolean(loadingMode)"
            >
                <VenueCard
                    v-for="item in items"
                    :key="[item.kind || 'news', item.id].join('-')"
                    :item="item"
                    :signed-in="signedIn"
                    @bookmark-toggled="onBookmarkToggled"
                    @bookmark-error="bookmarkNotice = $event"
                />
            </div>

            <p v-if="error" class="alert-banner" role="alert">
                <span>{{ error }}</span>
                <button type="button" class="plain-button" @click="load(failedPage)">Coba lagi</button>
            </p>

            <button
                v-if="data?.items?.next_page_url"
                class="button secondary more-button"
                type="button"
                :disabled="loadingMode !== null"
                @click="load(page + 1)"
            >
                {{ loadingMode === 'paginate' ? 'Memuat lainnya…' : 'Muat lebih banyak' }}
            </button>
        </template>
    </section>
</template>
