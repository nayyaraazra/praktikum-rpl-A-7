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

        <!-- Loader / Skeleton -->
        <main v-if="loadingProduct" class="max-w-5xl w-full mx-auto px-6 py-8 flex-1 flex items-center justify-center">
            <div class="text-center animate-pulse">
                <div class="w-12 h-12 rounded-full border-4 border-blue-200 border-t-blue-600 animate-spin mx-auto mb-4"></div>
                <p class="text-sm text-gray-500 font-semibold">Memuat formulir pemesanan...</p>
            </div>
        </main>

        <!-- Content Utama -->
        <main v-else-if="product" class="max-w-5xl w-full mx-auto px-6 py-8 flex-1 flex flex-col gap-6">
            <!-- Breadcrumb / Tombol Kembali -->
            <div class="flex items-center justify-between">
                <nav class="flex items-center gap-2 text-xs md:text-sm text-gray-500 font-medium">
                    <RouterLink to="/products" class="hover:text-blue-600 transition-colors">Katalog</RouterLink>
                    <span class="text-gray-300">/</span>
                    <RouterLink :to="{ name: 'product.detail', params: { id: product.id_product } }" class="hover:text-blue-600 transition-colors">{{ product.name }}</RouterLink>
                    <span class="text-gray-300">/</span>
                    <span class="text-gray-800 font-semibold">Buat Pesanan</span>
                </nav>
            </div>

            <!-- Formulir Pemesanan & Ringkasan -->
            <div class="grid grid-cols-1 md:grid-cols-10 gap-8 items-start">
                
                <!-- Kolom Kiri: Ringkasan Pesanan (40%) - Sticky -->
                <div class="md:col-span-4 sticky top-24 space-y-6">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                        <h2 class="text-base font-extrabold text-gray-900 pb-3 border-b border-gray-100 uppercase tracking-wider">
                            Ringkasan Pesanan
                        </h2>

                        <!-- Product Mini Card -->
                        <div class="flex gap-4 items-start">
                            <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-lg overflow-hidden flex-shrink-0 relative">
                                <img 
                                    v-if="product.image_product" 
                                    :src="'/storage/' + product.image_product" 
                                    :alt="product.name" 
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center bg-gray-100 text-xl">
                                    {{ getEmojiForCategory(product.category?.name) }}
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-sm text-gray-900 line-clamp-2 leading-tight mb-1">{{ product.name }}</h3>
                                <p class="text-xs text-gray-400 font-semibold mb-1">🏪 {{ product.store?.name }}</p>
                                <p class="text-xs font-extrabold text-blue-600">{{ product.price_formatted }} <span class="text-gray-400 font-medium">/ {{ product.unit }}</span></p>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="py-4 border-t border-gray-100 flex items-center justify-between gap-4">
                            <span class="text-sm text-gray-600 font-bold">Jumlah Pesanan</span>
                            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl px-1 py-1 shadow-inner">
                                <button 
                                    type="button"
                                    :disabled="quantity <= product.min_order"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-gray-600 bg-white shadow hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                                    @click="decreaseQty"
                                >
                                    &minus;
                                </button>
                                <input 
                                    v-model.number="quantity" 
                                    type="number" 
                                    class="w-12 text-center text-sm font-extrabold bg-transparent focus:outline-none border-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                    @change="validateQty"
                                />
                                <button 
                                    type="button"
                                    :disabled="quantity >= product.stock"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-gray-600 bg-white shadow hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                                    @click="increaseQty"
                                >
                                    &plus;
                                </button>
                            </div>
                        </div>

                        <!-- Subtotal & Total Info -->
                        <div class="pt-4 border-t border-gray-100 space-y-2.5">
                            <div class="flex justify-between text-xs text-gray-500 font-medium">
                                <span>Harga Satuan</span>
                                <span>{{ product.price_formatted }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 font-medium">
                                <span>Jumlah</span>
                                <span>{{ quantity }} {{ product.unit }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-bold text-gray-700 pt-2 border-t border-dashed border-gray-100">
                                <span>Subtotal</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-base font-black text-blue-600 pt-3 border-t border-gray-150">
                                <span>Total Pembayaran</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Form Alamat & Pembayaran (60%) -->
                <div class="md:col-span-6 w-full">
                    <!-- Sukses Panel -->
                    <div v-if="orderSuccess" class="bg-white rounded-2xl border border-green-100 p-8 shadow-md text-center space-y-6">
                        <div class="w-16 h-16 bg-green-50 border border-green-200 rounded-full flex items-center justify-center mx-auto text-green-500 text-3xl shadow-sm">
                            &check;
                        </div>
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-900 mb-1">Pesanan Berhasil Dibuat!</h2>
                            <p class="text-sm text-gray-500">
                                Pesanan Anda telah terdaftar di sistem kami dan diteruskan ke Penjual.
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 text-left space-y-3 max-w-md mx-auto">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Nomor Pesanan</span>
                                <strong class="text-gray-800 font-bold">#{{ createdOrder?.id_order }}</strong>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Metode Pembayaran</span>
                                <span class="capitalize text-gray-800 font-bold">{{ createdOrder?.payment_method }}</span>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Total Belanja</span>
                                <strong class="text-blue-600 font-extrabold">{{ formatPrice(createdOrder?.total_order || 0) }}</strong>
                            </div>
                            <div class="text-xs text-gray-500 pt-2 border-t border-gray-100">
                                <span>Alamat Pengiriman:</span>
                                <p class="text-gray-800 font-semibold mt-1 break-words leading-relaxed">{{ createdOrder?.shipping_address }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-center gap-3">
                            <button @click="goToKatalog" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow transition-colors">
                                Cari Produk Lain
                            </button>
                            <button @click="goToHome" class="px-5 py-2.5 border border-gray-300 hover:border-gray-400 bg-white text-gray-700 font-bold text-sm rounded-xl shadow-sm transition-colors">
                                Lihat Beranda
                            </button>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form v-else @submit.prevent="handleSubmit" class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 shadow-sm space-y-6">
                        <!-- Section 1: Metode Pembayaran -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-extrabold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-6 h-6 bg-blue-50 text-blue-600 text-xs font-extrabold rounded-full flex items-center justify-center">1</span>
                                Metode Pembayaran
                            </h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Radio Card COD -->
                                <label 
                                    :class="['border-2 rounded-xl p-4 flex gap-3 cursor-pointer items-start transition-all', form.payment_method === 'cod' ? 'border-blue-500 bg-blue-50/25' : 'border-gray-200 hover:border-gray-300']"
                                >
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        value="cod" 
                                        v-model="form.payment_method" 
                                        class="mt-1 text-blue-600 focus:ring-blue-500"
                                    />
                                    <div>
                                        <span class="block font-bold text-gray-800 text-sm">💵 COD (Bayar di Toko)</span>
                                        <span class="block text-xs text-gray-400 leading-normal mt-1">Bayar tunai di gerai saat mengambil barang pesanan.</span>
                                    </div>
                                </label>

                                <!-- Radio Card Transfer -->
                                <label 
                                    :class="['border-2 rounded-xl p-4 flex gap-3 cursor-pointer items-start transition-all', form.payment_method === 'transfer' ? 'border-blue-500 bg-blue-50/25' : 'border-gray-200 hover:border-gray-300']"
                                >
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        value="transfer" 
                                        v-model="form.payment_method" 
                                        class="mt-1 text-blue-600 focus:ring-blue-500"
                                    />
                                    <div>
                                        <span class="block font-bold text-gray-800 text-sm">🏦 Transfer Bank</span>
                                        <span class="block text-xs text-gray-400 leading-normal mt-1">Transfer kirim bukti via WhatsApp penjual secara manual.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Section 2: Alamat Pengiriman -->
                        <div class="space-y-3">
                            <h3 class="text-sm font-extrabold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-6 h-6 bg-blue-50 text-blue-600 text-xs font-extrabold rounded-full flex items-center justify-center">2</span>
                                Alamat Pengiriman <span class="text-red-500">*</span>
                            </h3>
                            <textarea 
                                v-model="form.shipping_address" 
                                rows="3" 
                                placeholder="Tulis alamat lengkap Anda termasuk RT/RW, nomor rumah, kelurahan..."
                                :class="['w-full text-sm border rounded-lg px-4 py-3 bg-gray-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all leading-relaxed', errors.shipping_address ? 'border-red-400 bg-red-50/20' : 'border-gray-200']"
                            ></textarea>
                            <span v-if="errors.shipping_address" class="text-xs text-red-500 font-semibold">{{ errors.shipping_address }}</span>
                        </div>

                        <!-- Section 3: Catatan Tambahan -->
                        <div class="space-y-3">
                            <h3 class="text-sm font-extrabold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-6 h-6 bg-blue-50 text-blue-600 text-xs font-extrabold rounded-full flex items-center justify-center">3</span>
                                Catatan Tambahan (Opsional)
                            </h3>
                            <textarea 
                                v-model="form.note" 
                                rows="2" 
                                placeholder="mis. Titip ke tetangga sebelah / satpam jika saya tidak ada di rumah..."
                                class="w-full text-sm border border-gray-200 rounded-lg px-4 py-3 bg-gray-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all leading-relaxed"
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button 
                                type="submit" 
                                :disabled="submitting"
                                class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-md hover:shadow-lg transition-all text-center uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="submitting">Memproses Pesanan...</span>
                                <span v-else>Kirim Pesanan</span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </main>

        <!-- Toast -->
        <div :class="['toast', toastType, toastVisible && 'show']" role="alert">
            {{ toastMsg }}
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRoute, useRouter, RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { productService } from '@/services/productService';
import { orderService } from '@/services/orderService';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const product = ref(null);
const loadingProduct = ref(true);
const quantity = ref(1);

const form = reactive({
    payment_method: 'cod',
    shipping_address: '',
    note: '',
});

const errors = reactive({});
const submitting = ref(false);
const orderSuccess = ref(false);
const createdOrder = ref(null);

const getProduct = async () => {
    loadingProduct.value = true;
    try {
        const id = route.params.productId;
        const response = await productService.getProductDetail(id);
        if (response.success) {
            product.value = response.data;
            quantity.value = response.data.min_order;
        }
    } catch (error) {
        console.error('Gagal mengambil data produk', error);
    } finally {
        loadingProduct.value = false;
    }
};

const increaseQty = () => {
    if (quantity.value < product.value.stock) {
        quantity.value++;
    }
};

const decreaseQty = () => {
    if (quantity.value > product.value.min_order) {
        quantity.value--;
    }
};

const validateQty = () => {
    if (!quantity.value || quantity.value < product.value.min_order) {
        quantity.value = product.value.min_order;
    } else if (quantity.value > product.value.stock) {
        quantity.value = product.value.stock;
    }
};

const subtotal = computed(() => {
    if (!product.value) return 0;
    return product.value.price * quantity.value;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
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

const validateForm = () => {
    errors.shipping_address = '';
    let valid = true;
    if (!form.shipping_address.trim()) {
        errors.shipping_address = 'Alamat pengiriman wajib diisi.';
        valid = false;
    }
    return valid;
};

const handleSubmit = async () => {
    if (!validateForm()) return;
    submitting.value = true;
    try {
        const payload = {
            id_product: product.value.id_product,
            quantity: quantity.value,
            payment_method: form.payment_method,
            shipping_address: form.shipping_address,
            note: form.note,
        };
        const response = await orderService.createOrder(payload);
        if (response.success) {
            createdOrder.value = response.data;
            orderSuccess.value = true;
            showToast('Pesanan berhasil dibuat!', 'success');
        }
    } catch (error) {
        const res = error.response;
        if (res?.status === 422) {
            showToast(res.data.message || 'Periksa kembali formulir Anda.', 'error');
        } else {
            showToast('Gagal memproses pesanan. Silakan coba lagi.', 'error');
        }
    } finally {
        submitting.value = false;
    }
};

const goToKatalog = () => {
    router.push({ name: 'products' });
};

const goToHome = () => {
    router.push('/home');
};

const goHome = () => {
    router.push('/home');
};

const handleLogout = async () => {
    await authStore.logout();
    router.push({ name: 'login' });
};

// Toast Notification State
const toastMsg = ref('');
const toastType = ref('');
const toastVisible = ref(false);
let toastTimer = null;

function showToast(msg, type = '', duration = 3000) {
    clearTimeout(toastTimer);
    toastMsg.value = msg;
    toastType.value = type;
    toastVisible.value = true;
    toastTimer = setTimeout(() => { toastVisible.value = false; }, duration);
}

onMounted(() => {
    getProduct();
});
</script>

<style scoped>
.navbar {
    background: linear-gradient(135deg, var(--blue-600) 0%, #1565a8 100%);
}

.toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    color: white;
    font-weight: 600;
    font-size: 14px;
    z-index: 50;
    box-shadow: var(--shadow-md);
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast.show {
    transform: translateY(0);
    opacity: 1;
}

.toast.success {
    background-color: #10B981;
}

.toast.error {
    background-color: #EF4444;
}
</style>
