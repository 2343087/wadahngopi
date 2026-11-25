<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps({
    variant: { type: String, default: 'grid' }, // 'grid' | 'detail' | 'rows' | 'table'
    count: { type: Number, default: 6 },
    delay: { type: Number, default: 120 },
})

const visible = ref(props.delay === 0)
let timer

onMounted(() => {
    if (visible.value) return
    timer = setTimeout(() => { visible.value = true }, props.delay)
})

onBeforeUnmount(() => {
    if (timer) clearTimeout(timer)
})
</script>

<template>
    <div v-if="!visible" aria-hidden="true"></div>
    <div v-else-if="variant === 'grid'" class="venue-grid" aria-busy="true" aria-label="Memuat daftar tempat">
        <div v-for="i in count" :key="i" class="skeleton-card">
            <div class="skeleton-media skeleton-pulse"></div>
            <div class="skeleton-body">
                <span class="skeleton-line skeleton-line--short skeleton-pulse"></span>
                <span class="skeleton-line skeleton-line--title skeleton-pulse"></span>
                <span class="skeleton-line skeleton-pulse"></span>
            </div>
        </div>
    </div>
    <div v-else-if="variant === 'detail'" class="skeleton-detail" aria-busy="true" aria-label="Memuat detail">
        <span class="skeleton-line skeleton-line--short skeleton-pulse"></span>
        <span class="skeleton-line skeleton-line--hero skeleton-pulse"></span>
        <div class="skeleton-cover skeleton-pulse"></div>
        <div class="detail-layout">
            <div class="skeleton-rows">
                <span class="skeleton-line skeleton-pulse"></span>
                <span class="skeleton-line skeleton-line--medium skeleton-pulse"></span>
                <span class="skeleton-line skeleton-pulse"></span>
            </div>
            <div class="skeleton-row">
                <span class="skeleton-line skeleton-line--medium skeleton-pulse"></span>
                <span class="skeleton-line skeleton-line--short skeleton-pulse"></span>
            </div>
        </div>
    </div>
    <div v-else-if="variant === 'table'" class="skeleton-rows" aria-busy="true" aria-label="Memuat tabel data">
        <div v-for="i in count" :key="i" class="skeleton-row skeleton-table-row">
            <span class="skeleton-line skeleton-line--medium skeleton-pulse"></span>
            <span class="skeleton-line skeleton-line--short skeleton-pulse"></span>
        </div>
    </div>
    <div v-else class="skeleton-rows" aria-busy="true" aria-label="Memuat data">
        <div v-for="i in count" :key="i" class="skeleton-row">
            <span class="skeleton-line skeleton-line--title skeleton-pulse"></span>
            <span class="skeleton-line skeleton-line--short skeleton-pulse"></span>
        </div>
    </div>
</template>
