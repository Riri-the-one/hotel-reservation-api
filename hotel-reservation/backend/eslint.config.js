import js from '@eslint/js';
import react from 'eslint-plugin-react';
import hooks from 'eslint-plugin-react-hooks';
import globals from 'globals';

export default [
    { ignores: ['node_modules/**', 'vendor/**', 'public/**'] },
    js.configs.recommended,
    {
        files: ['resources/js/**/*.{js,jsx}'],
        languageOptions: { globals: { ...globals.browser, route: 'readonly' }, parserOptions: { ecmaFeatures: { jsx: true } } },
        plugins: { react, 'react-hooks': hooks },
        settings: { react: { version: 'detect' } },
        rules: {
            'react/jsx-uses-react': 'error', 'react/jsx-uses-vars': 'error',
            'react-hooks/rules-of-hooks': 'error',
            'no-unused-vars': ['error', { argsIgnorePattern: '^_', varsIgnorePattern: '^_' }],
        },
    },
];
