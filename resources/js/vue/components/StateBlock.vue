<script setup>
import Icon from '../Icon.vue'

defineProps({
    variant: { type: String, default: 'empty' }, // 'empty' | 'error'
    title: { type: String, required: true },
    description: { type: String, default: '' },
    actionLabel: { type: String, default: '' },
    actionHref: { type: String, default: '' },
    secondaryLabel: { type: String, default: '' },
    secondaryHref: { type: String, default: '' },
    icon: { type: String, default: '' },
})
const emit = defineEmits(['action', 'secondary'])
</script>

<template>
    <div class="state-block" :class="`state-block--${variant}`" :role="variant === 'error' ? 'alert' : 'status'">
        <div v-if="icon" class="state-block-icon" aria-hidden="true">
            <Icon :name="icon" />
        </div>
        <div class="state-block-body">
            <h2>{{ title }}</h2>
            <p v-if="description">{{ description }}</p>
            <div v-if="actionLabel || secondaryLabel" class="action-row">
                <a v-if="actionLabel && actionHref" class="button" :href="actionHref">{{ actionLabel }}</a>
                <button v-else-if="actionLabel" type="button" class="button" @click="emit('action')">{{ actionLabel }}</button>
                <a v-if="secondaryLabel && secondaryHref" class="button secondary" :href="secondaryHref">{{ secondaryLabel }}</a>
                <button v-else-if="secondaryLabel" type="button" class="button secondary" @click="emit('secondary')">{{ secondaryLabel }}</button>
            </div>
        </div>
    </div>
</template>
