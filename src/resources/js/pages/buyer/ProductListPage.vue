<template>
  <div class="page-wrap">

    <!-- ── Sticky Header ── -->
    <header class="top-bar">
      <div class="top-bar-inner">
        <div class="top-bar-brand">
          <button class="btn-icon" @click="$router.push({ name: 'home' })" aria-label="Beranda">
            <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </button>
          <span class="brand-name">Kulaan<span class="brand-dot">.id</span></span>
        </div>
        <div class="top-bar-actions">
          <button class="btn-icon notif-btn" @click="$router.push({ name: 'buyer.notifications' })" aria-label="Notifikasi">
            <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
            <span class="notif-badge" v-if="unreadCount > 0">{{ unreadCount }}</span>
          </button>
        </div>
      </div>

      <!-- Hero text -->
      <div class="hero-text">
        <p class="hero-title">Temukan produk UMKM lokal<br/>di Jebres, Surakarta</p>
      </div>

      <!-- Search bar -->
      <div class="search-wrap">
        <div class="search-box">
          <span class="search-icon">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </span>
          <input
            v-model="searchQuery"
            type="search"
            placeholder="Cari produk atau toko..."
            class="search-input"
            @input="handleSearch"
          />
          <button v-if="searchQuery" class="search-clear" @click="clearSearch" aria-label="Hapus pencarian">
            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
          <!-- Camera icon (placeholder for AI search) -->
          <button class="search-cam" aria-label="Cari dengan gambar">
            <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
          </button>
        </div>
      </div>
    </header>

    <!-- ── Category Chips ── -->
    <div class="chips-scroll">
      <div class="chips-inner">
        <button
          v-for="cat in categories"
          :key="cat.value"
          :class="['chip', selectedCategory === cat.value && 'chip--active']"
          @click="selectCategory(cat.value)"
        >
          <span class="chip-icon">{{ cat.icon }}</span>
          {{ cat.name }}
        </button>
      </div>
    </div>

    <!-- ── Filter Bar ── -->
    <div class="filter-bar">
      <div class="price-range">
        <span class="filter-label">Harga:</span>
        <input
          v-model.number="priceMin"
          type="number"
          placeholder="Min"
          class="price-input"
          @input="applyFilters"
        />
        <span class="price-sep">–</span>
        <input
          v-model.number="priceMax"
          type="number"
          placeholder="Max"
          class="price-input"
          @input="applyFilters"
        />
      </div>
      <div class="filter-right">
        <button
          :class="['btn-filter', showFilterPanel && 'btn-filter--active']"
          @click="showFilterPanel = !showFilterPanel"
        >
          <svg viewBox="0 0 24 24"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
          Filter
        </button>
        <button
          :class="['btn-sort', sortOpen && 'btn-sort--active']"
          @click="sortOpen = !sortOpen"
        >
          <svg viewBox="0 0 24 24"><path d="M3 6h18M6 12h12M10 18h4"/></svg>
          Urutkan
        </button>
      </div>
    </div>

    <!-- ── Filter Panel (collapse) ── -->
    <div v-if="showFilterPanel" class="filter-panel">
      <div class="filter-panel-inner">
        <div class="filter-section">
          <span class="filter-section-label">Ketersediaan</span>
          <label class="check-label">
            <input type="checkbox" v-model="filterInStock" @change="applyFilters" />
            <span class="checkmark"></span>
            Stok tersedia
          </label>
        </div>
        <div class="filter-section">
          <span class="filter-section-label">Rating Minimum</span>
          <div class="stars-filter">
            <button
              v-for="r in [1,2,3,4,5]"
              :key="r"
              :class="['star-btn', minRating >= r && 'star-btn--on']"
              @click="setMinRating(r)"
            >★</button>
          </div>
        </div>
        <button class="btn-reset-filter" @click="resetFilters">Reset Filter</button>
      </div>
    </div>

    <!-- ── Sort dropdown ── -->
    <div v-if="sortOpen" class="sort-dropdown">
      <button
        v-for="opt in sortOptions"
        :key="opt.value"
        :class="['sort-opt', sortBy === opt.value && 'sort-opt--active']"
        @click="selectSort(opt.value)"
      >
        {{ opt.label }}
        <svg v-if="sortBy === opt.value" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      </button>
    </div>

    <!-- ── Main Content ── -->
    <main class="content">

      <!-- Hasil pencarian info -->
      <div v-if="searchQuery" class="search-info">
        <span>Hasil untuk <strong>"{{ searchQuery }}"</strong>: {{ filteredProducts.length }} produk</span>
      </div>

      <!-- Daftar produk -->
      <div v-if="filteredProducts.length > 0" class="product-grid">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          class="product-card"
          @click="goToDetail(product.id)"
        >
          <div class="product-img">
            <div class="img-placeholder">
              <span class="img-emoji">{{ getCategoryEmoji(product.category) }}</span>
            </div>
            <div v-if="product.stock <= 5 && product.stock > 0" class="stock-badge stock-badge--low">
              Sisa {{ product.stock }}
            </div>
            <div v-if="product.stock === 0" class="stock-badge stock-badge--empty">
              Habis
            </div>
          </div>
          <div class="product-info">
            <p class="product-store">
              <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
              {{ product.store_name }}
              <span class="store-rating">★ {{ product.store_rating }}</span>
            </p>
            <h3 class="product-name">{{ product.name }}</h3>
            <p class="product-price">Rp {{ formatPrice(product.price) }}</p>
            <div class="product-meta">
              <span class="product-stock">Stok: {{ product.stock }} {{ product.unit }}</span>
            </div>
            <button class="btn-detail" @click.stop="goToDetail(product.id)">Lihat Detail</button>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div v-else class="empty-state">
        <div class="empty-icon">🔍</div>
        <p class="empty-title">Produk tidak ditemukan</p>
        <p class="empty-desc">Coba kata kunci lain atau ubah filter pencarian</p>
        <button class="btn-reset" @click="resetAll">Reset Pencarian</button>
      </div>

    </main>

    <!-- ── Bottom Nav ── -->
    <nav class="bottom-nav">
      <button class="nav-btn nav-btn--active">
        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Beranda
      </button>
      <button class="nav-btn" @click="$router.push({ name: 'buyer.orders' })">
        <svg viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        Pesanan
      </button>
      <button class="nav-btn" @click="$router.push({ name: 'buyer.notifications' })">
        <svg viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
        Notifikasi
        <span v-if="unreadCount > 0" class="nav-badge">{{ unreadCount }}</span>
      </button>
      <button class="nav-btn">
        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Profil
      </button>
    </nav>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '@/services/api'
