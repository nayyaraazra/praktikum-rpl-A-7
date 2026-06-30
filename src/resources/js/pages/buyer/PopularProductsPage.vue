<template>
  <div class="app-layout">
    <BuyerSidebar />

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
            <div v-if="rank2.image_url" class="podium-img-wrap">
              <img :src="rank2.image_url" :alt="rank2.name" class="podium-img" />
            </div>
            <div v-else class="podium-emoji">{{ productEmoji(rank2) }}</div>
            <div class="podium-title">{{ rank2.name }}</div>
            <div class="podium-store" @click.stop="goToStore(rank2.store?.id_store)">{{ rank2.store?.store_name || '' }}</div>
            <div class="podium-price">Rp {{ formatPrice(rank2.price) }}</div>
            <div class="podium-sold">Terjual {{ rank2.sold_count || 0 }} pcs</div>
            <div v-if="rank2.store && !isStoreOpen(rank2.store.operating_hours)" class="podium-closed-overlay">
              <span class="podium-closed-text">TUTUP</span>
            </div>
          </div>

          <div class="podium-card rank-1" v-if="rank1" @click="goToProduct(rank1.id_product)">
            <div class="podium-rank">1</div>
            <div v-if="rank1.image_url" class="podium-img-wrap">
              <img :src="rank1.image_url" :alt="rank1.name" class="podium-img" />
            </div>
            <div v-else class="podium-emoji">{{ productEmoji(rank1) }}</div>
            <div class="podium-title">{{ rank1.name }}</div>
            <div class="podium-store" @click.stop="goToStore(rank1.store?.id_store)">{{ rank1.store?.store_name || '' }}</div>
            <div class="podium-price">Rp {{ formatPrice(rank1.price) }}</div>
            <div class="podium-sold">Terjual {{ rank1.sold_count || 0 }} pcs</div>
            <div v-if="rank1.store && !isStoreOpen(rank1.store.operating_hours)" class="podium-closed-overlay">
              <span class="podium-closed-text">TUTUP</span>
            </div>
          </div>

          <div class="podium-card" v-if="rank3" @click="goToProduct(rank3.id_product)">
            <div class="podium-rank">3</div>
            <div v-if="rank3.image_url" class="podium-img-wrap">
              <img :src="rank3.image_url" :alt="rank3.name" class="podium-img" />
            </div>
            <div v-else class="podium-emoji">{{ productEmoji(rank3) }}</div>
            <div class="podium-title">{{ rank3.name }}</div>
            <div class="podium-store" @click.stop="goToStore(rank3.store?.id_store)">{{ rank3.store?.store_name || '' }}</div>
            <div class="podium-price">Rp {{ formatPrice(rank3.price) }}</div>
            <div class="podium-sold">Terjual {{ rank3.sold_count || 0 }} pcs</div>
            <div v-if="rank3.store && !isStoreOpen(rank3.store.operating_hours)" class="podium-closed-overlay">
              <span class="podium-closed-text">TUTUP</span>
            </div>
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
              <div v-if="product.store && !isStoreOpen(product.store.operating_hours)" class="product-closed-overlay">
                <span class="product-closed-text">TUTUP</span>
              </div>
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
import BuyerSidebar from '@/components/common/BuyerSidebar.vue'
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { buyerApi } from '@/services/api/buyerApi'
import { isStoreOpen } from '@/services/storeHelper'

const router = useRouter()
const authStore = useAuthStore()

const products = ref([])
const loading = ref(true)



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







onMounted(() => {
  fetchPopularProducts()
  
  
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

.main-content {
  flex: 1;
  padding: 32px;
  max-width: 1100px;
}

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

.podium-img-wrap {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  overflow: hidden;
  margin: 12px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fdfdfd;
  border: 1.5px solid var(--gray-100);
  box-shadow: var(--shadow-xs);
}

.rank-1 .podium-img-wrap {
  width: 96px;
  height: 96px;
  border-radius: 16px;
  border-color: var(--amber-200);
  box-shadow: 0 4px 12px rgba(217, 119, 6, 0.12);
}

.podium-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 16px;
}

.product-card {
  background: #fff;
  border-radius: var(--radius-lg);
  border: 1px solid var(--gray-100);
  overflow: hidden;
  cursor: pointer;
  transition: all .22s;
  box-shadow: var(--shadow-xs);
  position: relative;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
  border-color: var(--gray-200);
}

.product-card-thumb {
  height: 160px;
  position: relative;
  overflow: hidden;
  background: var(--gray-50);
}

.product-card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.product-card-thumb-emoji {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 56px;
}

.product-card-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: #fff;
  border-radius: var(--radius-full);
  padding: 3px 8px;
  font-size: 11px;
  font-weight: 600;
  color: var(--brand-700);
  box-shadow: var(--shadow-xs);
}

.product-card-info {
  padding: 14px 16px 16px;
}

.product-card-store {
  font-size: 11px;
  color: var(--gray-400);
  font-weight: 500;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: .3px;
  cursor: pointer;
  transition: color .15s;
}

.product-card-store:hover {
  color: var(--brand-600);
}

.product-card-name {
  font-size: 15px;
  font-weight: 600;
  color: var(--gray-900);
  margin-bottom: 10px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-card-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.product-card-price {
  font-size: 16px;
  font-weight: 700;
  color: var(--brand-700);
}

.product-card-rating {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  color: var(--gray-600);
}

.star-icon {
  font-size: 15px;
  line-height: 1;
}

.review-count {
  color: var(--gray-400);
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

/* Store Closed Overlay Styles */
.product-closed-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(239, 68, 68, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(1.5px);
  z-index: 5;
}

.product-closed-text {
  background: #EF4444;
  color: #fff;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: var(--radius-full);
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}

.podium-closed-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(239, 68, 68, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(1.5px);
  z-index: 5;
}

.podium-closed-text {
  background: #EF4444;
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: var(--radius-full);
  letter-spacing: 0.5px;
  box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
}
</style>

