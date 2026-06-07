<template>
  <aside class="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
      <div class="logo-icon">K</div>
      <span class="logo-text">Kulaan.id</span>
    </div>

    <!-- Nav -->
    <nav class="nav-section">
      <div class="nav-label">Toko Saya</div>

      <RouterLink :to="{ name: 'seller.dashboard' }" class="nav-item" active-class="active">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <rect x="3" y="3" width="7" height="7" stroke-width="1.8"/>
          <rect x="14" y="3" width="7" height="7" stroke-width="1.8"/>
          <rect x="14" y="14" width="7" height="7" stroke-width="1.8"/>
          <rect x="3" y="14" width="7" height="7" stroke-width="1.8"/>
        </svg>
        Dashboard
        <span v-if="newOrdersCount > 0" class="nav-badge">{{ newOrdersCount }}</span>
      </RouterLink>

      <RouterLink :to="{ name: 'seller.products' }" class="nav-item" active-class="active">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <rect x="2" y="2" width="20" height="20" rx="2" stroke-width="1.8"/>
          <path d="M8 12h8M12 8v8" stroke-width="1.8"/>
        </svg>
        Kelola Produk
      </RouterLink>

      <RouterLink :to="{ name: 'store.setup' }" class="nav-item" active-class="active">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke-width="1.8"/>
        </svg>
        Profil Toko
      </RouterLink>
    </nav>

    <!-- User + tombol keluar -->
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="avatar">{{ initials }}</div>
        <div class="user-meta">
          <div class="user-name">{{ authStore.user?.name || 'Pemilik' }}</div>
          <div class="user-role">Pemilik UMKM</div>
        </div>
      </div>
      <button class="btn-logout" @click="handleLogout" title="Keluar">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" stroke-width="1.8" stroke-linecap="round"/>
          <polyline points="16 17 21 12 16 7" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          <line x1="21" y1="12" x2="9" y2="12" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
      </button>
    </div>

  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const props = defineProps({
  newOrdersCount: { type: Number, default: 0 },
})

const router    = useRouter()
const authStore = useAuthStore()

const initials = computed(() => {
  const name = authStore.user?.name || ''
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || 'BS'
})

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>

<style scoped>
.sidebar {
  width: 240px;
  min-width: 240px;
  flex-shrink: 0;
  background: #fff;
  border-right: 1px solid #E8E7E2;
  display: flex;
  flex-direction: column;
  padding: 24px 16px;
  position: sticky;
  top: 0;
  height: 100vh;
}

/* Logo */
.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px 24px;
}
.logo-icon {
  width: 32px; height: 32px;
  background: linear-gradient(135deg, rgb(24,95,165), #175F9E);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 14px; font-weight: 800;
  flex-shrink: 0;
}
.logo-text {
  font-family: 'Outfit', sans-serif;
  font-size: 18px; font-weight: 700;
  color: #175F9E; letter-spacing: -0.3px;
}

/* Nav */
.nav-section { flex: 1; }
.nav-label {
  font-size: 10px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .6px;
  color: #8A8980;
  padding: 0 12px; margin-bottom: 8px;
}

.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 12px;
  border-radius: 8px;
  font-size: 14px; font-weight: 500;
  color: #5C5B54;
  text-decoration: none;
  transition: all .15s;
  margin-bottom: 2px;
  position: relative;
}
.nav-item svg {
  width: 18px; height: 18px;
  flex-shrink: 0; opacity: .7;
}
.nav-item:hover {
  background: #F4F3F0;
  color: #282724;
}
.nav-item.active {
  background: #E8F1FB;
  color: #175F9E;
  font-weight: 600;
}
.nav-item.active svg { opacity: 1; }

.nav-badge {
  margin-left: auto;
  background: #E24B4A;
  color: #fff;
  font-size: 10px; font-weight: 700;
  padding: 2px 6px;
  border-radius: 9999px;
  min-width: 18px;
  text-align: center;
}

/* Footer */
.sidebar-footer {
  border-top: 1px solid #E8E7E2;
  padding-top: 12px;
  display: flex; align-items: center; gap: 8px;
}
.user-info {
  display: flex; align-items: center; gap: 10px;
  flex: 1; min-width: 0;
}
.avatar {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: #FDF3E3; color: #854F0B;
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; font-weight: 700;
  flex-shrink: 0;
}
.user-meta { min-width: 0; }
.user-name {
  font-size: 13px; font-weight: 600;
  color: #282724;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.user-role { font-size: 11px; color: #8A8980; }

.btn-logout {
  width: 32px; height: 32px;
  border-radius: 8px;
  border: 1px solid #E8E7E2;
  background: transparent;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; flex-shrink: 0;
  transition: all .15s;
  color: #8A8980;
}
.btn-logout svg { width: 16px; height: 16px; }
.btn-logout:hover {
  background: #FCEBEB;
  border-color: #E24B4A;
  color: #E24B4A;
}
</style>