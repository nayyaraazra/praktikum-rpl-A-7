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
        <a class="nav-item active" href="#">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <circle cx="11" cy="11" r="8" />
            <path d="m21 21-4.35-4.35" />
          </svg>
          Cari Produk
        </a>
        <a class="nav-item" href="#">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 0 1-8 0" />
          </svg>
          Pesanan Saya
          <span class="nav-badge">2</span>
        </a>
        <a class="nav-item" href="#">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
          </svg>
          Notifikasi
          <span class="nav-badge" style="background:var(--brand-500);">3</span>
        </a>
      </div>

      <div class="nav-section">
        <div class="nav-section-label">Eksplorasi</div>
        <a class="nav-item" href="#">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
          </svg>
          Toko UMKM
        </a>
        <a class="nav-item" href="#">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
          </svg>
          Produk Populer
        </a>
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
    <main class="main-content">
      <!-- Welcome Banner -->
      <div class="welcome-banner">
        <div class="welcome-banner-text">
          <div class="welcome-banner-title">Halo, {{ authStore.user?.name || 'Pembeli' }}! 👋</div>
          <div class="welcome-banner-sub">Temukan produk UMKM lokal terbaik di Jebres hari ini</div>
        </div>
        <div class="welcome-banner-emoji">🛍️</div>
      </div>

      <!-- Page Header -->
      <div class="page-header">
        <h1 class="page-title">Cari Produk UMKM</h1>
        <p class="page-sub">Temukan produk dari UMKM lokal Kelurahan Jebres</p>
      </div>

      <!-- Search & Filter -->
      <div class="search-bar-wrap">
        <div class="search-field">
          <span class="search-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <circle cx="11" cy="11" r="8" />
              <path d="m21 21-4.35-4.35" />
            </svg>
          </span>
          <input
            ref="searchInput"
            v-model="searchQuery"
            class="search-input"
            type="text"
            placeholder="Cari makanan, kerajinan, batik, atau produk lokal lainnya..."
            @input="onSearchInput"
          />
        </div>
        <button class="filter-btn" @click="showPriceFilter = !showPriceFilter">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <line x1="4" y1="6" x2="20" y2="6" />
            <line x1="8" y1="12" x2="16" y2="12" />
            <line x1="10" y1="18" x2="14" y2="18" />
          </svg>
          Filter
        </button>
        <!-- Price Filter Dropdown -->
        <div v-if="showPriceFilter" class="price-filter-dropdown">
          <button
            v-for="option in priceOptions"
            :key="option.value"
            class="price-option"
            :class="{ active: selectedPrice === option.value }"
            @click="selectPrice(option.value)"
          >
            {{ option.label }}
          </button>
        </div>
      </div>

      <!-- Category Pills -->
      <div class="category-row">
        <button
          class="category-pill"
          :class="{ active: selectedCategory === null }"
          @click="selectCategory(null)"
        >
          <span class="category-pill-icon">🔥</span> Semua
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id_category"
          class="category-pill"
          :class="{ active: selectedCategory === cat.id_category }"
          @click="selectCategory(cat.id_category)"
        >
          <span class="category-pill-icon">{{ categoryEmoji(cat.name_category) }}</span>
          {{ cat.name_category }}
        </button>
      </div>

      <!-- Results Info -->
      <div v-if="!loading" class="results-bar">
        <p class="results-count">
          Menampilkan <strong>{{ products.length }}</strong> produk
          <template v-if="searchQuery.trim()"> untuk "<strong style="color:var(--brand-700);">{{ searchQuery.trim() }}</strong>"</template>
        </p>
      </div>

      <!-- Product Grid -->
      <div class="products-section">
        <div v-if="loading" class="loading-grid">
          <div v-for="n in 6" :key="n" class="skeleton-card">
            <div class="skeleton-img"></div>
            <div class="skeleton-text"></div>
            <div class="skeleton-text short"></div>
          </div>
        </div>

        <div v-else-if="products.length === 0" class="empty-state">
          <div class="empty-icon">🔍</div>
          <h3>Produk tidak ditemukan</h3>
          <p>Coba gunakan kata kunci atau filter yang berbeda</p>
        </div>

        <div v-else class="products-grid">
          <div
            v-for="product in products"
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
              <div class="product-card-store">{{ product.store?.store_name || 'Toko UMKM' }}</div>
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
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination">
        <button class="page-btn" :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">&laquo;</button>
        <button
          v-for="p in visiblePages"
          :key="p"
          class="page-btn"
          :class="{ active: p === currentPage }"
          @click="goToPage(p)"
        >{{ p }}</button>
        <button class="page-btn" :disabled="currentPage >= totalPages" @click="goToPage(currentPage + 1)">&raquo;</button>
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

