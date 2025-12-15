const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    // --- SAFELIST START ---
    // This forces Tailwind to generate these colors even if they are dynamic variables
    safelist: [
        {
            pattern: /(bg|text|border|shadow)-(cyan|red|emerald|pink|lime|violet|sky|yellow|orange|fuchsia|teal)-(100|300|400|500|900)/,
            variants: ['hover', 'focus'], 
        },
        {
            // For opacity modifiers (e.g., bg-red-500/20 used in the glass cards)
            pattern: /(bg|text|border)-(cyan|red|emerald|pink|lime|violet|sky|yellow|orange|fuchsia|teal)-(500|900)\/(10|20|30|50|80|90)/,
        }
    ],
    // --- SAFELIST END ---

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};