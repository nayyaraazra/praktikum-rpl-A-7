import axios from 'axios'

const apiClient = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true,
})

// Sisipkan Bearer token Sanctum ke setiap request
apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    // Biarkan axios mengatur Content-Type (FormData → multipart, JSON → application/json)
    return config
})

// Tangani 401 global — token kadaluarsa atau tidak valid
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token')
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

export default apiClient
