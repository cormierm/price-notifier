import {defineConfig} from 'vite';
import vue from '@vitejs/plugin-vue2';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel([
            'resources/js/app.js',
            'resources/sass/app.scss',
            'resources/js/vue.js',
        ]),
        vue(),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            vue: 'vue/dist/vue.esm.js'
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import 'resources/sass/_variables.scss';`,
            },
        },
    },
});
