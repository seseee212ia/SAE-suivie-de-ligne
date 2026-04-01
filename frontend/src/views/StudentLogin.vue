<template>
  <div class="login-container">
    <div class="login-card">
      
      <div class="login-header">
        <h1>Connexion</h1>
        <h2>Connexion session étudiante</h2>
      </div>

      <div class="divider">
        <span class="line"></span>
        <span class="divider-text">Entrez votre numéro d’étudiant et votre mot de passe</span>
        <span class="line"></span>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        
        <div class="form-group">
          <label for="numeroEtudiant">Numéro étudiant</label>
          <input 
            type="text" 
            id="numeroEtudiant" 
            v-model="numeroEtudiant" 
            placeholder="22••••••" 
            maxlength="8"
            required
            @focus="lireTexte('Champ numéro étudiant. Il doit comporter 8 chiffres et commencer par 22.')"
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
              @focus="lireTexte('Champ mot de passe.')"
            />
            <button type="button" class="eye-btn" @click="togglePassword" :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'">
              <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
            </button>
          </div>
        </div>

        <div class="forgot-password">
          <span class="clickable-link" @click="ouvrirModifMotDePasse" tabindex="0" @keypress.enter="ouvrirModifMotDePasse">Mot de passe oublié ?</span>
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
          <button @click="fermerModifMotDePasse" class="btn-cancel">Annuler</button>
          <button @click="validerNouveauMotDePasse" class="btn-confirm">Enregistrer</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// --- VARIABLES DU FORMULAIRE ---
const numeroEtudiant = ref('');
const motDePasse = ref('');
const showPassword = ref(false);
const afficherMotDePassePopup = ref(false);

// --- ACCESSIBILITÉ VOCALE ---
const lireTexte = (texte) => {
  if (localStorage.getItem('voix_active') === 'true' && window.speechSynthesis) {
    window.speechSynthesis.cancel();
    const utterance = new SpeechSynthesisUtterance(texte);
    utterance.lang = 'fr-FR';
    utterance.rate = 0.95;
    window.speechSynthesis.speak(utterance);
  }
};

onMounted(() => {
  lireTexte("Page de connexion étudiante. Entrez votre numéro d'étudiant eCampus commençant par 22 et votre mot de passe.");
});

const togglePassword = () => {
  showPassword.value = !showPassword.value;
  lireTexte(showPassword.value ? "Mot de passe affiché en clair" : "Mot de passe masqué");
};

// --- LOGIQUE DE CONNEXION ÉTUDIANTE ---
const handleLogin = () => {
  const regexEtudiant = /^22\d{6}$/;

  if (!regexEtudiant.test(numeroEtudiant.value)) {
    const msgErreur = "Format invalide. Votre numéro d'étudiant doit comporter exactement 8 chiffres et commencer par 22.";
    alert(msgErreur);
    lireTexte("Erreur de connexion. " + msgErreur);
    return; 
  }

  if (motDePasse.value.length < 4) {
    alert("Le mot de passe est trop court.");
    lireTexte("Erreur. Le mot de passe saisi est trop court.");
    return;
  }

  lireTexte("Connexion réussie. Redirection vers votre tableau de bord.");
  router.push('/etudiant/dashboard'); 
};

// --- GESTION DU MODAL ---
const ouvrirModifMotDePasse = () => {
  afficherMotDePassePopup.value = true;
  lireTexte("Fenêtre de réinitialisation du mot de passe ouverte");
};

const fermerModifMotDePasse = () => {
  afficherMotDePassePopup.value = false;
  lireTexte("Fenêtre de mot de passe fermée");
};

const validerNouveauMotDePasse = () => {
  alert("Mot de passe mis à jour avec succès !");
  afficherMotDePassePopup.value = false;
  lireTexte("Mot de passe mis à jour avec succès");
};
</script>

<style scoped>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  width: 100vw; 
  margin: 0;
  padding: 0;
  background-color: #f8fafc; 
  font-family: 'Inter', sans-serif;
  position: absolute; 
  top: 0;
  left: 0;
}

.login-card {
  width: 100%;
  max-width: 550px;
  padding: 2rem;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
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
  margin-top: 1rem;
}

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

input {
  width: 100%;
  padding: 0.8rem 1rem;
  border: 1px solid #cce3de; 
  border-radius: 4px; 
  font-size: 1rem;
  background-color: #ffffff; 
  color: #000000; 
  box-sizing: border-box;
}

input::placeholder {
  color: #9ca3af;
  letter-spacing: 2px; 
}

input:focus {
  outline: none;
  border-color: #8b5cf6;
  box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2); 
}

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