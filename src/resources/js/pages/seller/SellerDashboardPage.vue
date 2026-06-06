<template>
  <div class="page-wrap">

    <!-- ── Header ── -->
    <header class="dash-header">
      <div class="header-brand">
        <span class="brand-logo">KULAAN<span class="brand-dot">.id</span></span>
        <span class="header-role-badge">Penjual</span>
      </div>
      <div class="header-actions">
        <button class="btn-icon" @click="$router.push({ name: 'seller.store' })" aria-label="Kelola Toko">
          <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
        </button>
      </div>
    </header>

    <!-- ── Store Info Banner ── -->
    <div class="store-banner">
      <div class="store-avatar">
        <span>{{ storeName[0] }}</span>
      </div>
      <div class="store-info">
        <h1 class="store-name">{{ storeName }}</h1>
        <p class="store-sub">{{ storeDistrict }} · {{ storeCategory }}</p>
        <div :class="['store-status-badge', storeStatus]">
          <div class="status-dot"></div>
          {{ storeStatusLabel }}
        </div>
      </div>
      <button class="btn-manage-store" @click="$router.push({ name: 'seller.store' })">
        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        Kelola
      </button>
    </div>

    <!-- ── Metrics ── -->
    <div class="metrics-row">
      <div class="metric-card">
        <span class="metric-icon">📦</span>
        <span class="metric-val">{{ metrics.totalOrders }}</span>
        <span class="metric-label">Total Pesanan</span>
      </div>
      <div class="metric-card">
        <span class="metric-icon">⏳</span>
        <span class="metric-val pending">{{ metrics.pendingOrders }}</span>
        <span class="metric-label">Menunggu</span>
      </div>
      <div class="metric-card">
        <span class="metric-icon">✅</span>
        <span class="metric-val success">{{ metrics.completedOrders }}</span>
        <span class="metric-label">Selesai</span>
      </div>
      <div class="metric-card">
        <span class="metric-icon">💰</span>
        <span class="metric-val revenue">{{ formatShortPrice(metrics.revenue) }}</span>
        <span class="metric-label">Pendapatan</span>
      </div>
    </div>

    <!-- ── Orders Section ── -->
    <div class="section-wrap">

      <!-- Order Filter -->
      <div class="order-filter-bar">
        <h2 class="section-title">Pesanan Masuk</h2>
        <div class="status-filter-tabs">
          <button
            v-for="tab in orderTabs"
            :key="tab.value"
            :class="['order-tab', activeOrderTab === tab.value && 'active']"
            @click="activeOrderTab = tab.value"
          >
            {{ tab.label }}
            <span v-if="tab.count > 0" :class="['order-tab-badge', tab.value]">{{ tab.count }}</span>
          </button>
        </div>
      </div>

      <!-- Search -->
      <div class="search-bar">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input v-model="searchQuery" type="search" placeholder="Cari nama pembeli atau produk..." class="search-input" />
      </div>

      <!-- Order cards -->
      <div v-if="filteredOrders.length === 0" class="empty-orders">
        <span style="font-size:40px;">📋</span>
        <p>Belum ada pesanan {{ activeOrderTab !== 'all' ? getStatusLabel(activeOrderTab) : '' }}</p>
      </div>

      <div v-else class="order-list">
        <div
          v-for="order in filteredOrders"
          :key="order.id"
          class="order-card"
        >
          <!-- Order header -->
          <div class="order-card-header">
            <div class="order-id-row">
              <span class="order-id">#{{ order.id.toString().padStart(4, '0') }}</span>
              <span :class="['order-status', order.status]">{{ getStatusLabel(order.status) }}</span>
            </div>
            <span class="order-time">{{ formatTime(order.created_at) }}</span>
          </div>

          <!-- Buyer info -->
          <div class="buyer-info-row">
            <div class="buyer-avatar">{{ order.buyer_name[0] }}</div>
            <div>
              <p class="buyer-name">{{ order.buyer_name }}</p>
              <p class="buyer-address">📍 {{ order.shipping_address }}</p>
            </div>
          </div>

          <!-- Items -->
          <div class="order-items">
            <div v-for="item in order.items" :key="item.product_id" class="order-item-row">
              <span class="item-emoji">{{ getCategoryEmoji(item.category) }}</span>
              <span class="item-name-text">{{ item.name }}</span>
              <span class="item-qty-text">x{{ item.qty }}</span>
              <span class="item-price-text">Rp {{ formatPrice(item.price * item.qty) }}</span>
            </div>
          </div>

          <!-- Footer: total + actions -->
          <div class="order-card-footer">
            <div class="order-footer-left">
              <span class="payment-method-badge" :class="order.payment_method">
                {{ order.payment_method === 'cod' ? '🤝 COD' : '🏦 Transfer' }}
              </span>
              <div class="order-total-row">
                <span class="total-label">Total</span>
                <span class="total-val">Rp {{ formatPrice(order.total) }}</span>
              </div>
            </div>

            <div class="order-actions">
              <!-- WA Contact button -->
              <a
                :href="`https://wa.me/${order.buyer_phone}?text=${encodeURIComponent(getWAMessage(order))}`"
                target="_blank" rel="noopener"
                class="btn-wa"
                aria-label="Hubungi via WhatsApp"
              >
                <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                WA
              </a>

              <!-- Status update dropdown -->
              <div class="status-update-wrap" v-if="order.status !== 'selesai' && order.status !== 'dibatalkan'">
                <select
                  v-model="order.status"
                  class="status-select"
                  @change="handleStatusChange(order)"
                >
                  <option value="menunggu">Menunggu</option>
                  <option value="diproses">Proses</option>
                  <option value="selesai">Selesai</option>
                  <option value="dibatalkan">Batalkan</option>
                </select>
              </div>
              <div v-else class="status-final">
                <span :class="['status-final-badge', order.status]">{{ getStatusLabel(order.status) }}</span>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div v-if="order.note" class="order-note">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <span>{{ order.note }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Spacer -->
    <div style="height:72px;"></div>

    <!-- Bottom Nav -->
    <nav class="bottom-nav">
      <button class="nav-btn nav-btn--active">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Pesanan
      </button>
      <button class="nav-btn" @click="$router.push({ name: 'seller.store' })">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
        Toko
      </button>
      <button class="nav-btn">
        <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
        Notifikasi
      </button>
      <button class="nav-btn">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profil
      </button>
    </nav>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { mockOrders } from '@/data/mockData'

// ── Store info (would come from authStore in real app) ────────
const storeName     = ref('Toko Batik Nusantara')
const storeDistrict = ref('Jebres')
const storeCategory = ref('Fashion & Batik')
const storeStatus   = ref('disetujui') // menunggu | disetujui | dibatalkan

const storeStatusLabel = computed(() => {
  const map = { menunggu: 'Menunggu Verifikasi', disetujui: 'Toko Aktif', dibatalkan: 'Ditolak' }
  return map[storeStatus.value] || storeStatus.value
})

// ── Metrics ───────────────────────────────────────────────────
const orders = ref(mockOrders || generateMockOrders())

const metrics = computed(() => ({
  totalOrders:     orders.value.length,
  pendingOrders:   orders.value.filter(o => o.status === 'menunggu').length,
  completedOrders: orders.value.filter(o => o.status === 'selesai').length,
  revenue:         orders.value.filter(o => o.status === 'selesai').reduce((s, o) => s + o.total, 0),
}))

// ── Order filters ─────────────────────────────────────────────
const activeOrderTab = ref('all')
const searchQuery    = ref('')

const orderTabs = computed(() => [
  { value: 'all',        label: 'Semua',   count: orders.value.length },
  { value: 'menunggu',   label: 'Baru',    count: orders.value.filter(o => o.status === 'menunggu').length },
  { value: 'diproses',   label: 'Proses',  count: orders.value.filter(o => o.status === 'diproses').length },
  { value: 'selesai',    label: 'Selesai', count: orders.value.filter(o => o.status === 'selesai').length },
])

const filteredOrders = computed(() => {
  let result = [...orders.value]
  if (activeOrderTab.value !== 'all') {
    result = result.filter(o => o.status === activeOrderTab.value)
  }
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase()
    result = result.filter(o =>
      o.buyer_name.toLowerCase().includes(q) ||
      o.items.some(i => i.name.toLowerCase().includes(q))
    )
  }
  return result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

// ── Actions ───────────────────────────────────────────────────
function handleStatusChange(order) {
  // Would call API in real app
  console.log(`Order #${order.id} status updated to: ${order.status}`)
}

function getWAMessage(order) {
  return `Halo ${order.buyer_name}, pesanan Anda #${order.id.toString().padStart(4,'0')} di Kulaan.id sedang ${getStatusLabel(order.status)}. Total: Rp ${formatPrice(order.total)}. Terima kasih!`
}

// ── Helpers ───────────────────────────────────────────────────
function getStatusLabel(status) {
  const map = { menunggu:'Menunggu', diproses:'Diproses', selesai:'Selesai', dibatalkan:'Dibatalkan' }
  return map[status] || status
}
function formatPrice(n) {
  return (n || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}
function formatShortPrice(n) {
  if (!n) return '0'
  if (n >= 1000000) return `${(n/1000000).toFixed(1)}jt`
  if (n >= 1000)    return `${(n/1000).toFixed(0)}rb`
  return n.toString()
}
function formatTime(dateStr) {
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' }) +
    ' · ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' })
}
function getCategoryEmoji(cat) {
  const map = { makanan_minuman:'🍱', fashion_batik:'👗', kerajinan:'🧶', kecantikan:'💄', pertanian:'🌾', jasa:'🛠️' }
  return map[cat] || '📦'
}

// Fallback mock orders jika mockData.js belum punya mockOrders
function generateMockOrders() {
  return [
    {
      id: 1001, buyer_name: 'Budi Santoso', buyer_phone: '628123456789',
      shipping_address: 'Jl. Adi Sucipto No. 10, Jebres',
      items: [{ product_id: 1, name: 'Batik Tulis Jebres', category: 'fashion_batik', qty: 2, price: 185000 }],
      total: 370000, status: 'menunggu', payment_method: 'transfer',
      note: 'Tolong dikemas dengan bubble wrap ya.',
      created_at: new Date(Date.now() - 3600000).toISOString(),
    },
    {
      id: 1002, buyer_name: 'Dewi Rahayu', buyer_phone: '628987654321',
      shipping_address: 'Jl. Surya No. 5, Jebres',
      items: [
        { product_id: 2, name: 'Kue Klepon Pandan', category: 'makanan_minuman', qty: 3, price: 25000 },
        { product_id: 3, name: 'Es Teh Jebres', category: 'makanan_minuman', qty: 5, price: 8000 },
      ],
      total: 115000, status: 'diproses', payment_method: 'cod',
      note: '',
      created_at: new Date(Date.now() - 86400000).toISOString(),
    },
    {
      id: 1003, buyer_name: 'Ahmad Fauzi', buyer_phone: '628555123456',
      shipping_address: 'Jl. Kebangkitan No. 3, Mojosongo',
      items: [{ product_id: 4, name: 'Kerajinan Bambu', category: 'kerajinan', qty: 1, price: 85000 }],
      total: 85000, status: 'selesai', payment_method: 'transfer',
      note: '',
      created_at: new Date(Date.now() - 172800000).toISOString(),
    },
  ]
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
}
@media (min-width: 768px) {
  .page-wrap { max-width: 1024px; }
}

/* Header */
.dash-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px;
  background: linear-gradient(135deg, #1a7fc4 0%, #1565a8 100%);
  position: sticky; top: 0; z-index: 100;
}
.header-brand { display: flex; align-items: center; gap: 10px; }
.brand-logo {
  font-size: 20px; font-weight: 800; color: #fff; letter-spacing: -0.5px;
}
.brand-dot { color: rgba(255,255,255,0.5); }
.header-role-badge {
  font-size: 10px; font-weight: 700;
  background: rgba(255,255,255,0.2); color: #fff;
  padding: 3px 8px; border-radius: 100px;
  border: 1px solid rgba(255,255,255,0.3);
}
.header-actions { display: flex; gap: 8px; }
.btn-icon {
  width: 36px; height: 36px;
  background: rgba(255,255,255,0.15); border: none;
  border-radius: 10px; display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #fff;
}
.btn-icon svg {
  width: 18px; height: 18px;
  fill: none; stroke: currentColor;
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}

/* Store banner */
.store-banner {
  background: linear-gradient(135deg, #1a7fc4 0%, #1565a8 100%);
  padding: 0 16px 20px;
  display: flex; align-items: center; gap: 12px;
}
.store-avatar {
  width: 52px; height: 52px;
  background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.4);
  border-radius: 14px; flex-shrink: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; font-weight: 800; color: #fff;
}
.store-info { flex: 1; }
.store-name { font-size: 16px; font-weight: 800; color: #fff; margin-bottom: 2px; }
.store-sub  { font-size: 12px; color: rgba(255,255,255,0.7); margin-bottom: 6px; }
.store-status-badge {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 11px; font-weight: 600;
  padding: 3px 8px; border-radius: 100px;
}
.store-status-badge.disetujui { background: rgba(110,231,183,0.2); color: #6EE7B7; border: 1px solid rgba(110,231,183,0.3); }
.store-status-badge.menunggu  { background: rgba(252,211,77,0.2); color: #FCD34D; border: 1px solid rgba(252,211,77,0.3); }
.store-status-badge.dibatalkan { background: rgba(252,165,165,0.2); color: #FCA5A5; border: 1px solid rgba(252,165,165,0.3); }
.status-dot {
  width: 6px; height: 6px; border-radius: 50%;
  background: currentColor;
}
.btn-manage-store {
  display: flex; align-items: center; gap: 5px;
  background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
  border-radius: 10px; padding: 8px 12px;
  font-size: 12px; font-weight: 600; color: #fff;
  cursor: pointer; font-family: inherit; flex-shrink: 0;
  transition: background 0.15s;
}
.btn-manage-store:hover { background: rgba(255,255,255,0.25); }
.btn-manage-store svg {
  width: 14px; height: 14px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}

/* Metrics */
.metrics-row {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 8px; padding: 12px 16px;
  background: #fff; border-bottom: 1px solid #e5e7eb;
}
.metric-card {
  display: flex; flex-direction: column; align-items: center;
  padding: 10px 4px; gap: 4px;
}
.metric-icon { font-size: 18px; }
.metric-val  { font-size: 18px; font-weight: 800; color: #111827; }
.metric-val.pending { color: #d97706; }
.metric-val.success { color: #16a34a; }
.metric-val.revenue { color: #1565a8; font-size: 15px; }
.metric-label { font-size: 10px; color: #6b7280; text-align: center; }

/* Section */
.section-wrap { padding: 0; }
@media (min-width: 768px) {
  .section-wrap { padding: 16px; }
  .order-list { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
}

/* Order filter */
.order-filter-bar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 16px 8px; background: #fff; border-bottom: 1px solid #e5e7eb;
  gap: 12px; flex-wrap: wrap;
}
.section-title { font-size: 15px; font-weight: 700; color: #111827; flex-shrink: 0; }
.status-filter-tabs { display: flex; gap: 4px; overflow-x: auto; }
.order-tab {
  display: flex; align-items: center; gap: 4px;
  padding: 5px 10px; border-radius: 100px;
  border: 1.5px solid #e5e7eb; background: #f9fafb;
  font-size: 12px; font-weight: 600; font-family: inherit;
  color: #6b7280; cursor: pointer; white-space: nowrap;
  transition: all 0.15s;
}
.order-tab.active { border-color: #1565a8; background: #eff6ff; color: #1565a8; }
.order-tab-badge {
  font-size: 10px; font-weight: 700; min-width: 16px;
  padding: 0px 5px; border-radius: 100px; text-align: center;
}
.order-tab-badge.menunggu { background: #fef3c7; color: #92400e; }
.order-tab-badge.diproses { background: #dbeafe; color: #1e40af; }
.order-tab-badge.selesai  { background: #dcfce7; color: #166534; }

/* Search */
.search-bar {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 16px; background: #fff;
  border-bottom: 1px solid #e5e7eb;
}
.search-bar svg {
  width: 16px; height: 16px; flex-shrink: 0;
  fill: none; stroke: #9ca3af;
  stroke-width: 2; stroke-linecap: round;
}
.search-input {
  flex: 1; border: none; outline: none;
  font-size: 13px; font-family: inherit;
  color: #1f2937; background: transparent;
}
.search-input::placeholder { color: #9ca3af; }

/* Empty orders */
.empty-orders {
  display: flex; flex-direction: column; align-items: center;
  padding: 48px 24px; gap: 8px; color: #9ca3af;
  font-size: 14px;
}

/* Order card */
.order-card {
  background: #fff; margin-bottom: 8px;
  border-radius: 0; border-bottom: 4px solid #f0f4f8;
}
@media (min-width: 768px) {
  .order-card { border-radius: 14px; border-bottom: none; box-shadow: 0 1px 4px rgba(0,0,0,0.07); }
}
.order-card-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px 8px;
  border-bottom: 1px solid #f9fafb;
}
.order-id-row { display: flex; align-items: center; gap: 8px; }
.order-id { font-family: monospace; font-size: 13px; font-weight: 700; color: #374151; }
.order-status {
  font-size: 11px; font-weight: 600;
  padding: 2px 8px; border-radius: 100px;
}
.order-status.menunggu   { background: #fef3c7; color: #92400e; }
.order-status.diproses   { background: #dbeafe; color: #1e40af; }
.order-status.selesai    { background: #dcfce7; color: #166534; }
.order-status.dibatalkan { background: #fee2e2; color: #991b1b; }
.order-time { font-size: 11px; color: #9ca3af; }

/* Buyer info */
.buyer-info-row {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 16px;
  border-bottom: 1px solid #f9fafb;
}
.buyer-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: #dbeafe; color: #1565a8;
  font-size: 14px; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.buyer-name { font-size: 13px; font-weight: 700; color: #1f2937; margin-bottom: 2px; }
.buyer-address { font-size: 11px; color: #6b7280; }

/* Order items */
.order-items { padding: 8px 16px; border-bottom: 1px solid #f9fafb; }
.order-item-row {
  display: flex; align-items: center; gap: 8px;
  font-size: 12px; padding: 4px 0;
}
.item-emoji { font-size: 16px; }
.item-name-text { flex: 1; color: #374151; font-weight: 500; }
.item-qty-text  { color: #9ca3af; flex-shrink: 0; }
.item-price-text { font-weight: 700; color: #1f2937; flex-shrink: 0; }

/* Card footer */
.order-card-footer {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 16px; gap: 10px;
}
.order-footer-left { display: flex; flex-direction: column; gap: 4px; }
.payment-method-badge {
  font-size: 11px; font-weight: 600;
  padding: 2px 8px; border-radius: 100px; display: inline-block;
}
.payment-method-badge.cod      { background: #fef3c7; color: #92400e; }
.payment-method-badge.transfer { background: #eff6ff; color: #1e40af; }
.order-total-row { display: flex; align-items: center; gap: 6px; }
.total-label { font-size: 11px; color: #6b7280; }
.total-val   { font-size: 15px; font-weight: 800; color: #1565a8; }
.order-actions { display: flex; align-items: center; gap: 8px; }
.btn-wa {
  display: flex; align-items: center; gap: 5px;
  padding: 8px 12px; background: #25d366; color: #fff;
  border-radius: 10px; text-decoration: none;
  font-size: 12px; font-weight: 700; flex-shrink: 0;
  transition: background 0.15s;
}
.btn-wa:hover { background: #16a34a; }
.btn-wa svg {
  width: 14px; height: 14px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round;
}
.status-select {
  padding: 8px 10px; border: 1.5px solid #e5e7eb;
  border-radius: 10px; font-size: 12px; font-weight: 600;
  font-family: inherit; color: #374151; background: #f9fafb;
  cursor: pointer; outline: none;
}
.status-select:focus { border-color: #1565a8; }
.status-final-badge {
  font-size: 12px; font-weight: 700;
  padding: 5px 10px; border-radius: 100px;
}
.status-final-badge.selesai    { background: #dcfce7; color: #166534; }
.status-final-badge.dibatalkan { background: #fee2e2; color: #991b1b; }

/* Note */
.order-note {
  display: flex; align-items: flex-start; gap: 7px;
  padding: 8px 16px 12px;
  font-size: 12px; color: #6b7280; font-style: italic;
}
.order-note svg {
  width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px;
  fill: none; stroke: currentColor; stroke-width: 1.7;
}

/* Bottom Nav */
.bottom-nav {
  position: fixed; bottom: 0; left: 50%;
  transform: translateX(-50%);
  width: 100%; max-width: 480px;
  background: #fff; border-top: 1px solid #e5e7eb;
  display: flex; z-index: 200;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
}
@media (min-width: 768px) { .bottom-nav { max-width: 1024px; } }
.nav-btn {
  flex: 1; padding: 8px 4px 10px;
  background: none; border: none; cursor: pointer;
  display: flex; flex-direction: column; align-items: center; gap: 3px;
  font-size: 10px; font-weight: 500; font-family: inherit;
  color: #9ca3af; transition: color 0.15s;
}
.nav-btn svg {
  width: 20px; height: 20px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
.nav-btn--active { color: #1565a8; }
</style>