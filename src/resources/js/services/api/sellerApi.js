import apiClient from './index'

export const sellerApi = {
    // Dashboard
    getDashboard() {
        return apiClient.get('/seller/dashboard')
    },
    getOrderDetail(id) {
        return apiClient.get(`/seller/orders/${id}`)
    },

    // Produk
    getProducts() {
        return apiClient.get('/seller/products')
    },
    createProduct(payload) {
        return apiClient.post('/seller/products', payload)
    },
    updateProduct(id, payload) {
        return apiClient.put(`/seller/products/${id}`, payload)
    },
    deleteProduct(id) {
        return apiClient.delete(`/seller/products/${id}`)
    },
}