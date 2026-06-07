<template>
  <div class="app-layout">
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

    <main class="main-content">
      <div v-if="loading" class="loading-state">
        <div class="skeleton-detail">
          <div class="skeleton-detail-img"></div>
          <div class="skeleton-detail-text">
            <div class="skeleton-line"></div>
            <div class="skeleton-line short"></div>
            <div class="skeleton-line"></div>
          </div>
        </div>
      </div>

      <div v-else-if="error" class="error-state">
        <div class="empty-icon">😕</div>
        <h3>Produk tidak ditemukan</h3>
        <p>Produk mungkin sudah tidak tersedia atau link tidak valid.</p>
        <router-link :to="{ name: 'buyer.dashboard' }" class="btn-back-shop">Kembali ke Katalog</router-link>
      </div>

      <div v-else-if="product" class="detail-layout">
        <div class="detail-left">
          <router-link class="detail-back" :to="{ name: 'buyer.dashboard' }">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
              <polyline points="15 18 9 12 15 6" stroke-width="2" />
            </svg>
            Kembali ke hasil pencarian
          </router-link>

          <div class="detail-img-main">
            <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="detail-img" />
            <span v-else class="detail-img-emoji">{{ productEmoji }}</span>
          </div>

          <div v-if="product.reviews && product.reviews.length > 0" class="reviews-section">
            <h3 class="reviews-title">Ulasan Pembeli</h3>
            <div class="reviews-list">
              <div v-for="(review, i) in product.reviews" :key="i" class="review-card">
                <div class="review-header">
                  <div class="review-avatar">{{ reviewInitials(review) }}</div>
                  <div>
                    <div class="review-name">{{ review.user?.name || 'Anonymous' }}</div>
                    <div class="review-stars">
                      <span v-for="s in 5" :key="s" class="star" :class="{ filled: s <= review.rating }">★</span>
                    </div>
                  </div>
                  <div class="review-date">{{ formatDate(review.created_at) }}</div>
                </div>
                <p v-if="review.comment" class="review-comment">{{ review.comment }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="detail-right">
          <div class="detail-category">{{ product.category?.name_category || 'Produk' }}</div>
          <h1 class="detail-name">{{ product.name }}</h1>

          <div class="detail-rating-row">
            <div class="stars">
              <span v-for="s in 5" :key="s" class="star-filled" :class="{ dim: s > Math.round(product.rating) }">★</span>
            </div>
            <span class="rating-count">{{ product.rating }} dari 5 · {{ product.review_count }} ulasan</span>
          </div>

          <div class="detail-price">Rp {{ formatPrice(product.price) }}</div>
          <div class="detail-unit">
            per {{ product.unit }} &middot; Min. {{ product.min_order }} {{ product.unit }} &middot; Stok: {{ product.stock }}
          </div>

          <div v-if="product.description" class="detail-desc-wrap">
            <div class="detail-desc-label">Deskripsi Produk</div>
            <p class="detail-desc">{{ product.description }}</p>
          </div>

          <div v-if="product.store" class="store-card">
          <img
            v-if="product.store?.logo"
            :src="product.store.logo"
            class="store-logo-img"
            alt="Logo Toko"
          />
          <div v-else class="store-avatar">🏪</div>
          <div>
              <div class="store-name">{{ product.store.store_name }}</div>
              <div class="store-location">{{ product.store.address || product.store.district }}</div>
            </div>
            <div class="store-verified">✓ Terverifikasi</div>
          </div>

          <div v-if="product.store?.payment_accounts && product.store.payment_accounts.length > 0" class="payment-section">
            <div class="payment-label">Metode Pembayaran</div>
            <div v-for="(pa, i) in product.store.payment_accounts" :key="i" class="payment-item">
              <div class="payment-icon">💳</div>
              <div>
                <div class="payment-name">{{ pa.bank_name }} — {{ pa.account_number }}</div>
                <div class="payment-owner">{{ pa.account_name }}</div>
              </div>
            </div>
          </div>

          <div class="qty-section">
            <div class="qty-label">Jumlah Pesanan</div>
            <div class="qty-control">
              <button class="qty-btn" @click="decrementQty">−</button>
              <input class="qty-input" type="number" v-model.number="quantity" :min="product.min_order" :max="product.stock" readonly />
              <button class="qty-btn qty-btn-right" @click="incrementQty">+</button>
            </div>
          </div>

          <button class="btn-order" :disabled="product.stock < 1" @click="orderNow">
            🛒 Pesan Sekarang — Rp {{ formatPrice(totalPrice) }}
          </button>
          <a v-if="waUrl" :href="waUrl" target="_blank" class="btn-whatsapp">
            <svg viewBox="0 0 24 24" fill="#25D366" width="20" height="20">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Hubungi via WhatsApp
          </a>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { buyerApi } from '@/services/api/buyerApi'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const product = ref(null)
const loading = ref(true)
const error = ref(false)
const quantity = ref(1)
const unreadCount = ref(0)
const activeOrdersCount = ref(0)

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

const waUrl = computed(() => {
  if (!product.value?.store?.phone_number) return null
  const phone = product.value.store.phone_number.replace(/[^0-9]/g, '')
  const msg = encodeURIComponent(`Halo, saya ingin memesan ${product.value.name} (${quantity.value} ${product.value.unit}) seharga Rp ${formatPrice(totalPrice.value)}.`)
  return `https://wa.me/${phone}?text=${msg}`
})

function formatPrice(price) {
  return Number(price).toLocaleString('id-ID')
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diff = now - date
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  if (days === 0) return 'Hari ini'
  if (days === 1) return '1 hari lalu'
  if (days < 7) return `${days} hari lalu`
  if (days < 30) return `${Math.floor(days / 7)} minggu lalu`
  return date.toLocaleDateString('id-ID')
}

function reviewInitials(review) {
  const name = review.user?.name || 'A'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
}

function incrementQty() {
  if (product.value && quantity.value < product.value.stock) {
    quantity.value++
  }
}

function decrementQty() {
  if (product.value && quantity.value > product.value.min_order) {
    quantity.value--
  }
}

function orderNow() {
  if (!product.value || product.value.stock < 1) return
  router.push({
    name: 'buyer.order',
    params: { productId: product.value.id_product },
    query: { qty: quantity.value }
  })
}

async function fetchProduct() {
  loading.value = true
  error.value = false
  try {
    const id = route.params.id
    const res = await buyerApi.getProduct(id)
    product.value = res.data.data
    quantity.value = product.value.min_order || 1
  } catch (err) {
    console.error('Failed to load product:', err)
    error.value = true
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
  fetchProduct()
  fetchUnreadNotificationsCount()
  fetchActiveOrdersCount()
})
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
  background-color: var(--gray-50);
  background-image: radial-gradient(var(--gray-200) 1px, transparent 1px);
  background-size: 20px 20px;
  color: var(--gray-800);
}

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
}

