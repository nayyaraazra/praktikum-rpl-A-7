import api from './api';

export const orderService = {
    /**
     * Create a new order (buyer).
     * @param {Object} payload - id_product, quantity, payment_method, shipping_address, note
     */
    async createOrder(payload) {
        const response = await api.post('/orders', payload);
        return response.data;
    },

    /**
     * Get orders for the authenticated seller's store.
     * @param {Object} params - status
     */
    async getSellerOrders(params = {}) {
        const response = await api.get('/seller/orders', { params });
        return response.data;
    },

    /**
     * Update status of a seller's order.
     * @param {number|string} id 
     * @param {string} status - 'diproses' | 'selesai' | 'dibatalkan'
     */
    async updateOrderStatus(id, status) {
        const response = await api.patch(`/seller/orders/${id}/status`, { status });
        return response.data;
    }
};
