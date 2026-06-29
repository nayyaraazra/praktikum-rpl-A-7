<template>
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="program-logo-sm">K</div>
      <div class="brand-text">Kulaan.id</div>
    </div>

    <div class="nav-section">
      <div class="nav-section-label">Menu Utama</div>
      <router-link class="nav-item" :class="{ active: isDashboardActive }" :to="{ name: 'buyer.dashboard' }">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <circle cx="11" cy="11" r="8" />
          <path d="m21 21-4.35-4.35" />
        </svg>
        Cari Produk
      </router-link>
      <router-link class="nav-item" :class="{ active: route.name === 'buyer.orders' }" :to="{ name: 'buyer.orders' }">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
          <line x1="3" y1="6" x2="21" y2="6" />
          <path d="M16 10a4 4 0 0 1-8 0" />
        </svg>
        Pesanan Saya
        <span v-if="activeOrdersCount > 0" class="nav-badge">{{ activeOrdersCount }}</span>
      </router-link>
      <router-link class="nav-item" :class="{ active: route.name === 'buyer.notifications' }" :to="{ name: 'buyer.notifications' }">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
          <path d="M13.73 21a2 2 0 0 1-3.46 0" />
        </svg>
        Notifikasi
        <span v-if="unreadCount > 0" class="nav-badge" style="background:var(--brand-500);">{{ unreadCount }}</span>
      </router-link>
      <router-link class="nav-item" :class="{ active: route.name === 'buyer.profile' }" :to="{ name: 'buyer.profile' }">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
          <circle cx="12" cy="7" r="4" />
        </svg>
        Profil Saya
      </router-link>
    </div>

    <div class="nav-section">
      <div class="nav-section-label">Eksplorasi</div>
      <router-link class="nav-item" :class="{ active: route.name === 'buyer.stores' }" :to="{ name: 'buyer.stores' }">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
        </svg>
        Toko UMKM
      </router-link>
      <router-link class="nav-item" :class="{ active: route.name === 'buyer.popular' }" :to="{ name: 'buyer.popular' }">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
        </svg>
        Produk Populer
      </router-link>
    </div>

    <div class="sidebar-user">
      <div class="avatar">{{ userInitials }}</div>
      <div class="sidebar-user-info">
        <div class="user-name">{{ authStore.user?.name || 'Pembeli' }}</div>
        <div class="user-role">Pembeli</div>
      </div>
      <button class="logout-icon-btn" title="Keluar" @click="handleLogout">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
          <polyline points="16 17 21 12 16 7" />
          <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { buyerApi } from '@/services/api/buyerApi'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const unreadCount = ref(0)
const activeOrdersCount = ref(0)

const isDashboardActive = computed(() => {
  const dashboardRoutes = [
    'buyer.dashboard',
    'buyer.store-profile',
    'buyer.product-detail',
    'buyer.order-form'
  ]
  return dashboardRoutes.includes(route.name)
})

const userInitials = computed(() => {
  const name = authStore.user?.name || 'P'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
})

async function fetchUnreadNotificationsCount() {
  try {
    const res = await buyerApi.getNotifications()
    if (res.data.success) {
      unreadCount.value = res.data.data.filter(n => n.is_read === 0 || n.is_read === false).length
    }
  } catch (err) {
    console.error('Failed to fetch unread notifications count:', err)
  }
}

async function fetchActiveOrdersCount() {
  try {
    const res = await buyerApi.getOrders()
    if (res.data.success) {
      activeOrdersCount.value = res.data.data.filter(
        o => o.status === 'menunggu' || o.status === 'diproses'
      ).length
    }
  } catch (err) {
    console.error('Failed to fetch active orders count:', err)
  }
}

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
}

onMounted(() => {
  fetchUnreadNotificationsCount()
  fetchActiveOrdersCount()
})
</script>

<style scoped>
.sidebar {
  width: 240px;
  min-width: 240px;
  background: #fff;
  border-right: 1px solid var(--gray-100);
  display: flex;
  flex-direction: column;
  padding: 24px 16px;
  position: sticky;
  top: 0;
  height: 100vh;
  box-sizing: border-box;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px 20px;
}

.program-logo-sm {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, var(--brand-500), var(--brand-700));
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 16px;
  font-weight: 800;
  box-shadow: var(--shadow-sm);
}

.brand-text {
  font-family: 'Outfit', 'Inter', sans-serif;
  font-size: 18px;
  font-weight: 700;
  color: var(--brand-700);
  letter-spacing: -0.3px;
}

.nav-section {
  margin-bottom: 24px;
}

.nav-section-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .6px;
  color: var(--gray-400);
  padding: 0 12px;
  margin-bottom: 8px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 12px;
  border-radius: var(--radius-sm);
  font-size: 14px;
  font-weight: 500;
  color: var(--gray-600);
  cursor: pointer;
  transition: all .15s;
  text-decoration: none;
}

.nav-item svg {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
  opacity: .7;
}

.nav-item:hover {
  background: var(--gray-50);
  color: var(--gray-800);
}

.nav-item.active {
  background: var(--brand-50);
  color: var(--brand-700);
  font-weight: 600;
}

.nav-item.active svg {
  opacity: 1;
}

.nav-badge {
  margin-left: auto;
  background: var(--red-400);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: var(--radius-full);
}

.sidebar-user {
  margin-top: auto;
  border-top: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 12px 0;
}

.sidebar-user-info {
  flex: 1;
  min-width: 0;
}

.logout-icon-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: none;
  color: var(--gray-400);
  cursor: pointer;
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .15s;
  flex-shrink: 0;
}

.logout-icon-btn:hover {
  background: var(--red-50);
  color: var(--red-400);
}

.logout-icon-btn svg {
  width: 18px;
  height: 18px;
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--brand-100);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  color: var(--brand-700);
  flex-shrink: 0;
}

.user-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-800);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: 11px;
  color: var(--gray-400);
}
</style>