<script setup>
import { RouterLink } from 'vue-router'

// Importation du logo (identique aux autres vues sombres)
import logoMmi from '@/assets/logo-mmi.svg'
import bgImage from '@/assets/background-landing.jpg' // L'image de fond à fournir dans assets
</script>

<template>
  <div class="landing-container" :style="{ backgroundImage: `url(${bgImage})` }">
    
    <header class="landing-header">
      <img :src="logoMmi" alt="Logo MMI" class="logo-mmi" />
    </header>

    <main class="glass-panel">
      
      <div class="circles-container">
        <div class="circle circle-1-purple"></div>
        <div class="circle circle-2-yellow"></div>
        <div class="circle circle-3-green"></div>
        <div class="circle circle-4-blue"></div>
      </div>

      <div class="text-content">
        <h1>Bienvenue sur la plateforme de gestion des projets du BUT Métiers du Multimédia et de l'Internet.</h1>
        <p>Découvrez des projets concrets qui illustrent les compétences développées en Métiers du Multimédia et de l'Internet.</p>
        
        <RouterLink to="/public-home" class="cta-button">
          Accéder aux SAE
        </RouterLink>
      </div>
    </main>
  </div>
</template>

<style scoped>
@import '../css/LandingPage.css';
</style>