import { mockCategories } from '@/data/mockData'

const router = useRouter()

// State
const products         = ref([])
const loading          = ref(false)
const searchQuery      = ref('')
const selectedCategory = ref('')
const priceMin         = ref(null)
const priceMax         = ref(null)
const filterInStock    = ref(false)
const minRating        = ref(0)
const sortBy           = ref('default')
const showFilterPanel  = ref(false)
const sortOpen         = ref(false)
const unreadCount      = ref(0)

const categories = mockCategories
const sortOptions = [
  { value: 'default',     label: 'Relevansi' },
  { value: 'price_asc',   label: 'Harga: Rendah ke Tinggi' },
  { value: 'price_desc',  label: 'Harga: Tinggi ke Rendah' },
  { value: 'rating_desc', label: 'Rating Tertinggi' },
  { value: 'newest',      label: 'Terbaru' },
]

// Fetch products from backend API (US-03 & US-04)
async function fetchProducts() {
  loading.value = true
  try {
    const params = {}
    if (searchQuery.value.trim()) params.search = searchQuery.value
    if (selectedCategory.value) params.category = selectedCategory.value
    if (priceMin.value !== null && priceMin.value !== '') params.price_min = priceMin.value
    if (priceMax.value !== null && priceMax.value !== '') params.price_max = priceMax.value
    if (filterInStock.value) params.in_stock = true
    if (minRating.value > 0) params.min_rating = minRating.value
    if (sortBy.value && sortBy.value !== 'default') params.sort_by = sortBy.value

    const { data } = await apiClient.get('/products', { params })
    products.value = data.data
  } catch (err) {
    console.error('Gagal memuat produk:', err)
  } finally {
    loading.value = false
  }
}

