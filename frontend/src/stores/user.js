import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUserStore = defineStore('user', () => {
  // Le rôle actuel. Par défaut, on met 'public'
  // Ça pourrait aussi être 'etudiant', 'enseignant' ou 'admin'
  const role = ref('public') 

  // Une fonction pour changer de rôle (utile pour tes tests)
  function setRole(newRole) {
    role.value = newRole
  }

  return { role, setRole }
})