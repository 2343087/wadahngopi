<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue'
import { api, locate } from './api'
import { useBookmarks } from './composables/useBookmarks'
import Icon from './Icon.vue'
import SkeletonList from './components/SkeletonList.vue'
import StateBlock from './components/StateBlock.vue'

const props = defineProps({ path: String, signedIn: Boolean })
const emit = defineEmits(['loaded'])
const item = ref(null)
let mounted = true
let loadController = null
const error = ref('')
const notice = ref('')
const actionError = ref('')
const busy = ref(false)
const vibe = ref(null)
const votedLevel = ref('')
const coords = ref(null)
const lightbox = ref(null)
const lightboxPhotos = ref([])
const lightboxIndex = ref(0)
let lightboxTrigger = null

const { ensureAccountBookmarks, isSaved, isBookmarkBusy, toggleBookmark } = useBookmarks()

const score = reactive({ wifi_rating: 3, outlet_rating: 3, comfort_rating: 3, comment: '' })
const fields = { wifi_rating: 'WiFi', outlet_rating: 'Stopkontak', comfort_rating: 'Kenyamanan' }
const levels = ['sepi', 'lumayan', 'rame', 'penuh']

const cafe = computed(() => item.value?.kind === 'cafe')
const article = computed(() => Boolean(item.value && !item.value.kind))
const endpoint = computed(() => '/cafes/' + item.value?.id)
const saved = computed(() => Boolean(item.value?.kind && isSaved(item.value.kind, item.value.id)))
const bookmarkBusy = computed(() => Boolean(item.value?.kind && isBookmarkBusy(item.value.kind, item.value.id)))
const activePhoto = computed(() => lightboxPhotos.value[lightboxIndex.value] || { src: '', caption: '' })

function isIllustration(src) {
    return typeof src === 'string' && src.includes('images.unsplash.com')
}

const menuHasIllustration = computed(() =>
    Boolean(item.value?.menu?.some(photo => isIllustration(photo?.image))),
)

const galleryPhotos = computed(() =>
    (item.value?.gallery || []).map((image, idx) => ({
        src: image,
        caption: `${item.value?.name || ''} · Foto ${idx + 1}`,
    })),
)

const menuPhotos = computed(() =>
    (item.value?.menu || []).map((photo, idx) => ({
        src: photo.image,
        caption: photo.tag || `Menu ${item.value?.name || ''} ${idx + 1}`,
    })),
)

const jumpSections = computed(() => {
    if (!item.value || article.value) return []
    const list = []
    if (item.value.description_html) list.push({ id: 'tentang', label: 'Tentang' })
    if (item.value.gallery?.length) list.push({ id: 'foto', label: 'Foto' })
    if (item.value.menu?.length) list.push({ id: 'menu', label: 'Menu' })
    if (cafe.value) {
        list.push({ id: 'suasana', label: 'Suasana' })
        list.push({ id: 'ulasan', label: 'Check-in & ulasan' })
    }
    return list
})

const formattedHours = computed(() => {
    const h = item.value?.hours
    if (!h) return ''
    if (h.label) return h.label
    if (h.open && h.close) return `${h.open.slice(0, 5)}–${h.close.slice(0, 5)}`
    return ''
})

const formattedDate = computed(() => {
    if (!item.value?.date) return ''
    try {
        return new Date(item.value.date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        })
    } catch {
        return item.value.date
    }
})

function vibeShare(level) {
    const total = vibe.value?.total || 0
    if (!total) return 0
    return Math.round(((vibe.value?.distribution?.[level] || 0) / total) * 100)
}

function openPhoto(photos, index = 0, event = null) {
    lightboxPhotos.value = photos
    lightboxIndex.value = index
    lightboxTrigger = event?.currentTarget || document.activeElement
    lightbox.value?.showModal()
}

function stepPhoto(delta) {
    if (lightboxPhotos.value.length < 2) return
    const total = lightboxPhotos.value.length
    lightboxIndex.value = (lightboxIndex.value + delta + total) % total
}

function restoreLightboxFocus() {
    lightboxTrigger?.focus?.()
    lightboxTrigger = null
}

function onLightboxKeydown(event) {
    if (!lightbox.value?.open) return
    if (event.key === 'ArrowRight') {
        event.preventDefault()
        stepPhoto(1)
    } else if (event.key === 'ArrowLeft') {
        event.preventDefault()
        stepPhoto(-1)
    }
}

async function load() {
    loadController?.abort()
    const controller = new AbortController()
    loadController = controller
    error.value = ''
    try {
        const result = await api(props.path, { signal: controller.signal })
        if (!mounted || controller.signal.aborted) return
        item.value = result
        if (result?.name) document.title = `${result.name} | WadahNgopi`
        if (!result.kind) return
        await ensureAccountBookmarks(props.signedIn)
        if (!mounted || controller.signal.aborted) return
        if (cafe.value) vibe.value = await api(endpoint.value + '/vibe', { signal: controller.signal })
    } catch (e) {
        if (!mounted || controller.signal.aborted) return
        error.value = e.message
    } finally {
        if (mounted && !controller.signal.aborted) emit('loaded')
    }
}

