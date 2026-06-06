import apiClient from './api'

export const sellerService = {
    getDashboard() {
        return apiClient.get('/seller/dashboard').then(res => res.data)
    },
    getStore() {
        return apiClient.get('/seller/store').then(res => res.data)
    },
    addProduct(formData) {
        return apiClient.post('/seller/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => res.data)
    },
    updateOrderStatus(orderId, status) {
        return apiClient.post(`/seller/orders/${orderId}/status`, { status }).then(res => res.data)
    },
    getCategories() {
        return apiClient.get('/seller/categories').then(res => res.data)
    },
    updateProfile(formData) {
        return apiClient.post('/seller/profile', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        }).then(res => res.data)
    }
}
