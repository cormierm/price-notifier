import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/vue.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@Components': path.resolve(__dirname, 'resources/vue/Components'),
            '@js': path.resolve(__dirname, 'resources/js')
        },
    }
});
