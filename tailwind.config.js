import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                display: ['Newsreader', 'serif'],
                sans: ['Noto Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#137fec',
                'background-light': '#f6f7f8',
                'background-dark': '#101922',
                'surface-light': '#ffffff',
                'surface-dark': '#18232e',
            },
        },
    },

    plugins: [forms],
};
