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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            /*
             * Satu warna aksen: sepia / coklat khas perpustakaan.
             * Dasar halaman tetap putih dengan netral hangat.
             */
            colors: {
                sepia: {
                    50: '#faf7f2',
                    100: '#f3ede4',
                    200: '#e7dccd',
                    300: '#d6c3a9',
                    400: '#c0a382',
                    500: '#a88461',
                    600: '#8b5e34',
                    700: '#744d2a',
                    800: '#5c3d22',
                    900: '#432c19',
                    950: '#2a1b10',
                },
                cream: {
                    50: '#faf9f7',
                    100: '#f7f4ef',
                    200: '#f0eae1',
                    300: '#e8e1d7',
                    400: '#ddd5c9',
                },
            },
        },
    },

    plugins: [forms],
};
