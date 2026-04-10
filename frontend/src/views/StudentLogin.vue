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
import { useUserStore } from '@/stores/user'; // Importation du store utilisateur

const router = useRouter();

// VARIABLES DU FORMULAIRE
const numeroEtudiant = ref('');
const motDePasse = ref('');
const showPassword = ref(false);
const afficherMotDePassePopup = ref(false);

// ACCESSIBILITÉ VOCALE
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

// LOGIQUE DE CONNEXION ÉTUDIANTE (Mise à jour avec API)
const handleLogin = async () => {
  const regexEtudiant = /^22\d{6}$/;

  // Vérifications locales
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

  try {
    // Appel au backend 
    const reponse = await fetch('/api/login_check', {
    method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        // Le backend attend "username" par défaut, on lui envoie le numéro étudiant
        username: numeroEtudiant.value,
        password: motDePasse.value
      })
    });

    if (!reponse.ok) {
      // AJOUT DE L'INDICE POUR L'ÉTUDIANT ICI
      throw new Error("Identifiants incorrects. Veuillez réessayer.\n\n essayez '22301234' avec le mot de passe 'etu123'.");
    }

    // Récupération et stockage du Token
    const data = await reponse.json();
    localStorage.setItem('jwt_token', data.token);

    // Mise à jour du store pour changer le rôle et l'interface globale
    const userStore = useUserStore();
    userStore.setRole('etudiant');

    lireTexte("Connexion réussie. Redirection vers votre tableau de bord.");
    router.push('/student/dashboard'); 

  } catch (error) {
    alert(error.message);
    // L'accessibilité vocale lira également l'indice à l'utilisateur !
    lireTexte("Erreur lors de la connexion. " + error.message);
  }
};

// GESTION DU MODAL
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

<style scoped src="../css/Studentlogin.css"></style>