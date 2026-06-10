import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
    // Root redirect berdasarkan role
    {
        path: '/',
        redirect: () => {
            const auth = useAuthStore()
            if (!auth.isLoggedIn) return '/login'
            if (auth.isSeller)   return '/seller/dashboard'
            if (auth.isAdmin)    return '/admin'
            return '/buyer/dashboard'
        },
    },

    // ── Auth ────────────────────────────────────────────────────────────
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

    // ── Buyer ────────────────────────────────────────────────────────────
    {
        path: '/buyer/dashboard',
        name: 'buyer.dashboard',
        component: () => import('@/pages/buyer/HomePage.vue'),
        meta: { requiresAuth: true, requiresRole: 'buyer' },
    },
    {
        path: '/buyer/product/:id',
        name: 'buyer.product-detail',
        component: () => import('@/pages/buyer/ProductDetailPage.vue'),
        meta: { requiresAuth: true, requiresRole: 'buyer' },
    },
    {
        path: '/buyer/order/:productId',
        name: 'buyer.order',
        component: () => import('@/pages/buyer/OrderFormPage.vue'),
        meta: { requiresAuth: true, requiresRole: 'buyer' },
    },
    {
        path: '/buyer/notifications',
        name: 'buyer.notifications',
        component: () => import('@/pages/buyer/NotificationPage.vue'),
        meta: { requiresAuth: true, requiresRole: 'buyer' },
    },
    {
        path: '/buyer/orders',
        name: 'buyer.orders',
        component: () => import('@/pages/buyer/MyOrdersPage.vue'),
        meta: { requiresAuth: true, requiresRole: 'buyer' },
    },
    {
        path: '/buyer/profile',
        name: 'buyer.profile',
        component: () => import('@/pages/buyer/ProfilePage.vue'),
        meta: { requiresAuth: true, requiresRole: 'buyer' },
    },

    // ── Seller ───────────────────────────────────────────────────────────
    // Onboarding profil toko (US-08 langkah 2)
    {
        path: '/store/setup',
        name: 'store.setup',
        component: () => import('@/pages/store/StoreOnboardingPage.vue'),
        meta: { requiresAuth: true, requiresRole: 'seller' },
    },
    // Dashboard utama seller (FR-12)
    {
        path: '/seller/dashboard',
        name: 'seller.dashboard',
        component: () => import('@/pages/seller/SellerDashboardPage.vue'),
        meta: { requiresAuth: true, requiresRole: 'seller' },
    },
    // Kelola produk seller
    {
        path: '/seller/products',
        name: 'seller.products',
        component: () => import('@/pages/seller/SellerProductsPage.vue'),
        meta: { requiresAuth: true, requiresRole: 'seller' },
    },
    // Profil toko seller
    {
        path: '/seller/store-profile',
        name: 'seller.store-profile',
        component: () => import('@/pages/seller/SellerStoreProfilePage.vue'),
        meta: { requiresAuth: true, requiresRole: 'seller' },
    },

    // ── Admin ────────────────────────────────────────────────────────────
    {
        path: '/admin/login',
        name: 'admin.login',
        component: () => import('@/pages/admin/AdminLoginPage.vue'),
        meta: { guestOnlyAdmin: true },
    },
    {
        path: '/admin',
        name: 'admin',
        component: () => import('@/pages/admin/AdminPage.vue'),
        meta: { requiresAuth: true, requiresRole: 'admin' },
    },

    // ── Catch-all ────────────────────────────────────────────────────────
    {
        path: '/:pathMatch(.*)*',
        redirect: '/login',
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to) => {
    const auth = useAuthStore()

    // Halaman guest-only tidak bisa diakses setelah login
    if (to.meta.guestOnly && auth.isLoggedIn) {
        if (auth.isSeller) return { path: '/seller/dashboard' }
        if (auth.isAdmin)  return { path: '/admin' }
        return { path: '/buyer/dashboard' }
    }

    if (to.meta.guestOnlyAdmin && auth.isLoggedIn) {
        if (auth.isAdmin) return { path: '/admin' }
        if (auth.isSeller) return { path: '/seller/dashboard' }
        return { path: '/buyer/dashboard' }
    }

    // Halaman protected tidak bisa diakses sebelum login
    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        if (to.meta.requiresRole === 'admin') {
            return { path: '/admin/login' }
        }
        return { path: '/login' }
    }

    // Guard role — seller tidak bisa akses halaman buyer, dan sebaliknya
    if (auth.isLoggedIn && to.meta.requiresRole) {
        const role = to.meta.requiresRole
        if (role === 'buyer'  && !auth.isBuyer)  return { path: '/seller/dashboard' }
        if (role === 'seller' && !auth.isSeller) return { path: '/buyer/dashboard' }
        if (role === 'admin'  && !auth.isAdmin)  return { path: '/buyer/dashboard' }
    }

    // Seller yang belum isi profil toko → onboarding dulu
    if (
        auth.isLoggedIn &&
        auth.isSeller &&
        to.name !== 'store.setup' &&
        to.meta.requiresRole === 'seller' &&
        !auth.user?.store
    ) {
        return { path: '/store/setup' }
    }
})

export default router