async function act(callback) {
    busy.value = true
    actionError.value = ''
    notice.value = ''
    try {
        await callback()
    } catch (e) {
        actionError.value = e.message
    } finally {
        busy.value = false
    }
}

function bookmark() {
    return act(async () => {
        const result = await toggleBookmark(item.value.kind, item.value.id, props.signedIn)
        notice.value = result.saved ? 'Tempat ditambahkan ke daftar simpan.' : 'Tempat dihapus dari daftar simpan.'
    })
}

function position() {
    return act(async () => {
        const { lat, lng } = await locate()
        coords.value = { user_lat: lat, user_lng: lng }
        notice.value = 'Lokasi siap dipakai untuk verifikasi check-in atau ulasan.'
    })
}

function submit(action, fields = {}) {
    return act(async () => {
        const result = await api(endpoint.value + '/' + action, { method: 'POST', body: { ...coords.value, ...fields } })
        if (action === 'vibe') {
            vibe.value = result.aggregate
            votedLevel.value = fields.level
        }
        if (action === 'check-in') item.value.checked_in = true
        if (action === 'wfc-score') {
            item.value.has_rated = true
            item.value.rating = result.new_aggregate.score
            item.value.rating_count = result.new_aggregate.count
        }
        notice.value = action === 'wfc-score' ? 'Ulasan kerja Anda tersimpan.' : result.message
    })
}

onMounted(() => {
    window.addEventListener('keydown', onLightboxKeydown)
    load()
})

onBeforeUnmount(() => {
    mounted = false
    loadController?.abort()
    window.removeEventListener('keydown', onLightboxKeydown)
})
</script>

