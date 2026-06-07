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
        <router-link class="nav-item active" :to="{ name: 'buyer.orders' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 0 1-8 0" />
          </svg>
          Pesanan Saya
          <span v-if="activeOrdersCount > 0" class="nav-badge">{{ activeOrdersCount }}</span>
        </router-link>
        <router-link class="nav-item" :to="{ name: 'buyer.notifications' }">
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
      <div style="max-width: 800px; margin: 0 auto;">
        <div class="page-header">
          <h1 class="page-title">Pesanan Saya</h1>
          <p class="page-sub">Pantau status pesanan yang telah Anda beli secara real-time</p>
        </div>

        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Memuat daftar pesanan...</p>
        </div>

        <div v-else-if="orders.length === 0" class="empty-state">
          <div class="empty-icon">🛍️</div>
          <h3>Belum ada pesanan</h3>
          <p>Mulailah berbelanja untuk mendukung UMKM Kelurahan Jebres!</p>
          <router-link :to="{ name: 'buyer.dashboard' }" class="btn-shop-now">Cari Produk</router-link>
        </div>

        <div v-else class="orders-list">
          <div v-for="order in orders" :key="order.id_order" class="order-card">
            <!-- Card Header -->
            <div class="order-card-header">
              <div>
                <span class="order-id">#ORD-{{ String(order.id_order).padStart(5, '0') }}</span>
                <span class="order-date-label">{{ formatDate(order.order_date) }}</span>
              </div>
              <span :class="['status-badge', statusClass(order.status)]">
                <span class="status-dot"></span>
                {{ statusLabel(order.status) }}
              </span>
            </div>

            <!-- Card Body (Items) -->
            <div class="order-card-body">
              <div v-for="item in order.items" :key="item.id_order_detail" class="order-item-row">
                <div class="item-thumb">
                  <img 
                    v-if="item.product?.image_url" 
                    :src="item.product.image_url" 
                    :alt="item.product.name" 
                    class="item-thumb-img" 
                  />
                  <span v-else class="item-emoji">{{ getProductEmoji(item.product?.id_product) }}</span>
                </div>
                <div class="item-details">
                  <div class="item-name">{{ item.product?.name || 'Produk' }}</div>
                  <div class="item-store">{{ item.product?.store?.store_name || 'Toko UMKM' }}</div>
                  <div class="item-qty">{{ item.quantity }} {{ item.product?.unit || 'pcs' }} &times; Rp {{ formatPrice(item.price_at_purchase) }}</div>
                </div>
                <div class="item-subtotal">Rp {{ formatPrice(item.price_at_purchase * item.quantity) }}</div>
              </div>
            </div>

            <!-- Card Footer -->
            <div class="order-card-footer">
              <div class="order-summary-info">
                <div class="info-line">
                  <span class="info-label">Metode Pembayaran:</span>
                  <span class="info-value">{{ paymentLabel(order.payment_method) }}</span>
                </div>
                <div v-if="order.note" class="info-line">
                  <span class="info-label">Catatan:</span>
                  <span class="info-value italic">"{{ order.note }}"</span>
                </div>
              </div>
              <div class="order-action-section">
                <div class="total-payment-wrap">
                  <span class="total-label">Total Belanja</span>
                  <span class="total-price">Rp {{ formatPrice(order.total_order) }}</span>
                </div>
                <a 
                  v-if="shouldShowWhatsApp(order)"
                  :href="getWhatsAppLink(order)"
                  target="_blank"
                  class="btn-wa-action"
                >
                  <svg viewBox="0 0 24 24" fill="#fff" width="16" height="16" style="margin-right:6px;">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                  </svg>
                  Tanya Penjual
                </a>
              </div>
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

const orders = ref([])
const unreadCount = ref(0)
const loading = ref(true)

const userInitials = computed(() => {
  const name = authStore.user?.name || 'P'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
})

const activeOrdersCount = computed(() => {
  return orders.value.filter(o => o.status === 'menunggu' || o.status === 'diproses').length
})

const emojis = ['🍱', '🍛', '🧆', '🍲', '🥙', '🍗', '🥟', '🍚', '🥘', '🍜', '🥗', '🍣']
function getProductEmoji(id) {
  const idx = id ? id % emojis.length : 0
  return emojis[idx]
}

function formatPrice(price) {
  return Number(price).toLocaleString('id-ID')
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  const cleanStr = dateStr.replace('T', ' ').split('.')[0].replace('Z', '')
  const date = new Date(cleanStr)
  return date.toLocaleString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function statusLabel(status) {
  return {
    menunggu: 'Menunggu',
    diproses: 'Diproses',
    selesai: 'Selesai',
    dibatalkan: 'Dibatalkan',
  }[status] ?? status
}

function statusClass(status) {
  return {
    menunggu: 'waiting',
    diproses: 'processing',
    selesai: 'done',
    dibatalkan: 'cancelled',
  }[status] ?? ''
}

function paymentLabel(method) {
  return { cod: 'COD (Bayar di Tempat)', transfer: 'Transfer Bank' }[method] ?? method
}

function shouldShowWhatsApp(order) {
  const allowed = ['menunggu', 'diproses']
  return allowed.includes(order.status) && order.items && order.items.length > 0 && order.items[0].product?.store?.phone_number
}

function getWhatsAppLink(order) {
  const item = order.items[0]
  const storePhone = item.product?.store?.phone_number
  if (!storePhone) return '#'
  
  const cleanedPhone = storePhone.replace(/[^0-9]/g, '')
  const firstProductName = item.product?.name || 'Produk'
  const remainingText = order.items.length > 1 ? ` dan ${order.items.length - 1} produk lainnya` : ''
  const statusStr = statusLabel(order.status).toUpperCase()

  const msg = encodeURIComponent(
    `Halo, saya ingin menanyakan status pesanan saya #ORD-${String(order.id_order).padStart(5, '0')} (${firstProductName}${remainingText}) senilai Rp ${formatPrice(order.total_order)} yang berstatus ${statusStr} di Kulaan.id.`
  )
  return `https://wa.me/${cleanedPhone}?text=${msg}`
}

async function fetchOrdersData() {
  loading.value = true
  try {
    const res = await buyerApi.getOrders()
    if (res.data.success) {
      orders.value = res.data.data
    }
  } catch (err) {
    console.error('Failed to load orders:', err)
  } finally {
    loading.value = false
  }
}

async function fetchUnreadNotificationsCount() {
  try {
    const res = await buyerApi.getNotifications()
    if (res.data.success) {
      unreadCount.value = res.data.data.filter(n => n.is_read === 0 || n.is_read === false).length
    }
  } catch (err) {
    console.error('Failed to load unread count:', err)
  }
}

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
}

