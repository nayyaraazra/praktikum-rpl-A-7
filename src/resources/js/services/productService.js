import apiClient from './api'

export const productService = {
    getProducts(params = {}) {
        return apiClient.get('/products', { params }).then(res => res.data)
    },
    getProduct(id) {
        return apiClient.get(`/products/${id}`).then(res => res.data)
    }
}
