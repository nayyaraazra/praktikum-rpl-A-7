import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi } from '@/services/api/authApi'

export const useAuthStore = defineStore('auth', () => {
    // ── State ────────────────────────────────────────────────────────
    const user  = ref(JSON.parse(localStorage.getItem('auth_user') || 'null'))
    const token = ref(localStorage.getItem('auth_token') || null)

    // ── Getters ──────────────────────────────────────────────────────
    const isLoggedIn = computed(() => !!token.value)
    const isBuyer    = computed(() => user.value?.roles?.includes('buyer'))
    const isSeller   = computed(() => user.value?.roles?.includes('seller'))
    const isAdmin    = computed(() => user.value?.roles?.includes('admin'))

    // ── Actions ──────────────────────────────────────────────────────

    async function register(payload) {
        const { data } = await authApi.register(payload)
        persistSession(data.data.user, data.data.token)
        return data
    }

    async function login(payload) {
        const { data } = await authApi.login(payload)
        persistSession(data.data.user, data.data.token)
        return data // data.data.notice berisi pesan verifikasi untuk seller
    }

    async function logout() {
        try {
            await authApi.logout()
        } finally {
            clearSession()
        }
    }

    async function fetchCurrentUser() {
        const { data } = await authApi.me()
        user.value = data.data
        localStorage.setItem('auth_user', JSON.stringify(data.data))
    }

    async function updateProfile(payload) {
        const { data } = await authApi.updateProfile(payload)
        user.value = data.data
        localStorage.setItem('auth_user', JSON.stringify(data.data))
        return data
    }

    // ── Internal helpers ─────────────────────────────────────────────

    function persistSession(userData, authToken) {
        user.value  = userData
        token.value = authToken
        localStorage.setItem('auth_user',  JSON.stringify(userData))
        localStorage.setItem('auth_token', authToken)
    }

    function clearSession() {
        user.value  = null
        token.value = null
        localStorage.removeItem('auth_user')
        localStorage.removeItem('auth_token')
    }

    return {
        user, token,
        isLoggedIn, isBuyer, isSeller, isAdmin,
        register, login, logout, fetchCurrentUser, updateProfile,
    }
})
