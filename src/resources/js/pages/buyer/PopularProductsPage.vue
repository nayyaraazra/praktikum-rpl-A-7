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

      <div class="nav-section">
        <div class="nav-section-label">Eksplorasi</div>
        <router-link class="nav-item" :to="{ name: 'buyer.stores' }">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
          </svg>
          Toko UMKM
        </router-link>
        <router-link class="nav-item active" :to="{ name: 'buyer.popular' }">
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

    <main class="main-content">
      <div class="page-header">
        <h1 class="page-title">Produk Terlaris & Populer</h1>
        <p class="page-sub">Produk lokal paling laris dan paling sering dipesan oleh pembeli</p>
      </div>

      <div v-if="loading" class="loading-state">Memuat produk populer...</div>

      <template v-else-if="products.length > 0">
        <div class="podium-wrap">
          <div class="podium-card" v-if="rank2" @click="goToProduct(rank2.id_product)">
            <div class="podium-rank">2</div>
            <div class="podium-emoji">{{ productEmoji(rank2) }}</div>
            <div class="podium-title">{{ rank2.name }}</div>
            <div class="podium-store" @click.stop="goToStore(rank2.store?.id_store)">{{ rank2.store?.store_name || '' }}</div>
            <div class="podium-price">Rp {{ formatPrice(rank2.price) }}</div>
            <div class="podium-sold">Terjual {{ rank2.sold_count || 0 }} pcs</div>
          </div>

          <div class="podium-card rank-1" v-if="rank1" @click="goToProduct(rank1.id_product)">
            <div class="podium-rank">1</div>
            <div class="podium-emoji">{{ productEmoji(rank1) }}</div>
            <div class="podium-title">{{ rank1.name }}</div>
            <div class="podium-store" @click.stop="goToStore(rank1.store?.id_store)">{{ rank1.store?.store_name || '' }}</div>
            <div class="podium-price">Rp {{ formatPrice(rank1.price) }}</div>
            <div class="podium-sold">Terjual {{ rank1.sold_count || 0 }} pcs</div>
          </div>

          <div class="podium-card" v-if="rank3" @click="goToProduct(rank3.id_product)">
            <div class="podium-rank">3</div>
            <div class="podium-emoji">{{ productEmoji(rank3) }}</div>
            <div class="podium-title">{{ rank3.name }}</div>
            <div class="podium-store" @click.stop="goToStore(rank3.store?.id_store)">{{ rank3.store?.store_name || '' }}</div>
            <div class="podium-price">Rp {{ formatPrice(rank3.price) }}</div>
            <div class="podium-sold">Terjual {{ rank3.sold_count || 0 }} pcs</div>
          </div>
        </div>

        <div class="grid-section-header" v-if="otherProducts.length > 0">
          <div class="grid-section-title">Produk Populer Lainnya</div>
        </div>

        <div class="products-grid" v-if="otherProducts.length > 0">
          <div
            v-for="product in otherProducts"
            :key="product.id_product"
            class="product-card"
            @click="goToProduct(product.id_product)"
          >
            <div class="product-card-thumb" :style="thumbStyle(product)">
              <div v-if="!product.image_url" class="product-card-thumb-emoji">{{ productEmoji(product) }}</div>
              <img v-else :src="product.image_url" :alt="product.name" class="product-card-img" />
              <div v-if="product.category" class="product-card-badge">{{ product.category.name_category }}</div>
            </div>
            <div class="product-card-info">
              <div class="product-card-store" @click.stop="goToStore(product.store?.id_store)">{{ product.store?.store_name || 'Toko UMKM' }}</div>
              <div class="product-card-name">{{ product.name }}</div>
              <div class="product-card-bottom">
                <div class="product-card-price">Rp {{ formatPrice(product.price) }}</div>
                <div class="product-card-rating">
                  <span class="star-icon">⭐</span>{{ product.rating || '-' }} <span class="review-count">({{ product.review_count || 0 }})</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>

      <div v-else class="empty-state">
        <div class="empty-icon">📊</div>
        <h3>Belum ada data</h3>
        <p>Belum ada produk yang terjual. Ajak lebih banyak pembeli untuk bertransaksi!</p>
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

