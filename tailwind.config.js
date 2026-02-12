import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                operra: {
                    50: '#f2fafa',
                    100: '#e5f5f5',
                    200: '#cceded',
                    300: '#b3e5e4',
                    400: '#8cd5d2',
                    500: '#4db9af', // Logo Primary Teal
                    600: '#45a79e',
                    700: '#3a8b83',
                    800: '#2e6f69',
                    900: '#265c57',
                    950: '#1a3e3b',
                },
            },
        },
    },

    plugins: [forms],
};