onMounted(() => {
  fetchOrdersData()
  fetchUnreadNotificationsCount()
})
</script>

<style scoped>
:root {
  --brand-50: #E8F1FB; --brand-100: #C8DBED; --brand-200: #9CBEDD; --brand-300: #6FA0CC;
  --brand-400: #3D7DBD; --brand-500: rgb(24, 95, 165); --brand-600: rgb(36, 103, 170);
  --brand-700: #175F9E; --brand-800: #124F85; --brand-900: #0D3D68;
  --amber-400: #E6962A; --amber-50: #FDF3E3; --red-400: #E24B4A; --red-50: #FCEBEB;
  --gray-0: #FAFAF9; --gray-50: #F4F3F0; --gray-100: #E8E7E2; --gray-200: #CFCEC7;
  --gray-400: #8A8980; --gray-500: #717066; --gray-600: #5C5B54; --gray-700: #3E3D38; --gray-800: #282724; --gray-900: #161514;
  --radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px; --radius-full: 9999px;
  --shadow-xs: 0 1px 2px rgba(0, 0, 0, .06);
}

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

/* Orders list */
.orders-list { display: flex; flex-direction: column; gap: 20px; }
.order-card { background: #fff; border: 1px solid var(--gray-100); border-radius: var(--radius-lg); box-shadow: var(--shadow-xs); display: flex; flex-direction: column; overflow: hidden; }

.order-card-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid var(--gray-50); background: #fbfbfa; }
.order-id { font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 700; color: var(--gray-900); margin-right: 12px; }
.order-date-label { font-size: 12px; color: var(--gray-400); }

/* Status Badges */
.status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
.status-badge.waiting { background: var(--amber-50); color: var(--amber-400); }
.status-badge.processing { background: var(--brand-50); color: var(--brand-700); }
.status-badge.done { background: #E6F4EA; color: #137333; }
.status-badge.cancelled { background: var(--red-50); color: var(--red-400); }
.status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

/* Items list */
.order-card-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; border-bottom: 1px solid var(--gray-50); }
.order-item-row { display: flex; align-items: center; gap: 14px; }
.item-thumb { width: 48px; height: 48px; border-radius: var(--radius-sm); background: var(--gray-50); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; overflow: hidden; }
.item-thumb-img { width: 100%; height: 100%; object-fit: cover; }
.item-details { flex: 1; min-width: 0; }
.item-name { font-size: 14px; font-weight: 600; color: var(--gray-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.item-store { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: .3px; font-weight: 500; margin-top: 1px; }
.item-qty { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
.item-subtotal { font-size: 14px; font-weight: 700; color: var(--gray-900); }

/* Footer */
.order-card-footer { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px; background: #fafafa; }
.order-summary-info { font-size: 12px; color: var(--gray-500); display: flex; flex-direction: column; gap: 4px; }
.info-line { display: flex; gap: 6px; }
.info-label { font-weight: 500; color: var(--gray-400); }
.info-value { color: var(--gray-700); }
.order-action-section { display: flex; align-items: center; gap: 20px; }
.total-payment-wrap { display: flex; flex-direction: column; align-items: flex-end; }
.total-label { font-size: 11px; color: var(--gray-400); text-transform: uppercase; letter-spacing: .3px; }
.total-price { font-size: 16px; font-weight: 800; color: var(--brand-700); }

.btn-wa-action { display: inline-flex; align-items: center; padding: 8px 16px; border-radius: var(--radius-sm); background: #25D366; color: #fff; font-size: 13px; font-weight: 700; text-decoration: none; transition: background 0.15s; border: none; cursor: pointer; }
.btn-wa-action:hover { background: #20ba59; }

.loading-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; padding: 80px 0; color: var(--gray-400); }
.spinner { width: 32px; height: 32px; border: 3px solid var(--gray-100); border-top-color: var(--brand-500); border-radius: 50%; animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.empty-state { text-align: center; padding: 60px 20px; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--gray-100); }
.empty-icon { font-size: 48px; margin-bottom: 16px; }
.empty-state h3 { font-size: 18px; color: var(--gray-900); margin: 0 0 6px; font-weight: 600; }
.empty-state p { color: var(--gray-400); font-size: 14px; margin-bottom: 20px; }
.btn-shop-now { display: inline-block; padding: 10px 24px; border-radius: var(--radius-sm); background: var(--brand-600); color: #fff; font-size: 14px; font-weight: 700; text-decoration: none; transition: background 0.15s; }
.btn-shop-now:hover { background: var(--brand-700); }
</style>
