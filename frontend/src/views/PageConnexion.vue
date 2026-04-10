<script setup>
import { useRouter, RouterLink } from 'vue-router'
import { useAccessibiliteStore } from '@/stores/accessibilite' // Import du store accessibilité

const router = useRouter()
const accessStore = useAccessibiliteStore()

// --- ACCESSIBILITÉ VOCALE ---
const lireAction = (texte) => {
  accessStore.lireTexte(texte);
}

// Fonction pour rediriger vers la page de login spécifique
const choisirSession = (role) => {
  // Petite annonce vocale avant de changer de page
  lireAction(`Ouverture de la session pour ${role}`);
  
  // On envoie le rôle (etudiant, enseignant ou admin) dans l'URL
  router.push({ name: 'connexion-session', params: { role: role } })
}
</script>

<template>
  <div class="login-layout">
    <div class="login-container">
      <h1 class="main-title" tabindex="0" @focus="lireAction('Page de connexion principale')">Connexion</h1>
      
      <div class="subtitle-wrapper">
        <hr class="line">
        <h2 class="subtitle" tabindex="0" @focus="lireAction('Choisissez votre session de connexion')">Choisissez la session de connexion</h2>
        <hr class="line">
      </div>

      <div class="buttons-group">
        <button 
          class="role-btn" 
          @click="choisirSession('enseignant')"
          @mouseenter="lireAction('Bouton : Connexion pour les enseignants')"
          @focus="lireAction('Connexion Enseignant')"
        >
          Enseignant
        </button>
        
        <button 
          class="role-btn" 
          @click="choisirSession('etudiant')"
          @mouseenter="lireAction('Bouton : Connexion pour les étudiants')"
          @focus="lireAction('Connexion Étudiant')"
        >
          Étudiant
        </button>
        
        <button 
          class="role-btn" 
          @click="choisirSession('admin')"
          @mouseenter="lireAction('Bouton : Connexion pour les administrateurs')"
          @focus="lireAction('Connexion Administrateur')"
        >
          Admin
        </button>
      </div>

      <div class="back-link-container">
        <RouterLink 
          to="/public-home" 
          class="back-link"
          @mouseenter="lireAction('Retourner au catalogue public')"
          @focus="lireAction('Lien : Retour au catalogue public')"
        >
          Retour au catalogue public
        </RouterLink>
      </div>

    </div>
  </div>
</template>

<style scoped>
@import '../css/PageConnexion.css';
</style>