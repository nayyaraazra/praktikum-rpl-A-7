import apiClient from './index'

export const buyerApi = {
    getProducts(params = {}) {
        return apiClient.get('/products', { params })
    },
    getPopularProducts() {
        return apiClient.get('/products/popular')
    },
    getProduct(id) {
        return apiClient.get(`/products/${id}`)
    },
    getCategories() {
        return apiClient.get('/categories')
    },
    getStores(params = {}) {
        return apiClient.get('/stores', { params })
    },
    getStore(id) {
        return apiClient.get(`/stores/${id}`)
    },
    createOrder(payload) {
        return apiClient.post('/orders', payload)
    },
    getOrders() {
        return apiClient.get('/orders')
    },
    getOrder(id) {
        return apiClient.get(`/orders/${id}`)
    },
    getNotifications() {
        return apiClient.get('/notifications')
    },
    markAllNotificationsAsRead() {
        return apiClient.post('/notifications/mark-read')
    },
    markNotificationAsRead(id) {
        return apiClient.post(`/notifications/${id}/read`)
    },
    updateProfile(payload) {
        return apiClient.put('/auth/profile', payload)
    },
}
