<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'
import Highbar from './components/Highbar.vue'

// On récupère la route actuelle
const route = useRoute()

// On crée une variable VRAIE si on DOIT afficher la navigation
const showNavigation = computed(() => {
  // Si le chemin de l'URL commence par /connexion OU qu'on est sur l'accueil (/)
  // Alors on masque la navigation !
  if (route.path === '/' || route.path.startsWith('/connexion')) {
    return false
  }
  // Sinon, on l'affiche partout ailleurs
  return true
})
</script>

<template>
  <div class="app-layout">
    <Sidebar v-if="showNavigation" />
    <Highbar v-if="showNavigation" />
    
    <main :class="['content', { 'full-screen': !showNavigation }]">
      <RouterView /> 
    </main>
  </div>
</template>

<style>
 @import './App.css';
</style>