<template>
    <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
        <!-- Navbar -->
        <header class="navbar bg-blue-600 shadow-md text-white sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2 cursor-pointer" @click="goHome">
                    <span class="text-2xl font-extrabold tracking-tight">KULAAN<span class="text-blue-200">.id</span></span>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full font-medium hidden sm:inline">Katalog UMKM Jebres</span>
                </div>
                
                <!-- Search Bar Tengah (Navbar) -->
                <div class="flex-1 max-w-md relative hidden md:block">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        v-model="filters.search" 
                        type="text" 
                        placeholder="Cari produk di KULAAN.id..." 
                        class="w-full bg-white/10 text-white placeholder-blue-200 pl-10 pr-4 py-2 rounded-lg border border-white/20 focus:outline-none focus:bg-white focus:text-gray-900 focus:placeholder-gray-400 focus:border-white transition-all text-sm shadow-inner"
                        @input="handleSearchInput"
                    />
                    <button v-if="filters.search" @click="clearSearch" class="absolute right-3 top-2.5 text-blue-200 hover:text-white transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-4">
                    <RouterLink to="/home" class="text-sm font-medium hover:text-blue-200 transition-colors">Beranda</RouterLink>
                    <span class="text-sm text-blue-100 hidden sm:block">
                        Halo, <strong class="text-white">{{ authStore.user?.name }}</strong>
                    </span>
                    <button @click="handleLogout" class="text-xs bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                        Keluar
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Workspace -->
        <div class="max-w-7xl w-full mx-auto px-6 py-8 flex flex-col md:flex-row gap-8 flex-1">
            <!-- Sidebar Filter Kiri (260px fixed) -->
            <aside class="w-full md:w-[260px] flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <div class="flex items-center justify-between mb-5 pb-3 border-b border-gray-100">
                        <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <span>Filter Produk</span>
                        </h2>
                        <button 
                            @click="resetFilters" 
                            class="text-xs text-gray-400 hover:text-blue-600 transition-colors font-medium"
                        >
                            Reset
                        </button>
                    </div>

                    <!-- Search Input on Mobile/Sidebar -->
                    <div class="mb-5 md:hidden">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Pencarian</label>
                        <input 
                            v-model="filters.search" 
                            type="text" 
                            placeholder="Cari nama produk..." 
                            class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 bg-gray-50 focus:outline-none focus:border-blue-500 transition-colors"
                            @input="handleSearchInput"
                        />
                    </div>

                    <!-- Kategori -->
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kategori</label>
                        <select 
                            v-model="filters.category" 
                            :class="['w-full text-sm border rounded-lg px-3 py-2 bg-gray-50 focus:outline-none transition-all', filters.category ? 'border-blue-500 bg-blue-50/10 font-medium' : 'border-gray-200']"
                            @change="applyFilters"
                        >
                            <option value="">Semua Kategori</option>
                            <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                                {{ cat.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Kecamatan -->
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kecamatan</label>
                        <select 
                            v-model="filters.district" 
                            :class="['w-full text-sm border rounded-lg px-3 py-2 bg-gray-50 focus:outline-none transition-all', filters.district ? 'border-blue-500 bg-blue-50/10 font-medium' : 'border-gray-200']"
                            @change="applyFilters"
                        >
                            <option value="">Semua Kelurahan</option>
                            <option v-for="dist in districts" :key="dist.value" :value="dist.value">
                                {{ dist.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Rentang Harga -->
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Rentang Harga (Rp)</label>
                        <div class="space-y-2">
                            <input 
                                v-model.number="filters.min_price" 
                                type="number" 
                                placeholder="Harga Min" 
                                :class="['w-full text-sm border rounded-lg px-3 py-2 bg-gray-50 focus:outline-none transition-all', filters.min_price ? 'border-blue-500 bg-blue-50/10 font-medium' : 'border-gray-200']"
                                @input="handlePriceInput"
                            />
                            <input 
                                v-model.number="filters.max_price" 
                                type="number" 
                                placeholder="Harga Maks" 
                                :class="['w-full text-sm border rounded-lg px-3 py-2 bg-gray-50 focus:outline-none transition-all', filters.max_price ? 'border-blue-500 bg-blue-50/10 font-medium' : 'border-gray-200']"
                                @input="handlePriceInput"
                            />
                        </div>
                    </div>

                    <button 
                        @click="applyFilters" 
                        class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-sm hover:shadow transition-all"
                    >
                        Terapkan Filter
                    </button>
                </div>
            </aside>

            <!-- Grid Produk Kanan -->
            <main class="flex-1 flex flex-col gap-6">
                <!-- Toolbar Atas Grid -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="text-sm text-gray-600 font-medium">
                        <span v-if="loading">Memuat produk...</span>
                        <span v-else>Menampilkan <strong class="text-gray-900">{{ meta?.total || 0 }}</strong> produk</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Urutkan:</span>
                        <select 
                            v-model="filters.sort" 
                            class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-gray-50 focus:outline-none focus:border-blue-500 transition-colors font-medium text-gray-700"
                            @change="applyFilters"
                        >
                            <option value="terbaru">Terbaru</option>
                            <option value="termurah">Harga: Termurah</option>
                            <option value="termahal">Harga: Termahal</option>
                        </select>
                    </div>
                </div>

                <!-- Chips / Active Filters Tags -->
                <div v-if="activeChips.length > 0" class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-400 font-medium mr-1">Filter aktif:</span>
                    <div 
                        v-for="chip in activeChips" 
                        :key="chip.key"
                        class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full border border-blue-100"
                    >
                        <span>{{ chip.label }}</span>
                        <button 
                            @click="removeChip(chip.key)" 
                            class="hover:text-blue-900 transition-colors font-bold text-sm"
                        >
                            &times;
                        </button>
                    </div>
                    <button 
                        @click="resetFilters" 
                        class="text-xs text-blue-600 hover:underline font-semibold ml-2"
                    >
                        Hapus Semua
                    </button>
                </div>

                <!-- Product Grid Area -->
                <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="i in 6" :key="i" class="bg-white rounded-xl border border-gray-100 p-4 animate-pulse">
                        <div class="aspect-square bg-gray-200 rounded-lg mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    </div>
                </div>

                <div v-else-if="products.length === 0" class="bg-white rounded-2xl border border-gray-100 p-16 text-center shadow-sm">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Produk tidak ditemukan</h3>
                    <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">
                        Maaf, kami tidak dapat menemukan produk yang sesuai dengan filter Anda. Silakan coba filter atau kata kunci lainnya.
                    </p>
                    <button @click="resetFilters" class="px-5 py-2 bg-blue-50 text-blue-600 font-bold text-sm rounded-lg hover:bg-blue-100 transition-colors">
                        Reset Semua Filter
                    </button>
                </div>

                <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div 
                        v-for="prod in products" 
                        :key="prod.id_product" 
                        class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col relative"
                    >
                        <!-- Category Badge -->
                        <span class="absolute top-3 left-3 bg-blue-600/90 text-white text-xs font-bold px-2.5 py-1 rounded-md z-10 shadow-sm">
                            {{ formatCategory(prod.category?.name) }}
                        </span>

                        <!-- Image -->
                        <div class="aspect-square bg-gray-50 overflow-hidden relative">
                            <img 
                                v-if="prod.image_product" 
                                :src="'/storage/' + prod.image_product" 
                                :alt="prod.name" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <!-- Placeholder -->
                            <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-100 text-gray-400">
                                <span class="text-4xl mb-2">{{ getEmojiForCategory(prod.category?.name) }}</span>
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">No Photo</span>
                            </div>

                            <!-- Hover Button "Lihat Detail" -->
                            <div class="absolute inset-0 bg-gray-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button 
                                    @click="viewDetail(prod.id_product)" 
                                    class="px-5 py-2.5 bg-white text-gray-900 font-bold text-sm rounded-xl shadow hover:bg-gray-50 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300"
                                >
                                    Lihat Detail
                                </button>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-1 mb-1" :title="prod.name">
                                    {{ prod.name }}
                                </h4>
                                <p class="text-xs text-gray-400 font-semibold mb-3 flex items-center gap-1">
                                    <span>🏪 {{ prod.store?.name }}</span>
                                    <span>&bull;</span>
                                    <span class="capitalize">{{ prod.store?.district }}</span>
                                </p>
                            </div>
                            
                            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                <span class="font-extrabold text-blue-600 text-base">
                                    {{ prod.price_formatted }}
                                </span>
                                <span class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-0.5 rounded">
                                    Stok: {{ prod.stock }} {{ prod.unit }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="meta && meta.last_page > 1" class="flex justify-center items-center gap-2 mt-8">
                    <!-- Prev -->
                    <button 
                        :disabled="filters.page === 1"
                        class="px-3.5 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm"
                        @click="changePage(filters.page - 1)"
                    >
                        Sebelumnya
                    </button>

                    <!-- Page Numbers -->
                    <button 
                        v-for="page in meta.last_page" 
                        :key="page"
                        :class="['w-9 h-9 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center justify-center border', filters.page === page ? 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50']"
                        @click="changePage(page)"
                    >
                        {{ page }}
                    </button>

                    <!-- Next -->
                    <button 
                        :disabled="filters.page === meta.last_page"
                        class="px-3.5 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm"
                        @click="changePage(filters.page + 1)"
                    >
                        Selanjutnya
                    </button>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute, RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { productService } from '@/services/productService';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const products = ref([]);
const meta = ref(null);
const loading = ref(true);

const categories = [
    { value: 'makanan_minuman', label: 'Makanan & Minuman' },
    { value: 'fashion_batik', label: 'Fashion & Batik' },
    { value: 'kerajinan', label: 'Kerajinan Tangan' },
    { value: 'elektronik', label: 'Elektronik & Aksesori' },
    { value: 'kecantikan', label: 'Kecantikan & Perawatan' },
    { value: 'pertanian', label: 'Pertanian & Hasil Bumi' },
    { value: 'jasa', label: 'Jasa & Layanan' },
    { value: 'lainnya', label: 'Lainnya' },
];

const districts = [
    { value: 'jebres', label: 'Jebres' },
    { value: 'tegalharjo', label: 'Tegalharjo' },
    { value: 'kepatihan_kulon', label: 'Kepatihan Kulon' },
    { value: 'kepatihan_wetan', label: 'Kepatihan Wetan' },
    { value: 'pucang_sawit', label: 'Pucang Sawit' },
    { value: 'gandekan', label: 'Gandekan' },
    { value: 'sewu', label: 'Sewu' },
    { value: 'pucangsawit', label: 'Pucangsawit' },
    { value: 'jagalan', label: 'Jagalan' },
    { value: 'mojosongo', label: 'Mojosongo' },
];

// Initial State dari query params URL
const filters = reactive({
    search: route.query.search || '',
    category: route.query.category || '',
    district: route.query.district || '',
    min_price: route.query.min_price ? Number(route.query.min_price) : '',
    max_price: route.query.max_price ? Number(route.query.max_price) : '',
    sort: route.query.sort || 'terbaru',
    page: route.query.page ? Number(route.query.page) : 1,
});

// Compute active filter chips
const activeChips = computed(() => {
    const chips = [];
    if (filters.search) {
        chips.push({ key: 'search', label: `Cari: "${filters.search}"` });
    }
    if (filters.category) {
        const cat = categories.find(c => c.value === filters.category);
        chips.push({ key: 'category', label: `Kategori: ${cat ? cat.label : filters.category}` });
    }
    if (filters.district) {
        const dist = districts.find(d => d.value === filters.district);
        chips.push({ key: 'district', label: `Wilayah: ${dist ? dist.label : filters.district}` });
    }
    if (filters.min_price) {
        chips.push({ key: 'min_price', label: `Min: Rp ${filters.min_price.toLocaleString('id-ID')}` });
    }
    if (filters.max_price) {
        chips.push({ key: 'max_price', label: `Maks: Rp ${filters.max_price.toLocaleString('id-ID')}` });
    }
    return chips;
});

let debounceTimer = null;

const loadProducts = async () => {
    loading.value = true;
    try {
        const cleanParams = {};
        Object.keys(filters).forEach(k => {
            if (filters[k] !== '' && filters[k] !== null && filters[k] !== undefined) {
                cleanParams[k] = filters[k];
            }
        });
        
        const response = await productService.getProducts(cleanParams);
        if (response.success) {
            products.value = response.data.data;
            meta.value = response.data.meta;
        }
    } catch (error) {
        console.error('Gagal mengambil data produk', error);
    } finally {
        loading.value = false;
    }
};

const applyFilters = () => {
    // Kapanpun filter di-apply, kembalikan ke page 1 kecuali jika pagenya yang diubah
    const query = {};
    Object.keys(filters).forEach(k => {
        if (filters[k] !== '' && filters[k] !== null && filters[k] !== undefined) {
            query[k] = filters[k];
        }
    });

    // Pushing new query to URL
    router.push({ name: 'products', query });
};

const handleSearchInput = () => {
    clearTimeout(debounceTimer);
    filters.page = 1;
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 400);
};

const handlePriceInput = () => {
    clearTimeout(debounceTimer);
    filters.page = 1;
    debounceTimer = setTimeout(() => {
        applyFilters();
    }, 600);
};

const removeChip = (key) => {
    filters[key] = '';
    filters.page = 1;
    applyFilters();
};

const resetFilters = () => {
    filters.search = '';
    filters.category = '';
    filters.district = '';
    filters.min_price = '';
    filters.max_price = '';
    filters.sort = 'terbaru';
    filters.page = 1;
    applyFilters();
};

const clearSearch = () => {
    filters.search = '';
    applyFilters();
};

const changePage = (page) => {
    filters.page = page;
    applyFilters();
};

const viewDetail = (id) => {
    router.push({ name: 'product.detail', params: { id } });
};

const goHome = () => {
    router.push('/home');
};

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};

const formatCategory = (val) => {
    const cat = categories.find(c => c.value === val);
    return cat ? cat.label : val;
};

const getEmojiForCategory = (val) => {
    if (val === 'makanan_minuman') return '🍱';
    if (val === 'fashion_batik') return '👗';
    if (val === 'kerajinan') return '🧶';
    if (val === 'elektronik') return '📱';
    if (val === 'kecantikan') return '💄';
    if (val === 'pertanian') return '🌾';
    if (val === 'jasa') return '🛠️';
    return '📦';
};

// Sinkronkan data filter dengan URL query parameter
watch(() => route.query, (newQuery) => {
    filters.search = newQuery.search || '';
    filters.category = newQuery.category || '';
    filters.district = newQuery.district || '';
    filters.min_price = newQuery.min_price ? Number(newQuery.min_price) : '';
    filters.max_price = newQuery.max_price ? Number(newQuery.max_price) : '';
    filters.sort = newQuery.sort || 'terbaru';
    filters.page = newQuery.page ? Number(newQuery.page) : 1;
    
    loadProducts();
}, { deep: true });

onMounted(() => {
    loadProducts();
});
</script>

<style scoped>
.navbar {
    background: linear-gradient(135deg, var(--blue-600) 0%, #1565a8 100%);
}
</style>
