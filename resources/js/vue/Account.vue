<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { api } from './api'
import Icon from './Icon.vue'
import SkeletonList from './components/SkeletonList.vue'
import StateBlock from './components/StateBlock.vue'

const props = defineProps({ path: String, cms: Boolean })
const register = computed(() => props.path.endsWith('/register'))
const profile = computed(() => props.path === '/profile/badges')
const form = reactive({ name: '', email: '', password: '', password_confirmation: '', role: 'user' })
const stats = ref(null)
const working = ref(false)
const showPassword = ref(false)
const error = ref('')
const fieldErrors = ref({})
const csrf = document.querySelector('meta[name="csrf-token"]').content

const roleOptions = [
    {
        value: 'user',
        title: 'Pengunjung',
        description: 'Simpan kedai favorit ke akun, check-in di lokasi, dan kumpulkan lencana.',
    },
    {
        value: 'admin',
        title: 'Pemilik kedai',
        description: 'Daftarkan dan kelola profil, jam buka, serta fasilitas kedai kopi Anda.',
    },
    {
        value: 'roastery',
        title: 'Pemilik roastery',
        description: 'Daftarkan dan kelola profil serta katalog foto roastery Anda.',
    },
]

function clearFieldError(name) {
    if (!fieldErrors.value[name]) return
    const next = { ...fieldErrors.value }
    delete next[name]
    fieldErrors.value = next
}

async function signIn() {
    working.value = true
    error.value = ''
    fieldErrors.value = {}
    try {
        const data = await api(props.path, { method: 'POST', body: form })
        location.assign(data.redirect)
    } catch (e) {
        error.value = e.message
        if (e.errors) {
            fieldErrors.value = Object.fromEntries(
                Object.entries(e.errors).map(([k, msgs]) => [k, Array.isArray(msgs) ? msgs[0] : msgs]),
            )
        }
    } finally {
        working.value = false
    }
}

async function badges() {
    error.value = ''
    try {
        stats.value = await api('/api/badges')
    } catch (e) {
        error.value = e.message
    }
}

onMounted(() => {
    if (profile.value) badges()
})
</script>

