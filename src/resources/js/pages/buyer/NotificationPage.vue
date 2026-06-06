<template>
  <div class="page-wrap">

    <!-- ── Header ── -->
    <header class="detail-header">
      <button class="btn-back" @click="$router.back()" aria-label="Kembali">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="header-title">Notifikasi Saya</span>
      <div style="width:36px;"></div>
    </header>

    <div class="page-content">
      <!-- Loading State -->
      <div v-if="isLoading" class="loading-state">
        <div class="spinner"></div>
        <p>Memuat notifikasi...</p>
      </div>

      <!-- Notifications List -->
      <div v-else-if="notifications.length > 0" class="notif-list">
        <div
          v-for="notif in notifications"
          :key="notif.id"
          :class="['notif-item', !notif.is_read && 'notif-item--unread']"
          @click="markRead(notif)"
        >
          <div class="notif-icon">
            <span class="emoji">{{ getNotifEmoji(notif.type) }}</span>
          </div>
          <div class="notif-body">
            <div class="notif-header">
              <h3 class="notif-title">{{ notif.title }}</h3>
              <span class="notif-time">{{ formatTime(notif.created_at) }}</span>
            </div>
            <p class="notif-message">{{ notif.message }}</p>
            <span class="order-ref">ID Pesanan: {{ notif.order_id }}</span>
          </div>
          <div v-if="!notif.is_read" class="unread-dot" title="Tandai dibaca"></div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-icon">🔔</div>
        <p class="empty-title">Tidak ada notifikasi</p>
        <p class="empty-desc">Semua pemberitahuan pesanan Anda akan muncul di sini.</p>
        <button class="btn-back-shop" @click="$router.push({ name: 'buyer.products' })">
          Mulai Belanja
        </button>
      </div>
    </div>

    <!-- ── Bottom Nav ── -->
    <nav class="bottom-nav">
      <button class="nav-btn" @click="$router.push({ name: 'buyer.products' })">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Beranda
      </button>
      <button class="nav-btn" @click="$router.push({ name: 'buyer.orders' })">
        <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        Pesanan
      </button>
      <button class="nav-btn nav-btn--active">
        <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
        Notifikasi
        <span v-if="unreadCount > 0" class="nav-badge">{{ unreadCount }}</span>
      </button>
      <button class="nav-btn">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profil
      </button>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import apiClient from '@/services/api'

const notifications = ref([])
const isLoading = ref(false)

const unreadCount = computed(() =>
  notifications.value.filter(n => !n.is_read).length
)

async function fetchNotifications() {
  isLoading.value = true
  try {
    const { data } = await apiClient.get('/notifications')
    notifications.value = data.data
  } catch (err) {
    console.error('Gagal mengambil notifikasi:', err)
  } finally {
    isLoading.value = false
  }
}

async function markRead(notif) {
  if (notif.is_read) return
  try {
    await apiClient.put(`/notifications/${notif.id}/read`)
    notif.is_read = true
  } catch (err) {
    console.error('Gagal menandai notifikasi sebagai dibaca:', err)
  }
}

onMounted(() => {
  fetchNotifications()
})

function getNotifEmoji(type) {
  const map = {
    order_received: '📦',
    order_processed: '⏳',
    order_completed: '✅',
    order_cancelled: '❌',
  }
  return map[type] || '🔔'
}

function formatTime(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day:'numeric', month:'short' }) + ' ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' })
}
</script>

<style scoped>
.page-wrap {
  min-height: 100vh;
  background: #f0f4f8;
  max-width: 480px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  padding-bottom: 72px;
}

/* Header */
.detail-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px;
  background: linear-gradient(135deg, #1a7fc4 0%, #1565a8 100%);
  position: sticky; top: 0; z-index: 100;
  box-shadow: 0 2px 8px rgba(21,101,168,0.2);
}
.header-title { font-size: 16px; font-weight: 700; color: #fff; }
.btn-back {
  width: 36px; height: 36px;
  background: rgba(255,255,255,0.15);
  border: none; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #fff;
}
.btn-back svg {
  width: 20px; height: 20px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

.page-content {
  flex: 1;
  padding: 12px;
}

/* Loading state */
.loading-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 48px 0; gap: 12px; color: #6b7280; font-size: 14px;
}
.spinner {
  width: 28px; height: 28px;
  border: 3px solid rgba(21,101,168,0.15);
  border-top-color: #1565a8;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Notification Items */
.notif-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.notif-item {
  background: #fff;
  border-radius: 12px;
  padding: 14px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  position: relative;
  cursor: pointer;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  transition: transform 0.15s, box-shadow 0.15s;
}
.notif-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
}
.notif-item--unread {
  background: #eff6ff;
  border-left: 4px solid #1565a8;
}
.notif-icon {
  width: 40px; height: 40px;
  background: #f1f5f9;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; flex-shrink: 0;
}
.notif-item--unread .notif-icon {
  background: #dbeafe;
}
.notif-body {
  flex: 1;
  min-width: 0;
}
.notif-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 8px;
  margin-bottom: 4px;
}
.notif-title {
  font-size: 13px; font-weight: 700; color: #111827;
}
.notif-time {
  font-size: 10px; color: #9ca3af; white-space: nowrap;
}
.notif-message {
  font-size: 12px; color: #4b5563; line-height: 1.5; margin-bottom: 6px;
}
.order-ref {
  font-size: 10px; font-weight: 600; color: #1565a8;
  background: #dbeafe; padding: 2px 8px; border-radius: 100px;
}

.unread-dot {
  width: 8px; height: 8px;
  background: #3b82f6;
  border-radius: 50%;
  position: absolute;
  top: 14px; right: 14px;
}

/* Empty state */
.empty-state {
  display: flex; flex-direction: column; align-items: center;
  padding: 48px 24px; gap: 10px;
}
.empty-icon { font-size: 52px; }
.empty-title { font-size: 18px; font-weight: 700; color: #1f2937; }
.empty-desc  { font-size: 13px; color: #6b7280; text-align: center; }
.btn-back-shop {
  margin-top: 8px; padding: 11px 28px;
  background: #1565a8; color: #fff;
  border: none; border-radius: 12px;
  font-family: inherit; font-size: 14px; font-weight: 700;
  cursor: pointer;
}

/* Bottom Nav */
.bottom-nav {
  position: fixed;
  bottom: 0; left: 50%;
  transform: translateX(-50%);
  width: 100%; max-width: 480px;
  background: #fff;
  border-top: 1px solid #e5e7eb;
  display: flex;
  z-index: 200;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
}
.nav-btn {
  flex: 1; padding: 8px 4px 10px;
  background: none; border: none; cursor: pointer;
  display: flex; flex-direction: column; align-items: center; gap: 3px;
  font-size: 10px; font-weight: 500; font-family: inherit;
  color: #9ca3af; position: relative;
  transition: color 0.15s;
}
.nav-btn svg {
  width: 20px; height: 20px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
.nav-btn--active { color: #1565a8; }
.nav-badge {
  position: absolute;
  top: 6px; right: calc(50% - 16px);
  width: 14px; height: 14px;
  background: #ef4444; color: #fff;
  font-size: 9px; font-weight: 700;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}
</style>