.user-role {
  font-size: 11px;
  color: var(--gray-400);
}

.main-content {
  flex: 1;
  padding: 32px;
}

.detail-layout {
  display: flex;
  gap: 32px;
  max-width: 1100px;
  margin: 0 auto;
  align-items: flex-start;
}

.detail-left {
  flex: 1;
  min-width: 0;
}

.detail-back {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 500;
  color: var(--gray-500);
  cursor: pointer;
  margin-bottom: 20px;
  text-decoration: none;
  transition: color .15s;
}

.detail-back:hover {
  color: var(--brand-600);
}

.detail-img-main {
  background: var(--gray-50);
  border: 1px solid var(--gray-100);
  border-radius: var(--radius-xl);
  height: 340px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}

.detail-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.detail-img-emoji {
  font-size: 100px;
  line-height: 1;
}

.reviews-section {
  margin-top: 32px;
}

.reviews-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 16px;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.review-card {
  background: #fff;
  border: 1px solid var(--gray-100);
  border-radius: var(--radius-md);
  padding: 16px;
}

.review-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
}

.review-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--brand-100);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 700;
  color: var(--brand-700);
  flex-shrink: 0;
}

.review-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-800);
}

.review-stars {
  display: flex;
  gap: 1px;
  font-size: 12px;
}

.star {
  color: var(--gray-200);
}

.star.filled {
  color: var(--amber-400);
}

.review-date {
  margin-left: auto;
  font-size: 12px;
  color: var(--gray-400);
  white-space: nowrap;
}

.review-comment {
  font-size: 13px;
  color: var(--gray-600);
  line-height: 1.5;
  margin: 0;
}

.detail-right {
  width: 360px;
  flex-shrink: 0;
  position: sticky;
  top: 32px;
}

.detail-category {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: var(--brand-600);
  margin-bottom: 8px;
}

.detail-name {
  font-size: 26px;
  font-weight: 700;
  color: var(--gray-900);
  letter-spacing: -.5px;
  line-height: 1.2;
  margin-bottom: 10px;
}

.detail-rating-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.stars {
  display: flex;
  gap: 2px;
}

.star-filled {
  color: var(--amber-400);
  font-size: 15px;
}

.star-filled.dim {
  opacity: .35;
}

.rating-count {
  font-size: 13px;
  color: var(--gray-400);
}

.detail-price {
  font-size: 32px;
  font-weight: 800;
  color: var(--brand-700);
  letter-spacing: -.5px;
  margin-bottom: 4px;
}

