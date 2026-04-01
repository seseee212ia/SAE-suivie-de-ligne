<template>
  <div class="sidebar-container">
    <div class="sidebar-top-banner">
      <img src="../assets/logo-mmi (3).svg" alt="Logo MMI" class="logo-image" />
    </div>
    
    <nav class="side-nav">
      <router-link to="/etudiant/dashboard" class="nav-item" exact-active-class="active" @click="lireTexte('Navigation vers le tableau de bord')">
        <img src="../assets/icon_home.svg" alt="" class="nav-icon" aria-hidden="true" /> 
        Tableau de bord
      </router-link>
      
      <router-link to="/etudiant/sae" class="nav-item" active-class="active" @click="lireTexte('Navigation vers mes S A E')">
        <img src="../assets/icon_sae.svg" alt="" class="nav-icon" aria-hidden="true" /> 
        Mes SAE
      </router-link>

      <router-link to="/etudiant/documents" class="nav-item" active-class="active" @click="lireTexte('Navigation vers les documents')">
        <img src="../assets/icon_document (2).svg" alt="" class="nav-icon" aria-hidden="true" /> 
        Documents
      </router-link>
    </nav>
  </div>
</template>

<script setup>
import { inject } from 'vue';

const lireTexte = inject('lireTexte', (texte) => {
  if (window.speechSynthesis && localStorage.getItem('voix_active') === 'true') {
    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(texte);
    utterance.lang = 'fr-FR';
    window.speechSynthesis.speak(utterance);
  }
});
</script>

<style scoped>
.sidebar-container {
  width: 250px;
  background-color: #B4D4FE; 
  display: flex;
  flex-direction: column;
  border-right: 1px solid #e5e7eb;
  height: 100vh;
  position: sticky;
  top: 0;
  left: 0;
  z-index: 50;
}

.sidebar-top-banner {
  display: flex;
  align-items: center;
  justify-content: center; 
  padding: 1rem; 
  height: 100px; 
  box-sizing: border-box;
}

.logo-image {
  max-width: 100%;
  height: auto;
  max-height: 80px; 
  display: block;
}

.side-nav {
  display: flex;
  flex-direction: column;
  margin-top: 1rem;
  flex: 1; 
  padding-bottom: 2rem;
}

.nav-item {
  padding: 1rem 1.5rem;
  text-decoration: none;
  color: #4b5563;
  font-weight: 500;
  display: flex; 
  align-items: center;
  gap: 12px; 
  transition: all 0.2s;
}

.nav-icon {
  width: 20px;
  height: 20px;
  transition: filter 0.2s;
}

.nav-item.active {
  background-color: #60a5fa;
  color: white;
}

.nav-item.active .nav-icon {
  filter: brightness(0) invert(1);
}

.nav-item:hover:not(.active) {
  background-color: rgba(96, 165, 250, 0.1);
}

/* --- MODE SOMBRE --- */
:global(body.theme-sombre .sidebar-container) { background-color: #111827 !important; border-right-color: #374151 !important; }
:global(body.theme-sombre .sidebar-top-banner) { background-color: #111827 !important; }
:global(body.theme-sombre .nav-item) { color: #d1d5db !important; }
:global(body.theme-sombre .nav-item.active) { background-color: #374151 !important; color: #60a5fa !important; }
:global(body.theme-sombre .nav-item:not(.active) .nav-icon) { filter: brightness(0) invert(1) opacity(0.8) !important; }
:global(body.theme-sombre .nav-item.active .nav-icon) { filter: brightness(0) saturate(100%) invert(58%) sepia(80%) saturate(2853%) hue-rotate(193deg) brightness(101%) contrast(98%) !important; }
</style>