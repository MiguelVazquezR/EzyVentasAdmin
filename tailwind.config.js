import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#F68C0F',
                primarylight: '#FCDCB5',
                secondary: '#25346D',
                secondarylight: '#25346D',
                redDanger: '#F80505',
                greenSuccess: '#278E16',
                black1: '#1A1A1A',
                gray9A: '#9A9A9A',
                gray66: '#666666',
                grayD9: '#D9D9D9',
                gray99: '#999999',
                grayF2: '#F2F2F2',
                gray77: '#777777',
                gray37: '#373737',
            },
        },
    },

    plugins: [forms, typography],
};
