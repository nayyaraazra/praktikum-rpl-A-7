import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
    // Root redirect ke login jika belum login, ke home jika sudah
    {
        path: '/',
        redirect: () => {
            const auth = useAuthStore()
            return auth.isLoggedIn ? '/home' : '/login'
        },
    },

    // Auth
    {
        path: '/login',
        name: 'login',
        component: () => import('@/pages/auth/AuthPage.vue'),
        meta: { guestOnly: true },
    },
    {
        path: '/register',
        name: 'register',
        redirect: { name: 'login', query: { tab: 'daftar' } },
    },

    // Beranda setelah login
    {
        path: '/home',
        name: 'home',
        component: () => import('@/pages/HomePage.vue'),
        meta: { requiresAuth: true, requiresStoreComplete: true },
    },

    // Onboarding profil toko untuk seller
    {
        path: '/store/setup',
        name: 'store.setup',
        component: () => import('@/pages/store/StoreOnboardingPage.vue'),
        meta: { requiresAuth: true, requiresSeller: true },
    },

    // Catch-all
    {
        path: '/:pathMatch(.*)*',
        redirect: '/login',
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Guard: halaman guestOnly tidak bisa diakses setelah login
// Guard: halaman requiresAuth tidak bisa diakses sebelum login
router.beforeEach((to) => {
    const auth = useAuthStore()

    if (to.meta.guestOnly && auth.isLoggedIn) {
        return { path: '/home' }
    }

    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return { path: '/login' }
    }

    // Seller yang belum isi profil toko wajib ke onboarding dulu
    if (
        auth.isLoggedIn &&
        auth.isSeller &&
        to.meta.requiresStoreComplete &&
        !auth.user?.store &&
        localStorage.getItem('store_onboarding_skipped') !== '1'
    ) {
        return { path: '/store/setup' }
    }
})

export default router