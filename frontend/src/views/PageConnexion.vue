<script setup>
import { useRouter, RouterLink } from 'vue-router'

const router = useRouter()

// Fonction pour rediriger vers la page de login spécifique
const choisirSession = (role) => {
  // On envoie le rôle (etudiant, enseignant ou admin) dans l'URL
  router.push({ name: 'connexion-session', params: { role: role } })
}
</script>

<template>
  <div class="login-layout">
    <div class="login-container">
      <h1 class="main-title">Connexion</h1>
      
      <div class="subtitle-wrapper">
        <hr class="line">
        <h2 class="subtitle">Choisissez la session de connexion</h2>
        <hr class="line">
      </div>

      <div class="buttons-group">
        <button class="role-btn" @click="choisirSession('enseignant')">Enseignant</button>
        <button class="role-btn" @click="choisirSession('etudiant')">Étudiant</button>
        <button class="role-btn" @click="choisirSession('admin')">Admin</button>
      </div>

      <div class="back-link-container">
        <RouterLink to="/public-home" class="back-link">Retour au catalogue public</RouterLink>
      </div>

    </div>
  </div>
</template>

<style scoped>
@import '../css/PageConnexion.css';
</style>