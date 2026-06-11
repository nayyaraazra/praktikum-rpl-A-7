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
    <main class="main-content">
      <div class="page-header">
        <h1 class="page-title">Direktori Toko UMKM</h1>
        <p class="page-sub">Temukan dan dukung pelaku usaha lokal di Kelurahan Jebres</p>
      </div>

      <div class="search-bar-wrap">
        <div class="search-field">
          <span class="search-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8" stroke-width="2" />
              <path d="m21 21-4.35-4.35" stroke-width="2" />
            </svg>
          </span>
          <input class="search-input" type="text" placeholder="Cari nama toko atau deskripsi..." v-model="searchQuery" @input="onSearchInput">
        </div>
      </div>

      <div class="category-row">
        <div
          v-for="cat in storeCategories"
          :key="cat.value"
          class="category-pill"
          :class="{ active: selectedCategory === cat.value }"
          @click="selectCategory(cat.value)"
        >
          {{ cat.icon }} {{ cat.label }}
        </div>
      </div>

      <div v-if="loading" style="padding: 40px; text-align: center; color: var(--gray-400);">Memuat toko...</div>
      <div v-else-if="stores.length === 0" style="padding: 40px; text-align: center; color: var(--gray-400);">Tidak ada toko ditemukan.</div>

      <div v-else class="stores-grid">
        <div v-for="store in stores" :key="store.id_store" class="store-card" @click="goToStore(store.id_store)">
          <div :class="['store-card-banner', getBannerClass(store)]">
            <div class="store-card-logo-wrap">
              <div v-if="!store.store_logo_url" class="store-card-logo" :style="getLogoStyle(store)">
                {{ getInitials(store.store_name) }}
              </div>
              <img v-else :src="store.store_logo_url" class="store-card-logo" style="object-fit: cover;" />
            </div>
          </div>
          <div class="store-card-body">
            <div class="store-card-name-row">
              <span class="store-card-name">{{ store.store_name }}</span>
              <span class="verified-icon">✓</span>
            </div>
            <div class="store-card-cat">{{ formatCategory(store.store_category) }}</div>
            <div class="store-card-info-list">
              <div class="store-card-info-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2a8 8 0 00-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 00-8-8z" stroke-width="2"/></svg>
                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ store.address }}</span>
              </div>
              <div class="store-card-info-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="2"/></svg>
                {{ store.products_count || 0 }} Produk Aktif
              </div>
            </div>
            <div class="store-card-footer">
              <div class="store-card-rating"><span>★</span> 4.8</div>
              <button class="btn-visit" @click.stop="goToStore(store.id_store)">Kunjungi Toko</button>
            </div>
          </div>
        </div>
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

const searchQuery = ref('')
const unreadCount = ref(0)
const activeOrdersCount = ref(0)
const stores = ref([])
const loading = ref(true)
const selectedCategory = ref('Semua Toko')

const storeCategories = [
  { value: 'Semua Toko',                  icon: '🏪', label: 'Semua Toko' },
  { value: 'pakaian',                   icon: '👕', label: 'Pakaian' },
  { value: 'fashion & aksesoris',        icon: '🕶️', label: 'Fashion & Aksesoris' },
  { value: 'makanan & minuman',          icon: '🍱', label: 'Makanan & Minuman' },
  { value: 'perawatan & kecantikan',     icon: '💄', label: 'Perawatan & Kecantikan' },
  { value: 'perlengkapan rumah',         icon: '🏠', label: 'Perlengkapan Rumah' },
  { value: 'hobi & koleksi',             icon: '🎨', label: 'Hobi & Koleksi' },
  { value: 'kesehatan',                  icon: '💊', label: 'Kesehatan' },
  { value: 'olahraga & outdoor',         icon: '🏕️', label: 'Olahraga & Outdoor' },
  { value: 'buku & alat tulis',          icon: '📚', label: 'Buku & Alat Tulis' },
  { value: 'kerajinan tangan',           icon: '🧶', label: 'Kerajinan Tangan' },
  { value: 'sembako & kebutuhan pokok',  icon: '🌾', label: 'Sembako & Kebutuhan Pokok' },
  { value: 'Jasa & Layanan',             icon: '🛠️', label: 'Jasa & Layanan' },
  { value: 'Katering',                   icon: '🍲', label: 'Katering' },
]

let debounceTimer = null

const userInitials = computed(() => {
  const name = authStore.user?.name || 'P'
  const parts = name.split(' ')
  return parts.length > 1 ? parts[0][0] + parts[1][0] : parts[0][0]
})

function onSearchInput() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    fetchStores()
  }, 400)
}

function selectCategory(val) {
  selectedCategory.value = val
  fetchStores()
}

