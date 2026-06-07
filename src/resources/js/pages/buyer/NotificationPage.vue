<template>
  <div class="app-layout">
    <!-- ══ SIDEBAR ══ -->
    <aside class="sidebar">
      <div class="sidebar-logo">
        <div class="program-logo-sm">K</div>
        <div class="brand-text">Kulaan.id</div>
      </div>
      <div class="nav-section">
        <div class="nav-section-label">Menu Utama</div>
        <router-link class="nav-item" :to="{ name: 'buyer.dashboard' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.35-4.35" />
          </svg>
          Cari Produk
        </router-link>
        <router-link class="nav-item" :to="{ name: 'buyer.orders' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 0 1-8 0" />
          </svg>
          Pesanan Saya
          <span v-if="activeOrdersCount > 0" class="nav-badge">{{ activeOrdersCount }}</span>
        </router-link>
        <router-link class="nav-item active" :to="{ name: 'buyer.notifications' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
          </svg>
          Notifikasi
          <span v-if="unreadCount > 0" class="nav-badge" style="background:var(--brand-500);">{{ unreadCount }}</span>
        </router-link>
        <router-link class="nav-item" :to="{ name: 'buyer.profile' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
          Profil Saya
        </router-link>
      </div>
      <div class="sidebar-user">
        <div class="avatar">{{ userInitials }}</div>
        <div class="sidebar-user-info">
          <div class="user-name">{{ authStore.user?.name || 'Pembeli' }}</div>
          <div class="user-role">Pembeli</div>
        </div>
        <button class="logout-icon-btn" @click="handleLogout" title="Keluar">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
        </button>
      </div>
    </aside>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="main-content">
      <div style="max-width: 700px; margin: 0 auto;">
        <div class="page-header">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="page-title">Notifikasi</h1>
              <p class="page-sub">{{ unreadCount }} notifikasi belum dibaca</p>
            </div>
            <button class="btn-outline" @click="markAllAsRead" :disabled="unreadCount === 0">
              Tandai Semua Dibaca
            </button>
          </div>
        </div>

        <div v-if="loading" class="loading-state">
          <p>Memuat notifikasi...</p>
        </div>

        <div v-else-if="notifications.length === 0" class="empty-state">
          <div class="empty-icon">🔔</div>
          <h3>Tidak ada notifikasi</h3>
          <p>Notifikasi tentang status pesanan Anda akan muncul di sini.</p>
        </div>

        <div v-else class="notif-list">
          <div
            v-for="notif in notifications"
            :key="notif.id"
            class="notif-item"
            :class="{ unread: !notif.isRead }"
            @click="markSingleAsRead(notif)"
          >
            <div :class="['notif-icon-wrap', notif.iconClass]">{{ notif.icon }}</div>
            <div class="notif-content">
              <div class="notif-title">{{ notif.title }}</div>
              <div class="notif-body">{{ notif.body }}</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;">
              <div class="notif-time">{{ notif.time }}</div>
              <div v-if="!notif.isRead" class="notif-dot"></div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { buyerApi } from '@/services/api/buyerApi'

const router = useRouter()
const authStore = useAuthStore()

const rawNotifications = ref([])
const loading = ref(true)
const activeOrdersCount = ref(0)

const userInitials = computed(() => {
  const name = authStore.user?.name || 'P'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
})

const notifications = computed(() => {
  return rawNotifications.value.map(notif => parseNotification(notif))
})

const unreadCount = computed(() => {
  return rawNotifications.value.filter(n => n.is_read === 0 || n.is_read === false).length
})

function formatRelativeTime(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diffMs = now - date

  if (diffMs < 0) return 'Baru saja'

  const diffSecs = Math.floor(diffMs / 1000)
  if (diffSecs < 60) return 'Baru saja'

  const diffMins = Math.floor(diffSecs / 60)
  if (diffMins < 60) return `${diffMins} menit lalu`

  const diffHours = Math.floor(diffMins / 60)
  if (diffHours < 24) return `${diffHours} jam lalu`

  const diffDays = Math.floor(diffHours / 24)
  if (diffDays === 1) return 'Kemarin'
  if (diffDays < 7) return `${diffDays} hari lalu`

  const diffWeeks = Math.floor(diffDays / 7)
  if (diffWeeks < 4) return `${diffWeeks} minggu lalu`

  const diffMonths = Math.floor(diffDays / 30)
  return `${diffMonths} bulan lalu`
}

