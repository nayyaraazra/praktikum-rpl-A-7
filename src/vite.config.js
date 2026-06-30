import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                'resources/views/**',
                'resources/js/**',
                'resources/css/**',
                'routes/**',
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: [
                '**/kulaan_db',
                '**/storage/**',
                '**/database/**',
            ],
        },
    },
    resolve: {
        alias: {
            // Alias @ → resources/js
            // Gunakan import '@/stores/auth' alih-alih '../../../stores/auth'
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
})