async function fetchStores() {
  loading.value = true
  try {
    const params = { per_page: 50 }
    if (searchQuery.value.trim()) params.keyword = searchQuery.value.trim()
    if (selectedCategory.value !== 'Semua Toko') params.category = selectedCategory.value

    const res = await buyerApi.getStores(params)
    stores.value = res.data.data
  } catch (err) {
    console.error('Failed to fetch stores:', err)
  } finally {
    loading.value = false
  }
}

function goToStore(id) {
  router.push({ name: 'buyer.store', params: { id } })
}

function getInitials(name) {
  if (!name) return 'ST'
  const parts = name.split(' ')
  return parts.length > 1 ? (parts[0][0] + parts[1][0]).toUpperCase() : parts[0].substring(0, 2).toUpperCase()
}

function getBannerClass(store) {
  const id = store.id_store || 0
  const classes = ['', 'amber-gradient', 'purple-gradient', 'green-gradient']
  return classes[id % classes.length]
}

function getLogoStyle(store) {
  const id = store.id_store || 0
  const colors = [
    { bg: 'var(--brand-50)', text: 'var(--brand-700)' },
    { bg: '#FEF3C7', text: '#D97706' },
    { bg: 'var(--purple-50)', text: '#534AB7' },
    { bg: '#E6F4EA', text: '#137333' }
  ]
  const c = colors[id % colors.length]
  return { backgroundColor: c.bg, color: c.text }
}

function formatCategory(val) {
  const found = storeCategories.find(c => c.value === val)
  return found ? found.label : val
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
  fetchUnreadNotificationsCount()
  fetchActiveOrdersCount()
  fetchStores()
})
</script>

<style scoped>
.category-row { display: flex; gap: 8px; margin-bottom: 24px; overflow-x: auto; scrollbar-width: none; padding-bottom: 2px; }
.category-row::-webkit-scrollbar { display: none; }
.category-pill { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: var(--radius-full); border: 1.5px solid var(--gray-100); background: #fff; font-size: 13px; font-weight: 500; color: var(--gray-600); cursor: pointer; white-space: nowrap; transition: all .15s; box-shadow: var(--shadow-xs); }
.category-pill:hover { border-color: var(--brand-300); color: var(--brand-700); background: var(--brand-50); }
.category-pill.active { background: var(--brand-700); color: #fff; border-color: var(--brand-700); }

/* Stores Grid */
.stores-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
.store-card { background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--gray-100); overflow: hidden; transition: all .25s ease; box-shadow: var(--shadow-xs); display: flex; flex-direction: column; }
.store-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: var(--gray-200); }

.store-card-banner { height: 80px; background: linear-gradient(135deg, var(--brand-300), var(--brand-500)); position: relative; }
.store-card-banner.amber-gradient { background: linear-gradient(135deg, #F59E0B, #D97706); }
.store-card-banner.purple-gradient { background: linear-gradient(135deg, #8B5CF6, #6D28D9); }
.store-card-banner.green-gradient { background: linear-gradient(135deg, #10B981, #047857); }

.store-card-logo-wrap { position: absolute; left: 16px; bottom: -24px; width: 56px; height: 56px; border-radius: var(--radius-md); background: #fff; padding: 4px; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: center; }
.store-card-logo { width: 100%; height: 100%; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; }

.store-card-body { padding: 36px 16px 16px; flex: 1; display: flex; flex-direction: column; }
.store-card-name-row { display: flex; align-items: center; gap: 6px; margin-bottom: 4px; }
.store-card-name { font-size: 16px; font-weight: 700; color: var(--gray-900); text-decoration: none; }
.store-card-name:hover { color: var(--brand-700); }
.verified-icon { color: var(--brand-500); font-size: 14px; flex-shrink: 0; }

.store-card-cat { font-size: 12px; color: var(--gray-400); font-weight: 500; margin-bottom: 12px; }
.store-card-info-list { display: flex; flex-direction: column; gap: 8px; font-size: 13px; color: var(--gray-600); margin-bottom: 16px; }
.store-card-info-item { display: flex; align-items: center; gap: 8px; }
.store-card-info-item svg { width: 15px; height: 15px; color: var(--gray-400); flex-shrink: 0; }

.store-card-footer { border-top: 1px solid var(--gray-100); padding-top: 14px; margin-top: auto; display: flex; align-items: center; justify-content: space-between; }
.store-card-rating { display: flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 600; color: var(--gray-800); }
.store-card-rating span { color: var(--amber-400); }

.btn-visit { padding: 6px 14px; border-radius: var(--radius-sm); border: 1.5px solid var(--gray-100); background: #fff; font-size: 12px; font-weight: 600; color: var(--gray-700); cursor: pointer; transition: all 0.15s; }
.btn-visit:hover { border-color: var(--brand-400); color: var(--brand-700); background: var(--brand-50); }
</style>
