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
        <a class="nav-item active" href="#">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 0 1-8 0" />
          </svg>
          Pesanan Saya
        </a>
        <router-link class="nav-item" :to="{ name: 'buyer.notifications' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
          </svg>
          Notifikasi
          <span v-if="unreadCount > 0" class="nav-badge" style="background:var(--brand-500);">{{ unreadCount }}</span>
        </router-link>
      </div>
      <div class="sidebar-user">
        <div class="avatar">{{ userInitials }}</div>
        <div class="sidebar-user-info">
          <div class="user-name">{{ authStore.user?.name || 'Pembeli' }}</div>
          <div class="user-role">Pembeli</div>
        </div>
      </div>
    </aside>

    <!-- ══ MAIN CONTENT ══ -->
    <main class="main-content">
      <div class="page-header">
        <h1 class="page-title">Form Pesanan</h1>
        <p class="page-sub">Lengkapi detail pesanan Anda</p>
      </div>

      <div v-if="loadingProduct" class="loading-state">
        <p>Memuat informasi produk...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <p>Produk tidak ditemukan atau tidak tersedia.</p>
        <router-link :to="{ name: 'buyer.dashboard' }">Kembali ke Katalog</router-link>
      </div>

      <div v-else-if="product" class="order-layout">
        <div class="order-left">
          <!-- Seksi 1: Informasi Pemesan -->
          <div class="form-section">
            <div class="form-section-title">
              <span class="form-section-num">1</span>
              Informasi Pemesan
            </div>
            <div class="grid-2">
              <div>
                <label class="form-label">Nama Pemesan <span class="req">*</span></label>
                <input
                  v-model="form.name"
                  class="form-input-plain"
                  type="text"
                  placeholder="Nama lengkap"
                  :class="{ 'border-red-400': errors.name }"
                />
                <span v-if="errors.name" class="error-msg">{{ errors.name }}</span>
              </div>
              <div>
                <label class="form-label">No. WhatsApp <span class="req">*</span></label>
                <input
                  v-model="form.phone_number"
                  class="form-input-plain"
                  type="tel"
                  placeholder="08xx"
                  :class="{ 'border-red-400': errors.phone_number }"
                />
                <span v-if="errors.phone_number" class="error-msg">{{ errors.phone_number }}</span>
              </div>
            </div>
            <div>
              <label class="form-label">Alamat Pengiriman <span class="req">*</span></label>
              <textarea
                v-model="form.shipping_address"
                class="form-input-plain"
                placeholder="Alamat lengkap termasuk RT/RW, kelurahan"
                :class="{ 'border-red-400': errors.shipping_address }"
              ></textarea>
              <span v-if="errors.shipping_address" class="error-msg">{{ errors.shipping_address }}</span>
            </div>
          </div>

          <!-- Seksi 2: Metode Pembayaran -->
          <div class="form-section">
            <div class="form-section-title">
              <span class="form-section-num">2</span>
              Metode Pembayaran
            </div>
            <div class="payment-options">
              <div
                class="payment-option"
                :class="{ selected: selectedPayment === 'cod' }"
                @click="selectedPayment = 'cod'"
              >
                <div class="payment-radio" :class="{ checked: selectedPayment === 'cod' }"></div>
                <div class="payment-icon">🤝</div>
                <div>
                  <div class="payment-name">COD (Bayar di Tempat)</div>
                  <div class="payment-desc">Bayar tunai saat barang tiba</div>
                </div>
              </div>
              <div
                class="payment-option"
                :class="{ selected: selectedPayment === 'bank_transfer' }"
                @click="selectedPayment = 'bank_transfer'"
              >
                <div class="payment-radio" :class="{ checked: selectedPayment === 'bank_transfer' }"></div>
                <div class="payment-icon">🏦</div>
                <div>
                  <div class="payment-name">Transfer Bank</div>
                  <div class="payment-desc">Konfirmasi via WhatsApp setelah transfer</div>
                </div>
              </div>
              <div
                class="payment-option"
                :class="{ selected: selectedPayment === 'qris' }"
                @click="selectedPayment = 'qris'"
              >
                <div class="payment-radio" :class="{ checked: selectedPayment === 'qris' }"></div>
                <div class="payment-icon">📱</div>
                <div>
                  <div class="payment-name">QRIS</div>
                  <div class="payment-desc">Scan kode QR dari penjual</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Seksi 3: Catatan Tambahan -->
          <div class="form-section">
            <div class="form-section-title">
              <span class="form-section-num">3</span>
              Catatan Tambahan
            </div>
            <textarea
              v-model="form.note"
              class="form-input-plain"
              placeholder="Contoh: Tidak pedas, kurangi garam, atau instruksi khusus lainnya"
            ></textarea>
          </div>
        </div>

        <div class="order-right">
          <div class="order-summary-card">
            <div class="order-summary-title">Ringkasan Pesanan</div>
            <div class="order-item-row">
              <div class="order-item-thumb">
                <span v-if="product.image_url">
                  <img :src="product.image_url" :alt="product.name" class="product-thumb-img" />
                </span>
                <span v-else>{{ productEmoji }}</span>
              </div>
              <div>
                <div class="order-item-name">{{ product.name }}</div>
                <div class="order-item-qty">
                  {{ product.store?.store_name || 'Toko UMKM' }} &middot; {{ quantity }} {{ product.unit }}
                </div>
              </div>
              <div class="order-item-price">Rp {{ formatPrice(totalPrice) }}</div>
            </div>
            <hr class="order-divider" />
            <div class="order-total-row">
              <div class="order-total-label">Subtotal</div>
              <div class="order-total-value">Rp {{ formatPrice(totalPrice) }}</div>
            </div>
            <div class="order-total-row">
              <div class="order-total-label">Ongkos kirim</div>
              <div class="order-total-value" style="color:var(--brand-600);">Gratis</div>
            </div>
            <div class="order-grand-row">
              <div class="order-grand-label">Total</div>
              <div class="order-grand-value">Rp {{ formatPrice(totalPrice) }}</div>
            </div>
            <button
              class="btn-order"
              :disabled="submitting"
              @click="submitOrder"
            >
              {{ submitting ? 'Mengirim...' : 'Kirim Pesanan' }}
            </button>
            <p class="order-summary-footer-text">
              Pesanan akan dikonfirmasi penjual via WhatsApp
            </p>
          </div>
        </div>
      </div>
    </main>

    <!-- Toast Notification -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert" aria-live="polite">
      {{ toastMsg }}
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { buyerApi } from '@/services/api/buyerApi'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const product = ref(null)
const loadingProduct = ref(true)
const error = ref(false)
const quantity = ref(Number(route.query.qty) || 1)
const selectedPayment = ref('cod')
const submitting = ref(false)
const unreadCount = ref(0)

