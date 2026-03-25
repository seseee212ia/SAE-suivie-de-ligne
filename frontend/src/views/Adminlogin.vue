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

<style scoped>
/* Conteneur principal modifié pour forcer la largeur totale */
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100vw; /* Force la largeur à 100% de la fenêtre */
  margin: 0;
  padding: 0;
  background-color: #f3f4f6;
  font-family: 'Inter', sans-serif;
  position: absolute; /* S'assure de couvrir tout le fond */
  top: 0;
  left: 0;
}

.login-card {
  width: 100%;
  max-width: 550px;
  padding: 2rem;
  /* Optionnel : tu peux ajouter un fond blanc à la carte si tu préfères
     background-color: white; 
     border-radius: 8px;
     box-shadow: 0 4px 6px rgba(0,0,0,0.1); */
}

/* Typographie En-tête */
.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-header h1 {
  color: #8b5cf6;
  font-size: 2.2rem;
  margin-bottom: 0.5rem;
}

.login-header h2 {
  color: #6b7280;
  font-size: 1.2rem;
  font-weight: 400;
}

/* Séparateur */
.divider {
  display: flex;
  align-items: center;
  text-align: center;
  margin-bottom: 2rem;
  color: #9ca3af;
}

.divider .line {
  flex: 1;
  border-bottom: 2px solid #e5e7eb;
}

.divider-text {
  padding: 0 1rem;
  font-size: 1rem;
}

/* Formulaire */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 1rem;
  color: #111827;
}

/* Inputs modifiés pour forcer la couleur noire */
input {
  width: 100%;
  padding: 0.8rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 4px; /* Un peu plus arrondi pour faire propre */
  font-size: 1rem;
  background-color: #ffffff; /* Fond blanc forcé */
  color: #000000; /* Texte noir forcé pour être lisible ! */
  box-sizing: border-box;
}

/* Style du placeholder pour qu'il soit gris (distinct du texte tapé) */
input::placeholder {
  color: #9ca3af;
}

input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2); /* Petit effet de focus sympa */
}

/* Wrapper pour positionner l'icône de l'œil */
.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.eye-btn {
  position: absolute;
  right: 10px;
  background: none;
  border: none;
  cursor: pointer;
  color: #9ca3af;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
}

.eye-btn:hover {
  color: #4b5563;
}

/* Mot de passe oublié (MODIFIÉ POUR LE SPAN CLIQUABLE) */
.forgot-password {
  text-align: right;
  margin-top: -0.5rem;
}

.clickable-link {
  color: #9ca3af;
  font-size: 0.9rem;
  cursor: pointer;
  transition: color 0.2s;
}

.clickable-link:hover {
  text-decoration: underline;
  color: #6b7280;
}

/* Bouton Se connecter */
.btn-submit {
  background-color: #8b5cf6;
  color: white;
  border: none;
  padding: 1rem;
  font-size: 1.1rem;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 1rem;
  transition: background-color 0.2s;
  font-weight: 500;
}

.btn-submit:hover {
  background-color: #7c3aed;
}

/* ==========================================================
   CSS DU MODAL (Copié depuis AdminDashboard.vue)
   ========================================================== */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-content-center { background: white; padding: 2rem; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.modal-content-center h3 { margin-top: 0; color: #111827; margin-bottom: 1.5rem;}
.form-group-modal { margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.5rem;}
.form-group-modal label { font-size: 0.9rem; color: #374151;}
.input-full { width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;}
.modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;}
.btn-cancel { background: transparent; border: none; color: #6b7280; cursor: pointer;}
.btn-confirm { background: #3b82f6; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;}
</style>