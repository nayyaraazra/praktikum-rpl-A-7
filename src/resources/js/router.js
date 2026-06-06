// resources/js/router.js
// Vue Router — mencakup semua halaman buyer, seller, dan auth

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
    // ── Root redirect ─────────────────────────────────────────────
    {
        path: '/',
        redirect: () => {
            const auth = useAuthStore()
            if (!auth.isLoggedIn) return '/login'
            return auth.isSeller ? '/seller/dashboard' : '/products'
        },
    },

    // ══════════════════════════════════════════════════════════════
    // AUTH
    // ══════════════════════════════════════════════════════════════
    {
        path: '/login',
        name: 'login',
        component: () => import('@/pages/auth/AuthPage.vue'),
        meta: { guestOnly: true },
    },
    {
        path: '/register',
        name: 'register',
        // Redirect ke halaman auth dengan tab daftar aktif
        redirect: { name: 'login', query: { tab: 'daftar' } },
    },

    // ══════════════════════════════════════════════════════════════
    // STORE ONBOARDING (seller, setelah register)
    // ══════════════════════════════════════════════════════════════
    {
        path: '/store/setup',
        name: 'store.setup',
        component: () => import('@/pages/store/StoreOnboardingPage.vue'),
        meta: { requiresAuth: true, requiresSeller: true },
    },

    // ══════════════════════════════════════════════════════════════
    // BUYER PAGES
    // ══════════════════════════════════════════════════════════════

    // US-03 + US-04 — Product List & Search
    {
        path: '/products',
        name: 'buyer.products',
        component: () => import('@/pages/buyer/ProductListPage.vue'),
        meta: { requiresAuth: true, requiresBuyer: true },
    },

    // US-05 — Product Detail
    {
        path: '/products/:id',
        name: 'buyer.product.detail',
        component: () => import('@/pages/buyer/ProductDetailPage.vue'),
        meta: { requiresAuth: true, requiresBuyer: true },
        props: true,
    },

    // US-06 — Order Form
    {
        path: '/order',
        name: 'buyer.order',
        component: () => import('@/pages/buyer/OrderFormPage.vue'),
        meta: { requiresAuth: true, requiresBuyer: true },
    },

    // US-07 — Notifications
    {
        path: '/notifications',
        name: 'buyer.notifications',
        component: () => import('@/pages/buyer/NotificationPage.vue'),
        meta: { requiresAuth: true, requiresBuyer: true },
    },

    // ══════════════════════════════════════════════════════════════
    // SELLER PAGES
    // ══════════════════════════════════════════════════════════════

    // US-10 + US-12 — Seller Dashboard (pesanan masuk)
    {
        path: '/seller/dashboard',
        name: 'seller.dashboard',
        component: () => import('@/pages/seller/SellerDashboardPage.vue'),
        meta: { requiresAuth: true, requiresSeller: true, requiresStoreComplete: true },
    },

    // US-11 — Manage Store Profile
    {
        path: '/seller/store',
        name: 'seller.store',
        component: () => import('@/pages/seller/ManageStorePage.vue'),
        meta: { requiresAuth: true, requiresSeller: true, requiresStoreComplete: true },
    },

    // Placeholder: Daftar produk milik seller (ditambah di iterasi berikutnya)
    {
        path: '/seller/products',
        name: 'seller.products',
        component: () => import('@/pages/seller/SellerDashboardPage.vue'), // sementara redirect ke dashboard
        meta: { requiresAuth: true, requiresSeller: true },
    },

    // Placeholder: Tambah produk (ditambah di iterasi berikutnya)
    {
        path: '/seller/products/add',
        name: 'seller.products.add',
        component: () => import('@/pages/seller/SellerDashboardPage.vue'), // sementara
        meta: { requiresAuth: true, requiresSeller: true },
    },

    // ══════════════════════════════════════════════════════════════
    // ADMIN (hidden route)
    // ══════════════════════════════════════════════════════════════
    {
        path: '/admin',
        name: 'admin',
        component: () => import('@/pages/admin/AdminPage.vue'),
        meta: { requiresAuth: true },
    },

    // ══════════════════════════════════════════════════════════════
    // LEGACY / LEGACY HOME
    // ══════════════════════════════════════════════════════════════
    {
        path: '/home',
        name: 'home',
        redirect: () => {
            const auth = useAuthStore()
            return auth.isSeller ? '/seller/dashboard' : '/products'
        },
    },

    // ── 404 catch-all ─────────────────────────────────────────────
    {
        path: '/:pathMatch(.*)*',
        redirect: '/login',
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    // Selalu scroll ke atas saat navigasi
    scrollBehavior: () => ({ top: 0, behavior: 'smooth' }),
})

// ── Navigation Guards ─────────────────────────────────────────
router.beforeEach((to) => {
    const auth = useAuthStore()

    // Halaman guestOnly tidak bisa diakses setelah login
    if (to.meta.guestOnly && auth.isLoggedIn) {
        return auth.isSeller ? { path: '/seller/dashboard' } : { path: '/products' }
    }

    // Halaman yang butuh login
    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return { path: '/login' }
    }

    // Role guard: halaman buyer tidak untuk seller (dan sebaliknya)
    if (to.meta.requiresBuyer && auth.isSeller) {
        return { path: '/seller/dashboard' }
    }
    if (to.meta.requiresSeller && auth.isBuyer) {
        return { path: '/products' }
    }

    // Seller yang belum isi profil toko → wajib onboarding dulu
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