function parseNotification(notif) {
  const msg = notif.message || ''
  let icon = '📦'
  let iconClass = 'info'
  let title = 'Notifikasi'

  if (msg.includes('berhasil dibuat')) {
    title = 'Pesanan Berhasil Dibuat'
    icon = '📦'
    iconClass = 'info'
  } else if (msg.includes('diubah menjadi: Diproses')) {
    title = 'Pesanan Dikonfirmasi Penjual'
    icon = '✅'
    iconClass = 'success'
  } else if (msg.includes('diubah menjadi: Selesai')) {
    title = 'Pesanan Selesai!'
    icon = '🎉'
    iconClass = 'success'
  } else if (msg.includes('diubah menjadi: Dibatalkan') || msg.includes('ditolak')) {
    title = 'Pesanan Dibatalkan/Ditolak'
    icon = '⚠️'
    iconClass = 'warning'
  } else if (msg.includes('Pesanan baru masuk')) {
    title = 'Pesanan Baru Masuk'
    icon = '📥'
    iconClass = 'success'
  }

  return {
    id: notif.id_notification,
    title,
    body: msg,
    icon,
    iconClass,
    isRead: notif.is_read === 1 || notif.is_read === true,
    time: formatRelativeTime(notif.created_at),
  }
}

async function fetchNotifications() {
  loading.value = true
  try {
    const res = await buyerApi.getNotifications()
    if (res.data.success) {
      rawNotifications.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to fetch notifications:', err)
  } finally {
    loading.value = false
  }
}

async function markAllAsRead() {
  try {
    const res = await buyerApi.markAllNotificationsAsRead()
    if (res.data.success) {
      rawNotifications.value = rawNotifications.value.map(n => ({
        ...n,
        is_read: 1
      }))
    }
  } catch (err) {
    console.error('Failed to mark all as read:', err)
  }
}

async function markSingleAsRead(notif) {
  if (notif.isRead) return
  try {
    const res = await buyerApi.markNotificationAsRead(notif.id)
    if (res.data.success) {
      const found = rawNotifications.value.find(n => n.id_notification === notif.id)
      if (found) {
        found.is_read = 1
      }
    }
  } catch (err) {
    console.error('Failed to mark notification as read:', err)
  }
}

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
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
    console.error('Failed to load active orders count:', err)
  }
}

onMounted(() => {
  fetchNotifications()
  fetchActiveOrdersCount()
})
</script>

<style scoped>
:root {
  --brand-50: #E8F1FB; --brand-100: #C8DBED; --brand-200: #9CBEDD; --brand-300: #6FA0CC;
  --brand-400: #3D7DBD; --brand-500: rgb(24, 95, 165); --brand-600: rgb(36, 103, 170);
  --brand-700: #175F9E; --brand-800: #124F85; --brand-900: #0D3D68;
  --amber-400: #E6962A; --amber-50: #FDF3E3; --red-400: #E24B4A; --red-50: #FCEBEB;
  --blue-400: #378ADD; --blue-50: #E6F1FB; --purple-400: #7F77DD; --purple-50: #EEEDFE;
  --gray-0: #FAFAF9; --gray-50: #F4F3F0; --gray-100: #E8E7E2; --gray-200: #CFCEC7;
  --gray-400: #8A8980; --gray-500: #717066; --gray-600: #5C5B54; --gray-700: #3E3D38; --gray-800: #282724; --gray-900: #161514;
  --radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px; --radius-full: 9999px;
  --shadow-xs: 0 1px 2px rgba(0, 0, 0, .06);
}

.flex { display: flex; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }

