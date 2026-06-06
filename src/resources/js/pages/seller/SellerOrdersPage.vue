<template>
    <div class="min-h-screen bg-gray-50 p-6 md:p-12 font-sans">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Kelola Pesanan Masuk</h1>
                    <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ authStore.user?.store?.store_name || 'Toko UMKM' }}
                    </span>
                </div>
                
                <div class="flex items-center gap-3">
                    <RouterLink to="/seller/profile" class="px-4 py-2 border border-gray-300 hover:border-gray-400 bg-white text-gray-700 font-bold text-sm rounded-lg shadow-sm hover:shadow transition-all text-center">
                        🏬 Profil Toko
                    </RouterLink>
                    <button @click="handleLogout" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-lg shadow-sm hover:shadow transition-all text-center">
                        Keluar
                    </button>
                </div>
            </div>

            <!-- Tab Filter Status & Counts -->
            <div class="flex flex-wrap items-center gap-2 border-b border-gray-200 pb-1">
                <button 
                    v-for="tab in tabs" 
                    :key="tab.value"
                    :class="['px-4 py-2.5 rounded-lg text-sm font-bold transition-all flex items-center gap-2 border', activeTab === tab.value ? 'bg-blue-600 border-blue-600 text-white shadow-sm' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300']"
                    @click="activeTab = tab.value"
                >
                    <span>{{ tab.label }}</span>
                    <span :class="['px-2 py-0.5 rounded-full text-xs font-extrabold shadow-inner', activeTab === tab.value ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500']">
                        {{ getTabCount(tab.value) }}
                    </span>
                </button>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h2 class="text-base font-extrabold text-gray-800 uppercase tracking-wider">Daftar Pesanan Masuk</h2>
                        <p class="text-xs text-gray-400 mt-1">Data diperbarui secara real-time</p>
                    </div>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ getTabCount('menunggu') }} Baru
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-[11px] uppercase text-gray-500 font-bold tracking-wider">
                                <th class="py-4 px-6">No. Order</th>
                                <th class="py-4 px-6">Produk</th>
                                <th class="py-4 px-6">Pembeli</th>
                                <th class="py-4 px-6">Qty</th>
                                <th class="py-4 px-6">Total</th>
                                <th class="py-4 px-6">Metode Bayar</th>
                                <th class="py-4 px-6">Tgl Order</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="loading" class="text-center">
                                <td colspan="9" class="py-8 text-sm text-gray-500">Memuat data pesanan...</td>
                            </tr>
                            <tr v-else-if="filteredOrders.length === 0" class="text-center">
                                <td colspan="9" class="py-12 text-gray-400">
                                    <div class="text-4xl mb-2">📋</div>
                                    <span class="text-sm font-semibold">Tidak ada pesanan pada status ini.</span>
                                </td>
                            </tr>
                            <tr v-else v-for="order in filteredOrders" :key="order.id_order" class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 font-bold text-gray-900 text-sm">#{{ order.id_order }}</td>
                                <td class="py-4 px-6">
                                    <div 
                                        @click="openDetailModal(order)"
                                        class="font-bold text-gray-900 text-sm hover:text-blue-600 cursor-pointer line-clamp-1"
                                    >
                                        {{ order.order_items[0]?.product?.name || 'Produk' }}
                                    </div>
                                    <span class="text-xs text-gray-400 font-medium">Unit: {{ order.order_items[0]?.product?.unit }}</span>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-600 font-medium">
                                    <div>{{ order.user?.name || '-' }}</div>
                                    <span class="text-xs text-gray-400 font-semibold">{{ order.user?.phone_number || '-' }}</span>
                                </td>
                                <td class="py-4 px-6 text-gray-900 font-bold text-sm">{{ order.order_items[0]?.quantity || 0 }}</td>
                                <td class="py-4 px-6 font-bold text-blue-600 text-sm">{{ formatPrice(order.total_order) }}</td>
                                <td class="py-4 px-6">
                                    <span :class="['px-2.5 py-0.5 rounded text-[11px] font-bold uppercase tracking-wider', order.payment_method === 'cod' ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-indigo-50 text-indigo-700 border border-indigo-100']">
                                        {{ order.payment_method }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-gray-500 text-xs font-semibold">{{ formatDate(order.created_at) }}</td>
                                <td class="py-4 px-6">
                                    <span :class="['px-2.5 py-1 rounded-full text-xs font-bold border', statusBadgeClass(order.status)]">
                                        {{ formatStatus(order.status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-1.5 whitespace-nowrap">
                                    <!-- Status: Menunggu -->
                                    <template v-if="order.status === 'menunggu'">
                                        <button 
                                            @click="updateStatus(order.id_order, 'diproses')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg text-xs font-bold transition-all"
                                        >
                                            Proses
                                        </button>
                                        <button 
                                            @click="updateStatus(order.id_order, 'dibatalkan')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 rounded-lg text-xs font-bold transition-all"
                                        >
                                            Batalkan
                                        </button>
                                    </template>

                                    <!-- Status: Diproses -->
                                    <template v-else-if="order.status === 'diproses'">
                                        <button 
                                            @click="updateStatus(order.id_order, 'selesai')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 rounded-lg text-xs font-bold transition-all"
                                        >
                                            Selesaikan
                                        </button>
                                    </template>
                                    
                                    <span v-else class="text-gray-300 font-semibold text-xs">&mdash;</span>

                                    <button 
                                        @click="openDetailModal(order)" 
                                        class="inline-flex items-center px-2 py-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-xs font-bold transition-colors"
                                    >
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div v-if="selectedOrder" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeDetailModal"></div>
            
            <div class="relative bg-white rounded-2xl shadow-xl max-w-xl w-full transform transition-all overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-150 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-base font-extrabold text-gray-800 uppercase tracking-wider">Detail Informasi Pesanan</h3>
                    <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-5">
                    <!-- Section A: Status info -->
                    <div class="flex justify-between items-center bg-blue-50/40 p-4 border border-blue-50 rounded-xl">
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Nomor Order</span>
                            <strong class="text-gray-800 text-sm font-bold">#{{ selectedOrder.id_order }}</strong>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wider text-right mb-1">Status</span>
                            <span :class="['px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wider', statusBadgeClass(selectedOrder.status)]">
                                {{ formatStatus(selectedOrder.status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Section B: Detail Produk -->
                    <div class="space-y-2">
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Detail Produk Yang Dipesan</h4>
                        <div class="flex gap-4 items-center bg-gray-50 p-4 border border-gray-100 rounded-xl">
                            <div class="flex-1">
                                <span class="block font-bold text-gray-800 text-sm">{{ selectedOrder.order_items[0]?.product?.name }}</span>
                                <span class="text-xs text-gray-400 font-semibold">Harga Beli: {{ formatPrice(selectedOrder.order_items[0]?.price_at_purchase) }} / {{ selectedOrder.order_items[0]?.product?.unit }}</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs text-gray-400 font-bold uppercase tracking-wider">Kuantitas</span>
                                <span class="font-extrabold text-sm text-gray-800">{{ selectedOrder.order_items[0]?.quantity }} {{ selectedOrder.order_items[0]?.product?.unit }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section C: Buyer & Shipping Detail -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nama Pembeli</h4>
                            <p class="text-gray-800 text-sm font-bold">{{ selectedOrder.user?.name || '-' }}</p>
                        </div>
                        <div>
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nomor Handphone</h4>
                            <a :href="'https://wa.me/' + selectedOrder.user?.phone_number" target="_blank" class="text-blue-600 hover:underline text-sm font-bold flex items-center gap-1">
                                💬 {{ selectedOrder.user?.phone_number || '-' }}
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Metode Pembayaran</h4>
                            <span class="capitalize text-gray-800 font-semibold text-xs px-2 py-0.5 bg-gray-100 rounded border border-gray-150">{{ selectedOrder.payment_method }}</span>
                        </div>
                        <div>
                            <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Total Transaksi</h4>
                            <strong class="text-blue-600 text-sm font-extrabold">{{ formatPrice(selectedOrder.total_order) }}</strong>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Alamat Pengiriman</h4>
                        <p class="text-gray-700 text-xs font-semibold leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100 break-words">{{ selectedOrder.shipping_address }}</p>
                    </div>

                    <div>
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan Tambahan</h4>
                        <p class="text-gray-600 text-xs bg-gray-50 p-3 rounded-lg border border-gray-100 break-words leading-relaxed">
                            {{ selectedOrder.note || 'Tidak ada catatan khusus.' }}
                        </p>
                    </div>

                    <!-- Timeline Status -->
                    <div>
                        <h4 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Pesanan Masuk</h4>
                        <p class="text-gray-500 text-xs font-semibold">{{ formatDateFull(selectedOrder.created_at) }}</p>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end space-x-3">
                    <button @click="closeDetailModal" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div :class="['toast', toastType, toastVisible && 'show']" role="alert">
            {{ toastMsg }}
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, RouterLink } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { orderService } from '@/services/orderService';

const router = useRouter();
const authStore = useAuthStore();

const orders = ref([]);
const loading = ref(true);
const selectedOrder = ref(null);
const activeTab = ref('semua');

const tabs = [
    { value: 'semua', label: 'Semua' },
    { value: 'menunggu', label: 'Menunggu' },
    { value: 'diproses', label: 'Diproses' },
    { value: 'selesai', label: 'Selesai' },
    { value: 'dibatalkan', label: 'Dibatalkan' },
];

const fetchOrders = async () => {
    loading.value = true;
    try {
        const res = await orderService.getSellerOrders();
        if (res.success) {
            orders.value = res.data;
        }
    } catch (error) {
        console.error('Failed to fetch seller orders', error);
        showToast('Gagal memuat pesanan masuk.', 'error');
    } finally {
        loading.value = false;
    }
};

const filteredOrders = computed(() => {
    if (activeTab.value === 'semua') {
        return orders.value;
    }
    return orders.value.filter(o => o.status === activeTab.value);
});

const getTabCount = (status) => {
    if (status === 'semua') {
        return orders.value.length;
    }
    return orders.value.filter(o => o.status === status).length;
};

const updateStatus = async (id, status) => {
    const confirmMsg = status === 'diproses' 
        ? 'Terima dan proses pesanan ini?' 
        : (status === 'selesai' ? 'Selesaikan pesanan ini? Stok akan dipotong permanen.' : 'Batalkan pesanan ini? Stok akan dikembalikan.');
        
    if (!confirm(confirmMsg)) return;

    try {
        const res = await orderService.updateOrderStatus(id, status);
        if (res.success) {
            showToast('Status pesanan berhasil diperbarui.', 'success');
            fetchOrders();
            if (selectedOrder.value && selectedOrder.value.id_order === id) {
                selectedOrder.value = res.data;
            }
        }
    } catch (error) {
        const res = error.response;
        if (res?.status === 422) {
            showToast(res.data.message || 'Gagal merubah status pesanan.', 'error');
        } else {
            showToast('Gagal memperbarui status pesanan.', 'error');
        }
    }
};

const openDetailModal = (order) => {
    selectedOrder.value = order;
};

const closeDetailModal = () => {
    selectedOrder.value = null;
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const formatDateFull = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) + ' WIB';
};

const formatStatus = (status) => {
    if (status === 'menunggu') return 'Menunggu';
    if (status === 'diproses') return 'Diproses';
    if (status === 'selesai') return 'Selesai';
    if (status === 'dibatalkan') return 'Dibatalkan';
    return status;
};

const statusBadgeClass = (status) => {
    if (status === 'menunggu') return 'bg-yellow-50 text-yellow-700 border-yellow-200';
    if (status === 'diproses') return 'bg-blue-50 text-blue-700 border-blue-200';
    if (status === 'selesai') return 'bg-green-50 text-green-700 border-green-200';
    if (status === 'dibatalkan') return 'bg-red-50 text-red-700 border-red-200';
    return 'bg-gray-50 text-gray-700 border-gray-200';
};

const handleLogout = async () => {
    if (!confirm('Apakah Anda yakin ingin keluar?')) return;
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
    fetchOrders();
});
</script>

<style scoped>
.toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    color: white;
    font-weight: 600;
    font-size: 14px;
    z-index: 9999;
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

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;  
    overflow: hidden;
}
</style>
