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
            colors: {
                // Definimos el color oficial del GOREPA
                'gorepa': {
                    500: '#57C1C7', // Color base
                    600: '#469da2', // Tono para hovers
                    700: '#3a8286', // Tono para bordes y sombras
                },
            },
        },
    },

    plugins: [forms],
};