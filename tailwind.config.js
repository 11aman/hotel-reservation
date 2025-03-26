import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/Http/Livewire/**/*.php",
    ],
    theme: {
        extend: {
            backgroundColor: {
                'gray-400': '#9CA3AF',
                'gray-500': '#6B7280',
            },
        },
    },
    variants: {
        extend: {},
    },
    plugins: [],
};