.app-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
  background-color: var(--gray-50);
  background-image: radial-gradient(var(--gray-200) 1px, transparent 1px);
  background-size: 20px 20px;
  color: var(--gray-800);
}

/* Sidebar */
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
.sidebar-logo { display: flex; align-items: center; gap: 10px; padding: 8px 12px 20px; }
.program-logo-sm { width: 40px; height: 40px; background: linear-gradient(135deg, var(--brand-500), var(--brand-700)); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 16px; font-weight: 800; box-shadow: var(--shadow-xs); }
.brand-text { font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700; color: var(--brand-700); letter-spacing: -0.3px; }
.nav-section { margin-bottom: 24px; }
.nav-section-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: var(--gray-400); padding: 0 12px; margin-bottom: 8px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; color: var(--gray-600); cursor: pointer; transition: all .15s; text-decoration: none; }
.nav-item svg { width: 18px; height: 18px; flex-shrink: 0; opacity: .7; }
.nav-item:hover { background: var(--gray-50); color: var(--gray-800); }
.nav-item.active { background: var(--brand-50); color: var(--brand-700); font-weight: 600; }
.nav-item.active svg { opacity: 1; }
.nav-badge { margin-left: auto; background: var(--red-400); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: var(--radius-full); }

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
.avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--brand-100); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: var(--brand-700); flex-shrink: 0; }
.user-name { font-size: 13px; font-weight: 600; color: var(--gray-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-role { font-size: 11px; color: var(--gray-400); }

/* Main Content */
.main-content { flex: 1; background: var(--gray-50); padding: 32px; box-sizing: border-box; min-width: 0; }
.page-header { margin-bottom: 28px; }
.page-title { font-size: 22px; font-weight: 700; color: var(--gray-900); letter-spacing: -.5px; }
.page-sub { font-size: 14px; color: var(--gray-400); margin-top: 4px; }

/* Notification List Specific */
.notif-list { display: flex; flex-direction: column; gap: 12px; max-width: 700px; margin: 0 auto; }

.notif-item { display: flex; align-items: flex-start; gap: 14px; padding: 16px 20px; border-radius: var(--radius-md); background: #fff; border: 1px solid var(--gray-100); transition: all .15s; cursor: pointer; }
.notif-item:hover { background: var(--gray-50); }
.notif-item.unread { background: var(--brand-50); border-color: var(--brand-100); }

.notif-icon-wrap { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 18px; }
.notif-icon-wrap.success { background: var(--brand-100); }
.notif-icon-wrap.warning { background: var(--amber-50); }
.notif-icon-wrap.error { background: var(--red-50); }
.notif-icon-wrap.info { background: var(--blue-50); }

.notif-content { flex: 1; min-width: 0; }
.notif-title { font-size: 14px; font-weight: 600; color: var(--gray-900); margin-bottom: 3px; }
.notif-item.unread .notif-title { color: var(--brand-800); }
.notif-body { font-size: 13px; color: var(--gray-500); line-height: 1.5; word-wrap: break-word; }

.notif-time { font-size: 12px; color: var(--gray-400); flex-shrink: 0; margin-top: 2px; }
.notif-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--brand-500); flex-shrink: 0; margin-top: 6px; }

.btn-outline { padding: 8px 16px; border-radius: var(--radius-sm); border: 1.5px solid var(--gray-100); background: #fff; font-size: 13px; font-weight: 600; color: var(--gray-600); cursor: pointer; transition: all 0.15s; }
.btn-outline:hover:not(:disabled) { background: var(--gray-50); border-color: var(--gray-200); }
.btn-outline:disabled { opacity: 0.5; cursor: not-allowed; }

.loading-state, .empty-state {
  text-align: center;
  padding: 40px 20px;
  background: #fff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-100);
}
.empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
}
.empty-state h3 {
  font-size: 18px;
  color: var(--gray-900);
  margin: 0 0 6px;
  font-weight: 600;
}
.empty-state p {
  color: var(--gray-400);
  font-size: 14px;
  margin: 0;
}
</style>
