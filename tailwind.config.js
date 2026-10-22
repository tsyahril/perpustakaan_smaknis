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
                jakarta: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Warna utama tampilan anggota; 950 sama dengan navy panel admin.
                brand: {
                    50: '#eef6ff',
                    100: '#d9eaff',
                    200: '#bcd9ff',
                    300: '#8ec0ff',
                    400: '#589dff',
                    500: '#2f7ef5',
                    600: '#1a63e0',
                    700: '#154fb8',
                    800: '#16418f',
                    900: '#163a73',
                    950: '#0b1d32',
                },
            },
            boxShadow: {
                kartu: '0 1px 2px rgba(11, 29, 50, 0.05), 0 6px 18px -8px rgba(11, 29, 50, 0.12)',
                'kartu-naik': '0 2px 4px rgba(11, 29, 50, 0.06), 0 14px 28px -10px rgba(11, 29, 50, 0.22)',
            },
        },
    },

    plugins: [forms],
};