// Fetch unread notifications count (US-07)
async function fetchNotificationsCount() {
  try {
    const { data } = await apiClient.get('/notifications')
    unreadCount.value = data.data.filter(n => !n.is_read).length
  } catch (err) {
    console.error('Gagal memuat count notifikasi:', err)
  }
}

onMounted(() => {
  fetchProducts()
  fetchNotificationsCount()
})

const filteredProducts = computed(() => products.value)

// Actions
function handleSearch() {
  sortOpen.value = false
  fetchProducts()
}
function clearSearch() {
  searchQuery.value = ''
  fetchProducts()
}
function selectCategory(val) {
  selectedCategory.value = val
  showFilterPanel.value = false
  sortOpen.value = false
  fetchProducts()
}
function applyFilters() {
  sortOpen.value = false
  fetchProducts()
}
function setMinRating(r) {
  minRating.value = minRating.value === r ? 0 : r
  fetchProducts()
}
function selectSort(val) {
  sortBy.value = val
  sortOpen.value = false
  fetchProducts()
}
function resetFilters() {
  filterInStock.value = false
  minRating.value = 0
  priceMin.value = null
  priceMax.value = null
  showFilterPanel.value = false
  fetchProducts()
}
function resetAll() {
  searchQuery.value = ''
  selectedCategory.value = ''
  resetFilters()
}
function goToDetail(id) {
  router.push({ name: 'buyer.product.detail', params: { id } })
}

// Helpers
function formatPrice(n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}
function getCategoryEmoji(cat) {
  const map = {
    makanan_minuman: '🍱',
    fashion_batik: '👗',
    kerajinan: '🧶',
    kecantikan: '💄',
    pertanian: '🌾',
    jasa: '🛠️',
  }
  return map[cat] || '📦'
}
</script>

<style scoped>
/* ── Layout ─────────────────────────────────────────────────── */
.page-wrap {
  min-height: 100vh;
  background: #f0f4f8;
  display: flex;
  flex-direction: column;
  max-width: 480px;
  margin: 0 auto;
  position: relative;
  padding-bottom: 72px;
}

