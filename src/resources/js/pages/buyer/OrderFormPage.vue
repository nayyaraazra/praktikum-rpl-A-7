<template>
  <div class="page-wrap">

    <!-- ── Header ── -->
    <header class="detail-header">
      <button class="btn-back" @click="$router.back()" aria-label="Kembali">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <span class="header-title">Form Pemesanan</span>
      <div style="width:36px;"></div>
    </header>

    <div v-if="cartItems.length > 0" class="page-content">

      <!-- ── Ringkasan Produk ── -->
      <div class="section-card">
        <h2 class="section-title">Ringkasan Pesanan</h2>

        <!-- Group by store -->
        <div v-for="(group, storeName) in groupedItems" :key="storeName" class="store-group">
          <div class="store-group-header">
            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>{{ storeName }}</span>
            <span class="store-district-tag">{{ group[0].store_district }}</span>
          </div>
          <div v-for="item in group" :key="item.id" class="order-item">
            <div class="item-img">
              <span>{{ getCategoryEmoji(item.category) }}</span>
            </div>
            <div class="item-info">
              <p class="item-name">{{ item.name }}</p>
              <p class="item-price">Rp {{ formatPrice(item.price) }} / {{ item.unit }}</p>
              <p class="item-stock-hint">Stok tersedia: {{ item.stock }} {{ item.unit }}</p>
            </div>
            <div class="item-qty-block">
              <div class="qty-control">
                <button class="qty-btn" @click="decreaseQty(item)" :disabled="item.qty <= (item.min_order || 1)">−</button>
                <span class="qty-val">{{ item.qty }}</span>
                <button class="qty-btn" @click="increaseQty(item)" :disabled="item.qty >= item.stock">+</button>
              </div>
              <p class="item-subtotal">Rp {{ formatPrice(item.price * item.qty) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Data Pembeli ── -->
      <div class="section-card">
        <h2 class="section-title">Data Pembeli</h2>

        <div class="field">
          <label class="field-label" for="buyerName">Nama Pembeli <span class="req">*</span></label>
          <div class="input-wrap">
            <span class="input-icon">
              <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <input
              v-model="form.buyer_name"
              type="text" id="buyerName"
              placeholder="Masukkan nama lengkap"
              :class="['form-input', errors.buyer_name && 'error']"
            />
          </div>
          <span v-if="errors.buyer_name" class="field-error">{{ errors.buyer_name }}</span>
        </div>

        <div class="field">
          <label class="field-label" for="shippingAddress">Alamat Pengiriman <span class="req">*</span></label>
          <div class="input-wrap textarea-wrap">
            <span class="input-icon top-icon">
              <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </span>
            <textarea
              v-model="form.shipping_address"
              id="shippingAddress"
              rows="2"
              placeholder="Jl. Contoh No. 1, RT/RW, Kelurahan..."
              :class="['form-input', 'form-textarea', errors.shipping_address && 'error']"
            ></textarea>
          </div>
          <span v-if="errors.shipping_address" class="field-error">{{ errors.shipping_address }}</span>
        </div>

        <div class="field">
          <label class="field-label" for="note">Catatan Tambahan</label>
          <div class="input-wrap textarea-wrap">
            <span class="input-icon top-icon">
              <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </span>
            <textarea
              v-model="form.note"
              id="note"
              rows="2"
              placeholder="mis. tolong dikemas dengan baik..."
              class="form-input form-textarea"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- ── Metode Pembayaran ── -->
      <div class="section-card">
        <h2 class="section-title">Metode Pembayaran</h2>
        <div class="payment-grid">
          <button
            type="button"
            :class="['payment-card', form.payment_method === 'cod' && 'selected']"
            @click="form.payment_method = 'cod'"
          >
            <div class="payment-icon">🤝</div>
            <div class="payment-name">COD</div>
            <div class="payment-desc">Bayar tunai di gerai saat mengambil barang</div>
          </button>
          <button
            type="button"
            :class="['payment-card', form.payment_method === 'transfer' && 'selected']"
            @click="form.payment_method = 'transfer'"
          >
            <div class="payment-icon">🏦</div>
            <div class="payment-name">Transfer</div>
            <div class="payment-desc">Transfer kirim bukti via WhatsApp penjual</div>
          </button>
        </div>
        <span v-if="errors.payment_method" class="field-error">{{ errors.payment_method }}</span>
      </div>

      <!-- ── Ringkasan Harga ── -->
      <div class="section-card summary-card">
        <div class="summary-row">
          <span class="summary-label">Subtotal ({{ totalQty }} item)</span>
          <span class="summary-value">Rp {{ formatPrice(subtotal) }}</span>
        </div>
        <div class="summary-row">
          <span class="summary-label">Biaya layanan</span>
          <span class="summary-value free-badge">Gratis</span>
        </div>
        <div class="summary-divider"></div>
        <div class="summary-row total-row">
          <span class="total-label">Total Estimasi</span>
          <span class="total-value">Rp {{ formatPrice(subtotal) }}</span>
        </div>
      </div>

      <!-- ── Success / Error Message ── -->
      <div v-if="submitSuccess" class="alert-success">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        <div>
          <p class="alert-title">Pesanan Berhasil Dikirim!</p>
          <p class="alert-desc">Penjual akan menghubungi Anda segera. Cek notifikasi untuk info selanjutnya.</p>
        </div>
      </div>

      <div v-if="globalError" class="alert-error">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>{{ globalError }}</span>
      </div>

      <!-- Spacer for bottom CTA -->
      <div style="height:96px;"></div>
    </div>

    <!-- Empty Cart State -->
    <div v-else class="empty-state">
      <div class="empty-icon">🛒</div>
      <p class="empty-title">Keranjang kosong</p>
      <p class="empty-desc">Silakan pilih produk terlebih dahulu</p>
      <button class="btn-back-shop" @click="$router.push({ name: 'buyer.products' })">
        Lihat Produk
      </button>
    </div>

    <!-- ── Bottom CTA ── -->
    <div v-if="cartItems.length > 0" class="bottom-cta">
      <div class="cta-summary">
        <span class="cta-total-label">Total</span>
        <span class="cta-total-val">Rp {{ formatPrice(subtotal) }}</span>
      </div>
      <button
        class="btn-order"
        :disabled="isLoading || submitSuccess"
        @click="handleSubmit"
      >
        <span v-if="isLoading" class="loading-spin">
          <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/><path d="M12 2a10 10 0 0110 10" stroke="white" stroke-width="3" stroke-linecap="round"/></svg>
          Mengirim...
        </span>
        <span v-else-if="submitSuccess">✓ Pesanan Terkirim</span>
        <span v-else>Kirim Pesanan</span>
      </button>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { mockProducts } from '@/data/mockData'

const router = useRouter()
const route  = useRoute()

// ── Cart State ────────────────────────────────────────────────
// Ambil produk dari route query atau gunakan mock data
const cartItems = ref([])

onMounted(() => {
  const productId = Number(route.params.productId || route.query.productId)
  if (productId) {
    const product = mockProducts.find(p => p.id === productId)
    if (product) {
      cartItems.value = [{
        ...product,
        qty: product.min_order || 1
      }]
    }
  } else {
    // Simulasi cart multi-produk jika tidak ada product id spesifik
    const sampleItems = mockProducts.slice(0, 2).map(p => ({
      ...p,
      qty: p.min_order || 1
    }))
    cartItems.value = sampleItems
  }
})

// ── Form ──────────────────────────────────────────────────────
const form = reactive({
  buyer_name:       '',
  shipping_address: '',
  note:             '',
  payment_method:   'cod',
})

const errors      = reactive({})
const globalError = ref('')
const isLoading   = ref(false)
const submitSuccess = ref(false)

// ── Computed ──────────────────────────────────────────────────
const groupedItems = computed(() => {
  const groups = {}
  cartItems.value.forEach(item => {
    if (!groups[item.store_name]) groups[item.store_name] = []
    groups[item.store_name].push(item)
  })
  return groups
})

const subtotal = computed(() =>
  cartItems.value.reduce((sum, item) => sum + item.price * item.qty, 0)
)

const totalQty = computed(() =>
  cartItems.value.reduce((sum, item) => sum + item.qty, 0)
)

// ── Qty Controls ──────────────────────────────────────────────
function increaseQty(item) {
  if (item.qty < item.stock) item.qty++
}
function decreaseQty(item) {
  const min = item.min_order || 1
  if (item.qty > min) item.qty--
}

// ── Validation ────────────────────────────────────────────────
function validate() {
  Object.keys(errors).forEach(k => delete errors[k])
  globalError.value = ''
  let valid = true

  if (!form.buyer_name.trim()) {
    errors.buyer_name = 'Nama pembeli wajib diisi.'
    valid = false
  }
  if (!form.shipping_address.trim()) {
    errors.shipping_address = 'Alamat pengiriman wajib diisi.'
    valid = false
  }
  if (!form.payment_method) {
    errors.payment_method = 'Pilih metode pembayaran.'
    valid = false
  }
  return valid
}

// ── Submit ────────────────────────────────────────────────────
async function handleSubmit() {
  if (!validate()) return

  isLoading.value = true
  // Simulasi pengiriman ke API
  await new Promise(r => setTimeout(r, 1400))
  isLoading.value   = false
  submitSuccess.value = true

  // Redirect ke halaman notifikasi setelah 2 detik
  setTimeout(() => {
    router.push({ name: 'buyer.notifications' })
  }, 2000)
}

// ── Helpers ───────────────────────────────────────────────────
function formatPrice(n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}
function getCategoryEmoji(cat) {
  const map = { makanan_minuman:'🍱', fashion_batik:'👗', kerajinan:'🧶', kecantikan:'💄', pertanian:'🌾', jasa:'🛠️' }
  return map[cat] || '📦'
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

/* Desktop override */
@media (min-width: 768px) {
  .page-wrap { max-width: 900px; }
  .page-content {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 0 16px;
    align-items: start;
    padding: 16px 16px 0;
  }
  .page-content > .section-card:nth-child(1) { grid-column: 1; grid-row: 1; }
  .page-content > .section-card:nth-child(2) { grid-column: 1; grid-row: 2; }
  .page-content > .section-card:nth-child(3) { grid-column: 2; grid-row: 1; }
  .page-content > .section-card:nth-child(4) { grid-column: 2; grid-row: 2; }
  .bottom-cta { max-width: 900px; }
}

/* Header */
.detail-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px;
  background: linear-gradient(135deg, #1a7fc4 0%, #1565a8 100%);
  position: sticky; top: 0; z-index: 100;
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

.page-content { padding-bottom: 0; }

/* Section cards */
.section-card {
  background: #fff;
  padding: 16px;
  margin-bottom: 8px;
  border-radius: 0;
}
@media (min-width: 768px) {
  .section-card { border-radius: 14px; margin-bottom: 12px; }
}
.section-title {
  font-size: 14px; font-weight: 700; color: #111827;
  margin-bottom: 14px;
  text-transform: uppercase; letter-spacing: 0.4px;
}

/* Store group */
.store-group { margin-bottom: 14px; }
.store-group:last-child { margin-bottom: 0; }
.store-group-header {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; font-weight: 700; color: #1565a8;
  margin-bottom: 10px;
  padding-bottom: 8px;
  border-bottom: 1px solid #e5e7eb;
}
.store-group-header svg {
  width: 13px; height: 13px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round;
}
.store-district-tag {
  background: #dbeafe; color: #1565a8;
  font-size: 10px; font-weight: 600;
  padding: 1px 7px; border-radius: 100px;
  margin-left: auto;
}

/* Order item */
.order-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #f9fafb;
}
.order-item:last-child { border-bottom: none; }
.item-img {
  width: 48px; height: 48px;
  background: #f1f5f9; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; flex-shrink: 0;
}
.item-info { flex: 1; }
.item-name { font-size: 13px; font-weight: 700; color: #1f2937; margin-bottom: 2px; }
.item-price { font-size: 12px; color: #1565a8; font-weight: 600; margin-bottom: 2px; }
.item-stock-hint { font-size: 11px; color: #9ca3af; }
.item-qty-block { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0; }
.qty-control { display: flex; align-items: center; gap: 8px; }
.qty-btn {
  width: 28px; height: 28px;
  background: #eff6ff; border: 1.5px solid #bfdbfe;
  border-radius: 8px; font-size: 16px; font-weight: 700;
  color: #1565a8; cursor: pointer; display: flex;
  align-items: center; justify-content: center;
  transition: all 0.15s;
}
.qty-btn:hover:not(:disabled) { background: #1565a8; color: #fff; }
.qty-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.qty-val { font-size: 14px; font-weight: 700; color: #111827; min-width: 20px; text-align: center; }
.item-subtotal { font-size: 13px; font-weight: 800; color: #111827; }

/* Fields */
.field { margin-bottom: 16px; }
.field-label {
  display: block; font-size: 13px; font-weight: 600;
  color: #374151; margin-bottom: 7px;
}
.req { color: #ef4444; }
.input-wrap { position: relative; display: flex; align-items: center; }
.textarea-wrap { align-items: flex-start; }
.input-icon {
  position: absolute; left: 13px;
  display: flex; align-items: center; pointer-events: none;
}
.top-icon { top: 11px; align-items: flex-start; }
.input-icon svg {
  width: 16px; height: 16px;
  fill: none; stroke: #9ca3af;
  stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round;
}
.form-input {
  width: 100%;
  padding: 10px 14px 10px 42px;
  border: 1.5px solid #e5e7eb;
  border-radius: 10px;
  font-family: inherit; font-size: 14px; color: #1f2937;
  background: #f9fafb; outline: none;
  transition: border-color 0.18s, box-shadow 0.18s;
}
.form-input:focus {
  border-color: #1565a8; background: #fff;
  box-shadow: 0 0 0 3px rgba(21,101,168,0.1);
}
.form-input.error { border-color: #ef4444; background: #fef2f2; }
.form-textarea { resize: vertical; min-height: 64px; padding-top: 10px; }
.field-error { display: block; margin-top: 5px; font-size: 12px; color: #ef4444; }

/* Payment */
.payment-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.payment-card {
  padding: 14px 12px; border: 1.5px solid #e5e7eb;
  border-radius: 12px; background: #f9fafb;
  cursor: pointer; text-align: center;
  font-family: inherit; transition: all 0.18s;
}
.payment-card:hover { border-color: #93c5fd; background: #eff6ff; }
.payment-card.selected {
  border-color: #1565a8; background: #eff6ff;
  box-shadow: 0 0 0 3px rgba(21,101,168,0.1);
}
.payment-icon { font-size: 24px; margin-bottom: 6px; }
.payment-name { font-size: 13px; font-weight: 700; color: #1f2937; margin-bottom: 4px; }
.payment-desc { font-size: 11px; color: #6b7280; line-height: 1.4; }
.payment-card.selected .payment-name { color: #1565a8; }

/* Summary card */
.summary-card { }
.summary-row {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 10px;
}
.summary-label { font-size: 13px; color: #6b7280; }
.summary-value { font-size: 13px; font-weight: 600; color: #1f2937; }
.free-badge {
  font-size: 12px; font-weight: 700;
  color: #16a34a; background: #dcfce7;
  padding: 2px 8px; border-radius: 100px;
}
.summary-divider { height: 1px; background: #f3f4f6; margin: 10px 0; }
.total-row { }
.total-label { font-size: 15px; font-weight: 700; color: #111827; }
.total-value { font-size: 18px; font-weight: 800; color: #1565a8; }

/* Alerts */
.alert-success {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 14px 16px; margin: 0 0 8px;
  background: #f0fdf4; border: 1px solid #bbf7d0;
  border-radius: 0;
}
@media (min-width: 768px) {
  .alert-success { border-radius: 12px; margin: 0 16px 12px; }
}
.alert-success svg {
  width: 20px; height: 20px; flex-shrink: 0; margin-top: 1px;
  fill: none; stroke: #16a34a; stroke-width: 2.5; stroke-linecap: round;
}
.alert-title { font-size: 13px; font-weight: 700; color: #15803d; margin-bottom: 3px; }
.alert-desc  { font-size: 12px; color: #16a34a; }
.alert-error {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px; margin: 0 0 8px;
  background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 0; font-size: 13px; color: #dc2626;
}
@media (min-width: 768px) {
  .alert-error { border-radius: 12px; margin: 0 16px 12px; }
}
.alert-error svg {
  width: 18px; height: 18px; flex-shrink: 0;
  fill: none; stroke: currentColor; stroke-width: 1.7;
}

/* Empty state */
.empty-state {
  flex: 1; display: flex; flex-direction: column;
  align-items: center; justify-content: center;
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

/* Bottom CTA */
.bottom-cta {
  position: fixed; bottom: 0; left: 50%;
  transform: translateX(-50%);
  width: 100%; max-width: 480px;
  background: #fff; border-top: 1px solid #e5e7eb;
  padding: 12px 16px;
  display: flex; align-items: center; gap: 12px;
  z-index: 200; box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
}
@media (min-width: 768px) { .bottom-cta { max-width: 900px; } }
.cta-summary { display: flex; flex-direction: column; }
.cta-total-label { font-size: 10px; color: #6b7280; }
.cta-total-val { font-size: 16px; font-weight: 800; color: #1565a8; }
.btn-order {
  flex: 1; padding: 13px;
  background: linear-gradient(135deg, #1a7fc4, #1565a8);
  color: #fff; border: none; border-radius: 12px;
  font-size: 15px; font-weight: 700; font-family: inherit;
  cursor: pointer; box-shadow: 0 2px 8px rgba(21,101,168,0.3);
  transition: all 0.15s;
}
.btn-order:hover:not(:disabled) { box-shadow: 0 4px 12px rgba(21,101,168,0.4); }
.btn-order:disabled { opacity: 0.65; cursor: not-allowed; }
.loading-spin {
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.loading-spin svg { width: 16px; height: 16px; animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>