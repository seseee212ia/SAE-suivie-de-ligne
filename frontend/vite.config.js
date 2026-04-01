import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  // Ajoute ces 3 lignes :
  server: {
    allowedHosts: true
  }
})
