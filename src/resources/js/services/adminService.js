import api from './api';

export const adminService = {
    async getDashboard() {
        const response = await api.get('/admin/dashboard');
        return response.data;
    },

    async verifyStore(id_store, status) {
        const response = await api.post(`/admin/stores/${id_store}/verify`, { status });
        return response.data;
    }
};
