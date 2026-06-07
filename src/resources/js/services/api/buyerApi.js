import apiClient from './index'

export const buyerApi = {
    getProducts(params = {}) {
        return apiClient.get('/products', { params })
    },
    getProduct(id) {
        return apiClient.get(`/products/${id}`)
    },
    getCategories() {
        return apiClient.get('/categories')
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
}
