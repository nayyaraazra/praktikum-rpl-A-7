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
}