const searchInput = ref(null)
const searchQuery = ref('')
const selectedCategory = ref(null)
const selectedPrice = ref('all')
const showPriceFilter = ref(false)
const categories = ref([])
const products = ref([])
const loading = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = 12

let debounceTimer = null

const userInitials = computed(() => {
  const name = authStore.user?.name || 'P'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
})

const priceOptions = [
  { label: 'Semua Harga', value: 'all' },
  { label: '< Rp 10.000', value: '0-10000' },
  { label: 'Rp 10rb - 25rb', value: '10000-25000' },
  { label: 'Rp 25rb - 50rb', value: '25000-50000' },
  { label: '> Rp 50.000', value: '50000-' },
]

const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const curr = currentPage.value
  let start = Math.max(1, curr - 2)
  let end = Math.min(total, curr + 2)
  if (end - start < 4) {
    if (start === 1) end = Math.min(total, start + 4)
    else start = Math.max(1, end - 4)
  }
  for (let i = start; i <= end; i++) pages.push(i)
  return pages
})

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
const defaultEmojis = ['📦', '✨', '🎁', '💫', '🌟', '⭐', '🔶', '🔵', '🟢', '🟣', '❤️', '💜']

function thumbStyle(product) {
  if (product.image_url) return {}
  const idx = product.id_product ? product.id_product % thumbColors.length : Math.floor(Math.random() * thumbColors.length)
  return { background: thumbColors[idx] }
}

function productEmoji(product) {
  const idx = product.id_product ? product.id_product % foodEmojis.length : 0
  return foodEmojis[idx]
}

function categoryEmoji(name) {
  const map = {
    'Makanan': '🍱', 'Snack': '🥟', 'Kue': '🥟', 'Fashion': '👘', 'Batik': '👘',
    'Kerajinan': '🎁', 'Minuman': '🌿', 'Kosmetik': '💄', 'Jajanan': '🥟',
    'Oleh-oleh': '🎁',
  }
  if (!name) return '📌'
  for (const [key, emoji] of Object.entries(map)) {
    if (name.toLowerCase().includes(key.toLowerCase())) return emoji
  }
  return defaultEmojis[name.length % defaultEmojis.length]
}

function formatPrice(price) {
  return Number(price).toLocaleString('id-ID')
}

function onSearchInput() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    currentPage.value = 1
    fetchProducts()
  }, 400)
}

function selectCategory(id) {
  selectedCategory.value = id
  currentPage.value = 1
  fetchProducts()
}

function selectPrice(value) {
  selectedPrice.value = value
  showPriceFilter.value = false
  currentPage.value = 1
  fetchProducts()
}

function goToPage(page) {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
  fetchProducts()
}

function goToProduct(id) {
  console.log('Navigate to product', id)
}

async function fetchProducts() {
  loading.value = true
  try {
    const params = { page: currentPage.value, per_page: perPage }
    if (searchQuery.value.trim()) params.keyword = searchQuery.value.trim()
    if (selectedCategory.value !== null) params.category = selectedCategory.value
    if (selectedPrice.value !== 'all') {
      const [min, max] = selectedPrice.value.split('-')
      if (min) params.min_price = min
      if (max) params.max_price = max
    }
    const res = await buyerApi.getProducts(params)
    products.value = res.data.data
    currentPage.value = res.data.meta.current_page
    totalPages.value = res.data.meta.last_page
  } catch (err) {
    console.error('Failed to fetch products:', err)
    products.value = []
  } finally {
    loading.value = false
  }
}