/* ── Top Bar ────────────────────────────────────────────────── */
.top-bar {
  background: linear-gradient(135deg, #1a7fc4 0%, #1565a8 100%);
  padding: 12px 16px 20px;
  position: sticky;
  top: 0;
  z-index: 100;
}
.top-bar-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
.top-bar-brand {
  display: flex;
  align-items: center;
  gap: 8px;
}
.brand-name {
  font-size: 20px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.5px;
}
.brand-dot { color: rgba(255,255,255,0.5); }
.btn-icon {
  width: 36px; height: 36px;
  background: rgba(255,255,255,0.15);
  border: none; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; color: #fff;
  transition: background 0.18s;
}
.btn-icon:hover { background: rgba(255,255,255,0.25); }
.btn-icon svg {
  width: 18px; height: 18px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
.top-bar-actions { display: flex; gap: 8px; }
.notif-btn { position: relative; }
.notif-badge {
  position: absolute;
  top: -4px; right: -4px;
  width: 16px; height: 16px;
  background: #ef4444;
  color: #fff; font-size: 10px; font-weight: 700;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid #1565a8;
}

/* Hero text */
.hero-text { margin-bottom: 12px; }
.hero-title {
  font-size: 18px; font-weight: 700; color: #fff;
  line-height: 1.35;
  margin: 0;
}

/* Search */
.search-wrap { position: relative; }
.search-box {
  display: flex; align-items: center;
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.12);
  height: 44px;
}
.search-icon {
  padding: 0 10px 0 14px;
  display: flex; align-items: center;
  flex-shrink: 0;
}
.search-icon svg {
  width: 18px; height: 18px;
  fill: none; stroke: #9ca3af;
  stroke-width: 2; stroke-linecap: round;
}
.search-input {
  flex: 1;
  border: none; outline: none;
  font-size: 14px; font-family: inherit;
  color: #1f2937;
  background: transparent;
  padding: 0;
}
.search-input::placeholder { color: #9ca3af; }
.search-clear, .search-cam {
  width: 40px; height: 44px;
  background: none; border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  color: #9ca3af; flex-shrink: 0;
}
.search-clear:hover { color: #374151; }
.search-cam {
  background: #f3f4f6;
  border-left: 1px solid #e5e7eb;
}
.search-cam svg, .search-clear svg {
  width: 17px; height: 17px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}

/* ── Category Chips ─────────────────────────────────────────── */
.chips-scroll {
  overflow-x: auto;
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}
.chips-scroll::-webkit-scrollbar { display: none; }
.chips-inner {
  display: flex; gap: 8px;
  padding: 10px 16px;
  width: max-content;
}
.chip {
  display: flex; align-items: center; gap: 5px;
  padding: 6px 14px;
  border-radius: 100px;
  border: 1.5px solid #e5e7eb;
  background: #f9fafb;
  font-size: 13px; font-weight: 500; font-family: inherit;
  color: #4b5563; cursor: pointer;
  white-space: nowrap;
  transition: all 0.15s;
}
.chip:hover { border-color: #93c5fd; background: #eff6ff; }
.chip--active {
  border-color: #1565a8;
  background: #1565a8;
  color: #fff;
}
.chip-icon { font-size: 15px; }

/* ── Filter Bar ─────────────────────────────────────────────── */
.filter-bar {
  display: flex; align-items: center;
  gap: 8px; padding: 10px 16px;
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
}
.filter-label {
  font-size: 12px; color: #6b7280; font-weight: 500;
  white-space: nowrap;
}
.price-range {
  display: flex; align-items: center; gap: 6px; flex: 1;
}
.price-input {
  width: 72px; padding: 5px 8px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px; font-size: 12px;
  font-family: inherit; outline: none;
  color: #374151;
  transition: border-color 0.15s;
}
.price-input:focus { border-color: #1565a8; }
.price-sep { font-size: 13px; color: #9ca3af; }
.filter-right { display: flex; gap: 6px; flex-shrink: 0; }
.btn-filter, .btn-sort {
  display: flex; align-items: center; gap: 5px;
  padding: 6px 12px;
  border: 1.5px solid #e5e7eb;
  border-radius: 8px; background: #fff;
  font-size: 12px; font-weight: 600; font-family: inherit;
  color: #374151; cursor: pointer;
  transition: all 0.15s;
}
.btn-filter svg, .btn-sort svg {
  width: 14px; height: 14px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round;
}
.btn-filter--active, .btn-sort--active {
  border-color: #1565a8;
  background: #eff6ff;
  color: #1565a8;
}

/* ── Filter Panel ────────────────────────────────────────────── */
.filter-panel {
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
}
.filter-panel-inner {
  padding: 14px 16px;
  display: flex; flex-direction: column; gap: 14px;
}
.filter-section { display: flex; flex-direction: column; gap: 8px; }
.filter-section-label {
  font-size: 12px; font-weight: 600;
  color: #374151; text-transform: uppercase; letter-spacing: 0.5px;
}
.check-label {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: #4b5563; cursor: pointer;
}
.check-label input { accent-color: #1565a8; width: 15px; height: 15px; }
.stars-filter { display: flex; gap: 6px; }
.star-btn {
  font-size: 20px; background: none; border: none;
  cursor: pointer; color: #d1d5db; padding: 2px;
  transition: color 0.15s;
}
.star-btn--on { color: #f59e0b; }
.btn-reset-filter {
  align-self: flex-start;
  padding: 6px 16px; border: 1.5px solid #e5e7eb;
  border-radius: 8px; background: #fff;
  font-size: 13px; font-weight: 600; font-family: inherit;
  color: #6b7280; cursor: pointer;
  transition: all 0.15s;
}
.btn-reset-filter:hover { border-color: #1565a8; color: #1565a8; }

/* ── Sort Dropdown ───────────────────────────────────────────── */
.sort-dropdown {
  position: absolute;
  right: 16px; top: calc(var(--filter-bar-bottom, 160px));
  z-index: 200;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.1);
  overflow: hidden;
  min-width: 200px;
}
.sort-opt {
  display: flex; align-items: center; justify-content: space-between;
  width: 100%; padding: 11px 16px;
  background: none; border: none;
  font-size: 13px; font-family: inherit; font-weight: 500;
  color: #374151; cursor: pointer; text-align: left;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.15s;
}
.sort-opt:last-child { border-bottom: none; }
.sort-opt:hover { background: #f9fafb; }
.sort-opt--active { color: #1565a8; font-weight: 700; background: #eff6ff; }
.sort-opt svg {
  width: 14px; height: 14px;
  fill: none; stroke: currentColor;
  stroke-width: 2.5; stroke-linecap: round;
}

/* ── Content ─────────────────────────────────────────────────── */
.content { padding: 14px 16px; }
.search-info {
  font-size: 13px; color: #6b7280;
  margin-bottom: 12px; padding: 0 2px;
}
.search-info strong { color: #1f2937; }

/* ── Product Grid ────────────────────────────────────────────── */
.product-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.product-card {
  background: #fff;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.07);
  cursor: pointer;
  transition: transform 0.15s, box-shadow 0.15s;
}
.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.product-img {
  position: relative;
  aspect-ratio: 1;
  background: #f1f5f9;
}
.img-placeholder {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
}
.img-emoji { font-size: 42px; }
.stock-badge {
  position: absolute;
  top: 8px; left: 8px;
  padding: 2px 8px;
  border-radius: 100px;
  font-size: 10px; font-weight: 700;
}
.stock-badge--low { background: #fef3c7; color: #92400e; }
.stock-badge--empty { background: #fee2e2; color: #991b1b; }

.product-info { padding: 10px; }
.product-store {
  display: flex; align-items: center; gap: 3px;
  font-size: 10px; color: #6b7280; margin-bottom: 3px;
}
.product-store svg {
  width: 10px; height: 10px;
  fill: none; stroke: currentColor;
  stroke-width: 2; stroke-linecap: round;
}
.store-rating {
  margin-left: auto;
  color: #f59e0b; font-weight: 600; font-size: 10px;
}
.product-name {
  font-size: 13px; font-weight: 700;
  color: #1f2937; margin-bottom: 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.3;
}
.product-price {
  font-size: 14px; font-weight: 800;
  color: #1565a8; margin-bottom: 4px;
}
.product-meta { margin-bottom: 8px; }
.product-stock { font-size: 10px; color: #9ca3af; }
.btn-detail {
  width: 100%; padding: 7px;
  background: #eff6ff;
  border: 1.5px solid #bfdbfe;
  border-radius: 8px;
  font-size: 12px; font-weight: 600; font-family: inherit;
  color: #1565a8; cursor: pointer;
  transition: all 0.15s;
}
.btn-detail:hover { background: #1565a8; color: #fff; }

/* ── Empty State ─────────────────────────────────────────────── */
.empty-state {
  display: flex; flex-direction: column; align-items: center;
  padding: 48px 24px; gap: 8px;
}
.empty-icon { font-size: 48px; }
.empty-title { font-size: 16px; font-weight: 700; color: #1f2937; }
.empty-desc { font-size: 13px; color: #6b7280; text-align: center; }
.btn-reset {
  margin-top: 8px; padding: 10px 24px;
  background: #1565a8; color: #fff;
  border: none; border-radius: 10px;
  font-size: 14px; font-weight: 600; font-family: inherit;
  cursor: pointer;
}

/* ── Bottom Nav ──────────────────────────────────────────────── */
.bottom-nav {
  position: fixed;
  bottom: 0; left: 50%;
  transform: translateX(-50%);
  width: 100%; max-width: 480px;
  background: #fff;
  border-top: 1px solid #e5e7eb;
  display: flex;
  z-index: 200;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.06);
}
.nav-btn {
  flex: 1; padding: 8px 4px 10px;
  background: none; border: none; cursor: pointer;
  display: flex; flex-direction: column; align-items: center; gap: 3px;
  font-size: 10px; font-weight: 500; font-family: inherit;
  color: #9ca3af; position: relative;
  transition: color 0.15s;
}
.nav-btn svg {
  width: 20px; height: 20px;
  fill: none; stroke: currentColor;
  stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
.nav-btn--active { color: #1565a8; }
.nav-badge {
  position: absolute;
  top: 6px; right: calc(50% - 16px);
  width: 14px; height: 14px;
  background: #ef4444; color: #fff;
  font-size: 9px; font-weight: 700;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
}
</style>