const form = reactive({
  name: authStore.user?.name || '',
  phone_number: authStore.user?.phone_number || '',
  shipping_address: '',
  note: '',
})

const errors = reactive({
  name: '',
  phone_number: '',
  shipping_address: '',
})

// Toast notification state
const toastMsg = ref('')
const toastType = ref('')
const toastVisible = ref(false)
let toastTimer = null

const userInitials = computed(() => {
  const name = authStore.user?.name || 'P'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
})

const productEmoji = computed(() => {
  const emojis = ['🍱', '🍛', '🧆', '🍲', '🥙', '🍗', '🥟', '🍚', '🥘', '🍜', '🥗', '🍣']
  const idx = product.value?.id_product ? product.value.id_product % emojis.length : 0
  return emojis[idx]
})

const totalPrice = computed(() => {
  if (!product.value) return 0
  return product.value.price * quantity.value
})

function formatPrice(price) {
  return Number(price).toLocaleString('id-ID')
}

function showToast(msg, type = '', duration = 3200) {
  clearTimeout(toastTimer)
  toastMsg.value = msg
  toastType.value = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}

function clearErrors() {
  errors.name = ''
  errors.phone_number = ''
  errors.shipping_address = ''
}

function validateForm() {
  clearErrors()
  let valid = true

  if (!form.name.trim()) {
    errors.name = 'Nama pemesan wajib diisi.'
    valid = false
  }
  if (!form.phone_number.trim()) {
    errors.phone_number = 'Nomor WhatsApp wajib diisi.'
    valid = false
  }
  if (!form.shipping_address.trim()) {
    errors.shipping_address = 'Alamat pengiriman wajib diisi.'
    valid = false
  }

  return valid
}

