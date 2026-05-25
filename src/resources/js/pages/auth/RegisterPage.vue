<template>
    <AuthLayout>

        <!-- Pilih Peran -->
        <div class="mb-5">
            <p class="text-sm font-semibold text-gray-700 mb-2">Pilih Peran</p>
            <div class="grid grid-cols-2 gap-3">
                <button
                    v-for="r in roles"
                    :key="r.value"
                    type="button"
                    @click="form.role = r.value"
                    :class="[
                        'flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 text-xs font-semibold transition-all',
                        form.role === r.value
                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                            : 'border-gray-200 bg-gray-50 text-gray-500 hover:border-gray-300'
                    ]"
                >
                    <span class="text-xl">{{ r.icon }}</span>
                    <span class="leading-tight text-center">{{ r.label }}</span>
                    <span class="text-[10px] font-normal opacity-70">{{ r.sub }}</span>
                </button>
            </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleRegister" novalidate class="space-y-4">

            <FormField label="Nama Lengkap" :error="errors.name" required>
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Masukkan nama lengkap"
                    autocomplete="name"
                    :class="inputClass(errors.name)"
                />
            </FormField>

            <FormField label="Email" :error="errors.email" required>
                <input
                    v-model="form.email"
                    type="email"
                    placeholder="email@contoh.com"
                    autocomplete="email"
                    :class="inputClass(errors.email)"
                />
            </FormField>

            <FormField label="No. WhatsApp / Telepon" :error="errors.phone_number" required>
                <input
                    v-model="form.phone_number"
                    type="tel"
                    placeholder="08xxxxxxxxxx"
                    :class="inputClass(errors.phone_number)"
                />
            </FormField>

            <FormField label="Password" :error="errors.password" required>
                <div class="relative">
                    <input
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Minimal 8 karakter"
                        autocomplete="new-password"
                        :class="[inputClass(errors.password), 'pr-10']"
                    />
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        @click="showPassword = !showPassword"
                    >
                        <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </FormField>

            <!-- Error global -->
            <div v-if="globalError" class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600 text-center">{{ globalError }}</p>
            </div>

            <button
                type="submit"
                :disabled="isLoading"
                style="background:linear-gradient(135deg,#1a7fc4,#1565a8);"
                class="w-full py-3 rounded-xl text-white font-bold text-sm shadow-sm hover:shadow-md disabled:opacity-60 transition-all mt-2"
            >
                <span v-if="isLoading" class="flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Mendaftarkan...
                </span>
                <span v-else>Buat Akun</span>
            </button>

            <!-- Catatan untuk seller — sesuai wireframe -->
            <p v-if="form.role === 'seller'" class="text-center text-xs text-gray-400 mt-2">
                Pemilik UMKM → Menunggu verifikasi admin
            </p>

        </form>

    </AuthLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AuthLayout from '@/components/common/AuthLayout.vue'
import FormField   from '@/components/common/FormField.vue'

const router    = useRouter()
const authStore = useAuthStore()

const roles = [
    { value: 'buyer',  icon: '🛒', label: 'Pembeli',       sub: 'Cari & Pesan' },
    { value: 'seller', icon: '🏪', label: 'Pemilik UMKM',  sub: 'Daftarkan toko' },
]

const showPassword = ref(false)
const isLoading    = ref(false)
const globalError  = ref('')

const form = reactive({
    name:                  '',
    email:                 '',
    phone_number:          '',
    password:              '',
    password_confirmation: '',
    role:                  'buyer',
})

// password_confirmation selalu sama dengan password untuk simplifikasi form
// (konfirmasi bisa ditambah sebagai field terpisah jika diminta)
import { watch } from 'vue'
watch(() => form.password, val => { form.password_confirmation = val })

const errors = reactive({})

async function handleRegister() {
    clearErrors()
    isLoading.value = true
    try {
        await authStore.register(form)
        router.push({ name: 'home' })
    } catch (err) {
        handleApiError(err)
    } finally {
        isLoading.value = false
    }
}

function handleApiError(err) {
    const res = err.response
    if (res?.status === 422) {
        const fieldErrors = res.data.errors ?? {}
        Object.keys(fieldErrors).forEach(f => { errors[f] = fieldErrors[f][0] })
    } else if (res?.status === 401) {
        globalError.value = res.data.message
    } else {
        globalError.value = 'Terjadi kesalahan. Silakan coba lagi.'
    }
}

function clearErrors() {
    Object.keys(errors).forEach(k => delete errors[k])
    globalError.value = ''
}

function inputClass(hasError) {
    return [
        'w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition-all bg-gray-50',
        hasError
            ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-200'
            : 'border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 focus:bg-white',
    ]
}
</script>
