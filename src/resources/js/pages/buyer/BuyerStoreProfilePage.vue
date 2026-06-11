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

      <div class="nav-section">
        <div class="nav-section-label">Eksplorasi</div>
        <router-link class="nav-item active" :to="{ name: 'buyer.stores' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
          </svg>
          Toko UMKM
        </router-link>
        <router-link class="nav-item" :to="{ name: 'buyer.popular' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
          </svg>
          Produk Populer
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

    <!-- ══ MAIN ══ -->
    <main class="profile-main">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
      </div>
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <router-link :to="{ name: 'buyer.stores' }" class="btn-primary">Kembali ke Direktori</router-link>
      </div>

      <template v-else-if="store">
        <!-- Cover -->
        <div class="store-cover">
          <div class="cover-pattern"></div>
          <div class="cover-orb"></div>
        </div>

        <!-- Profile Header -->
        <div class="profile-header">
          <div class="profile-header-inner">
            <div class="store-logo-wrap">
              <div v-if="store.store_logo_url" class="store-logo-img" :style="{ backgroundImage: 'url(' + store.store_logo_url + ')' }"></div>
              <div v-else class="store-logo">{{ storeInitials }}</div>
            </div>
            <div class="store-info">
              <div class="store-name-row">
                <h1 class="store-name">{{ store.store_name }}</h1>
                <span v-if="store.verification_status === 'disetujui'" class="verified-badge">Terverifikasi</span>
              </div>
              <p class="store-desc">{{ store.description || 'Belum ada deskripsi toko.' }}</p>
              <div class="store-meta">
                <span class="meta-item">📍 {{ store.district || 'Kelurahan Jebres, Surakarta' }}</span>
                <span class="meta-item">📅 Bergabung {{ joinedDate }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Body -->
        <div class="store-body">
          <div class="store-body-grid">
            <!-- Left: Products -->
            <div class="products-section">
              <h3 class="section-title">Produk Unggulan</h3>
              <div v-if="products.length === 0" class="empty-products">
                <p>Toko ini belum memiliki produk aktif.</p>
              </div>
              <div v-else class="products-grid">
                <router-link
                  v-for="(p, i) in products"
                  :key="p.id_product"
                  :to="{ name: 'buyer.product-detail', params: { id: p.id_product } }"
                  class="product-card-store"
                >
                  <div v-if="p.image_url" class="product-thumb-img" :style="{ backgroundImage: 'url(' + p.image_url + ')' }"></div>
                  <div v-else :class="['product-thumb', thumbBgClass(i)]">
                    <span class="product-thumb-emoji">{{ productEmoji(p.name) }}</span>
                  </div>
                  <div class="product-info">
                    <div class="product-name">{{ p.name }}</div>
                    <div class="product-price">{{ formatRupiah(p.price) }}</div>
                    <div class="product-category">{{ p.category?.name_category || '' }}</div>
                  </div>
                </router-link>
              </div>
            </div>

            <!-- Right: Info & Contact -->
            <div class="info-section">
              <div class="info-card">
                <h4 class="info-card-title">Informasi Toko</h4>
                <div class="info-list">
                  <div class="info-row">
                    <span class="info-label">Jam Buka</span>
                    <span class="info-value">{{ store.operating_hours || '-' }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Kategori</span>
                    <span class="info-value">{{ store.store_category || '-' }}</span>
                  </div>
                  <div class="info-row">
                    <span class="info-label">Alamat</span>
                    <span class="info-value">{{ store.address || '-' }}</span>
                  </div>
                </div>
              </div>

              <div class="info-card">
                <h4 class="info-card-title">Kontak & Sosial Media</h4>
                <div class="contact-list">
                  <a v-if="store.whatsapp" :href="'https://wa.me/' + formatWhatsApp(store.whatsapp)" target="_blank" class="contact-btn whatsapp">
                    <span>Hubungi via WhatsApp</span>
                  </a>
                  <a v-else class="contact-btn whatsapp disabled">
                    <span>WhatsApp tidak tersedia</span>
                  </a>
                  <a v-if="store.instagram" :href="'https://instagram.com/' + formatInstagram(store.instagram)" target="_blank" class="contact-btn instagram">
                    <span>Instagram @{{ formatInstagram(store.instagram) }}</span>
                  </a>
                  <a v-else class="contact-btn instagram disabled">
                    <span>Instagram tidak tersedia</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { buyerApi } from '@/services/api/buyerApi'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const store = ref(null)
const products = ref([])
const loading = ref(true)
const error = ref('')

const unreadCount = ref(0)
const activeOrdersCount = ref(0)

const userInitials = computed(() => {
  const name = authStore.user?.name || 'P'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
})

const storeInitials = computed(() => {
  const name = store.value?.store_name || ''
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || 'TK'
})

const joinedDate = computed(() => {
  if (!store.value?.created_at) return '-'
  const d = new Date(store.value.created_at)
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
  return months[d.getMonth()] + ' ' + d.getFullYear()
})

const thumbColors = ['thumb-bg-1', 'thumb-bg-2', 'thumb-bg-3', 'thumb-bg-4', 'thumb-bg-5']
function thumbBgClass(i) {
  return thumbColors[i % thumbColors.length]
}

function productEmoji(name) {
  const emojis = ['🍱', '🍚', '🧁', '🥘', '🍜', '🥗', '🍛', '🍝', '🥟', '🍩', '🍪', '🥐']
  let hash = 0
  for (let i = 0; i < name.length; i++) hash = ((hash << 5) - hash) + name.charCodeAt(i)
  return emojis[Math.abs(hash) % emojis.length]
}

function formatRupiah(value) {
  const num = Number(value)
  if (num >= 1000000) return 'Rp' + (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'jt'
  if (num >= 1000) return 'Rp' + (num / 1000).toFixed(1).replace(/\.0$/, '') + 'rb'
  return 'Rp' + num.toLocaleString('id-ID')
}

function formatWhatsApp(phone) {
  if (!phone) return ''
  let cleaned = phone.replace(/\D/g, '')
  if (cleaned.startsWith('0')) cleaned = '62' + cleaned.slice(1)
  return cleaned
}

function formatInstagram(ig) {
  if (!ig) return ''
  return ig.replace('@', '').trim()
}

async function fetchStoreProfile() {
  loading.value = true
  error.value = ''
  try {
    const res = await buyerApi.getStore(route.params.id)
    store.value = res.data.data
    products.value = res.data.data?.products || []
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'Toko tidak ditemukan.'
    } else {
      error.value = 'Gagal memuat profil toko.'
    }
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
  fetchStoreProfile()
  fetchUnreadNotificationsCount()
  fetchActiveOrdersCount()
})
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
}

.profile-main {
  flex: 1;
  background: #F4F3F0;
  overflow-y: auto;
}

/* Loading / Error */
.loading-state, .error-state {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 80px 24px; gap: 12px;
  color: #5C5B54; font-size: 15px;
}
.spinner {
  width: 32px; height: 32px;
  border: 3px solid #E8E7E2;
  border-top-color: rgb(24,95,165);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.error-state .btn-primary {
  padding: 10px 20px;
  border-radius: 8px;
  background: rgb(24,95,165);
  color: #fff;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  margin-top: 10px;
}

/* Cover */
.store-cover {
  height: 200px;
  background: linear-gradient(135deg, #124F85 0%, rgb(24,95,165) 60%, #3D7DBD 100%);
  position: relative;
  overflow: hidden;
  flex-shrink: 0;
}
.cover-pattern {
  position: absolute; inset: 0;
  background-image: radial-gradient(rgba(255,255,255,0.15) 1px, transparent 1px);
  background-size: 24px 24px;
}
.cover-orb {
  position: absolute;
  top: -60px; right: -60px;
  width: 240px; height: 240px;
  background: rgba(255,255,255,0.06);
  border-radius: 50%;
}

/* Profile Header */
.profile-header {
  background: #fff;
  border-bottom: 1px solid #E8E7E2;
  padding: 0 40px 32px;
}
.profile-header-inner {
  display: flex;
  gap: 32px;
  align-items: flex-start;
  margin-top: -64px;
  padding-top: 0;
}
.store-logo-wrap {
  width: 128px; height: 128px;
  border-radius: 20px;
  background: #fff;
  padding: 6px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.1);
  flex-shrink: 0;
  position: relative;
  z-index: 2;
}
.store-logo {
  width: 100%; height: 100%;
  background: linear-gradient(135deg, #FDF3E3, #FFF3D6);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 44px;
  color: #854F0B;
  font-weight: 800;
}
.store-logo-img {
  width: 100%; height: 100%;
  border-radius: 14px;
  background-size: cover;
  background-position: center;
}
.store-info {
  flex: 1;
  padding-top: 72px;
}
.store-name-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}
.store-name {
  font-size: 28px;
  font-weight: 800;
  color: #282724;
  letter-spacing: -0.5px;
}
.verified-badge {
  background: #E8F1FB;
  color: rgb(24,95,165);
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}
.store-desc {
  color: #5C5B54;
  font-size: 15px;
  margin-bottom: 16px;
  line-height: 1.5;
  max-width: 600px;
}
.store-meta {
  display: flex;
  gap: 24px;
  font-size: 13px;
  flex-wrap: wrap;
}
.meta-item {
  color: #5C5B54;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* Body */
.store-body {
  padding: 28px 40px 40px;
}
.store-body-grid {
  display: grid;
  grid-template-columns: 1fr 300px;
  gap: 32px;
}
.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #282724;
  margin-bottom: 20px;
}
.empty-products {
  color: #8A8980;
  font-size: 14px;
  padding: 24px;
  text-align: center;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #E8E7E2;
}

/* Product cards */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}
.product-card-store {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #E8E7E2;
  overflow: hidden;
  cursor: pointer;
  text-decoration: none;
  display: block;
  transition: all .22s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}
.product-card-store:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.10);
  border-color: #DDE1E9;
}
.product-thumb {
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 52px;
  position: relative;
}
.product-thumb-img {
  height: 140px;
  background-size: cover;
  background-position: center;
  position: relative;
}
.thumb-bg-1 { background: linear-gradient(135deg, #FFF3D6, #FFE8A3); }
.thumb-bg-2 { background: linear-gradient(135deg, #E6F0FF, #C5D8FF); }
.thumb-bg-3 { background: linear-gradient(135deg, #F0FFF4, #C6F6D5); }
.thumb-bg-4 { background: linear-gradient(135deg, #FFF0F0, #FFD6D6); }
.thumb-bg-5 { background: linear-gradient(135deg, #F3F0FF, #DDD6FF); }

.product-info {
  padding: 14px 16px 16px;
}
.product-name {
  font-size: 15px;
  font-weight: 600;
  color: #282724;
  margin-bottom: 8px;
  line-height: 1.3;
}
.product-price {
  font-size: 16px;
  font-weight: 700;
  color: rgb(24,95,165);
  margin-bottom: 4px;
}
.product-category {
  font-size: 12px;
  color: #8A8980;
}

/* Info cards */
.info-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.info-card {
  background: #fff;
  border-radius: 12px;
  border: 1px solid #E8E7E2;
  padding: 20px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}
.info-card-title {
  font-size: 15px;
  font-weight: 700;
  color: #282724;
  margin-bottom: 16px;
}
.info-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.info-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}
.info-label { color: #8A8980; }
.info-value {
  font-weight: 600;
  color: #3E3D38;
  text-align: right;
  max-width: 60%;
}

/* Contact buttons */
.contact-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.contact-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  text-decoration: none;
  transition: all .15s;
  font-family: inherit;
}
.contact-btn.whatsapp {
  background: #fff;
  color: #25D366;
  border: 1.5px solid #D4E8D5;
}
.contact-btn.whatsapp:hover {
  background: #F0FBF0;
}
.contact-btn.instagram {
  background: #F4F3F0;
  color: #3E3D38;
  border: 1.5px solid #E8E7E2;
}
.contact-btn.instagram:hover {
  background: #E8E7E2;
}
.contact-btn.disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

</style>
