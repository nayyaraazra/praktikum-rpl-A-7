<template>
    <div class="min-h-screen" style="background:#f0f4f8;">

        <!-- Navbar -->
        <header style="background:linear-gradient(135deg,#1a7fc4 0%,#1565a8 100%);" class="px-6 py-4 shadow-md">
            <div class="max-w-5xl mx-auto flex items-center justify-between">
                <div>
                    <span class="text-xl font-extrabold text-white tracking-tight">KULAAN.id</span>
                    <span class="text-blue-200 text-xs ml-2 hidden sm:inline">Katalog UMKM Lokal Jebres</span>
                </div>
                <div class="flex items-center gap-3">
                    <template v-if="authStore.isLoggedIn">
                        <span class="text-sm text-blue-100 hidden sm:block">
                            Halo, <strong class="text-white">{{ authStore.user?.name }}</strong>
                        </span>
                        <button
                            class="text-xs bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg font-medium transition-colors"
                            @click="handleLogout"
                        >
                            Keluar
                        </button>
                    </template>
                    <template v-else>
                        <RouterLink
                            to="/login"
                            class="text-sm text-white font-medium hover:text-blue-100 transition-colors"
                        >
                            Masuk
                        </RouterLink>
                        <RouterLink
                            to="/register"
                            class="text-sm bg-white text-blue-600 font-bold px-4 py-1.5 rounded-lg hover:bg-blue-50 transition-colors shadow-sm"
                        >
                            Daftar
                        </RouterLink>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <main class="max-w-2xl mx-auto px-6 py-20 text-center">
            <div class="text-5xl mb-4">🛍️</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-3">
                Temukan Produk UMKM Lokal
            </h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-8">
                Platform katalog digital untuk UMKM di Kelurahan Jebres, Surakarta.
                <br />Fitur pencarian produk akan tersedia di P7 (US-03).
            </p>

            <div v-if="!authStore.isLoggedIn" class="flex gap-3 justify-center">
                <RouterLink
                    to="/register"
                    style="background:linear-gradient(135deg,#1a7fc4,#1565a8);"
                    class="px-6 py-2.5 rounded-xl text-white font-bold text-sm shadow hover:shadow-md transition-all"
                >
                    Mulai Sekarang
                </RouterLink>
                <RouterLink
                    to="/login"
                    class="px-6 py-2.5 rounded-xl border-2 border-blue-200 text-blue-600 font-bold text-sm hover:bg-blue-50 transition-all"
                >
                    Sudah Punya Akun
                </RouterLink>
            </div>
        </main>

    </div>
</template>

<script setup>
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router    = useRouter()
const authStore = useAuthStore()

async function handleLogout() {
    await authStore.logout()
    router.push({ name: 'login' })
}
</script>
