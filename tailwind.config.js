/** @type {import('tailwindcss').Config} */
import { officeSupplyTheme } from './resources/js/theme/office-supply';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.ts',
    ],
    theme: {
        extend: {
            colors: {
                primary: officeSupplyTheme.colors.primary.DEFAULT,
                'primary-hover': officeSupplyTheme.colors.primary.hover,
                'primary-light': officeSupplyTheme.colors.primary.light,
                secondary: officeSupplyTheme.colors.secondary.DEFAULT,
                'secondary-hover': officeSupplyTheme.colors.secondary.hover,
                'secondary-light': officeSupplyTheme.colors.secondary.light,
                background: {
                    DEFAULT: officeSupplyTheme.colors.background.DEFAULT,
                    secondary: officeSupplyTheme.colors.background.secondary,
                    tertiary: officeSupplyTheme.colors.background.tertiary,
                },
                text: {
                    DEFAULT: officeSupplyTheme.colors.text.DEFAULT,
                    secondary: officeSupplyTheme.colors.text.secondary,
                    light: officeSupplyTheme.colors.text.light,
                },
                border: {
                    DEFAULT: officeSupplyTheme.colors.border.DEFAULT,
                    hover: officeSupplyTheme.colors.border.hover,
                },
                status: {
                    success: officeSupplyTheme.colors.status.success,
                    warning: officeSupplyTheme.colors.status.warning,
                    error: officeSupplyTheme.colors.status.error,
                    info: officeSupplyTheme.colors.status.info,
                },
            },
            // Menggunakan properti lain dari tema jika diperlukan
            // Contoh: sidebar, header, card, button, table
            sidebar: officeSupplyTheme.sidebar,
            header: officeSupplyTheme.header,
            card: officeSupplyTheme.card,
            button: officeSupplyTheme.button,
            table: officeSupplyTheme.table,
        },
    },
    plugins: [],
}; 