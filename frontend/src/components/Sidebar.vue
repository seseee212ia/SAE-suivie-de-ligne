<script setup>
import { useUserStore } from '@/stores/user'
import { useAccessibiliteStore } from '@/stores/accessibilite'
import { useUiStore } from '@/stores/ui' // Ajout de l'import de ton store UI

// Importation des fichiers SVG
import logoMmi from '@/assets/logo-mmi.svg'
import iconHome from '@/assets/icon_home.svg'
import iconSae from '@/assets/icon_sae.svg'
import iconDocument from '@/assets/icon_document.svg'

const userStore = useUserStore()
const accessStore = useAccessibiliteStore()
const uiStore = useUiStore() // Initialisation du store UI
</script>

<template>
  <div 
    v-if="uiStore.sidebarOpen" 
    class="sidebar-overlay" 
    @click="uiStore.fermerSidebar"
  ></div>

  <aside class="sidebar" :class="{ 'is-open': uiStore.sidebarOpen }">
    
    <div class="logo-container">
      <div 
        class="logo-mmi-mask" 
        :style="{ '--logo-url': `url(${logoMmi})` }" 
        role="img" 
        aria-label="Logo MMI"
        tabindex="0"
        @mouseenter="accessStore.lireTexte('Logo du département MMI')"
        @focusin="accessStore.lireTexte('Logo du département MMI')"
      ></div>
    </div>

    <nav>
      <ul v-if="userStore.role === 'public'">
        <li @mouseenter="accessStore.lireTexte('Accueil Public')" @focusin="accessStore.lireTexte('Accueil Public')">
          <RouterLink to="/public-home" @click="uiStore.fermerSidebar">
            <img :src="iconHome" alt="Icon Home" class="menu-icon" />
            Accueil Public
          </RouterLink>
        </li>
      </ul>

      <ul v-else-if="userStore.role === 'etudiant'">
        <li @mouseenter="accessStore.lireTexte('Aller au tableau de bord')" @focusin="accessStore.lireTexte('Aller au tableau de bord')">
          <RouterLink to="/etudiant/dashboard" @click="uiStore.fermerSidebar">
            <img :src="iconHome" alt="Icon Dashboard" class="menu-icon" />
            Tableau de bord
          </RouterLink>
        </li>
        <li @mouseenter="accessStore.lireTexte('Aller à mes S A E')" @focusin="accessStore.lireTexte('Aller à mes S A E')">
          <RouterLink to="/etudiant/sae" @click="uiStore.fermerSidebar">
            <img :src="iconSae" alt="Icon SAE" class="menu-icon" />
            SAE
          </RouterLink>
        </li>
      </ul>

      <ul v-else-if="userStore.role === 'enseignant'">
        <li @mouseenter="accessStore.lireTexte('Aller au tableau de bord enseignant')" @focusin="accessStore.lireTexte('Aller au tableau de bord enseignant')">
          <RouterLink to="/enseignant/dashboard" @click="uiStore.fermerSidebar">
            <img :src="iconHome" alt="Icon Dashboard" class="menu-icon" />
            Tableau de bord
          </RouterLink>
        </li>
        <li @mouseenter="accessStore.lireTexte('Aller à la gestion de mes S A E')" @focusin="accessStore.lireTexte('Aller à la gestion de mes S A E')">
          <RouterLink to="/enseignant/mes-sae" @click="uiStore.fermerSidebar">
            <img :src="iconSae" alt="Icon SAE" class="menu-icon" />
            SAE
          </RouterLink>
        </li>
      </ul>

      <ul v-else-if="userStore.role === 'admin'">
        <li @mouseenter="accessStore.lireTexte('Aller au tableau de bord administrateur')" @focusin="accessStore.lireTexte('Aller au tableau de bord administrateur')">
          <RouterLink to="/admin/dashboard" @click="uiStore.fermerSidebar">
            <img :src="iconHome" alt="Icon Dashboard" class="menu-icon" />
            Tableau de bord
          </RouterLink>
        </li>
        <li @mouseenter="accessStore.lireTexte('Aller à la gestion de toutes les S A E')" @focusin="accessStore.lireTexte('Aller à la gestion de toutes les S A E')">
          <RouterLink to="/admin/sae" @click="uiStore.fermerSidebar">
            <img :src="iconSae" alt="Icon Gérer SAE" class="menu-icon" />
            Gérer les SAE
          </RouterLink>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<style scoped>
@import '../css/Sidebar.css';
</style>