/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './student/**/*.php',
    './teacher/**/*.php',
    './admin/**/*.php',
    './index.php',
  ],

  theme: {
    extend: {
      colors: {
        primary: '#334155', // slate gray
        secondary: '#64748b', // soft blue
        accent: '#6366f1', // muted indigo
        background: '#f1f5f9', // light gray
        text: '#1e293b', // dark gray
      },
    },
  },
  plugins: [],
}