import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/booking.js',
                // Bundle riêng cho tính năng Stream & Device Management
                'resources/js/stream/main.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        // Plugin Vue 3 — xử lý Single File Components (.vue)
        vue(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
