<template>
  <header class="top-header">
    <div class="search-container">
      <slot name="search"></slot>
    </div>
    
    <div class="top-icons">
      <button @click="toggleMenuAccessibilite" class="icon-btn" title="Accessibilité" aria-label="Menu Accessibilité">
        <img src="../assets/icon_accessibilites.svg" alt="" class="svg-icon" aria-hidden="true" />
      </button>
      <button @click="toggleNotifications" class="icon-btn" title="Notifications" aria-label="Notifications">
        <img src="../assets/icon_annonces.svg" alt="" class="svg-icon" aria-hidden="true" />
      </button>
      <button @click="toggleModeSombre" class="icon-btn" title="Mode Sombre" aria-label="Activer ou désactiver le mode sombre">
        <img src="../assets/icon_dark_mode.svg" alt="" class="svg-icon" aria-hidden="true" />
      </button>
      <button @click="toggleParametres" class="icon-btn" title="Paramètres" aria-label="Paramètres administrateur">
        <img src="../assets/icon_parametres.svg" alt="" class="svg-icon" aria-hidden="true" />
      </button>
      
      <div class="user-profile clickable" @click="toggleProfil" role="button" aria-label="Profil administrateur">
        <span>Admin ⌄</span>
      </div>
    </div>

    <div v-if="afficherAccessibilite" class="popup-menu accessibilite-menu" role="dialog" aria-labelledby="acc-title">
      <div class="popup-header-black">
        <h3 id="acc-title">Accessibilité</h3>
        <button @click="toggleMenuAccessibilite" class="close-btn-white" aria-label="Fermer le menu accessibilité">✕</button>
      </div>
      <div class="popup-body">
        <div class="feature-item">
          <div class="feature-label">
            <span class="icon-wrapper" aria-hidden="true">🔊</span> 
            <span>Synthèse vocale</span>
          </div>
          <label class="switch" aria-label="Activer la synthèse vocale de la page">
            <input type="checkbox" v-model="accessibilite.voix" @change="emettreChangementVoix">
            <span class="slider round"></span>
          </label>
        </div>
        <div class="feature-item zoom-feature">
          <div class="feature-label">
            <span class="icon-wrapper font-bold" aria-hidden="true">A+</span> 
            <span>Agrandir le texte</span>
          </div>
          <div class="zoom-buttons">
            <button @click="definirPolice(1.3)" :class="{'active-zoom': fontScale === 1.3}" class="btn-toggle-action" aria-label="Taille de texte normale">x1</button>
            <button @click="definirPolice(1.6)" :class="{'active-zoom': fontScale === 1.6}" class="btn-toggle-action" aria-label="Taille de texte moyenne">x2</button>
            <button @click="definirPolice(1.9)" :class="{'active-zoom': fontScale === 1.9}" class="btn-toggle-action" aria-label="Taille de texte très grande">x3</button>
            <button @click="definirPolice(1)" class="btn-toggle-action reset-btn" v-if="fontScale > 1" aria-label="Réinitialiser la taille du texte">↺</button>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-label">
            <span class="icon-wrapper" aria-hidden="true">👁️</span> 
            <span>Mode Daltonien</span>
          </div>
          <label class="switch" aria-label="Activer le mode daltonien">
            <input type="checkbox" v-model="accessibilite.daltonien" @change="appliquerFiltreDaltonien">
            <span class="slider round"></span>
          </label>
        </div>
      </div>
    </div>

    <div v-if="afficherNotifications" class="popup-menu standard-popup">
      <h3>Notifications</h3>
      <ul class="notif-list">
        <li class="notif-item">
          <span class="notif-icon">📢</span> 
          <span class="notif-text">Aucune nouvelle notification pour le moment.</span>
        </li>
      </ul>
    </div>

    <div v-if="afficherParametres || afficherProfil" class="popup-menu standard-popup">
      <h3 v-if="afficherParametres">Paramètres</h3>
      <h3 v-if="afficherProfil">Profil Administrateur</h3>
      <ul class="action-list">
        <li @click="ouvrirModifMotDePasse" class="action-item">Modifier le mot de passe</li>
        <li @click="allerGererDroits" class="action-item">Gérer les droits</li>
        <li v-if="afficherProfil" @click="deconnexion" class="action-item text-red">Déconnexion</li>
      </ul>
    </div>

    <div v-if="afficherMotDePassePopup" class="modal-overlay" @click="afficherMotDePassePopup = false">
      <div class="modal-content-center" @click.stop>
        <h3>Réinitialiser le mot de passe</h3>
        <div class="form-group-modal">
          <label>Nouveau mot de passe</label>
          <input type="password" placeholder="••••••••" class="input-full" />
        </div>
        <div class="form-group-modal">
          <label>Confirmer le mot de passe</label>
          <input type="password" placeholder="••••••••" class="input-full" />
        </div>
        <div class="modal-actions">
          <button @click="afficherMotDePassePopup = false" class="btn-cancel">Annuler</button>
          <button @click="validerNouveauMotDePasse" class="btn-confirm">Enregistrer</button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const emit = defineEmits(['toggle-voix']);

const afficherAccessibilite = ref(false);
const afficherNotifications = ref(false);
const afficherParametres = ref(false);
const afficherProfil = ref(false);
const afficherMotDePassePopup = ref(false);

const fontScale = ref(1);
const accessibilite = ref({ voix: false, daltonien: false });

const fermerTout = () => {
  afficherAccessibilite.value = false;
  afficherNotifications.value = false;
  afficherParametres.value = false;
  afficherProfil.value = false;
};

const toggleMenuAccessibilite = () => { const etat = afficherAccessibilite.value; fermerTout(); afficherAccessibilite.value = !etat; };
const toggleNotifications = () => { const etat = afficherNotifications.value; fermerTout(); afficherNotifications.value = !etat; };
const toggleParametres = () => { const etat = afficherParametres.value; fermerTout(); afficherParametres.value = !etat; };
const toggleProfil = () => { const etat = afficherProfil.value; fermerTout(); afficherProfil.value = !etat; };

const ouvrirModifMotDePasse = () => { fermerTout(); afficherMotDePassePopup.value = true; };
const validerNouveauMotDePasse = () => { alert("Mot de passe mis à jour avec succès !"); afficherMotDePassePopup.value = false; };
const allerGererDroits = () => { fermerTout(); router.push('/admin/sae/202'); };
const deconnexion = () => { router.push('/'); };

const definirPolice = (scale) => {
  fontScale.value = scale;
  document.documentElement.style.fontSize = `${16 * fontScale.value}px`;
};

const appliquerFiltreDaltonien = () => {
  if (accessibilite.value.daltonien) {
    document.body.style.filter = "contrast(110%) sepia(50%) hue-rotate(180deg) saturate(150%)";
  } else {
    document.body.style.filter = "none";
  }
};

const toggleModeSombre = () => {
  document.body.classList.toggle('theme-sombre');
};

const emettreChangementVoix = () => {
  emit('toggle-voix', accessibilite.value.voix);
};
</script>

<style scoped>
/* Les styles du header sont hérités du CSS global que je te fournis plus bas */
</style>