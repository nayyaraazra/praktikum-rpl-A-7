import apiClient from './index'

export const sellerApi = {
    // Dashboard
    getDashboard() {
        return apiClient.get('/seller/dashboard')
    },
    getOrderDetail(id) {
        return apiClient.get(`/seller/orders/${id}`)
    },
    updateOrderStatus(id, status) {
        return apiClient.put(`/seller/orders/${id}/status`, { status })
    },

    // Produk
    getProducts() {
        return apiClient.get('/seller/products')
    },
    createProduct(payload) {
        return apiClient.post('/seller/products', payload)
    },
    updateProduct(id, payload) {
        return payload instanceof FormData
            ? apiClient.post(`/seller/products/${id}`, payload)
            : apiClient.put(`/seller/products/${id}`, payload)
    },
    deleteProduct(id) {
        return apiClient.delete(`/seller/products/${id}`)
    },

    // Profil toko
    getStore() {
        return apiClient.get('/seller/store')
    },
    updateStore(payload) {
        const isFormData = payload instanceof FormData
        return isFormData
            ? apiClient.post('/seller/store', payload)
            : apiClient.put('/seller/store', payload)
    },
}