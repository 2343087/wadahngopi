<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'
import { api, locate, query } from './api'
import Icon from './Icon.vue'
import SkeletonList from './components/SkeletonList.vue'
import StateBlock from './components/StateBlock.vue'

const resources = ref([])
const summary = ref([])
const current = ref(null)
const listing = ref(null)
const search = ref('')
const statusFilter = ref('all')
const form = ref(null)
const initialSnapshot = ref('')
const uploads = ref({})
const uploadPreviews = ref({})
const editable = ref(true)
const removable = ref(false)
const pending = ref(true)
const listLoading = ref(false)
const locating = ref(false)
const error = ref('')
const fieldErrors = ref({})
const feedback = ref('')
const slugTouched = ref(false)
const confirmDelete = ref(null)
const confirmDiscard = ref(null)
const menuToggleBtn = ref(null)
const drawerCloseBtn = ref(null)
const drawerPanel = ref(null)
const drawerOpen = ref(false)
const accountInfo = ref({ account: '', email: '', role: '', roleKey: '', canPublish: false })
const csrf = document.querySelector('meta[name="csrf-token"]').content

let pendingLeaveAction = null
let discardTrigger = null
let deleteTrigger = null
let reqSeq = 0
let activeController = null
let searchDebounce = null
let feedbackTimer = null

const statusLabels = {
    draft: 'Draf',
    review: 'Perlu ditinjau',
    published: 'Tayang',
    active: 'Aktif',
    developer: 'Developer',
    admin: 'Owner Cafe',
    roastery: 'Owner Roastery',
    user: 'Pengunjung',
}

const resourceIcons = {
    cafes: 'store',
    roasteries: 'bean',
    information: 'news',
    cities: 'pin',
    facilities: 'facility',
    users: 'users',
}

const facilityIconPresets = [
    { label: 'WiFi', value: 'bi-wifi' },
    { label: 'AC', value: 'bi-snow' },
    { label: 'Stopkontak', value: 'bi-plug' },
    { label: 'Outdoor', value: 'bi-tree' },
    { label: 'Indoor', value: 'bi-house' },
    { label: 'Mushola', value: 'bi-moon-stars' },
    { label: 'WC', value: 'bi-droplet' },
    { label: 'Parkir', value: 'bi-car-front' },
    { label: 'Live Music', value: 'bi-music-note-beamed' },
    { label: '24 Jam', value: 'bi-clock' },
]

const endpoint = computed(() => '/admin/data/' + current.value?.key)
const uploadKeys = { image_path: 'image_upload', images: 'images_upload', menu_images: 'menu_images_upload' }

const requiredFields = {
    cafes: ['name', 'city_id'],
    roasteries: ['name', 'city_id'],
    information: ['title', 'slug', 'category', 'content'],
    cities: ['name', 'slug'],
    facilities: ['cafe_id', 'name'],
    users: ['name', 'email', 'role'],
}

const placeGroupOrder = [
    {
        title: 'Identitas dan lokasi',
        names: ['name', 'city_id', 'address', 'description', 'latitude', 'longitude', 'google_maps_url'],
    },
    {
        title: 'Jam operasional dan fasilitas',
        names: ['is_24_hours', 'operating_hours', 'has_wifi'],
    },
    {
        title: 'Kontak dan tautan media sosial',
        names: ['whatsapp_number', 'social_links'],
    },
    {
        title: 'Media dan foto tempat',
        names: ['image_path', 'images', 'menu_images'],
    },
    {
        title: 'Status publikasi dan kepemilikan',
        names: ['status', 'owner_id'],
    },
]

const statusTabs = computed(() => {
    if (!listing.value?.supports_status || !listing.value?.counts) return []
    const counts = listing.value.counts
    const tabs = [
        { key: 'all', label: 'Semua', count: counts.all },
        { key: 'published', label: 'Tayang', count: counts.published },
    ]
    if (current.value?.key !== 'information') {
        tabs.push({ key: 'review', label: 'Perlu ditinjau', count: counts.review })
    }
    tabs.push({ key: 'draft', label: 'Draf', count: counts.draft })
    return tabs
})

const tableHeaders = computed(() => {
    const key = current.value?.key
    if (key === 'facilities') return { name: 'Fasilitas', detail: 'Kedai kopi', status: 'Ikon' }
    if (key === 'users') return { name: 'Pengguna', detail: 'Email', status: 'Peran' }
    if (key === 'cities') return { name: 'Kota', detail: 'Ringkasan', status: 'Status' }
    if (key === 'information') return { name: 'Judul kabar', detail: 'Kategori & tanggal', status: 'Status' }
    return { name: 'Nama tempat', detail: 'Kota & alamat', status: 'Status' }
})

const fieldSections = computed(() => {
    const fields = listing.value?.fields || []
    if (!fields.length) return []
    if (!['cafes', 'roasteries'].includes(current.value?.key)) {
        return [{ title: 'Informasi ' + current.value?.label.toLowerCase(), fields }]
    }
    const map = Object.fromEntries(fields.map(f => [f.name, f]))
    const used = new Set()
    const sections = []
    for (const group of placeGroupOrder) {
        const grouped = group.names.map(n => map[n]).filter(Boolean)
        grouped.forEach(f => used.add(f.name))
        if (grouped.length) sections.push({ title: group.title, fields: grouped })
    }
    const rest = fields.filter(f => !used.has(f.name))
    if (rest.length) sections.push({ title: 'Lainnya', fields: rest })
    return sections
})

function isFullWidth(field) {
    return ['textarea', 'hours', 'links', 'gallery', 'file', 'address', 'description', 'content', 'summary'].includes(field.type) ||
        ['address', 'description', 'content', 'summary', 'title', 'icon'].includes(field.name)
}

