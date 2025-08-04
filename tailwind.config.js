/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/**/*.php",
    // Filament vendor paths for proper styling
    "./vendor/filament/**/*.blade.php",
    "./app/Filament/**/*.php",
    "./app/Livewire/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        // I colori primari e secondari sono definiti in resources/css/app.css
        // usando la direttiva @theme di Tailwind CSS v4.
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
      },
    },
  },
  plugins: [
    // Tailwind CSS plugins for enhanced functionality
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}