.detail-unit {
  font-size: 13px;
  color: var(--gray-400);
  margin-bottom: 20px;
}

.detail-desc-wrap {
  margin-bottom: 20px;
}

.detail-desc-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-600);
  margin-bottom: 8px;
}

.detail-desc {
  font-size: 14px;
  line-height: 1.65;
  color: var(--gray-600);
  margin: 0;
}

.store-card {
  background: var(--gray-50);
  border: 1px solid var(--gray-100);
  border-radius: var(--radius-md);
  padding: 14px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.store-avatar {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  background: var(--brand-100);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.store-logo-img {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-sm);
  object-fit: cover;
  border: 1px solid var(--gray-100);
  flex-shrink: 0;
}

.store-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-800);
}

.store-location {
  font-size: 12px;
  color: var(--gray-400);
  margin-top: 2px;
}

.store-verified {
  margin-left: auto;
  background: var(--brand-50);
  color: var(--brand-700);
  font-size: 11px;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: var(--radius-full);
  white-space: nowrap;
}

.payment-section {
  margin-bottom: 16px;
}

.payment-label {
  font-size: 12px;
  font-weight: 600;
  color: var(--gray-400);
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: .4px;
}

.payment-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 0;
}

.payment-icon {
  font-size: 16px;
  width: 24px;
  text-align: center;
}

.payment-name {
  font-size: 13px;
  font-weight: 500;
  color: var(--gray-700);
}

.payment-owner {
  font-size: 11px;
  color: var(--gray-400);
}

.qty-section {
  margin-bottom: 16px;
}

.qty-label {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-600);
  margin-bottom: 8px;
}

.qty-control {
  display: flex;
  align-items: center;
}

.qty-btn {
  width: 36px;
  height: 36px;
  border-radius: var(--radius-sm) 0 0 var(--radius-sm);
  border: 1.5px solid var(--gray-100);
  background: var(--gray-50);
  font-size: 18px;
  font-weight: 700;
  cursor: pointer;
  color: var(--gray-600);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .15s;
  font-family: inherit;
}

.qty-btn-right {
  border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
}

.qty-btn:hover {
  background: var(--brand-50);
  color: var(--brand-600);
  border-color: var(--brand-300);
}

.qty-input {
  width: 56px;
  height: 36px;
  border: 1.5px solid var(--gray-100);
  border-left: none;
  border-right: none;
  text-align: center;
  font-family: inherit;
  font-size: 15px;
  font-weight: 600;
  background: #fff;
  outline: none;
  color: var(--gray-800);
}

.btn-order {
  width: 100%;
  padding: 14px;
  border-radius: var(--radius-md);
  background: var(--brand-600);
  color: #fff;
  font-family: inherit;
  font-size: 15px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  margin-bottom: 10px;
  transition: all .18s;
  letter-spacing: -.1px;
}

.btn-order:hover:not(:disabled) {
  background: var(--brand-700);
}

.btn-order:disabled {
  opacity: .4;
  cursor: default;
}

.btn-whatsapp {
  display: flex;
  width: 100%;
  padding: 13px;
  border-radius: var(--radius-md);
  background: #fff;
  color: #25D366;
  font-weight: 600;
  font-size: 14px;
  border: 1.5px solid #D4E8D5;
  cursor: pointer;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all .18s;
  font-family: inherit;
  text-decoration: none;
  box-sizing: border-box;
}

.btn-whatsapp:hover {
  background: #F0FBF0;
}

.loading-state {
  max-width: 1100px;
  margin: 0 auto;
}

.skeleton-detail {
  display: flex;
  gap: 32px;
}

.skeleton-detail-img {
  flex: 1;
  height: 340px;
  background: linear-gradient(90deg, var(--gray-50) 25%, var(--gray-100) 50%, var(--gray-50) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
  border-radius: var(--radius-xl);
}

.skeleton-detail-text {
  width: 360px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.skeleton-line {
  height: 16px;
  background: linear-gradient(90deg, var(--gray-50) 25%, var(--gray-100) 50%, var(--gray-50) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
  border-radius: 6px;
}

.skeleton-line.short {
  width: 60%;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

.error-state {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
}

.error-state h3 {
  font-size: 18px;
  color: var(--gray-900);
  margin: 0 0 6px;
  font-weight: 600;
}

.error-state p {
  color: var(--gray-400);
  font-size: 14px;
  margin: 0 0 20px;
}

.btn-back-shop {
  display: inline-block;
  padding: 10px 20px;
  background: var(--brand-600);
  color: #fff;
  border-radius: var(--radius-md);
  font-size: 14px;
  font-weight: 600;
  text-decoration: none;
  transition: background .15s;
}

.btn-back-shop:hover {
  background: var(--brand-700);
}
</style>
