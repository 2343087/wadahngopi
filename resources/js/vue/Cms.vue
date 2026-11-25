<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'
import { api, locate, query } from './api'
import Icon from './Icon.vue'
import SkeletonList from './components/SkeletonList.vue'
import StateBlock from './components/StateBlock.vue'

const resources = ref([])
const current = ref(null)
const listing = ref(null)
const search = ref('')
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
const confirmDelete = ref(null)
const confirmDiscard = ref(null)
const menuToggleBtn = ref(null)
const drawerCloseBtn = ref(null)
const drawerPanel = ref(null)
const drawerOpen = ref(false)
const accountInfo = ref({ account: '', role: '', canPublish: false })
const csrf = document.querySelector('meta[name="csrf-token"]').content
let pendingLeaveAction = null
let discardTrigger = null
let deleteTrigger = null
let reqSeq = 0
let activeController = null

const statusLabels = { draft: 'Draf', review: 'Perlu ditinjau', published: 'Tayang' }
const resourceIcons = {
    cafes: 'store',
    roasteries: 'bean',
    information: 'news',
    cities: 'pin',
    facilities: 'facility',
    users: 'users',
}

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
        ['address', 'description', 'content', 'summary', 'title'].includes(field.name)
}

function statusClass(val) {
    if (val === 'published') return 'is-open'
    if (val === 'review') return 'is-warning'
    return 'is-closed'
}

function mediaUrl(path) {
    if (!path) return ''
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/storage/')) return path
    return '/storage/' + path.replace(/^\/+/, '')
}