<template>
    <section v-if="profile" class="page-section">
        <div class="section-header">
            <div class="page-header page-header--functional">
                <h1>Lencana kopi</h1>
                <p>Jejak kunjungan dan check-in Anda di kedai kopi Samarinda.</p>
            </div>
            <div class="action-row">
                <a v-if="cms" class="button secondary" href="/admin"><Icon name="store" /> Kelola konten</a>
                <form action="/logout" method="post">
                    <input type="hidden" name="_token" :value="csrf">
                    <button type="submit" class="secondary">Keluar</button>
                </form>
            </div>
        </div>

        <StateBlock
            v-if="error"
            variant="error"
            title="Gagal memuat lencana"
            :description="error"
            action-label="Coba lagi"
            @action="badges"
        />

        <SkeletonList v-else-if="!stats" variant="rows" :count="4" />

        <template v-else>
            <div class="hint-box hint-box--accent">
                <strong class="tabular">{{ stats.total_check_ins }} check-in di {{ stats.unique_cafes }} kedai</strong>
                <span>Check-in dapat dilakukan dari halaman detail kedai yang sedang Anda kunjungi.</span>
            </div>

            <StateBlock
                v-if="stats.total_check_ins === 0"
                variant="empty"
                title="Belum ada kunjungan tercatat"
                description="Buka halaman detail kedai yang sedang Anda kunjungi, lalu tekan Check-in di sini untuk mulai mengumpulkan lencana."
                action-label="Cari kedai untuk check-in"
                action-href="/explore"
                icon="award"
            />

            <p v-if="stats.total_check_ins > 0 && !stats.badges.length" class="helper-text">Belum ada lencana yang tersedia.</p>
            <ul v-else class="badge-grid">
                <li v-for="badge in stats.badges" :key="badge.id" class="badge-card">
                    <div class="section-header">
                        <strong>{{ badge.name }}</strong>
                        <span class="status-pill" :class="badge.earned ? 'is-open' : 'is-closed'">
                            {{ badge.earned ? 'Diperoleh' : badge.progress + ' / ' + badge.target }}
                        </span>
                    </div>
                    <p class="helper-text">{{ badge.description }}</p>
                    <div v-if="!badge.earned && badge.target" class="progress-track" aria-hidden="true">
                        <span
                            class="progress-fill"
                            :style="{ width: Math.min(100, Math.round((badge.progress / badge.target) * 100)) + '%' }"
                        ></span>
                    </div>
                </li>
            </ul>
        </template>
    </section>

    <section v-else class="page-section account-page">
        <div class="auth-card">
            <header class="page-header">
                <h1>{{ register ? 'Daftar WadahNgopi' : 'Masuk ke WadahNgopi' }}</h1>
                <p>
                    {{ register
                        ? 'Buat akun untuk menyimpan tempat favorit, mencatat kunjungan, atau mengelola kedai.'
                        : 'Masuk untuk membuka daftar simpanan, check-in kedai, atau mengelola tempat Anda.' }}
                </p>
            </header>

            <form class="stack-form" @submit.prevent="signIn">
                <label v-if="register" for="reg-name">
                    Nama
                    <input
                        id="reg-name"
                        name="name"
                        v-model.trim="form.name"
                        autocomplete="name"
                        required
                        maxlength="100"
                        placeholder="Nama lengkap atau nama panggilan"
                        :aria-invalid="Boolean(fieldErrors.name) || undefined"
                        @input="clearFieldError('name')"
                    >
                    <small v-if="fieldErrors.name" class="field-error" role="alert">{{ fieldErrors.name }}</small>
                </label>

                <label for="auth-email">
                    Email
                    <input
                        id="auth-email"
                        name="email"
                        v-model.trim="form.email"
                        type="email"
                        autocomplete="email"
                        required
                        maxlength="255"
                        placeholder="nama@domain.com"
                        :aria-invalid="Boolean(fieldErrors.email) || undefined"
                        @input="clearFieldError('email')"
                    >
                    <small v-if="fieldErrors.email" class="field-error" role="alert">{{ fieldErrors.email }}</small>
                </label>

                <div class="field-block">
                    <label for="auth-password">Kata sandi</label>
                    <div class="input-row">
                        <input
                            id="auth-password"
                            name="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            :autocomplete="register ? 'new-password' : 'current-password'"
                            required
                            :minlength="register ? 8 : undefined"
                            maxlength="72"
                            :aria-describedby="register && !fieldErrors.password ? 'password-hint' : undefined"
                            :aria-invalid="Boolean(fieldErrors.password) || undefined"
                            @input="clearFieldError('password')"
                        >
                        <button
                            type="button"
                            class="secondary"
                            :aria-pressed="showPassword"
                            :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                            @click="showPassword = !showPassword"
                        >
                            <Icon :name="showPassword ? 'eyeOff' : 'eye'" />
                            <span>{{ showPassword ? 'Sembunyikan' : 'Tampilkan' }}</span>
                        </button>
                    </div>
                    <small v-if="fieldErrors.password" class="field-error" role="alert">{{ fieldErrors.password }}</small>
                    <small v-else-if="register" id="password-hint" class="field-hint">Minimal 8 karakter.</small>
                </div>

                <template v-if="register">
                    <label for="reg-password-confirm">
                        Ulangi kata sandi
                        <input
                            id="reg-password-confirm"
                            name="password_confirmation"
                            v-model="form.password_confirmation"
                            autocomplete="new-password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            maxlength="72"
                            :aria-invalid="Boolean(fieldErrors.password_confirmation) || undefined"
                            @input="clearFieldError('password_confirmation')"
                        >
                        <small v-if="fieldErrors.password_confirmation" class="field-error" role="alert">{{ fieldErrors.password_confirmation }}</small>
                    </label>

                    <fieldset class="field-block" style="border:0;padding:0">
                        <legend style="font-size:var(--text-sm);font-weight:600;margin-bottom:0.4rem">Jenis akun</legend>
                        <div class="role-choice-grid">
                            <label
                                v-for="opt in roleOptions"
                                :key="opt.value"
                                class="role-choice"
                                :class="{ 'is-selected': form.role === opt.value }"
                            >
                                <input type="radio" name="role" :value="opt.value" v-model="form.role" @change="clearFieldError('role')">
                                <span>
                                    <strong style="display:block">{{ opt.title }}</strong>
                                    <small class="field-hint">{{ opt.description }}</small>
                                </span>
                            </label>
                        </div>
                        <small v-if="fieldErrors.role" class="field-error" role="alert">{{ fieldErrors.role }}</small>
                    </fieldset>
                </template>

                <p v-if="error" class="alert-banner" role="alert">{{ error }}</p>

                <button type="submit" :disabled="working">
                    {{ working ? 'Sedang diproses…' : register ? 'Buat akun' : 'Masuk' }}
                </button>
            </form>

            <div>
                <a class="text-link" :href="register ? '/admin/login' : '/admin/register'">
                    {{ register ? 'Sudah punya akun? Masuk di sini' : 'Belum punya akun? Daftar sekarang' }}
                </a>
            </div>
        </div>
    </section>
</template>