async function submitOrder() {
  if (!validateForm()) {
    showToast('Harap lengkapi semua field wajib.', 'error')
    return
  }

  submitting.value = true
  try {
    const payload = {
      id_product: product.value.id_product,
      quantity: quantity.value,
      name: form.name,
      phone_number: form.phone_number,
      shipping_address: form.shipping_address,
      payment_method: selectedPayment.value === 'cod' ? 'cod' : 'transfer',
      note: form.note,
    }

    const res = await buyerApi.createOrder(payload)
    if (res.data.success) {
      showToast('Pesanan berhasil dibuat!', 'success')
      // Refetch user data so profile name/phone changes sync
      await authStore.fetchCurrentUser()
      
      // Redirect to whatsapp with chat message
      const storePhone = product.value.store?.phone_number
      if (storePhone) {
        const cleanedPhone = storePhone.replace(/[^0-9]/g, '')
        const msg = encodeURIComponent(
          `Halo, saya ${form.name} telah memesan ${product.value.name} sebanyak ${quantity.value} ${product.value.unit} dengan total Rp ${formatPrice(totalPrice.value)} melalui Kulaan.id.\n\nAlamat: ${form.shipping_address}\nCatatan: ${form.note || '-'}\nMetode Pembayaran: ${selectedPayment.value.toUpperCase()}`
        )
        const waUrl = `https://wa.me/${cleanedPhone}?text=${msg}`
        
        setTimeout(() => {
          window.open(waUrl, '_blank')
          router.push({ name: 'buyer.dashboard' })
        }, 1500)
      } else {
        setTimeout(() => {
          router.push({ name: 'buyer.dashboard' })
        }, 1500)
      }
    }
  } catch (err) {
    console.error('Failed to place order:', err)
    const res = err.response
    if (res?.status === 422) {
      const fieldErrors = res.data.errors ?? {}
      if (fieldErrors.name) errors.name = fieldErrors.name[0]
      if (fieldErrors.phone_number) errors.phone_number = fieldErrors.phone_number[0]
      if (fieldErrors.shipping_address) errors.shipping_address = fieldErrors.shipping_address[0]
      showToast(res.data.message || 'Periksa kembali data Anda.', 'error')
    } else {
      showToast(res?.data?.message || 'Gagal mengirim pesanan. Silakan coba lagi.', 'error')
    }
  } finally {
    submitting.value = false
  }
}

async function fetchProduct() {
  loadingProduct.value = true
  error.value = false
  try {
    const id = route.params.productId
    const res = await buyerApi.getProduct(id)
    product.value = res.data.data
  } catch (err) {
    console.error('Failed to load product:', err)
    error.value = true
  } finally {
    loadingProduct.value = false
  }
}

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

onMounted(() => {
  fetchProduct()
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
  --gray-300: #B2B0A8; --gray-400: #8A8980; --gray-500: #717066; --gray-600: #5C5B54; --gray-700: #3E3D38; --gray-800: #282724; --gray-900: #161514;
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
  box-shadow: var(--shadow-xs);
}
.brand-text {
  font-family: 'Outfit', sans-serif;
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
.nav-badge {
  margin-left: auto;
  background: var(--red-400);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: var(--radius-full);
}
.nav-item.active {
  background: var(--brand-50);
  color: var(--brand-700);
  font-weight: 600;
}
.nav-item.active svg {
  opacity: 1;
}
.sidebar-user {
  margin-top: auto;
  border-top: 1px solid var(--gray-100);
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 12px 0;
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
}
.user-role {
  font-size: 11px;
  color: var(--gray-400);
}

/* Main Content */
.main-content {
  flex: 1;
  background: var(--gray-50);
  padding: 32px;
}
.page-header {
  margin-bottom: 28px;
}
.page-title {
  font-size: 22px;
  font-weight: 700;
  color: var(--gray-900);
  letter-spacing: -.5px;
}
.page-sub {
  font-size: 14px;
  color: var(--gray-400);
  margin-top: 4px;
}

/* Order Layout */
.order-layout {
  display: flex;
  gap: 28px;
  max-width: 960px;
}
.order-left {
  flex: 1;
}
.order-right {
  width: 320px;
  flex-shrink: 0;
}

.form-section {
  background: #fff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-100);
  padding: 24px;
  margin-bottom: 16px;
}
.form-section-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--gray-900);
  letter-spacing: -.2px;
  margin-bottom: 18px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.form-section-num {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: var(--brand-700);
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.form-label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-600);
  margin-bottom: 7px;
}
.req {
  color: var(--red-400);
}
.form-input-plain {
  width: 100%;
  padding: 10px 14px;
  border: 1.5px solid var(--gray-100);
  border-radius: var(--radius-sm);
  font-family: inherit;
  font-size: 14px;
  color: var(--gray-800);
  background: var(--gray-0);
  outline: none;
  transition: all .18s;
  margin-bottom: 14px;
}
.form-input-plain:focus {
  border-color: var(--brand-400);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(29, 139, 111, .08);
}
textarea.form-input-plain {
  resize: vertical;
  min-height: 80px;
  margin-bottom: 0;
}
.grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  margin-bottom: 14px;
}
.grid-2 .form-input-plain {
  margin-bottom: 0;
}

