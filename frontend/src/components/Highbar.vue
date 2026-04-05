<script setup>
import { ref, computed } from 'vue'
import { useUserStore } from '@/stores/user'
import { useThemeStore } from '@/stores/theme'
import { useAccessibiliteStore } from '@/stores/accessibilite' 
import { useUiStore } from '@/stores/ui'
import { useRouter } from 'vue-router'

import iconAccessibilite from '@/assets/icon_accessibilites.svg'
import iconMode from '@/assets/icon_dark_mode.svg'
import iconParametres from '@/assets/icon_parametres.svg'
import iconAnnonces from '@/assets/icon_annonces.svg'

const userStore = useUserStore()
const themeStore = useThemeStore()
const accessStore = useAccessibiliteStore() 
const uiStore = useUiStore()
const router = useRouter()

const showAccMenu = ref(false)

const allerVersConnexion = () => {
  accessStore.lireTexte('Aller à la page de connexion');
  router.push('/connexion')
}

// NOUVELLES FONCTIONNALITÉS ADMIN & UTILISATEURS CONNECTÉS
const showParamMenu = ref(false)
const showNotifMenu = ref(false)
const showProfilMenu = ref(false)
const afficherMotDePassePopup = ref(false)

const fermerTout = () => {
  showAccMenu.value = false;
  showParamMenu.value = false;
  showNotifMenu.value = false;
  showProfilMenu.value = false;
}

const ouvrirModifMotDePasse = () => {
  fermerTout()
  afficherMotDePassePopup.value = true
  accessStore.lireTexte('Ouverture de la fenêtre de modification du mot de passe');
}

const validerNouveauMotDePasse = () => {
  alert("Mot de passe mis à jour avec succès !")
  afficherMotDePassePopup.value = false
  accessStore.lireTexte('Mot de passe mis à jour avec succès');
}

const allerGererDroits = () => {
  fermerTout()
  accessStore.lireTexte('Aller à la page de gestion des droits');
  router.push('/admin/sae/202')
}

const deconnexion = () => {
  fermerTout()
  userStore.setRole('public')
  accessStore.lireTexte('Déconnexion réussie. Retour à la page de connexion.');
  router.push('/connexion')
}

const getAvatarSrc = computed(() => {
  const role = userStore.role && userStore.role !== 'public' ? userStore.role : 'default'
  return new URL(`../assets/profil_${role}.svg`, import.meta.url).href
})

// Fonctions relais pour l'accessibilité
const triggerVoix = () => {
  accessStore.toggleVoix();
}

const triggerDaltonien = () => {
  accessStore.toggleDaltonien();
  if(accessStore.voix) {
    accessStore.lireTexte(accessStore.daltonien ? 'Filtre daltonien activé' : 'Filtre daltonien désactivé');
  }
}

const changeTaillePolice = (taille, texte) => {
  accessStore.setPolice(taille);
  accessStore.lireTexte('Taille de la police changée sur ' + texte);
}
</script>

