<template>
    <div class="min-h-screen bg-gray-50 flex flex-col font-sans">
        <!-- Navbar -->
        <header class="navbar bg-blue-600 shadow-md text-white sticky top-0 z-40">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between w-full">
                <div class="flex items-center gap-2 cursor-pointer" @click="goHome">
                    <span class="text-2xl font-extrabold tracking-tight">KULAAN<span class="text-blue-200">.id</span></span>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded-full font-medium hidden sm:inline">Katalog UMKM Jebres</span>
                </div>
                
                <div class="flex items-center gap-4">
                    <RouterLink to="/products" class="text-sm font-medium hover:text-blue-200 transition-colors">Cari Produk</RouterLink>
                    <span class="text-sm text-blue-100 hidden sm:block">
                        Halo, <strong class="text-white">{{ authStore.user?.name }}</strong>
                    </span>
                    <button @click="handleLogout" class="text-xs bg-white/20 hover:bg-white/30 text-white px-3 py-1.5 rounded-lg font-medium transition-colors">
                        Keluar
                    </button>
                </div>
            </div>
        </header>

        <!-- Container Utama -->
        <main class="max-w-5xl w-full mx-auto px-6 py-8 flex-1 flex flex-col gap-6">
            <!-- Breadcrumb / Tombol Kembali -->
            <div class="flex items-center justify-between">
                <nav class="flex items-center gap-2 text-xs md:text-sm text-gray-500 font-medium">
                    <RouterLink to="/home" class="hover:text-blue-600 transition-colors">Beranda</RouterLink>
                    <span class="text-gray-300">/</span>
                    <RouterLink to="/products" class="hover:text-blue-600 transition-colors">Cari Produk</RouterLink>
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-800 font-semibold truncate max-w-[150px] sm:max-w-xs">{{ product?.name || 'Detail Produk' }}</span>
                </nav>

                <button 
                    @click="goBack" 
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-blue-600 hover:bg-white border border-transparent hover:border-gray-200 px-3 py-1.5 rounded-lg transition-all"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </button>
            </div>

            <!-- Loader / Skeleton -->
            <div v-if="loading" class="grid grid-cols-1 md:grid-cols-10 gap-8 bg-white rounded-2xl border border-gray-100 p-8 animate-pulse">
                <div class="md:col-span-4 aspect-square bg-gray-200 rounded-xl"></div>
                <div class="md:col-span-6 space-y-4">
                    <div class="h-8 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                    <div class="h-8 bg-gray-200 rounded w-1/3"></div>
                    <div class="h-20 bg-gray-200 rounded"></div>
                    <div class="h-24 bg-gray-200 rounded-xl"></div>
                </div>
            </div>

            <!-- Detail Content -->
            <div v-else-if="product" class="grid grid-cols-1 md:grid-cols-10 gap-8 items-start">
                
                <!-- Kolom Kiri: Foto Produk (40%) -->
                <div class="md:col-span-4 w-full">
                    <div class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="aspect-square bg-gray-50 rounded-xl overflow-hidden relative shadow-inner">
                            <img 
                                v-if="product.image_product" 
                                :src="'/storage/' + product.image_product" 
                                :alt="product.name" 
                                class="w-full h-full object-cover"
                            />
                            <!-- Placeholder -->
                            <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-100 text-gray-400">
                                <span class="text-7xl mb-3">{{ getEmojiForCategory(product.category?.name) }}</span>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Gambar tidak tersedia</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Informasi Produk (60%) -->
                <div class="md:col-span-6 flex flex-col gap-6 w-full">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 shadow-sm space-y-6">
                        <!-- Judul, Rating & Category Badge -->
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-md border border-blue-100">
                                    {{ formatCategory(product.category?.name) }}
                                </span>
                                <span class="bg-green-50 text-green-700 text-xs font-bold px-2.5 py-1 rounded-md border border-green-100">
                                    Stok Tersedia
                                </span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight">
                                {{ product.name }}
                            </h1>
                            
                            <!-- Rating -->
                            <div class="flex items-center gap-2 mt-2.5 text-sm text-gray-500 font-medium">
                                <div class="flex items-center text-yellow-400">
                                    <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" :class="{ 'text-gray-200': i > Math.round(product.rating || 0) }" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </div>
                                <span class="font-bold text-gray-800">{{ product.rating || '0.00' }}</span>
                                <span>&bull;</span>
                                <span>{{ product.review_count || 0 }} Ulasan</span>
                            </div>
                        </div>

                        <!-- Harga -->
                        <div class="py-4 px-6 bg-blue-50/50 rounded-xl border border-blue-50/80 flex items-baseline justify-between">
                            <span class="text-3xl font-black text-blue-600">
                                {{ product.price_formatted }}
                            </span>
                            <span class="text-xs text-gray-400 font-bold">Harga per {{ product.unit }}</span>
                        </div>

                        <!-- Info Spesifikasi Ringkas -->
                        <div class="grid grid-cols-3 gap-4 border-y border-gray-100 py-4 text-center">
                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Stok</span>
                                <span class="font-bold text-gray-900">{{ product.stock }} {{ product.unit }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Min. Order</span>
                                <span class="font-bold text-gray-900">{{ product.min_order }} {{ product.unit }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Satuan</span>
                                <span class="font-bold text-gray-900 text-capitalize">{{ product.unit }}</span>
                            </div>
                        </div>

                        <!-- Deskripsi Collapsible -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Produk</h3>
                            <p class="text-sm text-gray-700 leading-relaxed break-words whitespace-pre-line">
                                {{ descriptionToShow }}
                            </p>
                            <button 
                                v-if="isDescriptionLong" 
                                @click="isDescriptionExpanded = !isDescriptionExpanded" 
                                class="text-xs text-blue-600 hover:text-blue-800 font-bold mt-2 focus:outline-none transition-colors"
                            >
                                {{ isDescriptionExpanded ? 'Sembunyikan' : 'Baca Selengkapnya' }}
                            </button>
                        </div>

                        <!-- Card Info Toko -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm mb-1 flex items-center gap-1.5">
                                    <span>🏪 {{ product.store?.name }}</span>
                                </h4>
                                <p class="text-xs text-gray-500 capitalize mb-1">
                                    📍 Kecamatan {{ product.store?.district }} &bull; {{ product.store?.address }}
                                </p>
                                <p class="text-xs text-blue-600 font-medium">
                                    🕒 Jam Buka: {{ product.store?.operating_hours || '-' }}
                                </p>
                            </div>
                            <!-- Ghost Button -->
                            <button @click="showStoreProfile" class="w-full sm:w-auto px-4 py-2 border border-gray-300 hover:border-gray-400 rounded-lg text-xs font-bold text-gray-700 bg-white shadow-sm hover:shadow transition-all text-center">
                                Lihat Toko
                            </button>
                        </div>

                        <!-- Sticky / Bottom Button Order -->
                        <div class="pt-4 sticky bottom-0 bg-white/95 backdrop-blur-sm pb-2 z-10">
                            <button 
                                @click="orderNow" 
                                :disabled="product.stock < product.min_order"
                                class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none transition-all text-center uppercase tracking-wider"
                            >
                                <span v-if="product.stock >= product.min_order">Pesan Sekarang</span>
                                <span v-else>Stok Habis / Tidak Cukup</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Error State -->
            <div v-else class="bg-white rounded-2xl border border-gray-100 p-16 text-center shadow-sm">
                <div class="text-6xl mb-4">⚠️</div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Produk tidak ditemukan</h3>
                <p class="text-sm text-gray-500 max-w-sm mx-auto mb-6">
                    Maaf, produk yang Anda cari tidak tersedia atau toko penyedia belum disetujui oleh admin.
                </p>
                <RouterLink to="/products" class="px-5 py-2.5 bg-blue-600 text-white font-bold text-sm rounded-lg hover:bg-blue-700 transition-colors inline-block">
                    Kembali ke Katalog
                </RouterLink>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { productService } from '@/services/productService';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const product = ref(null);
const loading = ref(true);
const isDescriptionExpanded = ref(false);

const getProductDetail = async () => {
    loading.value = true;
    try {
        const id = route.params.id;
        const response = await productService.getProductDetail(id);
        if (response.success) {
            product.value = response.data;
        }
    } catch (error) {
        console.error('Gagal mengambil detail produk', error);
    } finally {
        loading.value = false;
    }
};

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

// Collapsible description logic
const isDescriptionLong = computed(() => {
    return product.value?.description && product.value.description.length > 250;
});

const descriptionToShow = computed(() => {
    if (!product.value?.description) return 'Tidak ada deskripsi.';
    if (isDescriptionLong.value && !isDescriptionExpanded.value) {
        return product.value.description.substring(0, 250) + '...';
    }
    return product.value.description;
});

const orderNow = () => {
    if (product.value) {
        router.push({ name: 'order.create', params: { productId: product.value.id_product } });
    }
};

const showStoreProfile = () => {
    alert(`Profil Toko: ${product.value?.store?.name}\nAlamat: ${product.value?.store?.address}\nDeskripsi: ${product.value?.store?.description || '-'}`);
};

const goBack = () => {
    router.push({ name: 'products' });
};

const goHome = () => {
    router.push('/home');
};

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};

onMounted(() => {
    getProductDetail();
});
</script>

<style scoped>
.navbar {
    background: linear-gradient(135deg, var(--blue-600) 0%, #1565a8 100%);
}
</style>
