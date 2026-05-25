<template>
    <div class="min-h-screen flex flex-col" style="background:#f0f4f8;">

        <!-- Header biru sesuai wireframe -->
        <header style="background:linear-gradient(135deg,#1a7fc4 0%,#1565a8 100%);" class="py-8 px-6 text-center shadow-md">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">KULAAN.id</h1>
            <p class="text-blue-100 text-sm mt-1 font-medium">Katalog UMKM Lokal Jebres</p>
        </header>

        <!-- Tab Masuk / Daftar sesuai wireframe -->
        <div style="background:linear-gradient(135deg,#1a7fc4 0%,#1565a8 100%);" class="px-6 pb-0 flex justify-center">
            <div class="flex w-full max-w-sm rounded-t-xl overflow-hidden">
                <RouterLink
                    to="/login"
                    class="flex-1 py-3 text-center text-sm font-semibold transition-colors"
                    :class="activeTab === 'login'
                        ? 'bg-white text-blue-600'
                        : 'bg-blue-700/40 text-white hover:bg-blue-700/60'"
                >
                    Masuk
                </RouterLink>
                <RouterLink
                    to="/register"
                    class="flex-1 py-3 text-center text-sm font-semibold transition-colors"
                    :class="activeTab === 'register'
                        ? 'bg-white text-blue-600'
                        : 'bg-blue-700/40 text-white hover:bg-blue-700/60'"
                >
                    Daftar
                </RouterLink>
            </div>
        </div>

        <!-- Card konten -->
        <main class="flex-1 flex justify-center px-4 py-0">
            <div class="w-full max-w-sm bg-white shadow-lg rounded-b-xl rounded-tr-xl px-6 pt-6 pb-8">
                <slot />
            </div>
        </main>

    </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'

const route     = useRoute()
const activeTab = computed(() => route.name)
</script>
