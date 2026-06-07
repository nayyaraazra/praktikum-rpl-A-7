<template>
  <div class="app-layout">

    <!-- Sidebar -->
    <SellerSidebar :new-orders-count="metrics.new_orders" />

    <!-- Konten utama -->
    <main class="main-content">

      <!-- Header halaman -->
      <div class="page-header">
        <h1 class="page-title">Dashboard Toko</h1>
        <p class="page-sub">
          Selamat datang kembali,
          <strong>{{ storeName }}</strong> 👋
        </p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Memuat data dashboard…</p>
      </div>

      <template v-else>

        <!-- Metric cards -->
        <div class="dashboard-grid">

          <div class="metric-card">
            <div class="metric-icon green">📦</div>
            <div class="metric-value">{{ metrics.total_orders }}</div>
            <div class="metric-label">Total Pesanan</div>
            <div class="metric-trend up">↑ Semua waktu</div>
          </div>

          <div class="metric-card">
            <div class="metric-icon amber">💰</div>
            <div class="metric-value">{{ formatRupiah(metrics.monthly_revenue) }}</div>
            <div class="metric-label">Pendapatan Bulan Ini</div>
            <div class="metric-trend up">Status: selesai</div>
          </div>

          <div class="metric-card">
            <div class="metric-icon blue">🛍️</div>
            <div class="metric-value">{{ metrics.active_products }}</div>
            <div class="metric-label">Produk Aktif</div>
            <div class="metric-trend" :style="{ color: metrics.low_stock_count > 0 ? '#E6962A' : '#8A8980' }">
              {{ metrics.low_stock_count > 0 ? `${metrics.low_stock_count} perlu restok` : 'Stok aman' }}
            </div>
          </div>

          <div class="metric-card">
            <div class="metric-icon red">⚡</div>
            <div class="metric-value">{{ metrics.new_orders }}</div>
            <div class="metric-label">Pesanan Baru</div>
            <div class="metric-trend down" v-if="metrics.new_orders > 0">Perlu konfirmasi</div>
            <div class="metric-trend" style="color:#8A8980" v-else>Tidak ada pesanan baru</div>
          </div>

        </div>

        <!-- Tabel pesanan terbaru -->
        <div class="section-header">
          <div class="section-title">Pesanan Terbaru</div>
          <RouterLink :to="{ name: 'seller.products' }" class="section-link">
            Kelola Produk →
          </RouterLink>
        </div>

        <div class="orders-table" v-if="recentOrders.length > 0">
          <div class="table-header">
            <div class="table-header-cell">Pemesan</div>
            <div class="table-header-cell">Produk</div>
            <div class="table-header-cell">Total</div>
            <div class="table-header-cell">Pembayaran</div>
            <div class="table-header-cell">Status</div>
          </div>

          <div
            v-for="order in recentOrders"
            :key="order.id_order"
            class="table-row"
          >
            <div class="table-cell">
              <div class="table-cell-main">{{ order.buyer_name }}</div>
              <div class="table-cell-sub">
                {{ order.buyer_phone }} · {{ cityFromAddress(order.shipping_address) }}
              </div>
            </div>

            <div class="table-cell">
              <div class="table-cell-main">
                {{ order.products.map(p => `${p.name} ×${p.quantity}`).join(', ') }}
              </div>
            </div>

            <div class="table-cell">
              <div class="table-cell-main">{{ formatRupiah(order.total_order) }}</div>
            </div>

            <div class="table-cell">
              <div class="table-cell-main">{{ paymentLabel(order.payment_method) }}</div>
            </div>

            <div class="table-cell">
              <span :class="['status-badge', statusClass(order.status)]">
                <span class="status-dot"></span>
                {{ statusLabel(order.status) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Empty state pesanan -->
        <div v-else class="empty-state">
          <div class="empty-icon">📭</div>
          <p class="empty-title">Belum ada pesanan</p>
          <p class="empty-sub">Pesanan dari pembeli akan muncul di sini.</p>
        </div>

      </template>

    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SellerSidebar from '@/components/seller/SellerSidebar.vue'
import { sellerApi } from '@/services/api/sellerApi'

const loading      = ref(true)
const recentOrders = ref([])

const metrics = ref({
  total_orders:    0,
  monthly_revenue: 0,
  active_products: 0,
  low_stock_count: 0,
  new_orders:      0,
})

const storeName = ref('')

// ── Fetch data ────────────────────────────────────────────────
onMounted(async () => {
  try {
    const { data } = await sellerApi.getDashboard()
    if (data.success) {
      metrics.value      = data.data.metrics
      recentOrders.value = data.data.recent_orders
      storeName.value    = data.data.store.store_name
    }
  } catch (err) {
    console.error('Gagal memuat dashboard:', err)
  } finally {
    loading.value = false
  }
})

// ── Helpers ───────────────────────────────────────────────────
function formatRupiah(value) {
  const num = parseFloat(value) || 0
  if (num >= 1_000_000) return `Rp ${(num / 1_000_000).toFixed(1)}jt`
  if (num >= 1_000)     return `Rp ${(num / 1_000).toFixed(0)}K`
  return `Rp ${num.toFixed(0)}`
}

function cityFromAddress(address) {
  if (!address) return '—'
  const parts = address.split(',')
  return parts[parts.length - 1]?.trim().replace('Surakarta', 'Ska').slice(0, 20) || '—'
}

function paymentLabel(method) {
  return { cod: 'COD', transfer: 'Transfer', qris: 'QRIS' }[method] ?? method
}

function statusLabel(status) {
  return {
    menunggu:  'Menunggu',
    diproses:  'Diproses',
    selesai:   'Selesai',
    dibatalkan:'Dibatalkan',
  }[status] ?? status
}

function statusClass(status) {
  return {
    menunggu:  'waiting',
    diproses:  'processing',
    selesai:   'done',
    dibatalkan:'cancelled',
  }[status] ?? ''
}
</script>

<style scoped>
/* ── Layout ──────────────────────────────────────────────────── */
.app-layout {
  display: flex;
  min-height: 100vh;
  background: #F4F3F0;
}

.main-content {
  flex: 1;
  min-width: 0;
  padding: 32px;
}

/* ── Header ──────────────────────────────────────────────────── */
.page-header  { margin-bottom: 28px; }
.page-title   { font-size: 22px; font-weight: 700; color: #161514; letter-spacing: -.5px; }
.page-sub     { font-size: 14px; color: #8A8980; margin-top: 4px; }

/* ── Loading ─────────────────────────────────────────────────── */
.loading-state {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; gap: 16px;
  padding: 80px 0; color: #8A8980;
}
.spinner {
  width: 32px; height: 32px;
  border: 3px solid #E8E7E2;
  border-top-color: #175F9E;
  border-radius: 50%;
  animation: spin .8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Metric cards ────────────────────────────────────────────── */
.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 28px;
}
@media (max-width: 1100px) { .dashboard-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .dashboard-grid { grid-template-columns: 1fr; } }

.metric-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #E8E7E2;
  padding: 20px;
  box-shadow: 0 1px 2px rgba(0,0,0,.06);
}
.metric-icon {
  width: 40px; height: 40px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px; margin-bottom: 14px;
}
.metric-icon.green  { background: #E8F1FB; }
.metric-icon.amber  { background: #FDF3E3; }
.metric-icon.blue   { background: #E8F1FB; }
.metric-icon.red    { background: #FCEBEB; }

.metric-value {
  font-size: 28px; font-weight: 800;
  color: #161514; letter-spacing: -.5px;
  line-height: 1; margin-bottom: 4px;
}
.metric-label { font-size: 13px; color: #8A8980; }
.metric-trend { font-size: 12px; font-weight: 600; margin-top: 8px; }
.metric-trend.up   { color: rgb(36,103,170); }
.metric-trend.down { color: #E24B4A; }

/* ── Section ─────────────────────────────────────────────────── */
.section-header {
  display: flex; align-items: center;
  justify-content: space-between; margin-bottom: 16px;
}
.section-title { font-size: 16px; font-weight: 700; color: #161514; }
.section-link  {
  font-size: 13px; font-weight: 600;
  color: rgb(36,103,170); text-decoration: none;
}
.section-link:hover { text-decoration: underline; }

/* ── Orders table ────────────────────────────────────────────── */
.orders-table {
  background: #fff;
  border-radius: 16px; border: 1px solid #E8E7E2;
  overflow: hidden; margin-bottom: 24px;
  box-shadow: 0 1px 2px rgba(0,0,0,.06);
}
.table-header {
  display: grid;
  grid-template-columns: 2fr 1.8fr 1fr 1fr 1.2fr;
  padding: 12px 20px;
  background: #F4F3F0;
  border-bottom: 1px solid #E8E7E2;
}
.table-header-cell {
  font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .5px; color: #8A8980;
}
.table-row {
  display: grid;
  grid-template-columns: 2fr 1.8fr 1fr 1fr 1.2fr;
  padding: 14px 20px; align-items: center;
  border-bottom: 1px solid #F4F3F0;
  transition: background .15s;
}
.table-row:hover  { background: #F4F3F0; }
.table-row:last-child { border-bottom: none; }

.table-cell      { font-size: 14px; color: #3E3D38; }
.table-cell-main { font-weight: 600; color: #161514; }
.table-cell-sub  { font-size: 11px; color: #8A8980; margin-top: 1px; }

/* ── Status badges ───────────────────────────────────────────── */
.status-badge {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 9999px;
  font-size: 12px; font-weight: 600;
}
.status-badge.waiting    { background: #FDF3E3; color: #854F0B; }
.status-badge.processing { background: #E6F1FB; color: #0C447C; }
.status-badge.done       { background: #E8F1FB; color: #124F85; }
.status-badge.cancelled  { background: #FCEBEB; color: #791F1F; }
.status-dot {
  width: 6px; height: 6px;
  border-radius: 50%; background: currentColor;
}

/* ── Empty state ─────────────────────────────────────────────── */
.empty-state {
  text-align: center; padding: 60px 20px;
  background: #fff; border-radius: 16px;
  border: 1px solid #E8E7E2;
}
.empty-icon  { font-size: 40px; margin-bottom: 12px; }
.empty-title { font-size: 16px; font-weight: 600; color: #3E3D38; }
.empty-sub   { font-size: 13px; color: #8A8980; margin-top: 4px; }
</style>