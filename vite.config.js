import vue from '@vitejs/plugin-vue'
import { resolve } from 'node:path'
import { defineConfig } from 'vite'

export default defineConfig({
    plugins: [vue()],

    build: {
        outDir: 'dist',
        emptyOutDir: true,
        target: 'es2022',
        minify: true,
        lib: {
            entry: resolve(import.meta.dirname, 'resources/js/field.js'),
            name: 'NovaItemsField',
            formats: ['umd'],
            fileName: () => 'js/field.js',
            cssFileName: 'css/field',
        },
        rollupOptions: {
            // Provided by Nova at runtime — never bundle them.
            external: ['vue', 'laravel-nova'],
            output: {
                globals: {
                    vue: 'Vue',
                    'laravel-nova': 'LaravelNova',
                },
            },
        },
    },
})
