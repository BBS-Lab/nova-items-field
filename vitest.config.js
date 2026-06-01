import vue from '@vitejs/plugin-vue'
import { defineConfig } from 'vitest/config'

export default defineConfig({
    plugins: [vue()],

    test: {
        environment: 'happy-dom',
        globals: true,
        setupFiles: ['resources/js/__tests__/setup.js'],
        include: ['resources/js/**/*.spec.js'],
        coverage: {
            provider: 'v8',
            include: ['resources/js/**/*.{js,vue}'],
            exclude: ['resources/js/**/*.spec.js', 'resources/js/__tests__/**'],
            reporter: ['text', 'html'],
            thresholds: {
                lines: 100,
                functions: 100,
                statements: 100,
                branches: 100,
            },
        },
    },
})
