import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
  ],

  server: {
    // Autoriser les connexions depuis le domaine ngrok
    allowedHosts: [
      'leeanne-unbet-southerly.ngrok-free.dev'
    ],
    // Configurer le proxy pour rediriger les requêtes API vers le backend
    proxy: {
      '/api': {
        target: 'https://haby.coulibaly.mmi-velizy.fr/403/public',
        changeOrigin: true,
        secure: false // Désactive la vérification SSL pour les connexions HTTPS
      }
    }
  },

  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
})