import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useThemeStore = defineStore('theme', () => {
    // ── State ────────────────────────────────────────────────────────
    const theme = ref(localStorage.getItem('theme') || 'light')

    // ── Actions ──────────────────────────────────────────────────────
    function toggleTheme() {
        theme.value = theme.value === 'light' ? 'dark' : 'light'
        applyTheme()
    }

    function initTheme() {
        applyTheme()
    }

    function applyTheme() {
        localStorage.setItem('theme', theme.value)
        if (theme.value === 'dark') {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    }

    return {
        theme,
        toggleTheme,
        initTheme,
    }
})
