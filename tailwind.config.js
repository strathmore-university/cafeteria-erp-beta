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
                primary: 'Jost',
                secondary: 'Jost',
                inter: 'Inter',
            },

            colors: {
                primary: '#00A8A8',
                secondary: '#F06543',
            },

            borderWidth: {
                1.5: '1.5px',
            },
        },
    },
};
