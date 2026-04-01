<template>
  <div class="login-container">
    <div class="login-card">
      
      <div class="login-header">
        <h1>Connexion</h1>
        <h2>Connexion session admin</h2>
      </div>

      <div class="divider">
        <span class="line"></span>
        <span class="divider-text">Entrez votre identifiant et votre mot de passe</span>
        <span class="line"></span>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        
        <div class="form-group">
          <label for="identifiant">Identifiant</label>
          <input 
            type="text" 
            id="identifiant" 
            v-model="identifiant" 
            placeholder="******" 
            required
          />
        </div>

        <div class="form-group">
          <label for="password">Mot de passe</label>
          <div class="password-wrapper">
            <input 
              :type="showPassword ? 'text' : 'password'" 
              id="password" 
              v-model="motDePasse" 
              placeholder="•••••••••" 
              required
            />
            <button type="button" class="eye-btn" @click="togglePassword" aria-label="Afficher le mot de passe">
              <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
            </button>
          </div>
        </div>

        <div class="forgot-password">
          <span class="clickable-link" @click="ouvrirModifMotDePasse">Mot de passe oublié ?</span>
        </div>

        <button type="submit" class="btn-submit">Se connecter</button>
        
      </form>
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

  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router'; // Import de l'outil de navigation

const router = useRouter(); // Initialisation du routeur
const identifiant = ref('');
const motDePasse = ref('');
const showPassword = ref(false);

// --- NOUVELLES VARIABLES POUR LE MODAL ---
const afficherMotDePassePopup = ref(false);

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const handleLogin = () => {
  // Vérification des identifiants (comme dans ta BDD)
  if (identifiant.value === 'admin' && motDePasse.value === 'admin123') {
    // Redirection vers le tableau de bord
    router.push('/admin/dashboard');
  } else {
    alert("Identifiants incorrects. Essayez 'admin' et 'admin123'");
  }
};

// --- NOUVELLES FONCTIONS POUR LE MODAL ---
const ouvrirModifMotDePasse = () => {
  afficherMotDePassePopup.value = true;
};

const validerNouveauMotDePasse = () => {
  alert("Mot de passe mis à jour avec succès !");
  afficherMotDePassePopup.value = false;
};
</script>

<style scoped src="../css/Adminlogin.css"></style>