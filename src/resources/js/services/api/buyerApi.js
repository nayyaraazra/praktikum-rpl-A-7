import apiClient from './index'

export const buyerApi = {
    getProducts(params = {}) {
        return apiClient.get('/products', { params })
    },
    getCategories() {
        return apiClient.get('/categories')
    },
}
