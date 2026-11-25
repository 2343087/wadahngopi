<script setup>
import { computed, ref } from 'vue'
import Icon from './Icon.vue'
import { useBookmarks } from './composables/useBookmarks'

const props = defineProps({
    item: { type: Object, required: true },
    signedIn: Boolean,
})
const emit = defineEmits(['bookmark-toggled', 'bookmark-error'])

const imageBroken = ref(false)
const { isSaved, isBookmarkBusy, toggleBookmark } = useBookmarks()

const bookmarkable = computed(() => props.item.kind === 'cafe' || props.item.kind === 'roastery')
const saved = computed(() => bookmarkable.value && isSaved(props.item.kind, props.item.id))
const busy = computed(() => bookmarkable.value && isBookmarkBusy(props.item.kind, props.item.id))

async function onToggleBookmark() {
    if (!bookmarkable.value) return
    try {
        const result = await toggleBookmark(props.item.kind, props.item.id, props.signedIn)
        emit('bookmark-toggled', { kind: props.item.kind, id: props.item.id, saved: result.saved })
    } catch (e) {
        emit('bookmark-error', e.message)
    }
}

const hoursText = computed(() => {
    const h = props.item.hours
    if (!h) return ''
    if (h.label) return h.label
    if (h.open && h.close) return `${h.open.slice(0, 5)}–${h.close.slice(0, 5)}`
    return ''
})

const formattedDate = computed(() => {
    if (!props.item.date) return ''
    try {
        return new Date(props.item.date).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        })
    } catch {
        return props.item.date
    }
})
</script>

<template>
    <article class="venue-card">
        <div class="venue-cover">
            <img
                v-if="item.image && !imageBroken"
                :src="item.image"
                :alt="item.image_illustration ? '' : item.name"
                width="540"
                height="345"
                loading="lazy"
                decoding="async"
                @error="imageBroken = true"
            >
            <div v-else class="venue-cover-placeholder" aria-hidden="true">
                <Icon :name="item.category ? 'news' : item.kind === 'roastery' ? 'bean' : 'store'" />
            </div>
        </div>

        <div class="venue-body">
            <div v-if="item.category || item.hours || item.open !== undefined || bookmarkable" class="venue-top-meta">
                <div class="venue-status-group">
                    <span v-if="item.category" class="status-pill is-closed is-neutral">{{ item.category }}</span>
                    <span
                        v-else-if="item.hours || item.open !== undefined"
                        class="status-pill"
                        :class="{ 'is-open': item.open, 'is-closed': !item.open }"
                    >
                        {{ item.open ? 'Sedang buka' : 'Tutup' }}
                    </span>
                    <span v-if="hoursText" class="venue-hours-inline">{{ hoursText }}</span>
                    <span v-else-if="formattedDate" class="venue-hours-inline">{{ formattedDate }}</span>
                </div>

                <button
                    v-if="bookmarkable"
                    type="button"
                    class="secondary card-bookmark-btn"
                    :aria-pressed="saved"
                    :aria-label="(saved ? 'Hapus dari simpanan: ' : 'Simpan tempat: ') + item.name"
                    :disabled="busy"
                    @click.stop="onToggleBookmark"
                >
                    <Icon name="saved" />
                </button>
            </div>

            <component :is="item.heading || 'h2'" class="venue-name">
                <a :href="item.url" class="venue-card-link">{{ item.name }}</a>
            </component>
            <p v-if="item.address || item.summary" class="venue-address">{{ item.address || item.summary }}</p>

            <div class="venue-foot">
                <span v-if="item.distance != null" class="tabular">
                    {{ Number(item.distance).toLocaleString('id-ID', { maximumFractionDigits: 1 }) }} km
                </span>
                <span v-else-if="item.city">{{ item.city }}</span>
                <small v-if="item.image_illustration">Foto ilustrasi · Unsplash</small>
            </div>
        </div>
    </article>
</template>
