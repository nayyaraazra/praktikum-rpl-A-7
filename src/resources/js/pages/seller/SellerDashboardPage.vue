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
            <div class="metric-icon amber">💰</div>
            <div class="metric-value">{{ formatRupiah(metrics.monthly_revenue) }}</div>
            <div class="metric-label">Pendapatan Bulan Ini</div>
            <div class="metric-trend up">Status: selesai</div>
          </div>

          <div class="metric-card">
            <div class="metric-icon red">⚡</div>
            <div class="metric-value">{{ metrics.new_orders }}</div>
            <div class="metric-label">Pesanan Baru</div>
            <div class="metric-trend down" v-if="metrics.new_orders > 0">Perlu konfirmasi</div>
            <div class="metric-trend" style="color:#8A8980" v-else>Tidak ada pesanan baru</div>
          </div>

          <div class="metric-card">
            <div class="metric-icon blue">⚙️</div>
            <div class="metric-value">{{ metrics.processed_orders }}</div>
            <div class="metric-label">Pesanan Diproses</div>
            <div class="metric-trend up" style="color:rgb(36,103,170)" v-if="metrics.processed_orders > 0">Sedang diproses</div>
            <div class="metric-trend" style="color:#8A8980" v-else>Tidak ada pesanan diproses</div>
          </div>

          <div class="metric-card">
            <div class="metric-icon green">📦</div>
            <div class="metric-value">{{ metrics.total_orders }}</div>
            <div class="metric-label">Total Pesanan</div>
            <div class="metric-trend up">↑ Semua waktu</div>
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
            <div class="table-header-cell text-right">Aksi</div>
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

            <div class="table-cell text-right">
              <button class="btn-action" @click="viewOrderDetail(order.id_order)">Detail</button>
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

    <!-- Order Detail Modal -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="closeDetailModal">
      <div class="modal-container">
        
        <div class="modal-header">
          <h2 class="modal-title">Detail Pesanan #{{ String(selectedOrder?.id_order).padStart(3, '0') }}</h2>
          <button class="modal-close" @click="closeDetailModal">&times;</button>
        </div>

        <div class="modal-body" v-if="selectedOrder">
          <!-- Status & Waktu -->
          <div class="detail-row-flex">
            <div>
              <span class="detail-label-sub">Tanggal Pesanan</span>
              <div class="detail-value-main">{{ formatDate(selectedOrder.order_date) }}</div>
            </div>
            <div>
              <span class="detail-label-sub">Status Saat Ini</span>
              <div>
                <span :class="['status-badge', statusClass(selectedOrder.status)]">
                  <span class="status-dot"></span>
                  {{ statusLabel(selectedOrder.status) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Info Pemesan -->
          <div class="info-card-box">
            <h3 class="info-card-title">Informasi Pemesan</h3>
            <div class="info-grid-2">
              <div>
                <span class="info-item-label">Nama Pemesan</span>
                <div class="info-item-val">{{ selectedOrder.buyer?.name || selectedOrder.buyer_name }}</div>
              </div>
              <div>
                <span class="info-item-label">No. WhatsApp</span>
                <div class="info-item-val">
                  <a 
                    v-if="selectedOrder.buyer?.phone_number" 
                    :href="'https://wa.me/' + selectedOrder.buyer.phone_number.replace(/[^0-9]/g, '')"
                    target="_blank"
                    class="wa-link"
                  >
                    {{ selectedOrder.buyer.phone_number }} ↗
                  </a>
                  <span v-else>—</span>
                </div>
              </div>
            </div>
            <div style="margin-top: 12px;">
              <span class="info-item-label">Alamat Pengiriman</span>
              <div class="info-item-val address-text">{{ selectedOrder.shipping_address }}</div>
            </div>
            <div style="margin-top: 12px;" v-if="selectedOrder.note">
              <span class="info-item-label">Catatan Tambahan</span>
              <div class="info-item-val note-text">"{{ selectedOrder.note }}"</div>
            </div>
          </div>

          <!-- Rincian Item -->
          <div class="info-card-box" style="margin-top: 16px;">
            <h3 class="info-card-title">Rincian Item</h3>
            <div class="modal-items-list">
              <div v-for="item in selectedOrder.items" :key="item.id_order_detail" class="modal-item-row">
                <div class="modal-item-info">
                  <div class="modal-item-name">{{ item.product?.name || 'Produk' }}</div>
                  <div class="modal-item-qty">{{ item.quantity }} {{ item.product?.unit || 'pcs' }} &middot; Rp {{ Number(item.price_at_purchase).toLocaleString('id-ID') }}</div>
                </div>
                <div class="modal-item-subtotal">
                  Rp {{ (item.quantity * item.price_at_purchase).toLocaleString('id-ID') }}
                </div>
              </div>
            </div>
            
            <div class="modal-total-summary">
              <div class="modal-summary-line">
                <span>Subtotal</span>
                <span>Rp {{ Number(selectedOrder.total_order).toLocaleString('id-ID') }}</span>
              </div>
              <div class="modal-summary-line">
                <span>Ongkos Kirim</span>
                <span style="color: rgb(36,103,170); font-weight: 600;">Gratis</span>
              </div>
              <div class="modal-summary-line grand-total">
                <span>Total</span>
                <span>Rp {{ Number(selectedOrder.total_order).toLocaleString('id-ID') }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer" v-if="selectedOrder">
          <button 
            v-if="selectedOrder.status === 'menunggu'" 
            class="btn-modal-action btn-process"
            :disabled="updatingStatus"
            @click="changeStatus('diproses')"
          >
            Proses Pesanan
          </button>
          <button 
            v-if="selectedOrder.status === 'diproses'" 
            class="btn-modal-action btn-complete"
            :disabled="updatingStatus"
            @click="changeStatus('selesai')"
          >
            Selesaikan Pesanan
          </button>
          <button 
            v-if="selectedOrder.status === 'menunggu' || selectedOrder.status === 'diproses'" 
            class="btn-modal-action btn-cancel"
            :disabled="updatingStatus"
            @click="changeStatus('dibatalkan')"
          >
            Batalkan Pesanan
          </button>
          <button class="btn-modal-close" @click="closeDetailModal">Tutup</button>
        </div>

      </div>
    </div>

    <!-- Toast Notification -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert" aria-live="polite">
      {{ toastMsg }}
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SellerSidebar from '@/components/seller/SellerSidebar.vue'
import { sellerApi } from '@/services/api/sellerApi'

const loading       = ref(true)
const recentOrders  = ref([])
const storeName     = ref('')

const metrics = ref({
  total_orders:    0,
  monthly_revenue: 0,
  active_products: 0,
  low_stock_count: 0,
  new_orders:      0,
  processed_orders:0,
})

// Modal & update states
const showDetailModal = ref(false)
const selectedOrder   = ref(null)
const updatingStatus  = ref(false)

// Toast notification states
const toastMsg     = ref('')
const toastType    = ref('')
const toastVisible = ref(false)
let toastTimer     = null

function showToast(msg, type = '', duration = 3200) {
  clearTimeout(toastTimer)
  toastMsg.value     = msg
  toastType.value    = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}

async function fetchDashboardData() {
  try {
    const { data } = await sellerApi.getDashboard()
    if (data.success) {
      metrics.value      = data.data.metrics
      recentOrders.value = data.data.recent_orders
      storeName.value    = data.data.store.store_name
    }
  } catch (err) {
    console.error('Gagal memuat dashboard:', err)
  }
}

// ── Fetch data ────────────────────────────────────────────────
onMounted(async () => {
  loading.value = true
  await fetchDashboardData()
  loading.value = false
})

async function viewOrderDetail(id) {
  try {
    const { data } = await sellerApi.getOrderDetail(id)
    if (data.success) {
      selectedOrder.value = data.data
      showDetailModal.value = true
    }
  } catch (err) {
    console.error('Gagal mengambil detail pesanan:', err)
    showToast('Gagal memuat detail pesanan.', 'error')
  }
}

function closeDetailModal() {
  showDetailModal.value = false
  selectedOrder.value   = null
}

async function changeStatus(newStatus) {
  if (!selectedOrder.value) return
  
  updatingStatus.value = true
  try {
    const orderId = selectedOrder.value.id_order
    const { data } = await sellerApi.updateOrderStatus(orderId, newStatus)
    
    if (data.success) {
      showToast('Status pesanan berhasil diperbarui!', 'success')
      closeDetailModal()
      await fetchDashboardData()
    }
  } catch (err) {
    console.error('Gagal memperbarui status:', err)
    showToast(err.response?.data?.message || 'Gagal memperbarui status.', 'error')
  } finally {
    updatingStatus.value = false
  }
}

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
  grid-template-columns: 2fr 1.8fr 1fr 1fr 1.2fr 1fr;
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
  grid-template-columns: 2fr 1.8fr 1fr 1fr 1.2fr 1fr;
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
.status-badge.done       { background: #E6F4EA; color: #137333; }
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

/* ── Modal overlay ── */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 16px;
}
.modal-container {
  background: #fff;
  border-radius: 16px;
  width: 100%;
  max-width: 520px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  animation: modalScale 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
  border: 1px solid #E8E7E2;
}
@keyframes modalScale {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.modal-header {
  padding: 18px 24px;
  border-bottom: 1px solid #E8E7E2;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.modal-title {
  font-size: 16px;
  font-weight: 700;
  color: #161514;
  letter-spacing: -0.3px;
}
.modal-close {
  background: none;
  border: none;
  font-size: 24px;
  color: #8A8980;
  cursor: pointer;
  line-height: 1;
  padding: 4px;
  transition: color 0.1s;
}
.modal-close:hover {
  color: #161514;
}
.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
}
.detail-row-flex {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
  background: #F4F3F0;
  padding: 12px 16px;
  border-radius: 12px;
  border: 1px solid #E8E7E2;
}
.detail-label-sub {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  color: #8A8980;
  letter-spacing: 0.3px;
  display: block;
  margin-bottom: 2px;
}
.detail-value-main {
  font-size: 14px;
  font-weight: 700;
  color: #161514;
}
.info-card-box {
  background: #fff;
  border: 1px solid #E8E7E2;
  border-radius: 12px;
  padding: 16px;
}
.info-card-title {
  font-size: 13px;
  font-weight: 700;
  color: #161514;
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  border-bottom: 1px solid #F4F3F0;
  padding-bottom: 6px;
}
.info-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.info-item-label {
  font-size: 11px;
  color: #8A8980;
  font-weight: 600;
  display: block;
  margin-bottom: 2px;
}
.info-item-val {
  font-size: 13px;
  font-weight: 600;
  color: #3E3D38;
}
.wa-link {
  color: rgb(36,103,170);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.wa-link:hover {
  text-decoration: underline;
}
.address-text {
  line-height: 1.4;
  font-weight: 500;
}
.note-text {
  font-style: italic;
  color: #5C5B54;
  font-weight: 500;
}
.modal-items-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.modal-item-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
}
.modal-item-name {
  font-weight: 700;
  color: #161514;
}
.modal-item-qty {
  font-size: 11px;
  color: #8A8980;
  margin-top: 1px;
}
.modal-item-subtotal {
  font-weight: 700;
  color: #161514;
}
.modal-total-summary {
  margin-top: 14px;
  padding-top: 14px;
  border-top: 1px dashed #E8E7E2;
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 13px;
}
.modal-summary-line {
  display: flex;
  justify-content: space-between;
  color: #5C5B54;
}
.modal-summary-line.grand-total {
  font-size: 15px;
  font-weight: 850;
  color: #161514;
  border-top: 1px solid #F4F3F0;
  padding-top: 8px;
  margin-top: 4px;
}
.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #E8E7E2;
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  background: #FAFAF9;
}
.btn-modal-action {
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.15s, opacity 0.15s;
}
.btn-process {
  background: rgb(36,103,170);
  color: #fff;
}
.btn-process:hover:not(:disabled) {
  background: rgb(24,95,165);
}
.btn-complete {
  background: #15803d;
  color: #fff;
}
.btn-complete:hover:not(:disabled) {
  background: #166534;
}
.btn-cancel {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fca5a5;
}
.btn-cancel:hover:not(:disabled) {
  background: #fee2e2;
}
.btn-modal-action:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.btn-modal-close {
  padding: 10px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  background: #fff;
  border: 1px solid #E8E7E2;
  color: #5C5B54;
  cursor: pointer;
  font-family: inherit;
  transition: background 0.15s;
}
.btn-modal-close:hover {
  background: #F4F3F0;
}

/* ── Action button in table ── */
.btn-action {
  background: none;
  border: 1.5px solid #E8E7E2;
  padding: 5px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
  color: rgb(36,103,170);
  cursor: pointer;
  font-family: inherit;
  transition: all 0.15s;
}
.btn-action:hover {
  background: #E8F1FB;
  border-color: rgb(36,103,170);
}
.text-right {
  text-align: right;
}

/* Toast styling */
.toast {
  position: fixed;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%) translateY(12px);
  background: #2D3547;
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  padding: 10px 22px;
  border-radius: 100px;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease, transform 0.25s ease;
  z-index: 99999;
  white-space: nowrap;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
}
.toast.show {
  opacity: 1;
  transform: translateX(-50%) translateY(0);
}
.toast.success { background: #15803d; }
.toast.error   { background: #b91c1c; }
</style>