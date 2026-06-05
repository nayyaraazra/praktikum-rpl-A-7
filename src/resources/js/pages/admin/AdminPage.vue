<template>
    <div class="min-h-screen bg-gray-50 p-6 md:p-12">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Admin Panel</h1>
                <span class="inline-block mt-2 px-3 py-1 bg-gray-200 text-gray-700 rounded-full text-sm font-medium">Kulaan.id</span>
            </div>

            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center text-center">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Total Toko</h3>
                    <p class="text-4xl font-bold text-gray-900">{{ metrics?.total_stores || 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center text-center">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Toko Aktif</h3>
                    <p class="text-4xl font-bold text-green-600">{{ metrics?.active_stores || 0 }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-center items-center text-center">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Menunggu Verif</h3>
                    <p class="text-4xl font-bold text-yellow-500">{{ metrics?.pending_stores || 0 }}</p>
                </div>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Verifikasi Toko Baru</h2>
                        <p class="text-sm text-gray-500 mt-1">Data diperbarui otomatis</p>
                    </div>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                        {{ metrics?.pending_stores || 0 }} pending
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase text-gray-500 font-semibold tracking-wider">
                                <th class="py-4 px-6">Nama Toko</th>
                                <th class="py-4 px-6">Kategori</th>
                                <th class="py-4 px-6">Pemilik</th>
                                <th class="py-4 px-6">Alamat</th>
                                <th class="py-4 px-6">Tanggal</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="loading" class="text-center">
                                <td colspan="7" class="py-8 text-gray-500">Memuat data...</td>
                            </tr>
                            <tr v-else-if="stores.length === 0" class="text-center">
                                <td colspan="7" class="py-8 text-gray-500">Tidak ada data toko.</td>
                            </tr>
                            <tr v-else v-for="store in stores" :key="store.id_store" class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-4 px-6 font-medium text-gray-900">{{ store.store_name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ store.store_category || '-' }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ store.owner?.name || '-' }}</td>
                                <td class="py-4 px-6 text-gray-600 text-sm max-w-xs truncate" :title="store.address">{{ store.address || store.district }}</td>
                                <td class="py-4 px-6 text-gray-600 text-sm">{{ formatDate(store.created_at) }}</td>
                                <td class="py-4 px-6">
                                    <span :class="statusClass(store.verification_status)" class="px-2.5 py-1 rounded-full text-xs font-medium border">
                                        {{ formatStatus(store.verification_status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <template v-if="store.verification_status === 'menunggu'">
                                        <button @click="handleVerify(store.id_store, 'disetujui')" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 border border-green-200 rounded-lg text-sm font-medium transition-colors">
                                            Setujui
                                        </button>
                                        <button @click="handleVerify(store.id_store, 'dibatalkan')" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 rounded-lg text-sm font-medium transition-colors">
                                            Tolak
                                        </button>
                                    </template>
                                    <button v-if="store.verification_status === 'disetujui'" @click="handleVerify(store.id_store, 'dibatalkan')" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 rounded-lg text-sm font-medium transition-colors">
                                        Cabut
                                    </button>
                                    <button @click="openModal(store)" class="inline-flex items-center px-3 py-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg text-sm font-medium transition-colors">
                                        Lihat Data
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div v-if="selectedStore" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
            
            <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full transform transition-all overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Detail Toko</h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Toko</label>
                        <p class="text-gray-900 font-medium">{{ selectedStore.store_name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                            <p class="text-gray-900">{{ selectedStore.store_category || '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Pemilik</label>
                            <p class="text-gray-900">{{ selectedStore.owner?.name || '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deskripsi</label>
                        <p class="text-gray-700 text-sm leading-relaxed">{{ selectedStore.description || 'Tidak ada deskripsi.' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jam Operasional</label>
                        <p class="text-gray-900">{{ selectedStore.operating_hours || '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kecamatan</label>
                        <p class="text-gray-900">{{ selectedStore.district || '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
                        <p class="text-gray-900">{{ selectedStore.address || '-' }}</p>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end space-x-3">
                    <button @click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { adminService } from '@/services/adminService';

const metrics = ref(null);
const stores = ref([]);
const loading = ref(true);
const selectedStore = ref(null);

const fetchDashboard = async () => {
    try {
        loading.value = true;
        const res = await adminService.getDashboard();
        if (res.success) {
            metrics.value = res.data.metrics;
            stores.value = res.data.stores;
        }
    } catch (error) {
        console.error('Failed to fetch dashboard', error);
        alert('Gagal memuat data dashboard.');
    } finally {
        loading.value = false;
    }
};

const handleVerify = async (id, status) => {
    const confirmMsg = status === 'disetujui' ? 'Setujui toko ini?' : (status === 'dibatalkan' ? 'Tolak/cabut izin toko ini?' : 'Ubah status?');
    if (!confirm(confirmMsg)) return;

    try {
        const res = await adminService.verifyStore(id, status);
        if (res.success) {
            // Refresh data
            fetchDashboard();
        }
    } catch (error) {
        console.error('Failed to verify store', error);
        alert('Gagal mengubah status toko.');
    }
};

const openModal = (store) => {
    selectedStore.value = store;
};

const closeModal = () => {
    selectedStore.value = null;
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

const formatStatus = (status) => {
    if (status === 'menunggu') return 'Pending';
    if (status === 'disetujui') return 'Disetujui';
    if (status === 'dibatalkan') return 'Ditolak';
    return status;
};

const statusClass = (status) => {
    if (status === 'menunggu') return 'bg-yellow-100 text-yellow-800 border-yellow-200';
    if (status === 'disetujui') return 'bg-green-100 text-green-800 border-green-200';
    if (status === 'dibatalkan') return 'bg-red-100 text-red-800 border-red-200';
    return 'bg-gray-100 text-gray-800 border-gray-200';
};

onMounted(() => {
    fetchDashboard();
});
</script>
