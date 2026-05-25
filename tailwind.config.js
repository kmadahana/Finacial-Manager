import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Classes used inside JS template literals that Tailwind can't scan
    safelist: [
        'bg-slate-100', 'dark:bg-slate-800',
        'text-slate-900', 'dark:text-white',
        'text-slate-500', 'dark:text-slate-400',
        'text-slate-700', 'dark:text-slate-300',
        'bg-slate-200', 'dark:bg-slate-700',
        'divide-slate-100', 'dark:divide-slate-800',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
