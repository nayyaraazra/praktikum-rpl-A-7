import apiClient from './index'

export const authApi = {
    register(payload) {
        return apiClient.post('/auth/register', payload)
    },

    login(payload) {
        return apiClient.post('/auth/login', payload)
    },

    loginWithGoogle(payload) {
        return apiClient.post('/auth/google', payload)
    },

    logout() {
        return apiClient.post('/auth/logout')
    },

    me() {
        return apiClient.get('/auth/me')
    },
}