async function fetchCategories() {
  try {
    const res = await buyerApi.getCategories()
    categories.value = res.data
  } catch (err) {
    console.error('Failed to fetch categories:', err)
  }
}

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'login' })
}

onMounted(() => {
  fetchCategories()
  fetchProducts()
})
</script>

<style>
/* ── Variables ── */
:root {
  --brand-50: #E8F1FB; --brand-100: #C8DBED; --brand-200: #9CBEDD;
  --brand-300: #6FA0CC; --brand-400: #3D7DBD; --brand-500: rgb(24, 95, 165);
  --brand-600: rgb(36, 103, 170); --brand-700: #175F9E; --brand-800: #124F85; --brand-900: #0D3D68;
  --amber-400: #E6962A; --red-50: #FCEBEB; --red-400: #E24B4A; --blue-400: #378ADD;
  --gray-0: #FAFAF9; --gray-50: #F4F3F0; --gray-100: #E8E7E2;
  --gray-200: #CFCEC7; --gray-400: #8A8980; --gray-600: #5C5B54;
  --gray-700: #3E3D38; --gray-800: #282724; --gray-900: #161514;
  --radius-sm: 8px; --radius-md: 12px; --radius-lg: 16px; --radius-xl: 24px; --radius-full: 9999px;
  --shadow-xs: 0 1px 2px rgba(0,0,0,.06);
  --shadow-sm: 0 1px 4px rgba(0,0,0,.08), 0 0 1px rgba(0,0,0,.04);
  --shadow-md: 0 4px 16px rgba(0,0,0,.10), 0 1px 4px rgba(0,0,0,.06);
}

/* ── Layout ── */
.app-layout {
  display: flex;
  min-height: 100vh;
  font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
  background-color: var(--gray-50);
  background-image: radial-gradient(var(--gray-200) 1px, transparent 1px);
  background-size: 20px 20px;
  color: var(--gray-800);
}

/* ── Sidebar ── */
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

.nav-item.active {
  background: var(--brand-50);
  color: var(--brand-700);
  font-weight: 600;
}

.nav-item.active svg {
  opacity: 1;
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

/* ── Main ── */
.main-content {
  flex: 1;
  padding: 32px;
  max-width: 1100px;
}

/* Welcome Banner */
.welcome-banner {
  background: linear-gradient(120deg, var(--brand-700) 0%, var(--brand-500) 100%);
  border-radius: var(--radius-lg);
  padding: 24px 28px;
  margin-bottom: 28px;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.welcome-banner::before {
  content: '';
  position: absolute;
  top: -40px;
  right: -40px;
  width: 180px;
  height: 180px;
  background: rgba(255,255,255,0.07);
  border-radius: 50%;
}

.welcome-banner::after {
  content: '';
  position: absolute;
  bottom: -30px;
  right: 80px;
  width: 120px;
  height: 120px;
  background: rgba(255,255,255,0.05);
  border-radius: 50%;
}

.welcome-banner-text {
  position: relative;
  z-index: 1;
}

.welcome-banner-title {
  font-size: 20px;
  font-weight: 700;
  color: #fff;
  letter-spacing: -0.3px;
  margin-bottom: 4px;
}

.welcome-banner-sub {
  font-size: 13px;
  color: rgba(255,255,255,0.65);
}

.welcome-banner-emoji {
  font-size: 48px;
  position: relative;
  z-index: 1;
  line-height: 1;
}

/* Page Header */
.page-header {
  margin-bottom: 20px;
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

/* ── Search ── */
.search-bar-wrap {
  display: flex;
  gap: 12px;
  margin-bottom: 24px;
  align-items: center;
  position: relative;
}

.search-field {
  flex: 1;
  position: relative;
}

.search-input {
  width: 100%;
  padding: 13px 20px 13px 48px;
  border: 1.5px solid var(--gray-100);
  border-radius: var(--radius-lg);
  font-family: inherit;
  font-size: 15px;
  background: #fff;
  outline: none;
  box-shadow: var(--shadow-xs);
  transition: all .18s;
  color: var(--gray-800);
}

.search-input:focus {
  border-color: var(--brand-400);
  box-shadow: 0 0 0 4px rgba(29, 139, 111, .08), var(--shadow-xs);
}

.search-input::placeholder {
  color: var(--gray-400);
}

.search-icon {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray-400);
  display: flex;
}

.search-icon svg {
  width: 20px;
  height: 20px;
}

.filter-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: var(--radius-md);
  border: 1.5px solid var(--gray-100);
  background: #fff;
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-600);
  cursor: pointer;
  box-shadow: var(--shadow-xs);
  transition: all .18s;
}

