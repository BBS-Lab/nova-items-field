import vue from 'eslint-plugin-vue'

export default [
    ...vue.configs['flat/essential'],
    {
        files: ['**/*.{js,vue}'],
        languageOptions: {
            ecmaVersion: 2023,
            sourceType: 'module',
            globals: {
                window: 'readonly',
                document: 'readonly',
                console: 'readonly',
            },
        },
        rules: {
            'vue/multi-word-component-names': 'off',
        },
    },
    {
        ignores: ['dist/**', 'vendor/**', 'node_modules/**'],
    },
]
