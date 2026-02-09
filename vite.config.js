import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { svelte } from '@sveltejs/vite-plugin-svelte';
import { compression, defineAlgorithm } from 'vite-plugin-compression2';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        svelte(),
        compression({
            algorithms: [
                defineAlgorithm('gz', { level: 9 }),
                defineAlgorithm('br', { level: 11 }),
            ],
        }),
    ],
    build: {
        minify: 'terser',
        reportCompressedSize: false,
    },
    server: {
        host: '127.0.0.1',
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
    },
});