.filter-btn:hover {
  border-color: var(--brand-400);
  color: var(--brand-600);
}

/* Price Filter Dropdown */
.price-filter-dropdown {
  position: absolute;
  right: 0;
  top: calc(100% + 6px);
  background: #fff;
  border: 1px solid var(--gray-100);
  border-radius: var(--radius-md);
  padding: 6px;
  box-shadow: var(--shadow-md);
  z-index: 20;
  min-width: 180px;
}

.price-option {
  display: block;
  width: 100%;
  text-align: left;
  padding: 9px 14px;
  border: none;
  background: none;
  color: var(--gray-700);
  font-size: 13px;
  font-weight: 500;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: all .1s;
}

.price-option:hover {
  background: var(--gray-50);
}

.price-option.active {
  background: var(--brand-50);
  color: var(--brand-700);
  font-weight: 600;
}

/* ── Category Pills ── */
.category-row {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  overflow-x: auto;
  scrollbar-width: none;
  padding-bottom: 2px;
}

.category-row::-webkit-scrollbar {
  display: none;
}

.category-pill {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border-radius: var(--radius-full);
  border: 1.5px solid var(--gray-100);
  background: #fff;
  font-size: 13px;
  font-weight: 500;
  color: var(--gray-600);
  cursor: pointer;
  white-space: nowrap;
  transition: all .15s;
  box-shadow: var(--shadow-xs);
}

.category-pill:hover {
  border-color: var(--brand-300);
  color: var(--brand-700);
  background: var(--brand-50);
}

.category-pill.active {
  background: var(--brand-700);
  color: #fff;
  border-color: var(--brand-700);
}

.category-pill-icon {
  font-size: 14px;
  line-height: 1;
}

/* ── Results Bar ── */
.results-bar {
  margin-bottom: 16px;
}

.results-count {
  font-size: 13px;
  color: var(--gray-400);
}

.results-count strong {
  color: var(--gray-800);
}

/* ── Products Grid ── */
.products-section {
  margin-bottom: 28px;
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

/* ── Loading ── */
.loading-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 16px;
}

.skeleton-card {
  background: #fff;
  border-radius: var(--radius-lg);
  overflow: hidden;
  border: 1px solid var(--gray-100);
}

.skeleton-img {
  width: 100%;
  height: 160px;
  background: linear-gradient(90deg, var(--gray-50) 25%, var(--gray-100) 50%, var(--gray-50) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
}

.skeleton-text {
  height: 14px;
  background: linear-gradient(90deg, var(--gray-50) 25%, var(--gray-100) 50%, var(--gray-50) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.4s infinite;
  border-radius: 6px;
  margin: 12px 16px 8px;
}

.skeleton-text.short {
  width: 60%;
  margin-bottom: 16px;
}

@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ── Empty State ── */
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

/* ── Pagination ── */
.pagination {
  display: flex;
  justify-content: center;
  gap: 6px;
  padding: 8px 0 32px;
}

.page-btn {
  width: 38px;
  height: 38px;
  border: 1px solid var(--gray-100);
  background: #fff;
  border-radius: var(--radius-sm);
  font-size: 14px;
  font-weight: 600;
  color: var(--gray-600);
  cursor: pointer;
  transition: all .15s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.page-btn:hover:not(:disabled) {
  border-color: var(--brand-400);
  color: var(--brand-700);
}

.page-btn.active {
  background: var(--brand-700);
  color: #fff;
  border-color: var(--brand-700);
}

.page-btn:disabled {
  opacity: .3;
  cursor: default;
}
</style>