<template>
  <header class="highbar">
    
    <button 
      class="hamburger-btn" 
      @click="uiStore.toggleSidebar()" 
      aria-label="Ouvrir le menu"
    >
      <svg viewBox="0 0 24 24" width="28" height="28" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
    </button>

    <div class="search-container">
      <input 
        type="text" 
        placeholder="Rechercher..." 
        class="search-input" 
        @focus="accessStore.lireTexte('Barre de recherche. Tapez ici pour rechercher.')" 
      />
    </div>

    <div class="user-actions">
      
      <div class="icons-container">
        
        <div class="relative-wrapper">
          <img 
            :src="iconAccessibilite" 
            alt="Icon Acces" 
            class="action-icon" 
            @click="fermerTout(); showAccMenu = !showAccMenu; accessStore.lireTexte(showAccMenu ? 'Menu accessibilité ouvert' : 'Menu accessibilité fermé');" 
            @focus="accessStore.lireTexte('Bouton du menu Accessibilité')"
            tabindex="0"
            @keydown.enter="fermerTout(); showAccMenu = !showAccMenu"
          />
          
          <div v-if="showAccMenu" class="dropdown-menu">
            <div class="menu-header">
              <h4 tabindex="0" @focus="accessStore.lireTexte('Menu Accessibilité')">Accessibilité</h4>
              <button class="close-btn" @click="showAccMenu = false; accessStore.lireTexte('Fermeture du menu accessibilité');" aria-label="Fermer le menu">✕</button>
            </div>
            
           <div class="acc-option">
              <span tabindex="0" @focus="accessStore.lireTexte('Option : Voix descriptive')">Voix descriptive</span>
              <label class="switch" aria-label="Activer ou désactiver la voix descriptive">
                <input type="checkbox" :checked="accessStore.voix" @change="triggerVoix">
                <span class="slider round"></span>
              </label>
            </div>
            
            <div class="acc-option">
              <span tabindex="0" @focus="accessStore.lireTexte('Option : Filtre Daltonien')">Filtre Daltonien</span>
              <label class="switch" aria-label="Activer ou désactiver le filtre daltonien">
                <input type="checkbox" :checked="accessStore.daltonien" @change="triggerDaltonien">
                <span class="slider round"></span>
              </label>
            </div>
            
            <div class="acc-option font-sizing">
              <span tabindex="0" @focus="accessStore.lireTexte('Option : Taille de la police')">Taille police</span>
              <div class="font-size-controls">
                <button 
                  @click="changeTaillePolice(1, 'Normale')" 
                  :class="{ active: accessStore.fontScale === 1 }"
                  aria-label="Taille de police normale"
                  @focus="accessStore.lireTexte('Taille de police normale')"
                >A</button>
                <button 
                  @click="changeTaillePolice(1.2, 'Moyenne')" 
                  :class="{ active: accessStore.fontScale === 1.2 }"
                  aria-label="Taille de police moyenne"
                  @focus="accessStore.lireTexte('Taille de police moyenne')"
                >A+</button>
                <button 
                  @click="changeTaillePolice(1.5, 'Grande')" 
                  :class="{ active: accessStore.fontScale === 1.5 }"
                  aria-label="Grande taille de police"
                  @focus="accessStore.lireTexte('Grande taille de police')"
                >A++</button>
              </div>
            </div>
          </div>
        </div>

        <img 
          :src="iconMode" 
          alt="Icon Mode" 
          class="action-icon" 
          @click="themeStore.toggleTheme(); accessStore.lireTexte(themeStore.isDark ? 'Mode sombre activé' : 'Mode clair activé');" 
          @focus="accessStore.lireTexte('Bouton pour changer le thème')"
          tabindex="0"
          @keydown.enter="themeStore.toggleTheme()"
        />

        <div v-if="userStore.role !== 'public'" class="relative-wrapper">
          <img :src="iconParametres" alt="Icon Parametre" class="action-icon" @click="fermerTout(); showParamMenu = !showParamMenu; accessStore.lireTexte('Ouverture des paramètres');" tabindex="0" @focus="accessStore.lireTexte('Bouton Paramètres')" />
          <div v-if="showParamMenu" class="dropdown-menu">
            <div class="menu-header">
              <h4>Paramètres</h4>
              <button class="close-btn" @click="showParamMenu = false">✕</button>
            </div>
            <div class="acc-option clickable-text" @click="ouvrirModifMotDePasse" tabindex="0" @focus="accessStore.lireTexte('Modifier le mot de passe')">Modifier le mot de passe</div>
          </div>
        </div>

      </div>

      <div v-if="userStore.role !== 'public'" class="icons-container">
        <div class="relative-wrapper">
          <img :src="iconAnnonces" alt="Icon Annonce" class="action-icon" @click="fermerTout(); showNotifMenu = !showNotifMenu; accessStore.lireTexte('Ouverture des annonces');" tabindex="0" @focus="accessStore.lireTexte('Bouton Annonces')"/>
          <div v-if="showNotifMenu" class="dropdown-menu notif-popup">
             <div class="menu-header">
               <h4>Annonces</h4>
               <button class="close-btn" @click="showNotifMenu = false">✕</button>
             </div>
             <ul class="notif-list">
               <li class="notif-item" tabindex="0" @focus="accessStore.lireTexte('Annonce : L\'étudiant Annissa a rendu son livrable.')">L'étudiant Annissa a rendu son livrable.</li>
               <li class="notif-item" tabindex="0" @focus="accessStore.lireTexte('Alerte : Échéance S A E 316 dans 2 jours.')">⚠️ Échéance SAE 316 dans 2 jours.</li>
             </ul>
          </div>
        </div>
      </div>

      <button 
        v-if="userStore.role === 'public'" 
        @click="allerVersConnexion" 
        class="login-btn"
        @focus="accessStore.lireTexte('Bouton Connexion')"
      >
        Connexion
      </button>

      <div v-else class="profil-connecte relative-wrapper">
        <img 
          :src="getAvatarSrc" 
          alt="Avatar" 
          class="avatar-svg" 
          @click="fermerTout(); showProfilMenu = !showProfilMenu; accessStore.lireTexte('Ouverture du menu profil');" 
          style="cursor:pointer; width:40px; height:40px; border-radius:50%;"
          :title="`Profil ${userStore.role}`"
          tabindex="0"
          @focus="accessStore.lireTexte('Menu Profil')"
        />
        
        <div v-if="showProfilMenu" class="dropdown-menu">
           <div class="menu-header">
             <h4 style="text-transform: capitalize;">Profil {{ userStore.role }}</h4>
             <button class="close-btn" @click="showProfilMenu = false">✕</button>
           </div>
           
           <div class="acc-option clickable-text" @click="ouvrirModifMotDePasse" tabindex="0" @focus="accessStore.lireTexte('Modifier le mot de passe')">Modifier le mot de passe</div>
           
           <div v-if="userStore.role === 'admin'" class="acc-option clickable-text" @click="allerGererDroits" tabindex="0" @focus="accessStore.lireTexte('Gérer les droits')">Gérer les droits</div>
           
           <div class="acc-option clickable-text text-red" @click="deconnexion" tabindex="0" @focus="accessStore.lireTexte('Se déconnecter')">Déconnexion</div>
        </div>
      </div>

    </div>
  </header>

  <div v-if="afficherMotDePassePopup" class="modal-overlay" @click="afficherMotDePassePopup = false">
    <div class="modal-content-center" @click.stop>
      <h3 tabindex="0" @focus="accessStore.lireTexte('Réinitialiser le mot de passe')">Réinitialiser le mot de passe</h3>
      <div class="form-group-modal">
        <label>Nouveau mot de passe</label>
        <input type="password" placeholder="••••••••" class="input-full" @focus="accessStore.lireTexte('Champ : Nouveau mot de passe')" />
      </div>
      <div class="form-group-modal">
        <label>Confirmer le mot de passe</label>
        <input type="password" placeholder="••••••••" class="input-full" @focus="accessStore.lireTexte('Champ : Confirmer le mot de passe')" />
      </div>
      <div class="modal-actions">
        <button @click="afficherMotDePassePopup = false" class="btn-cancel" @focus="accessStore.lireTexte('Bouton Annuler')">Annuler</button>
        <button @click="validerNouveauMotDePasse" class="btn-confirm" @focus="accessStore.lireTexte('Bouton Enregistrer')">Enregistrer</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import '../css/Highbar.css';
</style>