.payment-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.payment-option {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  border-radius: var(--radius-md);
  border: 1.5px solid var(--gray-100);
  cursor: pointer;
  transition: all .15s;
}
.payment-option:hover {
  border-color: var(--brand-300);
}
.payment-option.selected {
  border-color: var(--brand-500);
  background: var(--brand-50);
}
.payment-radio {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 2px solid var(--gray-300);
  background: #fff;
  flex-shrink: 0;
  position: relative;
}
.payment-radio.checked {
  border-color: var(--brand-600);
}
.payment-radio.checked::after {
  content: '';
  position: absolute;
  inset: 3px;
  background: var(--brand-600);
  border-radius: 50%;
}
.payment-icon {
  font-size: 24px;
}
.payment-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-800);
}
.payment-desc {
  font-size: 13px;
  color: var(--gray-400);
  margin-top: 2px;
}

.order-summary-card {
  background: #fff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-100);
  padding: 20px;
  position: sticky;
  top: 32px;
}
.order-summary-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 16px;
}
.order-item-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}
.order-item-thumb {
  width: 64px;
  height: 64px;
  border-radius: var(--radius-sm);
  background: var(--gray-50);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  overflow: hidden;
}
.product-thumb-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.order-item-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-800);
  line-height: 1.3;
}
.order-item-qty {
  font-size: 12px;
  color: var(--gray-400);
  margin-top: 2px;
}
.order-item-price {
  margin-left: auto;
  font-size: 14px;
  font-weight: 700;
  color: var(--brand-700);
}
.order-divider {
  border: none;
  border-top: 1px dashed var(--gray-100);
  margin: 14px 0;
}

.order-total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
}
.order-total-label {
  font-size: 13px;
  color: var(--gray-500);
}
.order-total-value {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-800);
}
.order-grand-row {
  display: flex;
  justify-content: space-between;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1.5px solid var(--gray-100);
}
.order-grand-label {
  font-size: 15px;
  font-weight: 700;
  color: var(--gray-900);
}
.order-grand-value {
  font-size: 18px;
  font-weight: 800;
  color: var(--brand-700);
}

.btn-order {
  width: 100%;
  padding: 14px;
  border-radius: var(--radius-md);
  background: var(--brand-600);
  color: #fff;
  font-family: inherit;
  font-size: 14px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  margin-top: 16px;
  transition: all .18s;
  letter-spacing: -.1px;
}
.btn-order:hover:not(:disabled) {
  background: var(--brand-700);
}
.btn-order:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.order-summary-footer-text {
  text-align: center;
  font-size: 12px;
  color: var(--gray-400);
  margin-top: 10px;
}

.error-msg {
  color: var(--red-400);
  font-size: 11px;
  font-weight: 500;
  display: block;
  margin-top: -10px;
  margin-bottom: 10px;
}

.loading-state, .error-state {
  text-align: center;
  padding: 40px;
  background: #fff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-100);
}

/* Toast Notification */
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
