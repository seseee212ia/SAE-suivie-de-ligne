<template>
  <div class="login-layout">
    
    <div class="back-action">
      <RouterLink to="/connexion" class="btn-retour">← Retour</RouterLink>
    </div>

    <div class="role-selector">
      <button class="dropdown-btn" @click="dropdownOpen = !dropdownOpen" type="button">
        <img :src="iconParametres" alt="Paramètres" class="btn-icon" /> 
        {{ formattedRole }}
      </button>
      
      <ul v-if="dropdownOpen" class="dropdown-menu">
        <li @click="switchRole('admin')">Administrateur</li>
        <li @click="switchRole('etudiant')">Étudiant</li>
        <li @click="switchRole('enseignant')">Enseignant</li>
      </ul>
    </div>

    <div class="form-container">
      <h1 class="main-title">Connexion</h1>
      
      <div class="subtitle-wrapper">
        <hr class="line">
        <h2 class="subtitle">Connexion session {{ currentRole }}</h2>
        <hr class="line">
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        
        <div class="input-group">
          <label for="identifiant">
            {{ currentRole === 'etudiant' ? 'Numéro étudiant' : "Nom d'utilisateur" }}
          </label>
          <input 
            id="identifiant" 
            type="text" 
            v-model="identifiant" 
            :placeholder="currentRole === 'etudiant' ? '22••••••' : 'Saisissez votre identifiant'" 
            required 
            autocomplete="username"
          />
        </div>

        <div class="input-group">
          <label for="password">Mot de passe</label>
          <div class="password-wrapper">
            <input 
              id="password" 
              :type="showPassword ? 'text' : 'password'" 
              v-model="password" 
              placeholder="••••••••" 
              required 
              autocomplete="current-password"
            />
            
            <button 
              type="button" 
              class="toggle-pwd-btn" 
              @click="showPassword = !showPassword"
              title="Afficher/Masquer"
            >
              <img 
                :src="iconOeuil" 
                alt="Afficher/Masquer" 
                class="icon-pwd" 
                :class="{ 'is-hidden': !showPassword }" 
              />
            </button>
          </div>
        </div>

        <button type="submit" class="submit-btn">Se connecter</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useUserStore } from '@/stores/user'

import iconParametres from '@/assets/icon_parametres.svg'
import iconOeuil from '@/assets/icon_oeuil.svg'

const route = useRoute()
const router = useRouter()
const userStore = useUserStore()

// On récupère le rôle choisi sur la page précédente
const currentRole = ref(route.params.role || 'etudiant')
const identifiant = ref('')
const password = ref('')
const showPassword = ref(false)
const dropdownOpen = ref(false)

const roleLabels = {
  admin: 'Administrateur',
  enseignant: 'Enseignant',
  etudiant: 'Étudiant'
}

const formattedRole = computed(() => roleLabels[currentRole.value] || currentRole.value)

const switchRole = (newRole) => {
  currentRole.value = newRole
  dropdownOpen.value = false
  identifiant.value = ''
  password.value = ''
  router.replace({ name: 'connexion-session', params: { role: newRole } })
}

// LOGIQUE DE CONNEXION AVEC L'API SYMFONY
const handleLogin = async () => {
  if (!identifiant.value || !password.value) {
    alert("Veuillez remplir tous les champs.");
    return;
  }

  // Vérification regex supplémentaire si c'est un étudiant
  if (currentRole.value === 'etudiant') {
    const regexEtudiant = /^22\d{6}$/;
    if (!regexEtudiant.test(identifiant.value)) {
      alert("Format invalide. Votre numéro d'étudiant doit comporter exactement 8 chiffres et commencer par 22.");
      return; 
    }
  }

  try {
    // 1. Appel au backend pour vérifier les identifiants
    const reponse = await fetch('/api/login_check', {
    method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        username: identifiant.value,
        password: password.value
      })
    });

    if (!reponse.ok) {
      // CRÉATION DE L'INDICE DYNAMIQUE SELON LE RÔLE
      let messageIndice = "";
      if (currentRole.value === 'admin') {
        messageIndice = "Indice : essayez 'admin_test' avec le mot de passe 'admin123'.";
      } else if (currentRole.value === 'enseignant') {
        messageIndice = "Indice : essayez 'ens_test' avec le mot de passe 'ens123'.";
      } else if (currentRole.value === 'etudiant') {
        messageIndice = "Indice : essayez '22301234' avec le mot de passe 'etu123'.";
      }
      throw new Error(`Identifiants incorrects. Veuillez réessayer.\n\n${messageIndice}`);
    }

    // Récupération des données renvoyées par le serveur
    const data = await reponse.json();

    //  VÉRIFICATION DE SÉCURITÉ : Est-ce que le rôle correspond au portail ?
    // On décode la partie "Payload" du Token JWT (qui est en Base64)
    const tokenPayload = JSON.parse(atob(data.token.split('.')[1]));
    const userRoles = tokenPayload.roles || [];

    if (currentRole.value === 'enseignant' && !userRoles.includes('ROLE_ENSEIGNANT')) {
      throw new Error("Accès refusé : Ces identifiants n'appartiennent pas à un Enseignant.\n\nIndice : essayez 'ens_test' avec 'ens123'.");
    }
    if (currentRole.value === 'admin' && !userRoles.includes('ROLE_ADMIN')) {
      throw new Error("Accès refusé : Ces identifiants n'appartiennent pas à un Administrateur.\n\nIndice : essayez 'admin_test' avec 'admin123'.");
    }
    if (currentRole.value === 'etudiant' && !userRoles.includes('ROLE_ETUDIANT')) {
      throw new Error("Accès refusé : Ces identifiants n'appartiennent pas à un Étudiant.\n\n Indice : essayez '22301234' avec 'etu123'.");
    }

    // Si la vérification passe, on stocke le Token JWT
    localStorage.setItem('jwt_token', data.token);

    // Mise à jour de l'interface globale via Pinia
    userStore.setRole(currentRole.value);

    // Redirection vers le bon tableau de bord selon le rôle
    if (currentRole.value === 'admin') {
      router.push('/admin/dashboard');
    } else if (currentRole.value === 'enseignant') {
      router.push('/enseignant/dashboard');
    } else {
      router.push('/etudiant/dashboard');
    }

  } catch (error) {
    // On affiche l'erreur détaillée (Mauvais mdp OU Mauvais rôle) avec l'indice
    alert(error.message);
  }
}
</script>

<style scoped>
@import '../css/PageConnexionSession.css';
</style>