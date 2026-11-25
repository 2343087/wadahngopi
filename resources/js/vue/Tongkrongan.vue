<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { api, query } from './api'
import Icon from './Icon.vue'
import SkeletonList from './components/SkeletonList.vue'
import StateBlock from './components/StateBlock.vue'

const props = defineProps({ path: String })
const emit = defineEmits(['loaded'])
let mounted = true
let loadController = null
const creating = computed(() => props.path === '/tongkrongan/buat')
const term = ref('')
const title = ref('')
const choices = ref([])
const suggestions = ref([])
const selected = ref([])
const list = ref(null)
const busy = ref(false)
const error = ref('')
const notice = ref('')
const searched = ref(false)

const visibleSuggestions = computed(() => {
    const picked = new Set(selected.value.map(item => item.id))
    return suggestions.value.filter(item => !picked.has(item.id))
})

function thumbSrc(item) {
    const raw = item?.image || item?.image_path || ''
    if (!raw) return ''
    if (raw.startsWith('http://') || raw.startsWith('https://') || raw.startsWith('/')) return raw
    return '/storage/' + raw.replace(/^\/+/, '')
}

const maxVotes = computed(() => {
    const counts = (list.value?.items || []).map(i => i.votes || 0)
    return Math.max(1, ...counts)
})

async function run(action) {
    busy.value = true
    error.value = ''
    try {
        await action()
    } catch (e) {
        error.value = e.message
    } finally {
        busy.value = false
    }
}

function search() {
    return run(async () => {
        choices.value = await api(query('/tongkrongan/search-cafes', {
            q: term.value,
            selected: selected.value.map(item => item.id),
        }))
        searched.value = true
    })
}

function addChoice(item) {
    if (selected.value.length >= 5 || selected.value.some(c => c.id === item.id)) return
    selected.value.push(item)
    choices.value = choices.value.filter(choice => choice.id !== item.id)
}

function removeChoice(id) {
    selected.value = selected.value.filter(choice => choice.id !== id)
}

function create() {
    return run(async () => {
        const result = await api('/tongkrongan', {
            method: 'POST',
            body: { title: title.value, cafe_ids: selected.value.map(item => item.id) },
        })
        location.assign('/tongkrongan/' + encodeURIComponent(result.uuid))
    })
}

function vote(item) {
    return run(async () => {
        const result = await api(props.path + '/vote/' + item.id, { method: 'POST' })
        item.voted = result.action === 'added'
        item.votes = result.vote_count
    })
}

async function share() {
    error.value = ''
    try {
        await navigator.clipboard.writeText(location.href)
        notice.value = 'Tautan disalin. Bagikan ke teman untuk mulai memilih bersama.'
    } catch {
        notice.value = 'Salin alamat halaman ini dari bilah browser untuk dibagikan: ' + location.href
    }
}

function load() {
    loadController?.abort()
    const controller = new AbortController()
    loadController = controller
    return run(async () => {
        const result = await api(props.path, { signal: controller.signal })
        if (!mounted || controller.signal.aborted) return
        list.value = result
        if (result?.title) document.title = `${result.title} | Tongkrongan WadahNgopi`
        emit('loaded')
    })
}

async function loadSuggestions() {
    try {
        const home = await api('/')
        if (mounted) suggestions.value = (home?.items || []).slice(0, 4)
    } catch {
        if (mounted) suggestions.value = []
    }
}

onMounted(() => {
    if (creating.value) {
        loadSuggestions()
        emit('loaded')
    } else {
        load()
    }
})

onBeforeUnmount(() => {
    mounted = false
    loadController?.abort()
})
</script>

