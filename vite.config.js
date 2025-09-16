import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
        port: 5173,
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/trip-filters.js',
                'resources/js/trip-details-modal.js'
            ],
            refresh: true,
            hotFile: '/var/www/html/public/hot',
        }),
    ],
});
