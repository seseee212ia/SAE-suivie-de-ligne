import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(localStorage.getItem('theme') === 'dark')

  // Met à jour la classe sur la balise <html>
  function updateDom(sombre) {
    if (sombre) {
      document.documentElement.classList.add('dark-theme')
      localStorage.setItem('theme', 'dark')
    } else {
      document.documentElement.classList.remove('dark-theme')
      localStorage.setItem('theme', 'light')
    }
  }

  // Initialisation immédiate au chargement du script
  updateDom(isDark.value)

  function toggleTheme() {
    isDark.value = !isDark.value
    updateDom(isDark.value) // Déclenchement explicite
  }

  return { isDark, toggleTheme }
})