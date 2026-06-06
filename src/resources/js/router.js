import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
    // Root redirect ke login jika belum login, ke home jika sudah
    {
        path: '/',
        redirect: () => {
            const auth = useAuthStore()
            if (auth.isLoggedIn) {
                return auth.isSeller ? '/seller/orders' : '/home'
            }
            return '/login'
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

    // Buyer Routes
    {
        path: '/products',
        name: 'products',
        component: () => import('@/pages/buyer/SearchProductsPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/products/:id',
        name: 'product.detail',
        component: () => import('@/pages/buyer/ProductDetailPage.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/order/:productId',
        name: 'order.create',
        component: () => import('@/pages/buyer/OrderFormPage.vue'),
        meta: { requiresAuth: true },
    },

    // Seller Routes
    {
        path: '/seller/profile',
        name: 'seller.profile',
        component: () => import('@/pages/seller/StoreProfilePage.vue'),
        meta: { requiresAuth: true, requiresSeller: true },
    },
    {
        path: '/seller/orders',
        name: 'seller.orders',
        component: () => import('@/pages/seller/SellerOrdersPage.vue'),
        meta: { requiresAuth: true, requiresSeller: true },
    },

    // Admin Panel (Hidden Route)
    {
        path: '/admin',
        name: 'admin',
        component: () => import('@/pages/admin/AdminPage.vue'),
        meta: { requiresAuth: true },
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
        return auth.isSeller ? { path: '/seller/orders' } : { path: '/home' }
    }

    if (to.meta.requiresAuth && !auth.isLoggedIn) {
        return { path: '/login' }
    }

    // Guard: requiresSeller
    if (to.meta.requiresSeller && !auth.isSeller) {
        return { path: '/home' }
    }

    // Seller yang login tidak boleh mengakses halaman buyer (halaman yang tidak membutuhkan requiresSeller, kecuali onboarding /store/setup)
    if (
        auth.isLoggedIn &&
        auth.isSeller &&
        !to.meta.requiresSeller &&
        to.path !== '/store/setup'
    ) {
        return { path: '/seller/orders' }
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