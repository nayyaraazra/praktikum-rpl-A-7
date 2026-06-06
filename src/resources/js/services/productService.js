import api from './api';

export const productService = {
    /**
     * Get a paginated list of products with filters.
     * @param {Object} params - search, category, district, min_price, max_price, sort, page
     */
    async getProducts(params = {}) {
        const response = await api.get('/products', { params });
        return response.data;
    },

    /**
     * Get details of a single product.
     * @param {number|string} id 
     */
    async getProductDetail(id) {
        const response = await api.get(`/products/${id}`);
        return response.data;
    }
};