const products = ref([])
const loading = ref(true)
const unreadCount = ref(0)
const activeOrdersCount = ref(0)

const thumbColors = [
  'linear-gradient(135deg, #FFF3D6, #FFE8A3)',
  'linear-gradient(135deg, #E6F0FF, #C5D8FF)',
  'linear-gradient(135deg, #F0FFF4, #C6F6D5)',
  'linear-gradient(135deg, #FFF0F0, #FFD6D6)',
  'linear-gradient(135deg, #F3F0FF, #DDD6FF)',
  'linear-gradient(135deg, #FFF9EE, #FEF3C7)',
  'linear-gradient(135deg, #E8F4F0, #D1EDE5)',
  'linear-gradient(135deg, #FDF3E3, #FDEDCC)',
  'linear-gradient(135deg, #FCEBEB, #FDDDD8)',
]

const foodEmojis = ['🍱', '🍛', '🧆', '🍲', '🥙', '🍗', '🥟', '🌿', '🍚', '🥘', '🍜', '🥗', '🍣']

const userInitials = computed(() => {
  const name = authStore.user?.name || 'P'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
})

const rank1 = computed(() => products.value[0] || null)
const rank2 = computed(() => products.value[1] || null)
const rank3 = computed(() => products.value[2] || null)
const otherProducts = computed(() => products.value.slice(3) || [])

function thumbStyle(product) {
  if (product.image_url) return {}
  const idx = product.id_product ? product.id_product % thumbColors.length : 0
  return { background: thumbColors[idx] }
}

function productEmoji(product) {
  const idx = product.id_product ? product.id_product % foodEmojis.length : 0
  return foodEmojis[idx]
}

function formatPrice(price) {
  return Number(price).toLocaleString('id-ID')
}

function goToProduct(id) {
  router.push({ name: 'buyer.product-detail', params: { id } })
}

function goToStore(id) {
  if (!id) return
  router.push({ name: 'buyer.store', params: { id } })
}

async function fetchPopularProducts() {
  loading.value = true
  try {
    const res = await buyerApi.getPopularProducts()
    products.value = res.data.data || []
  } catch (err) {
    console.error('Failed to fetch popular products:', err)
    products.value = []
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
  fetchPopularProducts()
  fetchUnreadNotificationsCount()
  fetchActiveOrdersCount()
})
</script>

<style scoped>
.podium-wrap {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 32px;
  align-items: end;
}

.podium-card {
  background: #fff;
  border-radius: var(--radius-lg);
  border: 1.5px solid var(--gray-100);
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  text-align: center;
  box-shadow: var(--shadow-sm);
  overflow: hidden;
  cursor: pointer;
  transition: all .22s;
}

.podium-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.podium-rank {
  position: absolute;
  top: 12px;
  left: 12px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--brand-700);
  color: #fff;
  font-family: 'Outfit', sans-serif;
  font-weight: 700;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.podium-card.rank-1 {
  border-color: var(--amber-400);
  transform: translateY(-8px);
  box-shadow: var(--shadow-md);
}

.podium-card.rank-1:hover {
  transform: translateY(-10px);
}

.podium-card.rank-1 .podium-rank {
  background: var(--amber-400);
}

.podium-emoji {
  font-size: 64px;
  margin: 12px 0;
}

.podium-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--gray-900);
  margin-bottom: 4px;
}

.podium-store {
  font-size: 11px;
  font-weight: 600;
  color: var(--gray-400);
  text-transform: uppercase;
  margin-bottom: 12px;
  cursor: pointer;
  transition: color .15s;
}

.podium-store:hover {
  color: var(--brand-600);
}

.podium-price {
  font-size: 16px;
  font-weight: 800;
  color: var(--brand-700);
  margin-bottom: 8px;
}

.podium-sold {
  font-size: 12px;
  font-weight: 600;
  color: var(--brand-600);
  background: var(--brand-50);
  padding: 4px 10px;
  border-radius: var(--radius-full);
}

.grid-section-header {
  margin: 24px 0 16px;
}

.grid-section-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--gray-900);
}

.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: var(--gray-400);
  font-size: 14px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
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
