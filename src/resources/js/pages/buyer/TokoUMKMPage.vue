<template>
  <div class="app-layout">
    <!-- ══ SIDEBAR ══ -->
    <BuyerSidebar />

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
              <span v-if="!isStoreOpen(store.operating_hours)" class="store-closed-badge">Tutup</span>
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
import BuyerSidebar from '@/components/common/BuyerSidebar.vue'
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { buyerApi } from '@/services/api/buyerApi'
import { isStoreOpen } from '@/services/storeHelper'

const router = useRouter()
const authStore = useAuthStore()

const searchQuery = ref('')


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







onMounted(() => {
  
  
  fetchStores()
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

.store-closed-badge {
  background: var(--red-50);
  color: var(--red-400);
  border: 1px solid var(--red-400);
  font-size: 10px;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: var(--radius-full);
  margin-left: 4px;
  text-transform: uppercase;
}
</style>
