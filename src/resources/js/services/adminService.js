import api from './api';

export const adminService = {
    /**
     * Get admin dashboard data (metrics and stores)
     */
    async getDashboard() {
        const response = await api.get('/admin/dashboard');
        return response.data;
    },

    /**
     * Verify a store (approve or reject)
     * @param {number} id_store 
     * @param {string} status - 'disetujui' | 'dibatalkan'
     */
    async verifyStore(id_store, status) {
        const response = await api.post(`/admin/stores/${id_store}/verify`, { status });
        return response.data;
    }
};
