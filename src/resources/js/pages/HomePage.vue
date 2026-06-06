<template>
  <div class="min-h-screen bg-slate-50 font-sans text-slate-800 pb-12">
    <!-- ══════════════════════════════════════════════════
         1. NAVBAR / HEADER
    ══════════════════════════════════════════════════ -->
    <header class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 text-white shadow-lg sticky top-0 z-40 px-6 py-4">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo and Brand -->
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center font-black text-xl text-white border border-white/20 shadow-inner">K</div>
          <div>
            <h1 class="font-extrabold text-lg tracking-tight">KULAAN.id</h1>
            <span class="text-[10px] text-blue-200 font-semibold tracking-wider uppercase">Katalog UMKM Jebres</span>
          </div>
        </div>

        <!-- Desktop Navigation Tabs -->
        <nav class="hidden md:flex items-center gap-2 bg-slate-900/30 p-1.5 rounded-xl border border-white/10 backdrop-blur-md">
          <button
            @click="activeTab = 'beranda'"
            :class="[
              'px-5 py-2 rounded-lg text-xs font-bold transition-all duration-200',
              activeTab === 'beranda'
                ? 'bg-white text-blue-800 shadow-md'
                : 'text-blue-100 hover:text-white hover:bg-white/10'
            ]"
          >
            Beranda
          </button>
          <button
            @click="activeTab = 'profil'"
            :class="[
              'px-5 py-2 rounded-lg text-xs font-bold transition-all duration-200',
              activeTab === 'profil'
                ? 'bg-white text-blue-800 shadow-md'
                : 'text-blue-100 hover:text-white hover:bg-white/10'
            ]"
          >
            Profil Saya
          </button>
        </nav>

        <!-- User Actions / Status -->
        <div class="flex items-center gap-4">
          <template v-if="authStore.isLoggedIn">
            <!-- Greeting -->
            <div class="text-right hidden lg:block">
              <span class="text-[10px] text-blue-200 block font-semibold uppercase">Pembeli</span>
              <span class="text-xs font-bold text-white block">{{ authStore.user?.name }}</span>
            </div>

            <!-- Buka Toko upgrade button for buyers -->
            <RouterLink
              v-if="!authStore.isSeller && !authStore.isAdmin"
              :to="{ name: 'store.setup' }"
              class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white text-xs font-extrabold rounded-xl transition-all shadow-md shadow-emerald-900/30 flex items-center gap-1.5"
            >
              🏪 Buka Toko
            </RouterLink>

            <!-- Dashboard button for sellers -->
            <RouterLink
              v-if="authStore.isSeller"
              :to="{ name: 'seller.dashboard' }"
              class="px-4 py-2 bg-white hover:bg-blue-50 text-blue-800 text-xs font-extrabold rounded-xl transition-all shadow"
            >
              Dashboard Toko
            </RouterLink>

            <button
              @click="handleLogout"
              class="px-3.5 py-2 bg-white/10 hover:bg-white/20 active:scale-95 rounded-xl text-xs font-bold transition-all border border-white/10 text-white"
            >
              Keluar
            </button>
          </template>
          <template v-else>
            <RouterLink to="/login" class="text-xs font-bold text-blue-100 hover:text-white">Masuk</RouterLink>
            <RouterLink to="/register" class="px-4 py-2 bg-white text-blue-800 text-xs font-bold rounded-xl hover:bg-blue-50 transition-all">Daftar</RouterLink>
          </template>
        </div>
      </div>
    </header>

    <!-- ══════════════════════════════════════════════════
         2. TAB CONTENT: BERANDA (PRODUCT DISCOVERY)
    ══════════════════════════════════════════════════ -->
    <main v-if="activeTab === 'beranda'" class="max-w-7xl mx-auto px-6 mt-8 space-y-8">
      
      <!-- HERO SEARCH HEADER -->
      <section class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-8 md:p-12 text-white relative overflow-hidden shadow-xl border border-slate-700/50">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -top-10 w-64 h-64 bg-indigo-600/10 rounded-full blur-3xl"></div>

        <div class="relative z-10 max-w-2xl space-y-4">
          <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
            Temukan Produk UMKM Lokal <br class="hidden sm:inline" /> di Jebres, Surakarta
          </h2>
          <p class="text-sm text-slate-300 font-medium">
            Temukan bahan makanan segar, kerajinan tangan, fashion unik, dan ragam produk dari UMKM unggulan kelurahan sekitar.
          </p>

          <!-- Unified Search Bar -->
          <div class="pt-2 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
              <span class="absolute left-4 top-3.5 text-slate-400 text-lg">🔍</span>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Cari produk atau nama toko..."
                class="w-full pl-11 pr-4 py-3 bg-white text-slate-800 border-none rounded-2xl outline-none text-sm font-semibold shadow-inner focus:ring-4 focus:ring-blue-500/20 transition-all"
                @keyup.enter="fetchProducts"
              />
            </div>
            <button
              @click="fetchProducts"
              class="px-6 py-3 bg-blue-600 hover:bg-blue-500 active:scale-95 text-white text-sm font-bold rounded-2xl transition-all shadow-lg shadow-blue-900/30"
            >
              Cari Sekarang
            </button>
          </div>
        </div>
      </section>

      <!-- FILTERS & GRID WRAPPER -->
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
        
        <!-- SIDEBAR FILTER PANEL -->
        <aside class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 space-y-6 lg:sticky lg:top-24">
          <!-- Title -->
          <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-sm">Filter Pencarian</h3>
            <button @click="resetFilters" class="text-xs text-blue-600 font-bold hover:underline">Reset</button>
          </div>

          <!-- Price Range Filter -->
          <div class="space-y-3">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Rentang Harga</label>
            <div class="space-y-2">
              <div class="relative flex items-center">
                <span class="absolute left-3.5 text-slate-400 text-xs font-bold">Rp</span>
                <input
                  v-model="filters.min_price"
                  type="number"
                  placeholder="Min"
                  class="w-full pl-9 pr-3 py-2 border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 outline-none rounded-xl text-xs font-semibold transition-all"
                />
              </div>
              <div class="text-center text-slate-300 font-bold text-xs">s/d</div>
              <div class="relative flex items-center">
                <span class="absolute left-3.5 text-slate-400 text-xs font-bold">Rp</span>
                <input
                  v-model="filters.max_price"
                  type="number"
                  placeholder="Maks"
                  class="w-full pl-9 pr-3 py-2 border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 outline-none rounded-xl text-xs font-semibold transition-all"
                />
              </div>
            </div>
            <button
              @click="fetchProducts"
              class="w-full py-2 bg-slate-800 hover:bg-slate-700 active:scale-95 text-white text-xs font-bold rounded-xl transition-all shadow-sm"
            >
              Terapkan Harga
            </button>
          </div>
        </aside>

        <!-- MAIN PRODUCT SECTION (Takes 3 Columns) -->
        <section class="lg:col-span-3 space-y-6">
          
          <!-- Category Filter Pills -->
          <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin">
            <button
              v-for="cat in categoryOptions"
              :key="cat.value"
              @click="selectCategory(cat.value)"
              :class="[
                'px-4 py-2 rounded-xl text-xs font-bold border whitespace-nowrap transition-all duration-150',
                filters.category === cat.value
                  ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-900/10'
                  : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-slate-800'
              ]"
            >
              {{ cat.label }}
            </button>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="flex flex-col items-center justify-center py-20">
            <svg class="w-10 h-10 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-xs text-slate-500 mt-3 font-semibold">Mengambil katalog produk...</span>
          </div>

          <!-- Product Grid -->
          <div v-else>
            <!-- Empty State -->
            <div v-if="products.length === 0" class="bg-white rounded-2xl border border-slate-200/60 p-12 text-center text-slate-400">
              <div class="text-4xl mb-3">🛍️</div>
              <h4 class="font-bold text-slate-700 text-sm">Produk Tidak Ditemukan</h4>
              <p class="text-xs text-slate-400 mt-1">Cobalah menggunakan kata kunci atau filter yang berbeda.</p>
            </div>

            <!-- Grid -->
            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div
                v-for="prod in products"
                :key="prod.id_product"
                class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden hover:shadow-md hover:border-slate-300/60 transition-all duration-200 flex flex-col"
              >
                <!-- Image Section -->
                <div class="relative bg-slate-100 h-44 flex items-center justify-center overflow-hidden border-b border-slate-100 flex-shrink-0">
                  <img
                    v-if="prod.image_product"
                    :src="prod.image_product"
                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                    alt="Foto produk"
                  />
                  <div v-else class="text-5xl font-bold text-slate-300 select-none">
                    🍱
                  </div>
                  <!-- Rating Badge -->
                  <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-2 py-0.5 rounded-lg border border-slate-200/50 flex items-center gap-1 shadow-sm">
                    <span class="text-[10px]">⭐</span>
                    <span class="text-[10px] font-bold text-slate-700">{{ prod.rating || '4.0' }}</span>
                  </div>
                </div>

                <!-- Product Info -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                  <div class="space-y-1">
                    <h4 class="font-extrabold text-sm text-slate-800 line-clamp-1" :title="prod.name">
                      {{ prod.name }}
                    </h4>
                    <p class="text-[11px] text-slate-400 font-semibold flex items-center gap-1">
                      <span>🏪</span> {{ prod.store?.store_name || 'Toko Anonim' }}
                    </p>
                  </div>

                  <div class="flex items-end justify-between border-t border-slate-100/80 pt-3">
                    <div>
                      <span class="text-[10px] text-slate-400 font-semibold block uppercase">Harga</span>
                      <span class="text-sm font-extrabold text-blue-600">Rp {{ formatNumber(prod.price) }}</span>
                    </div>
                    <div class="text-right">
                      <span class="text-[10px] text-slate-400 font-semibold block uppercase">Stok</span>
                      <span class="text-xs font-bold text-slate-600">{{ prod.stock }} {{ prod.unit || 'pcs' }}</span>
                    </div>
                  </div>

                  <!-- Details Trigger button -->
                  <button
                    @click="openProductDetail(prod)"
                    class="w-full py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 active:scale-[0.98] text-xs font-bold rounded-xl transition-all border border-blue-200/50 flex items-center justify-center gap-1"
                  >
                    Lihat Detail
                  </button>
                </div>
              </div>
            </div>
          </div>
        </section>

      </div>
    </main>

    <!-- ══════════════════════════════════════════════════
         3. TAB CONTENT: PROFIL SAYA (PROFILE CARD & EDIT)
    ══════════════════════════════════════════════════ -->
    <main v-if="activeTab === 'profil'" class="max-w-xl mx-auto px-6 mt-8">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-6 space-y-6">
        <!-- Header -->
        <div class="border-b border-slate-100 pb-4">
          <h3 class="font-bold text-slate-800 text-lg">Profil Akun Saya</h3>
          <p class="text-xs text-slate-400 mt-0.5">Kelola detail personal login dan kontak Anda</p>
        </div>

        <!-- Display Card -->
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 space-y-3 text-xs">
          <div class="grid grid-cols-3 gap-2">
            <span class="text-slate-400 font-semibold">Nama Lengkap</span>
            <span class="col-span-2 font-bold text-slate-800">{{ authStore.user?.name }}</span>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <span class="text-slate-400 font-semibold">Alamat Email</span>
            <span class="col-span-2 font-bold text-slate-800">{{ authStore.user?.email }}</span>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <span class="text-slate-400 font-semibold">No. WhatsApp</span>
            <span class="col-span-2 font-bold text-slate-800">{{ authStore.user?.phone_number || '-' }}</span>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <span class="text-slate-400 font-semibold">Peran Akun</span>
            <span class="col-span-2 font-bold text-slate-800">
              <span class="inline-block bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full capitalize text-[10px]">
                {{ authStore.user?.roles?.join(', ') }}
              </span>
            </span>
          </div>
        </div>

        <!-- Editing Form -->
        <form @submit.prevent="submitProfile" class="space-y-4 text-sm">
          <h4 class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-2 text-blue-600 border-b border-slate-50 pb-2">Ubah Data Diri</h4>

          <!-- Name -->
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1" for="editName">Nama Lengkap <span class="text-red-500">*</span></label>
            <input
              v-model="profileForm.name"
              type="text"
              id="editName"
              class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium"
            />
            <span v-if="errors.name" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.name }}</span>
          </div>

          <!-- Phone number -->
          <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1" for="editPhone">No. WhatsApp <span class="text-red-500">*</span></label>
            <input
              v-model="profileForm.phone_number"
              type="text"
              id="editPhone"
              class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl outline-none bg-slate-50 focus:bg-white focus:border-blue-500 transition-all text-xs font-medium"
            />
            <span v-if="errors.phone_number" class="text-[10px] text-red-500 font-medium mt-1 block">{{ errors.phone_number }}</span>
          </div>

          <!-- Submit Button -->
          <div class="pt-3">
            <button
              type="submit"
              :disabled="savingProfile"
              class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-all shadow-md shadow-blue-900/10 flex items-center justify-center gap-1.5"
            >
              <span v-if="savingProfile" class="w-3.5 h-3.5 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
              {{ savingProfile ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </main>

    <!-- ══════════════════════════════════════════════════
         4. PRODUCT DETAIL MODAL
    ══════════════════════════════════════════════════ -->
    <div v-if="selectedProduct" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" @click="closeProductDetail"></div>

      <!-- Modal Card -->
      <div class="relative bg-white rounded-3xl shadow-xl max-w-xl w-full max-h-[85vh] flex flex-col transform transition-all overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-slate-150 flex justify-between items-center bg-slate-50">
          <div>
            <h3 class="text-base font-bold text-slate-800">Detail Produk</h3>
            <span class="text-xs text-slate-400 mt-0.5 block">Katalog UMKM Kulaan.id</span>
          </div>
          <button @click="closeProductDetail" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>

        <!-- Scrollable Detail Body -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1 text-sm leading-relaxed">
          
          <!-- Image banner -->
          <div class="bg-slate-100 h-64 rounded-2xl flex items-center justify-center overflow-hidden border border-slate-200/50">
            <img
              v-if="selectedProduct.image_product"
              :src="selectedProduct.image_product"
              class="w-full h-full object-cover"
              alt="Foto produk detail"
            />
            <div v-else class="text-7xl">
              🍱
            </div>
          </div>

          <!-- Product meta header -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <span class="inline-block bg-blue-50 text-blue-700 border border-blue-100 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                {{ formatCategoryText(selectedProduct.category?.name_category) }}
              </span>
              <span class="text-xs font-bold text-slate-500 flex items-center gap-1">
                ⭐ {{ selectedProduct.rating || '4.0' }} ({{ selectedProduct.review_count || 0 }} ulasan)
              </span>
            </div>
            <h4 class="text-xl font-extrabold text-slate-800">{{ selectedProduct.name }}</h4>
            <div class="text-lg font-black text-blue-600">Rp {{ formatNumber(selectedProduct.price) }}</div>
          </div>

          <!-- Detailed Info List -->
          <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 text-xs">
            <div>
              <span class="text-slate-400 font-semibold block">Minimal Order</span>
              <span class="font-bold text-slate-800 text-sm mt-0.5">{{ selectedProduct.min_order }} {{ selectedProduct.unit || 'pcs' }}</span>
            </div>
            <div>
              <span class="text-slate-400 font-semibold block">Sisa Stok</span>
              <span class="font-bold text-slate-800 text-sm mt-0.5">{{ selectedProduct.stock }} {{ selectedProduct.unit || 'pcs' }}</span>
            </div>
          </div>

          <!-- Description -->
          <div class="space-y-1.5">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Deskripsi Produk</span>
            <p class="text-slate-700 text-xs">{{ selectedProduct.description || 'Tidak ada deskripsi produk.' }}</p>
          </div>

          <!-- Store Info Card -->
          <div class="border-t border-slate-100 pt-4 space-y-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Informasi Toko</span>
            <div class="flex items-center gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
              <!-- Store Logo -->
              <img
                v-if="selectedProduct.store?.logo"
                :src="selectedProduct.store?.logo"
                class="w-12 h-12 rounded-xl object-cover border border-slate-200 flex-shrink-0"
                alt="Logo Toko"
              />
              <div v-else class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-2xl flex-shrink-0 font-bold">
                🏪
              </div>
              <div class="text-xs space-y-0.5 flex-1">
                <h5 class="font-bold text-slate-800 text-sm">{{ selectedProduct.store?.store_name || 'Toko Anonim' }}</h5>
                <p class="text-slate-500">📍 Kelurahan: {{ formatDistrictText(selectedProduct.store?.district) }}</p>
                <p class="text-slate-500 font-medium">🕒 Buka: {{ selectedProduct.store?.operating_hours || '-' }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer actions -->
        <div class="px-6 py-4 border-t border-slate-150 bg-slate-50 flex justify-end">
          <button
            @click="closeProductDetail"
            class="px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>

    <!-- Toast Notification -->
    <div :class="['toast', toastType, toastVisible && 'show']" role="alert" aria-live="polite">
      {{ toastMsg }}
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { productService } from '@/services/productService'

const router = useRouter()
const authStore = useAuthStore()

// ── State variables ──────────────────────────────────────────
const activeTab = ref('beranda')
const loading = ref(false)
const products = ref([])
const selectedProduct = ref(null)

const filters = reactive({
  search: '',
  category: 'semua',
  min_price: '',
  max_price: ''
})

// Edit profile form state
const savingProfile = ref(false)
const errors = reactive({})
const profileForm = reactive({
  name: '',
  phone_number: ''
})

// Toast notification state
const toastMsg = ref('')
const toastType = ref('')
const toastVisible = ref(false)
let toastTimer = null

// Category options mapping
const categoryOptions = [
  { value: 'semua', label: '🌟 Semua' },
  { value: 'makanan_minuman', label: '🍱 Makanan' },
  { value: 'fashion_batik', label: '👗 Fashion' },
  { value: 'kerajinan', label: '🧶 Kerajinan' },
  { value: 'elektronik', label: '📱 Elektronik' },
  { value: 'kecantikan', label: '💄 Kecantikan' },
  { value: 'pertanian', label: '🌾 Pertanian' },
  { value: 'jasa', label: '🛠️ Jasa' },
  { value: 'lainnya', label: '📦 Lainnya' }
]

// ── Actions & Methods ─────────────────────────────────────────

// Fetch Products from Backend
const fetchProducts = async () => {
  try {
    loading.value = true
    const res = await productService.getProducts({
      search: filters.search,
      category: filters.category,
      min_price: filters.min_price,
      max_price: filters.max_price
    })
    if (res.success) {
      products.value = res.data
    }
  } catch (error) {
    console.error('Failed fetching products', error)
    showToast('Gagal memuat produk.', 'error')
  } finally {
    loading.value = false
  }
}

// Select Category Pill
const selectCategory = (categoryValue) => {
  filters.category = categoryValue
  fetchProducts()
}

// Reset Search Filters
const resetFilters = () => {
  filters.search = ''
  filters.category = 'semua'
  filters.min_price = ''
  filters.max_price = ''
  fetchProducts()
}

// Open Detail Modal
const openProductDetail = (prod) => {
  selectedProduct.value = prod
}

const closeProductDetail = () => {
  selectedProduct.value = null
}

// Populate Edit Profile Form
const initProfileForm = () => {
  profileForm.name = authStore.user?.name || ''
  profileForm.phone_number = authStore.user?.phone_number || ''
  clearErrors()
}

// Submit Profile Changes
const submitProfile = async () => {
  clearErrors()
  let valid = true

  if (!profileForm.name.trim()) {
    errors.name = 'Nama lengkap wajib diisi.'
    valid = false
  }
  if (!profileForm.phone_number.trim()) {
    errors.phone_number = 'No. WhatsApp wajib diisi.'
    valid = false
  }

  if (!valid) {
    showToast('Mohon isi field wajib.', 'error')
    return
  }

  try {
    savingProfile.value = true
    const res = await authStore.updateProfile({
      name: profileForm.name,
      phone_number: profileForm.phone_number
    })
    if (res.success) {
      showToast('Profil berhasil diperbarui.', 'success')
      initProfileForm()
    }
  } catch (err) {
    console.error('Failed updating profile', err)
    const res = err.response
    if (res?.status === 422) {
      const fieldErrors = res.data.errors ?? {}
      Object.keys(fieldErrors).forEach(f => {
        errors[f] = fieldErrors[f][0]
      })
      showToast('Periksa kembali isian form.', 'error')
    } else {
      showToast(res?.data?.message || 'Gagal menyimpan profil.', 'error')
    }
  } finally {
    savingProfile.value = false
  }
}

// Logout
const handleLogout = async () => {
  await authStore.logout()
  router.push({ name: 'login' })
}

// Formatting helpers
const formatNumber = (num) => {
  if (!num) return '0'
  return parseFloat(num).toLocaleString('id-ID')
}

const formatCategoryText = (cat) => {
  if (!cat) return '-'
  const mapping = {
    'makanan_minuman': '🍱 Makanan & Minuman',
    'fashion_batik': '👗 Fashion & Batik',
    'kerajinan': '🧶 Kerajinan Tangan',
    'elektronik': '📱 Elektronik & Aksesori',
    'kecantikan': '💄 Kecantikan & Perawatan',
    'pertanian': '🌾 Pertanian & Hasil Bumi',
    'jasa': '🛠️ Jasa & Layanan',
    'lainnya': '📦 Lainnya'
  }
  return mapping[cat] || cat
}

const formatDistrictText = (dist) => {
  if (!dist) return '-'
  return dist.charAt(0).toUpperCase() + dist.slice(1)
}

const clearErrors = () => {
  Object.keys(errors).forEach(k => delete errors[k])
}

const showToast = (msg, type = '', duration = 3200) => {
  clearTimeout(toastTimer)
  toastMsg.value = msg
  toastType.value = type
  toastVisible.value = true
  toastTimer = setTimeout(() => { toastVisible.value = false }, duration)
}

// Watch activeTab to load user profile
watch(activeTab, (newTab) => {
  if (newTab === 'profil') {
    initProfileForm()
  }
})

// ── Lifecycle Hooks ───────────────────────────────────────────
onMounted(() => {
  fetchProducts()
  if (activeTab.value === 'profil') {
    initProfileForm()
  }
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

.font-sans {
  font-family: 'Plus Jakarta Sans', sans-serif;
}

/* Custom Scrollbar */
.scrollbar-thin::-webkit-scrollbar {
  height: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 100px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Toast styling overrides */
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