function statusClass(val) {
    if (val === 'published' || val === 'developer') return 'is-open'
    if (val === 'review' || val === 'admin' || val === 'roastery') return 'is-warning'
    return 'is-closed'
}

function mediaUrl(path) {
    if (!path) return ''
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/storage/')) return path
    return '/storage/' + path.replace(/^\/+/, '')
}

function slugify(text) {
    return String(text || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
        .slice(0, 120)
}

function showFeedback(message) {
    if (feedbackTimer) clearTimeout(feedbackTimer)
    feedback.value = message
    if (message) {
        feedbackTimer = setTimeout(() => {
            feedback.value = ''
            feedbackTimer = null
        }, 6000)
    }
}

function fieldHint(field) {
    if (field.name === 'slug') return 'Terisi otomatis dari nama/judul. Gunakan huruf kecil, angka, dan tanda hubung.'
    if (field.name === 'status' && !accountInfo.value.canPublish) {
        return 'Perubahan pada tempat yang sudah tayang akan masuk status Perlu ditinjau sebelum ditayangkan developer.'
    }
    if (field.name === 'latitude' || field.name === 'longitude') {
        return 'Isi keduanya agar tempat tampil pada pencarian Terdekat, atau gunakan tombol lokasi browser.'
    }
    if (field.name === 'content') return 'Mendukung tag HTML dasar seperti <p>, <strong>, dan <ul>.'
    if (field.name === 'password') return form.value?.id ? 'Kosongkan jika tidak ingin mengubah kata sandi.' : 'Minimal 8 karakter.'
    if (field.type === 'file') return 'Format JPG, PNG, WebP, atau GIF (maks. 5 MB).'
    if (field.type === 'gallery') return 'Maksimal 10 foto (JPG, PNG, WebP, GIF, maks. 5 MB/foto). Centang foto yang ingin dipertahankan.'
    if (field.name === 'icon') return 'Pilih ikon cepat di bawah atau ketik nama kelas ikon (contoh: bi-wifi).'
    return ''
}

function isRequired(field) {
    if (field.name === 'password' && !form.value?.id) return true
    return (requiredFields[current.value?.key] || []).includes(field.name)
}

function serializeForm(val) {
    return val ? JSON.stringify(val) : ''
}

const isDirty = computed(() => {
    if (!form.value || !editable.value) return false
    const hasNewUploads = Object.values(uploads.value).some(arr => Array.isArray(arr) && arr.length > 0)
    return hasNewUploads || serializeForm(form.value) !== initialSnapshot.value
})

function clearFieldError(name) {
    if (!fieldErrors.value[name]) return
    const next = { ...fieldErrors.value }
    delete next[name]
    fieldErrors.value = next
}

function onTextInput(field) {
    clearFieldError(field.name)
    if (field.name === 'slug') {
        slugTouched.value = true
        return
    }
    if (!form.value?.id && !slugTouched.value && (field.name === 'title' || field.name === 'name') && 'slug' in form.value) {
        form.value.slug = slugify(form.value[field.name])
    }
}

function clearPreviews() {
    for (const urls of Object.values(uploadPreviews.value)) {
        urls.forEach(url => URL.revokeObjectURL(url))
    }
    uploadPreviews.value = {}
}

function handleFileChange(fieldName, fileList) {
    const files = [...(fileList || [])]
    uploads.value[fieldName] = files
    clearFieldError(uploadKeys[fieldName] || fieldName)
    if (uploadPreviews.value[fieldName]) {
        uploadPreviews.value[fieldName].forEach(url => URL.revokeObjectURL(url))
    }
    uploadPreviews.value[fieldName] = files.map(f => URL.createObjectURL(f))
}

function removeNewUpload(fieldName, idx) {
    const files = [...(uploads.value[fieldName] || [])]
    const previews = [...(uploadPreviews.value[fieldName] || [])]
    if (previews[idx]) URL.revokeObjectURL(previews[idx])
    files.splice(idx, 1)
    previews.splice(idx, 1)
    uploads.value[fieldName] = files
    uploadPreviews.value[fieldName] = previews
}

function scrollTop() {
    window.scrollTo({ top: 0, behavior: 'auto' })
}

async function run(action, { listOnly = false } = {}) {
    if (listOnly) listLoading.value = true
    else pending.value = true
    error.value = ''
    try {
        await action()
    } catch (e) {
        if (e?.name === 'AbortError') return
        error.value = e.message
        if (e.errors) {
            fieldErrors.value = Object.fromEntries(
                Object.entries(e.errors).map(([k, msgs]) => [k, Array.isArray(msgs) ? msgs[0] : msgs]),
            )
        }
        if (form.value) scrollTop()
    } finally {
        pending.value = false
        listLoading.value = false
    }
}

async function refreshHomeMeta() {
    const homeData = await api('/admin')
    resources.value = homeData.resources
    summary.value = homeData.summary || []
    accountInfo.value = {
        account: homeData.account || '',
        email: homeData.email || '',
        role: homeData.role || '',
        roleKey: homeData.role_key || '',
        canPublish: Boolean(homeData.can_publish),
    }
}

async function init() {
    await run(async () => {
        await refreshHomeMeta()
        if (resources.value.length) {
            current.value = resources.value[0]
            await reload()
        }
    })
}

async function reload(page = 1) {
    const seq = ++reqSeq
    activeController?.abort()
    const controller = new AbortController()
    activeController = controller
    const params = { search: search.value, page }
    if (statusFilter.value && statusFilter.value !== 'all') {
        params.status = statusFilter.value
    }
    const data = await api(query(endpoint.value, params), { signal: controller.signal })
    if (controller.signal.aborted || seq !== reqSeq) return
    listing.value = data
}

function onSearchInput() {
    if (searchDebounce) clearTimeout(searchDebounce)
    searchDebounce = setTimeout(() => {
        searchDebounce = null
        void run(() => reload(1), { listOnly: true })
    }, 320)
}

function setStatusFilter(nextStatus) {
    if (statusFilter.value === nextStatus) return
    statusFilter.value = nextStatus
    void run(() => reload(1), { listOnly: true })
}

function jumpFromSummary(card) {
    if (!card?.resource) return
    const target = resources.value.find(r => r.key === card.resource)
    if (!target) return
    if (target.key === current.value?.key && !form.value) {
        statusFilter.value = card.status || 'all'
        void run(() => reload(1), { listOnly: true })
        return
    }
    choose(target, card.status || 'all')
}

function requestLeave(action, trigger = document.activeElement) {
    if (isDirty.value) {
        pendingLeaveAction = action
        discardTrigger = trigger
        confirmDiscard.value?.showModal()
        return
    }
    action()
}

function confirmLeave() {
    const act = pendingLeaveAction
    pendingLeaveAction = null
    discardTrigger = null
    confirmDiscard.value?.close()
    act?.()
}

function cancelLeave() {
    confirmDiscard.value?.close()
}

function onDiscardClosed() {
    pendingLeaveAction = null
    discardTrigger?.focus?.()
    discardTrigger = null
}

function choose(resource, initialStatus = 'all') {
    closeDrawer({ restoreFocus: false })
    if (pending.value || !resource || (resource.key === current.value?.key && !form.value && statusFilter.value === initialStatus)) return
    requestLeave(() => {
        reqSeq++
        activeController?.abort()
        if (searchDebounce) clearTimeout(searchDebounce)
        current.value = resource
        form.value = null
        initialSnapshot.value = ''
        uploads.value = {}
        fieldErrors.value = {}
        clearPreviews()
        listing.value = null
        search.value = ''
        statusFilter.value = initialStatus
        showFeedback('')
        scrollTop()
        void run(() => reload())
    }, menuToggleBtn.value)
}

function closeForm() {
    requestLeave(() => {
        form.value = null
        initialSnapshot.value = ''
        uploads.value = {}
        fieldErrors.value = {}
        error.value = ''
        clearPreviews()
    })
}

function resetSearch() {
    if (searchDebounce) clearTimeout(searchDebounce)
    search.value = ''
    void run(() => reload(1), { listOnly: true })
}

function defaults(record = {}) {
    return Object.fromEntries(listing.value.fields.map(field => {
        let value = record[field.name]
        if (field.type === 'hours') {
            value = Object.fromEntries(['weekday', 'weekend'].map(key => [key, {
                open: value?.[key]?.open?.slice(0, 5) ?? '',
                close: value?.[key]?.close?.slice(0, 5) ?? '',
            }]))
        }
        if (field.type === 'datetime-local' && value) value = String(value).slice(0, 16)
        if (field.type === 'gallery') {
            value = Object.values(value ?? {}).map(image => typeof image === 'string' ? { image } : { ...image })
        }
        const initial = { status: accountInfo.value.canPublish ? 'published' : 'draft', role: 'user', category: 'Edukasi' }
        return [
            field.name,
            value ?? initial[field.name] ?? (field.type === 'checkbox' ? false : field.type === 'links' || field.type === 'gallery' ? [] : ''),
        ]
    }))
}

function open(record = null) {
    showFeedback('')
    fieldErrors.value = {}
    uploads.value = {}
    clearPreviews()
    slugTouched.value = Boolean(record)
    scrollTop()
    if (!record) {
        form.value = defaults()
        initialSnapshot.value = serializeForm(form.value)
        editable.value = true
        removable.value = false
        return
    }
    return run(async () => {
        const seq = ++reqSeq
        activeController?.abort()
        const controller = new AbortController()
        activeController = controller
        const data = await api(endpoint.value + '/' + record.id, { signal: controller.signal })
        if (controller.signal.aborted || seq !== reqSeq || !listing.value) return
        form.value = { ...defaults(data.record), id: record.id }
        initialSnapshot.value = serializeForm(form.value)
        editable.value = data.update
        removable.value = data.delete
    })
}

async function fillCoords() {
    locating.value = true
    error.value = ''
    try {
        const coords = await locate()
        form.value.latitude = Number(coords.lat.toFixed(6))
        form.value.longitude = Number(coords.lng.toFixed(6))
        showFeedback('Koordinat lokasi berhasil diisi dari browser.')
    } catch (e) {
        error.value = e.message
    } finally {
        locating.value = false
    }
}

function append(data, name, value) {
    if (value instanceof File) data.append(name, value)
    else if (value && typeof value === 'object') Object.entries(value).forEach(([key, child]) => append(data, name + '[' + key + ']', child))
    else data.append(name, typeof value === 'boolean' ? (value ? '1' : '0') : (value ?? ''))
}

function submit() {
    fieldErrors.value = {}
    return run(async () => {
        const body = new FormData()
        if (form.value.id) body.append('_method', 'PATCH')
        for (const { name, type, options } of listing.value.fields) {
            const value = form.value[name]
            if (uploadKeys[name]) {
                const files = uploads.value[name] ?? []
                if (type === 'file' && files[0]) append(body, uploadKeys[name], files[0])
                if (type === 'gallery') {
                    files.forEach(file => append(body, uploadKeys[name] + '[]', file))
                    if (form.value.id) {
                        const indices = value.map((item, index) => item.removed ? null : index).filter(index => index !== null)
                        append(body, name + '_keep', JSON.stringify(indices))
                    }
                }
            } else if (!(name === 'status' && value && !(value in options)) && !(name === 'owner_id' && value === '')) {
                if (type === 'links') append(body, 'replace_links', true)
                append(body, name, value)
            }
        }
        await api(endpoint.value + (form.value.id ? '/' + form.value.id : ''), { method: 'POST', body })
        form.value = null
        initialSnapshot.value = ''
        uploads.value = {}
        clearPreviews()
        showFeedback(accountInfo.value.canPublish
            ? 'Perubahan data berhasil disimpan.'
            : 'Perubahan disimpan. Editan pada tempat tayang akan ditinjau developer terlebih dahulu.')
        scrollTop()
        await Promise.all([reload(listing.value?.items?.current_page || 1), refreshHomeMeta()])
    })
}

function openDeleteModal(event) {
    deleteTrigger = event?.currentTarget || document.activeElement
    confirmDelete.value?.showModal()
}

function restoreDeleteFocus() {
    deleteTrigger?.focus?.()
    deleteTrigger = null
}

function remove() {
    confirmDelete.value?.close()
    return run(async () => {
        await api(endpoint.value + '/' + form.value.id, { method: 'DELETE' })
        form.value = null
        initialSnapshot.value = ''
        uploads.value = {}
        clearPreviews()
        showFeedback('Data berhasil dihapus.')
        scrollTop()
        await Promise.all([reload(1), refreshHomeMeta()])
    })
}

function openDrawer() {
    drawerOpen.value = true
    nextTick(() => drawerCloseBtn.value?.focus())
}

function closeDrawer({ restoreFocus = true } = {}) {
    if (!drawerOpen.value) return
    drawerOpen.value = false
    if (restoreFocus) nextTick(() => menuToggleBtn.value?.focus())
}

function onKeydown(e) {
    if (!drawerOpen.value) return
    if (e.key === 'Escape') {
        e.preventDefault()
        closeDrawer()
        return
    }
    if (e.key === 'Tab' && drawerPanel.value) {
        const focusables = [...drawerPanel.value.querySelectorAll('a[href], button:not(:disabled), [tabindex]:not([tabindex="-1"])')]
        if (!focusables.length) return
        const first = focusables[0]
        const last = focusables[focusables.length - 1]
        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault()
            last.focus()
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault()
            first.focus()
        }
    }
}

