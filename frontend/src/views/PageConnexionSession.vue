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

// LOGIQUE DE CONNEXION LOCALE (Test sans backend)
const handleLogin = () => {
  if (identifiant.value && password.value) {
    
    // 1. On met à jour le Store global pour que la Sidebar et la Highbar s'adaptent
    userStore.setRole(currentRole.value)

    // 2. On redirige vers le bon tableau de bord selon le rôle
    if (currentRole.value === 'admin') {
      router.push('/admin/dashboard')
    } else if (currentRole.value === 'enseignant'){
      router.push('/enseignant/dashboard')
    } else {
      router.push('/etudiant/dashboard')
    }
    
  } else {
    alert("Veuillez remplir tous les champs.");
  }
}
</script>

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

<style scoped>
@import '../css/PageConnexionSession.css';
</style>