<template>
    <section class="page-section detail-page">
        <StateBlock
            v-if="error"
            variant="error"
            title="Halaman detail tidak dapat dimuat"
            :description="error"
            action-label="Coba lagi"
            secondary-label="Kembali ke jelajahi"
            secondary-href="/explore"
            @action="load"
        />

        <SkeletonList v-else-if="!item" variant="detail" />

        <template v-else>
            <nav class="detail-back-nav" aria-label="Navigasi kembali">
                <a class="text-link" :href="article ? '/information' : cafe ? '/explore' : '/roastery'">
                    <Icon name="arrowLeft" />
                    {{ article ? 'Kembali ke Kabar kopi' : cafe ? 'Kembali ke Jelajahi kedai' : 'Kembali ke Daftar roastery' }}
                </a>
            </nav>

            <header class="detail-hero-header">
                <h1>{{ item.name }}</h1>
                <p v-if="item.address || item.summary" class="detail-address">{{ item.address || item.summary }}</p>

                <div v-if="article" class="status-line">
                    <span v-if="item.category" class="status-pill is-closed is-neutral">{{ item.category }}</span>
                    <span v-if="formattedDate" class="helper-text">{{ formattedDate }}</span>
                </div>

                <div v-else-if="item.hours || item.open !== undefined" class="status-line">
                    <span class="status-pill" :class="{ 'is-open': item.open, 'is-closed': !item.open }">
                        {{ item.open ? 'Sedang buka' : 'Tutup' }}
                    </span>
                    <span v-if="formattedHours" class="helper-text tabular">{{ formattedHours }}</span>
                    <span v-if="item.city" class="helper-text">· {{ item.city }}</span>
                </div>

                <div v-if="!article" class="action-row">
                    <button type="button" class="button" :disabled="busy || bookmarkBusy" :aria-pressed="saved" @click="bookmark">
                        <Icon name="saved" /> {{ saved ? 'Tersimpan' : 'Simpan tempat' }}
                    </button>
                    <a v-if="item.maps" class="button secondary" :href="item.maps" target="_blank" rel="noopener">
                        <Icon name="pin" /> Buka peta
                    </a>
                    <a
                        v-if="item.phone"
                        class="button secondary"
                        :href="'https://wa.me/' + (item.phone.startsWith('0') ? '62' + item.phone.slice(1) : item.phone)"
                        target="_blank"
                        rel="noopener"
                    >
                        <Icon name="external" /> WhatsApp
                    </a>
                </div>
            </header>

            <p v-if="notice" class="notice-banner" role="status">{{ notice }}</p>
            <p v-if="actionError" class="alert-banner" role="alert">{{ actionError }}</p>

            <figure v-if="item.image" class="detail-photo">
                <img
                    class="detail-cover"
                    :src="item.image"
                    :alt="item.image_illustration ? 'Ilustrasi kedai ' + item.name : item.name"
                    width="1000"
                    height="562"
                    fetchpriority="high"
                    @error="$event.currentTarget.hidden = true"
                >
            </figure>

            <!-- Article layout -->
            <template v-if="article">
                <div v-if="item.content_html" class="rich-text rich-text--article" v-html="item.content_html"></div>
                <p v-if="item.source">
                    <a class="text-link" :href="item.source" target="_blank" rel="noopener">
                        Baca di {{ item.source_name || 'sumber asli' }} <Icon name="external" />
                    </a>
                </p>
            </template>

            <!-- Venue layout -->
            <template v-else>
                <nav v-if="jumpSections.length >= 2" class="detail-jump-nav" aria-label="Bagian halaman">
                    <a v-for="sec in jumpSections" :key="sec.id" class="detail-jump-link" :href="'#' + sec.id">
                        {{ sec.label }}
                    </a>
                </nav>

                <div class="detail-layout">
                    <div class="detail-main">
                        <section v-if="item.description_html" id="tentang" class="detail-section">
                            <h2>Tentang tempat ini</h2>
                            <div class="rich-text" v-html="item.description_html"></div>
                        </section>

                        <section v-if="item.gallery?.length" id="foto" class="detail-section">
                            <h2>Foto tempat</h2>
                            <div class="gallery">
                                <button
                                    v-for="(photo, index) in galleryPhotos"
                                    :key="index"
                                    type="button"
                                    class="gallery-thumb plain-button"
                                    :aria-label="'Perbesar foto ' + (index + 1) + ' ' + item.name"
                                    @click="openPhoto(galleryPhotos, index, $event)"
                                >
                                    <img :src="photo.src" :alt="'Foto ' + (index + 1) + ' ' + item.name" width="360" height="240" loading="lazy">
                                </button>
                            </div>
                        </section>

                        <section v-if="item.menu?.length" id="menu" class="detail-section">
                            <h2>Menu</h2>
                            <p v-if="menuHasIllustration" class="helper-text">
                                Foto di bawah adalah foto ilustrasi (Unsplash), bukan daftar menu resmi kedai.
                            </p>
                            <div class="gallery">
                                <button
                                    v-for="(photo, index) in menuPhotos"
                                    :key="index"
                                    type="button"
                                    class="gallery-thumb plain-button"
                                    :aria-label="'Perbesar menu ' + (item.menu[index]?.tag || (index + 1))"
                                    @click="openPhoto(menuPhotos, index, $event)"
                                >
                                    <img :src="photo.src" :alt="item.menu[index]?.tag || 'Menu ' + (index + 1)" width="360" height="240" loading="lazy">
                                </button>
                            </div>
                        </section>

                        <template v-if="cafe">
                            <section id="suasana" class="detail-section">
                                <h2>Suasana saat ini</h2>
                            <p v-if="vibe?.dominant">
                                Dominan <strong style="text-transform: capitalize">{{ vibe.dominant }}</strong> ·
                                {{ vibe.total }} laporan dalam 4 jam terakhir
                                <span v-if="vibe.last_updated" class="helper-text"> ({{ vibe.last_updated }})</span>.
                            </p>
                            <p v-else>Belum ada laporan suasana dalam 4 jam terakhir.</p>
                            <p class="helper-text">Bantu pengunjung lain mengetahui kondisi keramaian. Bisa dikirim 1 kali tiap 4 jam.</p>

                            <div class="vibe-grid">
                                <button
                                    v-for="level in levels"
                                    :key="level"
                                    type="button"
                                    class="secondary vibe-option"
                                    :aria-pressed="votedLevel === level"
                                    :disabled="busy"
                                    @click="submit('vibe', { level })"
                                >
                                    <span>{{ level }}</span>
                                    <span v-if="vibe?.total" class="tabular">{{ vibeShare(level) }}%</span>
                                </button>
                            </div>
                        </section>

                        <section id="ulasan" class="detail-section">
                            <h2>Check-in dan ulasan kerja</h2>
                            <p v-if="item.rating_count">
                                Rata-rata kenyamanan kerja <strong class="tabular">{{ item.rating }}</strong> dari 5
                                ({{ item.rating_count }} ulasan).
                            </p>
                            <p v-else>Belum ada ulasan kenyamanan kerja untuk kedai ini.</p>

                            <div v-if="!signedIn" class="hint-box hint-box--accent">
                                <p>Masuk ke akun Anda untuk mencatat check-in dan menulis ulasan kerja.</p>
                                <a class="text-link" href="/admin/login">Masuk ke WadahNgopi <Icon name="arrow" /></a>
                            </div>

                            <template v-else>
                                <p class="helper-text">Opsional: bagikan lokasi browser untuk verifikasi check-in di area kedai.</p>
                                <div class="action-row">
                                    <button type="button" class="secondary" :disabled="busy" @click="position">
                                        <Icon name="pin" /> {{ coords ? 'Perbarui lokasi' : 'Verifikasi lokasi GPS' }}
                                    </button>
                                    <button
                                        type="button"
                                        :class="item.checked_in ? 'secondary' : 'button'"
                                        :disabled="busy || item.checked_in"
                                        @click="submit('check-in')"
                                    >
                                        <Icon name="check" /> {{ item.checked_in ? 'Sudah check-in hari ini' : 'Check-in di sini' }}
                                    </button>
                                </div>

                                <form v-if="!item.has_rated" class="stack-form info-panel" @submit.prevent="submit('wfc-score', score)">
                                    <h3>Tulis ulasan kerja</h3>
                                    <p class="helper-text">Pilih nilai 1 (kurang) hingga 5 (sangat nyaman). Ulasan hanya dapat dikirim satu kali.</p>

                                    <div v-for="(label, key) in fields" :key="key" class="field-block">
                                        <span>{{ label }}</span>
                                        <div class="score-selector" role="group" :aria-label="label">
                                            <button
                                                v-for="i in 5"
                                                :key="i"
                                                type="button"
                                                class="secondary score-pill tabular"
                                                :aria-pressed="score[key] === i"
                                                @click="score[key] = i"
                                            >
                                                {{ i }}
                                            </button>
                                        </div>
                                    </div>

                                    <label for="wfc-comment">
                                        Catatan pengalaman (opsional)
                                        <textarea
                                            id="wfc-comment"
                                            v-model.trim="score.comment"
                                            rows="3"
                                            maxlength="500"
                                            placeholder="Ceritakan kestabilan WiFi, ketersediaan colokan, atau kenyamanan meja kerja"
                                        ></textarea>
                                    </label>
                                    <button type="submit" :disabled="busy">Simpan ulasan</button>
                                </form>
                            </template>

                            <template v-if="item.reviews?.length">
                                <h3>Ulasan pengunjung</h3>
                                <ul class="review-list">
                                    <li v-for="(review, index) in item.reviews" :key="index" class="review-item">
                                        <strong>{{ review.name || 'Pengunjung' }}</strong>
                                        <p>{{ review.comment }}</p>
                                    </li>
                                </ul>
                            </template>
                        </section>
                    </template>
                </div>

                <aside class="detail-aside">
                    <div class="info-panel">
                        <h2>Informasi praktis</h2>
                        <p v-if="formattedHours">
                            <strong>Jam buka hari ini:</strong> <span class="tabular">{{ formattedHours }}</span>
                        </p>
                        <p v-else class="helper-text">Jadwal buka belum dilengkapi pemilik tempat.</p>
                        <p v-if="item.city"><strong>Kota:</strong> {{ item.city }}</p>
                        <p v-if="item.image_illustration" class="helper-text">Foto sampul menggunakan gambar ilustrasi dari Unsplash.</p>
                    </div>

                    <div v-if="item.facilities?.length" class="info-panel">
                        <h2>Fasilitas tersedia</h2>
                        <ul class="chip-list">
                            <li v-for="(name, index) in item.facilities" :key="index" class="chip-item">{{ name }}</li>
                        </ul>
                    </div>

                    <div v-if="item.links?.length" class="info-panel">
                        <h2>Tautan resmi</h2>
                        <ul class="chip-list">
                            <li v-for="(link, index) in item.links" :key="index">
                                <a class="text-link" :href="link.url" target="_blank" rel="noopener">
                                    {{ link.platform }} <Icon name="external" />
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside>
            </div>
            </template>
        </template>
    </section>

    <dialog ref="lightbox" class="lightbox-dialog" aria-label="Pratinjau foto" @close="restoreLightboxFocus">
        <img v-if="activePhoto.src" :src="activePhoto.src" :alt="activePhoto.caption || 'Foto tempat'" class="lightbox-img">
        <p v-if="activePhoto.caption || lightboxPhotos.length > 1" class="lightbox-caption">
            <span>{{ activePhoto.caption }}</span>
            <span v-if="lightboxPhotos.length > 1" class="tabular">Foto {{ lightboxIndex + 1 }} dari {{ lightboxPhotos.length }}</span>
        </p>
        <div class="lightbox-controls">
            <div v-if="lightboxPhotos.length > 1" class="action-row">
                <button type="button" class="secondary" aria-label="Foto sebelumnya" @click="stepPhoto(-1)">
                    <Icon name="arrowLeft" /> Sebelumnya
                </button>
                <button type="button" class="secondary" aria-label="Foto berikutnya" @click="stepPhoto(1)">
                    Berikutnya <Icon name="arrow" />
                </button>
            </div>
            <form method="dialog"><button type="submit" class="secondary">Tutup foto</button></form>
        </div>
    </dialog>
</template>