function onBeforeUnload(e) {
    if (!isDirty.value) return
    e.preventDefault()
    e.returnValue = ''
}

onMounted(() => {
    window.addEventListener('keydown', onKeydown)
    window.addEventListener('beforeunload', onBeforeUnload)
    init()
})

onBeforeUnmount(() => {
    activeController?.abort()
    if (searchDebounce) clearTimeout(searchDebounce)
    if (feedbackTimer) clearTimeout(feedbackTimer)
    window.removeEventListener('keydown', onKeydown)
    window.removeEventListener('beforeunload', onBeforeUnload)
    clearPreviews()
})
</script>

<template>
    <div class="cms-shell" data-ui>
        <header class="cms-topbar">
            <div class="cms-topbar-left">
                <button
                    ref="menuToggleBtn"
                    type="button"
                    class="secondary cms-menu-toggle"
                    :aria-expanded="drawerOpen"
                    aria-controls="cms-mobile-drawer"
                    @click="openDrawer"
                >
                    <Icon name="menu" />
                    <span class="cms-menu-label">Menu</span>
                </button>

                <a class="cms-topbar-brand" href="/">
                    <img src="/wadahngopi.png" alt="" width="28" height="28">
                    <span>WadahNgopi</span>
                    <span class="cms-brand-tag">CMS</span>
                </a>
            </div>

            <div class="cms-topbar-actions">
                <a class="button secondary cms-topbar-btn" href="/">
                    <Icon name="explore" />
                    <span>Lihat situs</span>
                </a>
                <form action="/logout" method="post">
                    <input type="hidden" name="_token" :value="csrf">
                    <button type="submit" class="secondary cms-topbar-btn">Keluar</button>
                </form>
            </div>
        </header>

        <div class="cms-body">
            <!-- Sticky Desktop Sidebar -->
            <aside class="cms-sidebar" aria-label="Menu pengelolaan CMS">
                <nav aria-label="Modul CMS">
                    <p class="cms-nav-heading">Ruang Kelola</p>
                    <ul class="cms-nav-list">
                        <li v-for="res in resources" :key="res.key">
                            <button
                                type="button"
                                class="cms-nav-item"
                                :class="{ 'is-active': current?.key === res.key }"
                                :aria-current="current?.key === res.key ? 'page' : undefined"
                                :disabled="pending"
                                @click="choose(res)"
                            >
                                <Icon :name="resourceIcons[res.key] || 'store'" />
                                <span class="cms-nav-label">{{ res.label }}</span>
                                <span v-if="res.pending > 0" class="cms-nav-badge is-warning" :title="res.pending + ' menunggu ditinjau'">
                                    {{ res.pending }}
                                </span>
                                <span v-else class="cms-nav-count tabular">{{ res.count }}</span>
                            </button>
                        </li>
                    </ul>
                </nav>

                <div class="cms-sidebar-footer">
                    <div v-if="accountInfo.account" class="cms-account-card">
                        <div class="cms-account-avatar" aria-hidden="true">
                            {{ accountInfo.account.slice(0, 1).toUpperCase() }}
                        </div>
                        <div class="cms-account-meta">
                            <strong>{{ accountInfo.account }}</strong>
                            <span>{{ accountInfo.role || 'Pengelola' }}</span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- White Workspace Sheet -->
            <main id="content" class="cms-sheet" tabindex="-1">
                <div class="cms-workspace">
                    <div v-if="feedback" class="notice-banner cms-feedback" role="status" aria-live="polite">
                        <span>{{ feedback }}</span>
                        <button type="button" class="cms-banner-close" aria-label="Tutup pemberitahuan" @click="showFeedback('')">
                            <Icon name="close" />
                        </button>
                    </div>

                    <StateBlock
                        v-if="error && !resources.length"
                        variant="error"
                        title="Gagal memuat CMS"
                        :description="error"
                        action-label="Coba lagi"
                        @action="init"
                    />
                    <p v-else-if="error" class="alert-banner" role="alert">
                        <span>{{ error }}</span>
                        <button v-if="!form" type="button" class="plain-button" @click="run(() => reload(listing?.items?.current_page || 1), { listOnly: true })">
                            Coba lagi
                        </button>
                    </p>

                    <!-- Role-Aware Summary Strip (Listing View) -->
                    <section v-if="!form && summary.length" class="cms-kpi-strip" aria-label="Ringkasan data">
                        <button
                            v-for="card in summary"
                            :key="card.label"
                            type="button"
                            class="cms-kpi-card"
                            :class="'tone-' + card.tone"
                            @click="jumpFromSummary(card)"
                        >
                            <span class="cms-kpi-label">{{ card.label }}</span>
                            <strong class="cms-kpi-value tabular">{{ card.value }}</strong>
                        </button>
                    </section>

                    <SkeletonList v-if="pending && !listing" variant="table" :count="5" />

                    <template v-else-if="current && listing">
                        <div class="cms-head">
                            <div>
                                <h1>{{ form ? (form.id ? 'Edit ' : 'Tambah ') + current.label.toLowerCase() : current.label }}</h1>
                                <p v-if="!form">
                                    {{ accountInfo.canPublish
                                        ? 'Tinjau, perbarui, dan tayangkan data langsung ke direktori publik.'
                                        : 'Kelola data milik Anda. Perubahan pada tempat tayang akan ditinjau developer terlebih dahulu.' }}
                                </p>
                            </div>

                            <div class="action-row">
                                <button
                                    v-if="form"
                                    class="secondary"
                                    type="button"
                                    :disabled="pending"
                                    @click="closeForm"
                                >
                                    <Icon name="arrowLeft" /> Kembali ke daftar
                                </button>
                                <button
                                    v-else-if="listing.create"
                                    type="button"
                                    :disabled="pending"
                                    @click="open()"
                                >
                                    <Icon name="plus" /> Tambah {{ current.label.toLowerCase() }}
                                </button>
                            </div>
                        </div>

                        <p v-if="!form && current.key === 'facilities' && !listing.create && !accountInfo.canPublish" class="hint-box hint-box--accent">
                            Tambahkan minimal satu kedai kopi di menu Kedai kopi terlebih dahulu sebelum menambah fasilitas.
                        </p>

                        <!-- Resource Editor -->
                        <form v-if="form" class="cms-form" @submit.prevent="submit">
                            <fieldset :disabled="!editable || pending">
                                <legend class="sr-only">Data {{ current.label.toLowerCase() }}</legend>

                                <p v-if="!editable" class="hint-box hint-box--accent">
                                    Data ini hanya dapat dilihat dan tidak dapat diubah oleh akun ini.
                                </p>

                                <section v-for="section in fieldSections" :key="section.title" class="cms-form-section">
                                    <h2 class="cms-form-section-title">{{ section.title }}</h2>
                                    <div class="cms-field-grid">
                                        <template v-for="field in section.fields" :key="field.name">
                                            <label v-if="field.type === 'select'" :class="{ 'cms-field-full': isFullWidth(field) }">
                                                <span>{{ field.label }} <span v-if="isRequired(field)" class="field-required">*</span></span>
                                                <select
                                                    :name="field.name"
                                                    v-model="form[field.name]"
                                                    :required="isRequired(field)"
                                                    :aria-required="isRequired(field) ? 'true' : undefined"
                                                    :aria-invalid="Boolean(fieldErrors[field.name]) || undefined"
                                                    @change="clearFieldError(field.name)"
                                                >
                                                    <option value="">Pilih {{ field.label.toLowerCase() }}</option>
                                                    <option v-if="form[field.name] && !(form[field.name] in field.options)" :value="form[field.name]">
                                                        {{ statusLabels[form[field.name]] || form[field.name] }}
                                                    </option>
                                                    <option v-for="(label, key) in field.options" :key="key" :value="key">{{ label }}</option>
                                                </select>
                                                <small v-if="fieldErrors[field.name]" class="field-error" role="alert">{{ fieldErrors[field.name] }}</small>
                                                <small v-else-if="fieldHint(field)" class="field-hint">{{ fieldHint(field) }}</small>
                                            </label>

                                            <label v-else-if="field.type === 'textarea'" class="cms-field-full">
                                                <span>{{ field.label }} <span v-if="isRequired(field)" class="field-required">*</span></span>
                                                <textarea
                                                    :name="field.name"
                                                    v-model="form[field.name]"
                                                    rows="4"
                                                    :required="isRequired(field)"
                                                    :aria-required="isRequired(field) ? 'true' : undefined"
                                                    :aria-invalid="Boolean(fieldErrors[field.name]) || undefined"
                                                    @input="clearFieldError(field.name)"
                                                ></textarea>
                                                <small v-if="fieldErrors[field.name]" class="field-error" role="alert">{{ fieldErrors[field.name] }}</small>
                                                <small v-else-if="fieldHint(field)" class="field-hint">{{ fieldHint(field) }}</small>
                                            </label>

                                            <label v-else-if="field.type === 'checkbox'" class="check-label cms-field-full">
                                                <input :name="field.name" v-model="form[field.name]" type="checkbox">
                                                <span>{{ field.label }}</span>
                                            </label>

                                            <fieldset
                                                v-else-if="field.type === 'hours'"
                                                class="edit-group cms-field-full"
                                                :class="{ 'is-dimmed': Boolean(form.is_24_hours) }"
                                            >
                                                <legend>{{ field.label }}</legend>
                                                <small class="field-hint">
                                                    {{ form.is_24_hours
                                                        ? 'Buka 24 jam sedang aktif. Matikan centang Buka 24 jam untuk mengatur jam spesifik.'
                                                        : 'Kosongkan jika jadwal belum ditentukan, atau aktifkan Buka 24 jam.' }}
                                                </small>
                                                <div v-for="(schedule, key) in form[field.name]" :key="key" class="form-row">
                                                    <label>{{ key === 'weekday' ? 'Hari kerja buka' : 'Akhir pekan buka' }}<input v-model="schedule.open" type="time" :disabled="Boolean(form.is_24_hours)"></label>
                                                    <label>{{ key === 'weekday' ? 'Hari kerja tutup' : 'Akhir pekan tutup' }}<input v-model="schedule.close" type="time" :disabled="Boolean(form.is_24_hours)"></label>
                                                </div>
                                            </fieldset>

                                            <fieldset v-else-if="field.type === 'links'" class="edit-group cms-field-full">
                                                <legend>{{ field.label }}</legend>
                                                <div v-for="(link, index) in form[field.name]" :key="index" class="form-row">
                                                    <label>Platform<input v-model.trim="link.platform" required maxlength="30" placeholder="Instagram"></label>
                                                    <label>Tautan<input v-model.trim="link.url" type="url" required placeholder="https://..."></label>
                                                    <label class="check-label"><input v-model="link.show" type="checkbox"> Tampilkan</label>
                                                    <button class="secondary" type="button" @click="form[field.name].splice(index, 1)">Hapus</button>
                                                </div>
                                                <button
                                                    class="secondary"
                                                    type="button"
                                                    :disabled="form[field.name].length >= 10"
                                                    @click="form[field.name].push({ platform: '', url: '', show: true })"
                                                >
                                                    <Icon name="plus" /> Tambah tautan
                                                </button>
                                            </fieldset>

                                            <fieldset v-else-if="field.type === 'gallery' || field.type === 'file'" class="edit-group cms-field-full">
                                                <legend>{{ field.label }}</legend>

                                                <div v-if="field.type === 'file' && (uploadPreviews[field.name]?.[0] || form[field.name])" class="cms-media-preview">
                                                    <img
                                                        class="cms-thumb"
                                                        :src="uploadPreviews[field.name]?.[0] || mediaUrl(form[field.name])"
                                                        :alt="field.label"
                                                        width="120"
                                                        height="80"
                                                    >
                                                    <div class="cms-media-meta">
                                                        <span class="field-hint">
                                                            {{ uploadPreviews[field.name]?.[0] ? 'Pratinjau foto baru (belum disimpan)' : 'Foto tersimpan saat ini' }}
                                                        </span>
                                                        <button
                                                            v-if="uploadPreviews[field.name]?.[0]"
                                                            type="button"
                                                            class="plain-button"
                                                            @click="removeNewUpload(field.name, 0)"
                                                        >
                                                            Batalkan pilihan foto baru
                                                        </button>
                                                    </div>
                                                </div>

                                                <ul v-if="field.type === 'gallery' && form[field.name].length" class="upload-grid">
                                                    <li v-for="(image, index) in form[field.name]" :key="index" class="upload-tile" :class="{ 'is-removed': image.removed }">
                                                        <img
                                                            v-if="image.image"
                                                            :src="mediaUrl(image.image)"
                                                            :alt="image.tag || 'Foto ' + (index + 1)"
                                                            width="144"
                                                            height="96"
                                                        >
                                                        <label class="check-label">
                                                            <input type="checkbox" :checked="!image.removed" @change="image.removed = !$event.target.checked">
                                                            <span>{{ image.removed ? 'Akan dihapus' : (image.tag || 'Pertahankan') }}</span>
                                                        </label>
                                                    </li>
                                                </ul>

                                                <ul v-if="field.type === 'gallery' && uploadPreviews[field.name]?.length" class="upload-grid">
                                                    <li v-for="(src, idx) in uploadPreviews[field.name]" :key="idx" class="upload-tile">
                                                        <img :src="src" :alt="'Foto baru ' + (idx + 1)" width="144" height="96">
                                                        <button type="button" class="plain-button" @click="removeNewUpload(field.name, idx)">
                                                            Hapus pilihan
                                                        </button>
                                                    </li>
                                                </ul>

                                                <label>
                                                    <span>Unggah {{ field.label.toLowerCase() }}</span>
                                                    <input
                                                        type="file"
                                                        accept="image/jpeg,image/png,image/webp,image/gif"
                                                        :multiple="field.type === 'gallery'"
                                                        @change="handleFileChange(field.name, $event.target.files)"
                                                    >
                                                </label>
                                                <small v-if="fieldErrors[uploadKeys[field.name] || field.name]" class="field-error" role="alert">
                                                    {{ fieldErrors[uploadKeys[field.name] || field.name] }}
                                                </small>
                                                <small v-else class="field-hint">{{ fieldHint(field) }}</small>
                                            </fieldset>

                                            <div v-else class="field-block" :class="{ 'cms-field-full': isFullWidth(field) }">
                                                <label>
                                                    <span>{{ field.label }} <span v-if="isRequired(field)" class="field-required">*</span></span>
                                                    <input
                                                        :name="field.name"
                                                        v-model="form[field.name]"
                                                        :type="field.type"
                                                        :required="isRequired(field)"
                                                        :aria-required="isRequired(field) ? 'true' : undefined"
                                                        :aria-invalid="Boolean(fieldErrors[field.name]) || undefined"
                                                        :step="field.type === 'number' ? 'any' : undefined"
                                                        :autocomplete="field.type === 'password' ? 'new-password' : undefined"
                                                        @input="onTextInput(field)"
                                                    >
                                                </label>
                                                <div v-if="field.name === 'icon' && current.key === 'facilities'" class="cms-icon-presets">
                                                    <button
                                                        v-for="preset in facilityIconPresets"
                                                        :key="preset.value"
                                                        type="button"
                                                        class="cms-preset-chip"
                                                        :class="{ 'is-selected': form.icon === preset.value }"
                                                        @click="form.icon = preset.value; clearFieldError('icon')"
                                                    >
                                                        {{ preset.label }}
                                                    </button>
                                                </div>
                                                <small v-if="fieldErrors[field.name]" class="field-error" role="alert">{{ fieldErrors[field.name] }}</small>
                                                <small v-else-if="fieldHint(field)" class="field-hint">{{ fieldHint(field) }}</small>
                                                <button
                                                    v-if="field.name === 'longitude'"
                                                    type="button"
                                                    class="secondary"
                                                    :disabled="locating"
                                                    @click="fillCoords"
                                                >
                                                    <Icon name="pin" /> {{ locating ? 'Mengambil lokasi…' : 'Isi koordinat dari browser' }}
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </section>
                            </fieldset>

                            <div v-if="editable" class="cms-form-footer">
                                <div class="action-row">
                                    <button type="submit" :disabled="pending">
                                        {{ pending ? 'Menyimpan…' : 'Simpan perubahan' }}
                                    </button>
                                    <button
                                        class="secondary"
                                        type="button"
                                        :disabled="pending"
                                        @click="closeForm"
                                    >
                                        Batal
                                    </button>
                                    <span v-if="isDirty" class="cms-dirty-badge">Belum disimpan</span>
                                </div>
                                <button
                                    v-if="removable"
                                    class="danger"
                                    type="button"
                                    :disabled="pending"
                                    @click="openDeleteModal($event)"
                                >
                                    <Icon name="trash" /> Hapus data
                                </button>
                            </div>
                        </form>

                        <!-- Resource Listing -->
                        <template v-else>
                            <div class="cms-controls">
                                <div v-if="statusTabs.length" class="cms-status-tabs" role="tablist" aria-label="Saring status">
                                    <button
                                        v-for="tab in statusTabs"
                                        :key="tab.key"
                                        type="button"
                                        role="tab"
                                        class="cms-status-tab"
                                        :class="{ 'is-active': statusFilter === tab.key }"
                                        :aria-selected="statusFilter === tab.key"
                                        @click="setStatusFilter(tab.key)"
                                    >
                                        <span>{{ tab.label }}</span>
                                        <span class="cms-tab-count tabular">{{ tab.count }}</span>
                                    </button>
                                </div>

                                <div class="cms-toolbar">
                                    <form class="cms-search-form" @submit.prevent="run(() => reload(1), { listOnly: true })">
                                        <label for="cms-search" class="sr-only">Cari {{ current.label.toLowerCase() }}</label>
                                        <div class="cms-search-box">
                                            <Icon name="search" class="cms-search-icon" />
                                            <input
                                                id="cms-search"
                                                name="search"
                                                v-model.trim="search"
                                                type="search"
                                                maxlength="100"
                                                :placeholder="current.key === 'facilities' ? 'Cari fasilitas atau nama kedai…' : 'Cari ' + current.label.toLowerCase() + '…'"
                                                @input="onSearchInput"
                                            >
                                            <button
                                                v-if="search"
                                                type="button"
                                                class="cms-search-clear"
                                                aria-label="Hapus pencarian"
                                                @click="resetSearch"
                                            >
                                                <Icon name="close" />
                                            </button>
                                        </div>
                                    </form>

                                    <span v-if="listing.items.data.length" class="helper-text tabular">
                                        {{ listLoading ? 'Memuat daftar…' : 'Hal. ' + listing.items.current_page + ' · ' + listing.items.data.length + ' dari ' + (listing.total ?? listing.items.data.length) + ' data' }}
                                    </span>
                                </div>
                            </div>

                            <StateBlock
                                v-if="!listing.items.data.length"
                                variant="empty"
                                :title="search || statusFilter !== 'all' ? 'Tidak ada data yang cocok' : 'Belum ada data ' + current.label.toLowerCase()"
                                :description="search || statusFilter !== 'all' ? 'Ubah kata kunci atau saringan status untuk melihat data lain.' : 'Tambahkan data baru untuk mulai mengelola ' + current.label.toLowerCase() + '.'"
                                :action-label="search || statusFilter !== 'all' ? 'Tampilkan semua' : listing.create ? 'Tambah ' + current.label.toLowerCase() : ''"
                                @action="search || statusFilter !== 'all' ? (search = '', statusFilter = 'all', run(() => reload(1), { listOnly: true })) : open()"
                            />

                            <template v-else>
                                <!-- Desktop Table -->
                                <div class="cms-table-wrap" :class="{ 'is-loading': listLoading }" :aria-busy="listLoading">
                                    <table class="cms-table">
                                        <thead>
                                            <tr>
                                                <th>{{ tableHeaders.name }}</th>
                                                <th>{{ tableHeaders.detail }}</th>
                                                <th>{{ tableHeaders.status }}</th>
                                                <th class="cms-table-actions">Tindakan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="record in listing.items.data" :key="record.id">
                                                <td>
                                                    <div class="cms-row-identity">
                                                        <img
                                                            v-if="record.image"
                                                            class="cms-row-thumb"
                                                            :src="record.image"
                                                            alt=""
                                                            width="40"
                                                            height="40"
                                                            loading="lazy"
                                                            @error="$event.currentTarget.style.display = 'none'"
                                                        >
                                                        <span v-else class="cms-row-avatar" aria-hidden="true">
                                                            <Icon :name="resourceIcons[current.key] || 'store'" />
                                                        </span>
                                                        <strong>{{ record.name }}</strong>
                                                    </div>
                                                </td>
                                                <td class="helper-text cms-row-detail">{{ record.detail || '-' }}</td>
                                                <td>
                                                    <span
                                                        class="status-pill"
                                                        :class="statusClass(record.status)"
                                                    >
                                                        {{ record.status_label || statusLabels[record.status] || record.status || 'Aktif' }}
                                                    </span>
                                                </td>
                                                <td class="cms-table-actions">
                                                    <button
                                                        class="secondary cms-row-btn"
                                                        type="button"
                                                        :disabled="pending"
                                                        @click="open(record)"
                                                    >
                                                        Kelola <span class="sr-only">{{ record.name }}</span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mobile Compact List -->
                                <ul class="cms-mobile-list" :class="{ 'is-loading': listLoading }" :aria-busy="listLoading">
                                    <li v-for="record in listing.items.data" :key="record.id" class="cms-mobile-card">
                                        <div class="cms-mobile-card-head">
                                            <div class="cms-row-identity">
                                                <img
                                                    v-if="record.image"
                                                    class="cms-row-thumb"
                                                    :src="record.image"
                                                    alt=""
                                                    width="40"
                                                    height="40"
                                                    loading="lazy"
                                                    @error="$event.currentTarget.style.display = 'none'"
                                                >
                                                <span v-else class="cms-row-avatar" aria-hidden="true">
                                                    <Icon :name="resourceIcons[current.key] || 'store'" />
                                                </span>
                                                <strong>{{ record.name }}</strong>
                                            </div>
                                            <span
                                                class="status-pill"
                                                :class="statusClass(record.status)"
                                            >
                                                {{ record.status_label || statusLabels[record.status] || record.status || 'Aktif' }}
                                            </span>
                                        </div>
                                        <p v-if="record.detail" class="helper-text cms-mobile-detail">{{ record.detail }}</p>
                                        <button
                                            class="secondary"
                                            type="button"
                                            :disabled="pending"
                                            @click="open(record)"
                                        >
                                            Kelola <span class="sr-only">{{ record.name }}</span>
                                        </button>
                                    </li>
                                </ul>

                                <div v-if="listing.items.current_page > 1 || listing.items.next_page_url" class="cms-pagination">
                                    <button
                                        v-if="listing.items.current_page > 1"
                                        class="secondary"
                                        type="button"
                                        :disabled="pending || listLoading"
                                        @click="run(() => reload(listing.items.current_page - 1), { listOnly: true })"
                                    >
                                        <Icon name="arrowLeft" /> Sebelumnya
                                    </button>
                                    <span v-else></span>
                                    <span class="helper-text tabular">Halaman {{ listing.items.current_page }}</span>
                                    <button
                                        v-if="listing.items.next_page_url"
                                        class="secondary"
                                        type="button"
                                        :disabled="pending || listLoading"
                                        @click="run(() => reload(listing.items.current_page + 1), { listOnly: true })"
                                    >
                                        Selanjutnya <Icon name="arrow" />
                                    </button>
                                    <span v-else></span>
                                </div>
                            </template>
                        </template>
                    </template>
                </div>
            </main>
        </div>

        <!-- Mobile CMS Drawer -->
        <Transition name="cms-drawer">
            <div v-if="drawerOpen" class="cms-drawer-backdrop" @click.self="closeDrawer">
                <div
                    id="cms-mobile-drawer"
                    ref="drawerPanel"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="cms-drawer-title"
                    class="cms-drawer"
                >
                    <div class="cms-drawer-head">
                        <strong id="cms-drawer-title">Ruang Kelola</strong>
                        <button ref="drawerCloseBtn" type="button" class="secondary" aria-label="Tutup menu" @click="closeDrawer">
                            <Icon name="close" />
                        </button>
                    </div>
                    <nav aria-label="Menu pengelolaan CMS">
                        <ul class="cms-nav-list">
                            <li v-for="res in resources" :key="res.key">
                                <button
                                    type="button"
                                    class="cms-nav-item"
                                    :class="{ 'is-active': current?.key === res.key }"
                                    :disabled="pending"
                                    @click="choose(res)"
                                >
                                    <Icon :name="resourceIcons[res.key] || 'store'" />
                                    <span class="cms-nav-label">{{ res.label }}</span>
                                    <span v-if="res.pending > 0" class="cms-nav-badge is-warning">{{ res.pending }}</span>
                                    <span v-else class="cms-nav-count tabular">{{ res.count }}</span>
                                </button>
                            </li>
                        </ul>
                    </nav>
                    <div class="cms-sidebar-footer">
                        <div v-if="accountInfo.account" class="cms-account-card">
                            <div class="cms-account-avatar" aria-hidden="true">
                                {{ accountInfo.account.slice(0, 1).toUpperCase() }}
                            </div>
                            <div class="cms-account-meta">
                                <strong>{{ accountInfo.account }}</strong>
                                <span>{{ accountInfo.role || 'Pengelola' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <dialog ref="confirmDelete" aria-labelledby="delete-title" @close="restoreDeleteFocus">
            <h2 id="delete-title">Hapus "{{ form?.name || form?.title || 'data ini' }}"?</h2>
            <p>Data akan dihapus dari daftar aktif dan tidak dapat langsung dikembalikan.</p>
            <div class="action-row">
                <form method="dialog"><button type="submit" class="secondary">Batal</button></form>
                <button type="button" class="danger" @click="remove">Ya, hapus data</button>
            </div>
        </dialog>

        <dialog ref="confirmDiscard" aria-labelledby="discard-title" @close="onDiscardClosed">
            <h2 id="discard-title">Perubahan belum disimpan</h2>
            <p>Anda memiliki perubahan pada formulir ini. Tetap lanjutkan tanpa menyimpan?</p>
            <div class="action-row">
                <button type="button" class="secondary" @click="cancelLeave">Lanjut mengedit</button>
                <button type="button" class="danger" @click="confirmLeave">Buang perubahan</button>
            </div>
        </dialog>
    </div>
</template>