<template>
    <section class="page-section">
        <p v-if="error" class="alert-banner" role="alert">{{ error }}</p>

        <template v-if="creating">
            <header class="page-header page-header--functional">
                <h1>Buat daftar tongkrongan</h1>
                <p>Pilih 2 sampai 5 kedai kopi, lalu bagikan tautannya agar teman bisa ikut memilih. Maksimal 3 daftar per hari.</p>
            </header>

            <div class="tongkrongan-grid">
                <div class="info-panel">
                    <h2>Cari dan pilih kedai</h2>
                    <form class="stack-form" @submit.prevent="search">
                        <label for="tongkrongan-search">Nama kedai</label>
                        <div class="search-row">
                            <input
                                id="tongkrongan-search"
                                name="q"
                                v-model.trim="term"
                                type="search"
                                required
                                minlength="2"
                                maxlength="100"
                                placeholder="Ketik minimal 2 huruf nama kedai"
                            >
                            <button type="submit" class="secondary" :disabled="busy || selected.length === 5">
                                <Icon name="search" /> Cari
                            </button>
                        </div>
                    </form>

                    <p v-if="busy" class="helper-text" role="status">Mencari kedai…</p>
                    <p v-else-if="searched && !choices.length" class="helper-text">Tidak ada kedai yang cocok. Coba kata kunci lain.</p>

                    <ul v-if="choices.length" class="vote-picker-list">
                        <li v-for="item in choices" :key="item.id" class="vote-picker-row">
                            <div class="vote-item-brand">
                                <img
                                    v-if="thumbSrc(item)"
                                    :src="thumbSrc(item)"
                                    :alt="item.name"
                                    class="vote-thumb"
                                    width="96"
                                    height="68"
                                    loading="lazy"
                                    @error="$event.currentTarget.hidden = true"
                                >
                                <div>
                                    <strong>{{ item.name }}</strong>
                                    <p v-if="item.address" class="helper-text">{{ item.address }}</p>
                                </div>
                            </div>
                            <button
                                class="secondary"
                                type="button"
                                :disabled="busy || selected.length === 5"
                                @click="addChoice(item)"
                            >
                                <Icon name="plus" /> Pilih
                            </button>
                        </li>
                    </ul>

                    <template v-else-if="!searched && visibleSuggestions.length">
                        <p class="helper-text">Atau pilih langsung dari beberapa kedai aktif:</p>
                        <ul class="vote-picker-list">
                            <li v-for="item in visibleSuggestions" :key="'sugg-' + item.id" class="vote-picker-row">
                                <div class="vote-item-brand">
                                    <img
                                        v-if="thumbSrc(item)"
                                        :src="thumbSrc(item)"
                                        :alt="item.name"
                                        class="vote-thumb"
                                        width="96"
                                        height="68"
                                        loading="lazy"
                                        @error="$event.currentTarget.hidden = true"
                                    >
                                    <div>
                                        <strong>{{ item.name }}</strong>
                                        <p v-if="item.address" class="helper-text">{{ item.address }}</p>
                                    </div>
                                </div>
                                <button
                                    class="secondary"
                                    type="button"
                                    :disabled="busy || selected.length === 5"
                                    @click="addChoice(item)"
                                >
                                    <Icon name="plus" /> Pilih
                                </button>
                            </li>
                        </ul>
                    </template>
                </div>

                <div class="info-panel">
                    <h2>Daftar pilihan ({{ selected.length }}/5)</h2>
                    <p v-if="!selected.length" class="hint-box hint-box--accent">
                        Belum ada kedai dipilih. Pilih minimal 2 kedai dari hasil pencarian.
                    </p>
                    <ul v-else class="vote-list">
                        <li v-for="item in selected" :key="item.id" class="vote-item is-voted">
                            <div class="vote-item-head">
                                <strong>{{ item.name }}</strong>
                                <button class="plain-button" type="button" @click="removeChoice(item.id)">Hapus</button>
                            </div>
                        </li>
                    </ul>

                    <form class="stack-form" @submit.prevent="create">
                        <label for="tongkrongan-title">
                            Judul daftar voting
                            <input
                                id="tongkrongan-title"
                                name="title"
                                v-model.trim="title"
                                required
                                maxlength="100"
                                placeholder="Contoh: Ngopi Jumat malam"
                            >
                        </label>
                        <small v-if="selected.length < 2" class="field-hint">
                            Pilih minimal {{ 2 - selected.length }} kedai lagi untuk membuat tautan voting.
                        </small>
                        <button type="submit" :disabled="busy || selected.length < 2">Buat tautan voting</button>
                    </form>
                </div>
            </div>
        </template>

        <template v-else>
            <SkeletonList v-if="!list && busy" variant="rows" :count="4" />

            <StateBlock
                v-else-if="!list && error"
                variant="error"
                title="Daftar voting gagal dimuat"
                :description="error"
                action-label="Coba lagi"
                @action="load"
            />

            <template v-else-if="list">
                <header class="page-header">
                    <div class="status-line">
                        <span class="status-pill" :class="list.expired ? 'is-closed' : 'is-open'">
                            {{ list.expired ? 'Voting ditutup' : 'Voting aktif · 24 jam' }}
                        </span>
                    </div>
                    <h1>{{ list.title }}</h1>
                    <p v-if="list.expired">Masa voting untuk daftar ini sudah berakhir.</p>
                    <p v-else>Pilih kedai yang ingin Anda kunjungi bersama teman. Tekan lagi untuk membatalkan pilihan.</p>
                </header>

                <div class="action-row">
                    <button class="button" type="button" :disabled="busy" @click="share">
                        <Icon name="share" /> Salin tautan
                    </button>
                    <button class="secondary" type="button" :disabled="busy" @click="load">
                        <Icon name="refresh" /> Muat ulang suara
                    </button>
                    <a class="text-link" href="/tongkrongan/buat">Buat daftar baru</a>
                </div>

                <p v-if="notice" class="notice-banner" role="status">{{ notice }}</p>

                <StateBlock
                    v-if="!list.items.length"
                    variant="empty"
                    title="Kedai dalam daftar ini tidak tersedia"
                    description="Kedai pada daftar ini sudah tidak ditayangkan."
                    action-label="Buat daftar baru"
                    action-href="/tongkrongan/buat"
                />

                <ul v-else class="vote-list">
                    <li v-for="item in list.items" :key="item.id" class="vote-item" :class="{ 'is-voted': item.voted }">
                        <div class="vote-item-head">
                            <div>
                                <a class="vote-item-title" :href="item.url">{{ item.name }}</a>
                                <p v-if="item.address" class="helper-text">{{ item.address }}</p>
                            </div>
                            <div class="vote-item-meta">
                                <span class="tabular"><strong>{{ item.votes }}</strong> suara</span>
                                <button
                                    v-if="!list.expired"
                                    type="button"
                                    :class="item.voted ? 'button' : 'secondary'"
                                    :aria-pressed="item.voted"
                                    :disabled="busy"
                                    @click="vote(item)"
                                >
                                    <Icon v-if="item.voted" name="check" />
                                    {{ item.voted ? 'Dipilih' : 'Pilih' }}
                                </button>
                            </div>
                        </div>
                        <div class="progress-track" aria-hidden="true">
                            <span class="progress-fill" :style="{ width: Math.round(((item.votes || 0) / maxVotes) * 100) + '%' }"></span>
                        </div>
                    </li>
                </ul>
            </template>
        </template>
    </section>
</template>