function fieldHint(field) {
    if (field.name === 'slug') return 'Gunakan huruf kecil, angka, dan tanda hubung (contoh: kopi-samarinda).'
    if (field.name === 'status' && !accountInfo.value.canPublish) {
        return 'Perubahan pada tempat yang sudah tayang akan masuk status Perlu ditinjau sebelum ditayangkan admin.'
    }
    if (field.name === 'latitude' || field.name === 'longitude') {
        return 'Isi keduanya agar tempat tampil pada pencarian Terdekat, atau gunakan tombol lokasi browser.'
    }
    if (field.name === 'content') return 'Mendukung tag HTML dasar seperti <p>, <strong>, dan <ul>.'
    if (field.name === 'password') return form.value?.id ? 'Kosongkan jika tidak ingin mengubah kata sandi.' : 'Minimal 8 karakter.'
    if (field.type === 'file') return 'Format JPG, PNG, WebP, atau GIF (maks. 5 MB).'
    if (field.type === 'gallery') return 'Maksimal 10 foto (JPG, PNG, WebP, GIF, maks. 5 MB/foto). Centang foto yang ingin dipertahankan.'
    if (field.name === 'icon') return 'Opsional. Gunakan nama ikon huruf, angka, atau tanda hubung (contoh: wifi).'
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

async function init() {
    await run(async () => {
        const homeData = await api('/admin')
        resources.value = homeData.resources
        accountInfo.value = {
            account: homeData.account || '',
            role: homeData.role || '',
            canPublish: Boolean(homeData.can_publish),
        }
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
    const data = await api(query(endpoint.value, { search: search.value, page }), { signal: controller.signal })
    if (controller.signal.aborted || seq !== reqSeq) return
    listing.value = data
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

function choose(resource) {
    closeDrawer({ restoreFocus: false })
    if (pending.value || !resource || (resource.key === current.value?.key && !form.value)) return
    requestLeave(() => {
        reqSeq++
        activeController?.abort()
        current.value = resource
        form.value = null
        initialSnapshot.value = ''
        uploads.value = {}
        fieldErrors.value = {}
        clearPreviews()
        listing.value = null
        search.value = ''
        feedback.value = ''
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
        const initial = { status: 'draft', role: 'user', category: 'Berita' }
        return [
            field.name,
            value ?? initial[field.name] ?? (field.type === 'checkbox' ? false : field.type === 'links' || field.type === 'gallery' ? [] : ''),
        ]
    }))
}

function open(record = null) {
    feedback.value = ''
    fieldErrors.value = {}
    uploads.value = {}
    clearPreviews()
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
        feedback.value = 'Koordinat lokasi berhasil diisi dari browser.'
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
        feedback.value = accountInfo.value.canPublish
            ? 'Perubahan data berhasil disimpan.'
            : 'Perubahan disimpan. Editan pada tempat tayang akan ditinjau admin terlebih dahulu.'
        scrollTop()
        await reload()
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
        feedback.value = 'Data berhasil dihapus.'
        scrollTop()
        await reload()
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
                    <span>Menu</span>
                </button>

                <a class="cms-topbar-brand" href="/">
                    <img src="/wadahngopi.png" alt="" width="28" height="28">
                    <span>WadahNgopi CMS</span>
                </a>
            </div>

            <div class="cms-topbar-actions">
                <a class="button secondary" href="/">Lihat situs</a>
                <a class="button secondary" href="/profile/badges">Lencana</a>
                <form action="/logout" method="post">
                    <input type="hidden" name="_token" :value="csrf">
                    <button type="submit" class="secondary">Keluar</button>
                </form>
            </div>
        </header>

        <div class="cms-body">
            <!-- Desktop Sidebar -->
            <aside class="cms-sidebar" aria-label="Menu pengelolaan CMS">
                <nav aria-label="Modul CMS">
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
                                <span>{{ res.label }}</span>
                            </button>
                        </li>
                    </ul>
                </nav>

                <div class="cms-sidebar-footer">
                    <div v-if="accountInfo.account" class="cms-account-meta">
                        <strong>{{ accountInfo.account }}</strong>
                        <span>{{ accountInfo.role || 'Pengelola' }}</span>
                    </div>
                </div>
            </aside>

            <!-- White Workspace Sheet -->
            <main id="content" class="cms-sheet" tabindex="-1">
                <div class="cms-workspace">
                    <p v-if="feedback" class="notice-banner" role="status">{{ feedback }}</p>
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

                    <SkeletonList v-if="pending && !listing" variant="table" :count="5" />

                    <template v-else-if="current && listing">
                        <div class="cms-head">
                            <div>
                                <h1>{{ form ? (form.id ? 'Edit ' : 'Tambah ') + current.label.toLowerCase() : current.label }}</h1>
                                <p v-if="!form">
                                    {{ accountInfo.canPublish
                                        ? 'Anda memiliki akses penuh untuk meninjau dan menayangkan data.'
                                        : 'Kelola data milik Anda. Perubahan pada tempat tayang perlu ditinjau admin.' }}
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

                                            <fieldset v-else-if="field.type === 'hours'" class="edit-group cms-field-full">
                                                <legend>{{ field.label }}</legend>
                                                <small class="field-hint">Kosongkan jika jadwal belum ditentukan, atau aktifkan Buka 24 jam.</small>
                                                <div v-for="(schedule, key) in form[field.name]" :key="key" class="form-row">
                                                    <label>{{ key === 'weekday' ? 'Hari kerja buka' : 'Akhir pekan buka' }}<input v-model="schedule.open" type="time"></label>
                                                    <label>{{ key === 'weekday' ? 'Hari kerja tutup' : 'Akhir pekan tutup' }}<input v-model="schedule.close" type="time"></label>
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
                                                    <span class="field-hint">
                                                        {{ uploadPreviews[field.name]?.[0] ? 'Pratinjau foto baru (belum disimpan)' : 'Foto tersimpan saat ini' }}
                                                    </span>
                                                </div>

                                                <ul v-if="field.type === 'gallery' && form[field.name].length" class="upload-grid">
                                                    <li v-for="(image, index) in form[field.name]" :key="index" class="upload-tile">
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

                                                <div v-if="field.type === 'gallery' && uploadPreviews[field.name]?.length" class="cms-media-preview">
                                                    <img
                                                        v-for="(src, idx) in uploadPreviews[field.name]"
                                                        :key="idx"
                                                        class="cms-thumb"
                                                        :src="src"
                                                        :alt="'Foto baru ' + (idx + 1)"
                                                        width="120"
                                                        height="80"
                                                    >
                                                </div>

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
                                                        @input="clearFieldError(field.name)"
                                                    >
                                                </label>
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
                                    <span v-if="isDirty" class="cms-dirty-badge">Ada perubahan belum disimpan</span>
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
                            <div class="cms-toolbar">
                                <form class="cms-search-form" @submit.prevent="run(() => reload(1), { listOnly: true })">
                                    <label for="cms-search" class="sr-only">Cari {{ current.label.toLowerCase() }}</label>
                                    <input
                                        id="cms-search"
                                        name="search"
                                        v-model.trim="search"
                                        type="search"
                                        maxlength="100"
                                        :placeholder="'Cari ' + current.label.toLowerCase() + '…'"
                                    >
                                    <button type="submit" class="secondary" :disabled="pending || listLoading">
                                        <Icon name="search" /> Cari
                                    </button>
                                    <button v-if="search" type="button" class="plain-button" :disabled="listLoading" @click="resetSearch">
                                        Reset
                                    </button>
                                </form>

                                <span v-if="listing.items.data.length" class="helper-text tabular">
                                    {{ listLoading ? 'Memuat daftar…' : 'Halaman ' + listing.items.current_page + ' · ' + listing.items.data.length + ' entri ditampilkan' }}
                                </span>
                            </div>

                            <StateBlock
                                v-if="!listing.items.data.length"
                                variant="empty"
                                :title="search ? 'Tidak ada data yang cocok' : 'Belum ada data ' + current.label.toLowerCase()"
                                :description="search ? 'Ubah kata kunci pencarian atau tampilkan semua data.' : 'Tambahkan data baru untuk mulai mengelola ' + current.label.toLowerCase() + '.'"
                                :action-label="search ? 'Reset pencarian' : listing.create ? 'Tambah ' + current.label.toLowerCase() : ''"
                                @action="search ? (search = '', run(() => reload())) : open()"
                            />

                            <template v-else>
                                <!-- Desktop Table -->
                                <div class="cms-table-wrap" :class="{ 'is-loading': listLoading }" :aria-busy="listLoading">
                                    <table class="cms-table">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Keterangan</th>
                                                <th>Status</th>
                                                <th class="cms-table-actions">Tindakan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="record in listing.items.data" :key="record.id">
                                                <td><strong>{{ record.name }}</strong></td>
                                                <td class="helper-text">{{ record.detail || '-' }}</td>
                                                <td>
                                                    <span
                                                        v-if="record.status"
                                                        class="status-pill"
                                                        :class="statusClass(record.status)"
                                                    >
                                                        {{ statusLabels[record.status] || record.status }}
                                                    </span>
                                                    <span v-else class="helper-text">Aktif</span>
                                                </td>
                                                <td class="cms-table-actions">
                                                    <button
                                                        class="secondary"
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
                                            <strong>{{ record.name }}</strong>
                                            <span
                                                v-if="record.status"
                                                class="status-pill"
                                                :class="statusClass(record.status)"
                                            >
                                                {{ statusLabels[record.status] || record.status }}
                                            </span>
                                        </div>
                                        <p v-if="record.detail" class="helper-text">{{ record.detail }}</p>
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
                                        Sebelumnya
                                    </button>
                                    <span v-else></span>
                                    <button
                                        v-if="listing.items.next_page_url"
                                        class="secondary"
                                        type="button"
                                        :disabled="pending || listLoading"
                                        @click="run(() => reload(listing.items.current_page + 1), { listOnly: true })"
                                    >
                                        Selanjutnya
                                    </button>
                                </div>
                            </template>
                        </template>
                    </template>
                </div>
            </main>
        </div>

        <!-- Mobile CMS Drawer -->
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
                    <strong id="cms-drawer-title">Menu CMS</strong>
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
                                <span>{{ res.label }}</span>
                            </button>
                        </li>
                    </ul>
                </nav>
                <div class="cms-sidebar-footer">
                    <div v-if="accountInfo.account" class="cms-account-meta">
                        <strong>{{ accountInfo.account }}</strong>
                        <span>{{ accountInfo.role || 'Pengelola' }}</span>
                    </div>
                </div>
            </div>
        </div>

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
