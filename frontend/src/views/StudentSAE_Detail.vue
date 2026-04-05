<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAccessibiliteStore } from '@/stores/accessibilite'

import iconAgraffe from '@/assets/icon_agraffe.svg'
import iconCalendrier from '@/assets/icon_calendar_today.svg'
import iconTime from '@/assets/icon_access_time.svg'
import iconAnnonces from '@/assets/icon_meeting.svg'
import iconDossier from '@/assets/icon_dossier.svg'
import iconDocument from '@/assets/icon_document.svg'
import iconDownload from '@/assets/icon_download.svg'
import iconAjoutDossier from '@/assets/icon_dossierAjout.svg'
import iconEdit from '@/assets/icon_modif.svg'

const accessStore = useAccessibiliteStore()
const route = useRoute()
const saeId = computed(() => route.params.id || '401')

const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre']

// --- DONNÉES DE LA SAE (Dynamiques depuis LocalStorage) ---
const saeData = ref({
  title: `SAE ${saeId.value}`,
  category: 'Générique',
  categoryColor: '#4C6FFF',
  categoryBg: '#E0E7FF',
  description: 'Description en cours de chargement...',
  semestre: 'S1',
  deadlineDate: '2026-11-30',
  phasesAdded: true 
})

// Utilitaires de couleur pour les tags de filière
const getColorForCategory = (filiere) => {
    if (filiere === 'Développement Web') return { categoryColor: '#059669', categoryBg: '#D1FAE5' };
    if (filiere === 'Création Numérique') return { categoryColor: '#E11D48', categoryBg: '#FFE4E6' };
    if (filiere === 'Communication') return { categoryColor: '#D97706', categoryBg: '#FEF3C7' };
    return { categoryColor: '#4C6FFF', categoryBg: '#E0E7FF' }; 
};

const deadlineDisplay = computed(() => {
  const d = new Date(saeData.value.deadlineDate)
  if(isNaN(d)) return saeData.value.deadlineDate; 
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
})

// Calcul dynamique précis du temps restant (Jours et Heures)
const timeRemaining = computed(() => {
  const targetDate = new Date(saeData.value.deadlineDate)
  if(isNaN(targetDate)) return "Non défini"
  
  targetDate.setHours(23, 59, 59) // Fin de journée
  const now = new Date()
  const diffMs = targetDate - now

  if (diffMs <= 0) return "Terminé"
  
  const days = Math.floor(diffMs / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  
  return `${days} j ${hours.toString().padStart(2, '0')} h`
})

// --- GESTION DES PHASES ---
const phases = ref([
  { id: 1, name: 'Phase 1: Maquette', completed: true },
  { id: 2, name: 'Phase 2: Vue Etudiant', completed: false },
  { id: 3, name: 'Phase 3: Vue Enseignant', completed: false }
])

const globalProgress = computed(() => {
  if (phases.value.length === 0) return 0
  const completedCount = phases.value.filter(p => p.completed).length
  return Math.round((completedCount / phases.value.length) * 100)
})

const radius = 85
const circumference = 2 * Math.PI * radius
const strokeDashoffset = computed(() => {
  return circumference - (globalProgress.value / 100) * circumference
})

const togglePhase = (phase) => {
  phase.completed = !phase.completed
  accessStore.lireTexte(`Phase ${phase.name} marquée comme ${phase.completed ? 'terminée' : 'non terminée'}. Avancement total : ${globalProgress.value} pourcent.`)
}


// --- LISTES ---
const ressources = ref([
  { id: 1, title: 'Doc Non Consulté', author: 'M. LE CADET', date: '10/03/2026', time: '11:30', status: 'unread' },
  { id: 2, title: 'Doc Consulté', author: 'M. LE CADET', date: '10/03/2026', time: '11:30', status: 'read' },
])

const annonces = ref([
  { id: 1, title: 'Annonce Non Lue', author: 'M. LE CADET', date: '10/03/2026', time: '11:30', content: "Contenu de l'annonce détaillée ici...\n\nIl est très important de lire ce message." },
  { id: 2, title: 'Annonce Non Lue', author: 'M. LE CADET', date: '10/03/2026', time: '11:30', content: "N'oubliez pas vos rendus pour demain !" }
])

const rendus = ref([
  { id: 1, title: 'Rapport' },
  { id: 2, title: 'Maquettes Des Visuelles' }
])

const consigneText = ref("Chargement des consignes...")

// --- MODALES ET ACTIONS ---
const modals = ref({
  renduUpload: false, renduManage: false, ressourceView: false, annonceView: false
})

const fermerModales = () => { for (let key in modals.value) { modals.value[key] = false } }

// ANNONCE VIEW
const selectedAnnonce = ref(null)
const openAnnonceView = (ann) => {
  selectedAnnonce.value = ann
  modals.value.annonceView = true
  accessStore.lireTexte(`Lecture de l'annonce : ${ann.title}. Postée par ${ann.author}.`)
}

// RESSOURCE VIEW
const selectedRessource = ref(null)
const openRessourceView = (res) => { 
  res.status = 'read'
  selectedRessource.value = res
  modals.value.ressourceView = true 
  accessStore.lireTexte(`Consultation de la ressource : ${res.title}.`)
}

// RENDU ACTIONS (Consultation, Téléchargement, Upload)
const currentRenduTarget = ref(null)

const openRenduVisualisation = (rendu) => {
  // Simule l'ouverture du document de l'étudiant dans la visionneuse
  selectedRessource.value = {
    title: `Mon_Travail_${rendu.title.replace(/\s+/g, '_')}.pdf`,
    author: 'Moi-même (Annissa)',
    date: new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }),
    time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
    status: 'read'
  }
  modals.value.ressourceView = true
  accessStore.lireTexte(`Visualisation de votre rendu : ${rendu.title}.`)
}

const simulateDownload = (rendu) => {
  alert(`Le document sujet pour le rendu "${rendu.title}" a été téléchargé avec succès !`)
  accessStore.lireTexte(`Téléchargement du document sujet pour ${rendu.title}.`)
}

const openUploadRendu = (rendu) => {
  currentRenduTarget.value = rendu
  modals.value.renduUpload = true
  accessStore.lireTexte(`Ouverture de la fenêtre de dépôt pour le rendu : ${rendu.title}.`)
}

const simulateUpload = () => {
  fermerModales()
  alert("Fichier déposé avec succès !")
  accessStore.lireTexte("Votre fichier a été déposé avec succès.")
}


// --- CHARGEMENT DES DONNÉES & LOGIQUE KANBAN ---
onMounted(() => {
  const stored = localStorage.getItem('mmi_data_sae_v2');
  if (stored) {
    let bddSaeGlobale = JSON.parse(stored);
    const foundSae = bddSaeGlobale.find(s => s.id === parseInt(saeId.value));
    
    if (foundSae) {
      const tagColors = getColorForCategory(foundSae.filiere);
      
      // LOGIQUE POUR ÉVITER "Non défini" (Adaptation aux dates du Kanban)
      let calcDate = foundSae.dateEcheance;
      let dateObj = new Date(calcDate);
      
      if (!calcDate || calcDate === "À définir" || isNaN(dateObj)) {
         // Si la date globale n'est pas formatée, on la fabrique selon Kanban
         const moisIndex = nomsMoisGlobaux.findIndex(m => m.toLowerCase() === (foundSae.mois || '').toLowerCase());
         const now = new Date();
         
         if (moisIndex !== -1 && foundSae.jour) {
            dateObj = new Date(now.getFullYear(), moisIndex, foundSae.jour, 23, 59, 59);
         } else {
            // Si rien n'est renseigné, on attribue un délai en fonction de l'ID pour simuler A faire / En cours / Fini
            if (foundSae.id % 4 === 0) {
                now.setDate(now.getDate() - 5); // Fini (Passé)
            } else if (foundSae.id % 3 === 0) {
                // En cours (Aujourd'hui)
            } else {
                now.setDate(now.getDate() + 15); // A faire (Futur)
            }
            dateObj = now;
            dateObj.setHours(23, 59, 59);
         }
         calcDate = dateObj.toISOString();
      }

      saeData.value = {
        title: foundSae.titre || `SAE ${saeId.value}`,
        category: foundSae.filiere || 'Générique',
        ...tagColors,
        description: foundSae.description || "Aucune description n'a été fournie pour cette SAE.",
        semestre: foundSae.semestre || 'S1',
        deadlineDate: calcDate,
        phasesAdded: true 
      };
      consigneText.value = foundSae.consignes || "Aucune consigne spécifique n'est disponible.";
    }
  } else {
     // Fallback s'il n'y a pas de LocalStorage
     const fallbackDate = new Date();
     fallbackDate.setDate(fallbackDate.getDate() + 14);
     saeData.value.deadlineDate = fallbackDate.toISOString();
  }

  if (accessStore.voix) lireDescriptionGenerale()
})

watch(() => accessStore.voix, (estActive) => {
  if (estActive) lireDescriptionGenerale()
})

const lireDescriptionGenerale = () => {
    let texteALire = `Détails de la S.A.E ${saeData.value.title}. `
    texteALire += `Projet : ${saeData.value.description} `
    texteALire += `Votre avancement personnel est de ${globalProgress.value} %. `
    texteALire += `La date d'échéance est fixée au ${deadlineDisplay.value}. Vous avez ${annonces.value.length} annonces et ${ressources.value.length} ressources à consulter.`
    accessStore.lireTexte(texteALire)
}
</script>

<template>
  <div class="sae-detail-container">
    
    <header class="page-header">
      <div class="title-section">
        <h2>Bienvenue Annissa 👋</h2>
        <span class="breadcrumb">Page / Mes SAE / <strong>{{ saeData.title }}</strong></span>
      </div>
      <hr class="separator" />
    </header>

    <div class="dashboard-grid">
      
      <div class="main-column">
        
        <div class="banner-card">
          <div class="banner-content">
            <div class="banner-top-row">
              <span class="category-tag" :style="{ color: saeData.categoryColor, backgroundColor: saeData.categoryBg }">{{ saeData.category }}</span>
              <span class="more-options">•••</span>
            </div>
            
            <h1 class="sae-main-title">{{ saeData.title }}</h1>
            <div class="flower-banner-bg"></div>

            <div v-if="saeData.phasesAdded" class="phases-student-container">
              <label v-for="phase in phases" :key="phase.id" class="custom-checkbox-wrap">
                {{ phase.name }}
                <input type="checkbox" :checked="phase.completed" @change="togglePhase(phase)">
                <span class="checkmark"></span>
              </label>
            </div>
          </div>
        </div>

        <div class="split-row">
          
          <div class="info-card">
            <div class="card-header">
              <h3>Ressources</h3>
            </div>
            <ul class="item-list">
              <li v-for="res in ressources" :key="res.id">
                <button class="item-icon agrafe-btn" :class="{ 'unread': res.status === 'unread' }" @click="openRessourceView(res)">
                  <img :src="iconAgraffe" alt="Icon Agraffe" class="action-icon" />
                </button>
                <div class="item-details">
                  <span class="item-title" :class="{'bold-unread': res.status === 'unread'}">
                    {{ res.status === 'unread' ? 'Doc Non Consulté' : 'Doc Consulté' }}
                  </span>
                  <span class="item-author">DÉPOSÉ PAR {{ res.author }}</span>
                </div>
                <div class="item-meta">
                  <div class="icons-container"><img :src="iconCalendrier" alt="Icon Date" class="action-icon" /> {{ res.date }}</div>
                  <div class="icons-container"><img :src="iconTime" alt="Icon Time" class="action-icon" /> {{ res.time }}</div>
                </div>
              </li>
            </ul>
          </div>

          <div class="info-card">
            <div class="card-header">
              <h3>Annonces</h3>
            </div>
            <ul class="item-list">
              <li v-for="ann in annonces" :key="ann.id">
                <button class="item-icon agrafe-btn" @click="openAnnonceView(ann)">
                  <img :src="iconAnnonces" alt="Icon Notif" class="action-icon" />
                </button>
                <div class="item-details">
                  <span class="item-title bold-unread">{{ ann.title }}</span>
                  <span class="item-author">{{ ann.author }}</span>
                </div>
                <div class="item-meta">
                  <div class="icons-container"><img :src="iconCalendrier" alt="Icon Date" class="action-icon" /> {{ ann.date }}</div>
                  <div class="icons-container"><img :src="iconTime" alt="Icon Time" class="action-icon" /> {{ ann.time }}</div>
                </div>
              </li>
            </ul>
          </div>

        </div>

        <div class="info-card rendus-card">
          <div class="card-header">
            <h3>Rendus</h3>
          </div>
          <ul class="item-list list-separated">
            <li v-for="rendu in rendus" :key="rendu.id" class="rendu-item-student">
              <div class="rendu-left">
                <div class="file-icon folder">
                  <img :src="iconDossier" alt="Dossier" class="action-icon" style="width: 20px; height: 20px; opacity: 0.8;" />
                </div>
                <span class="item-title">{{ rendu.title }}</span>
              </div>
              <div class="rendu-actions">
                <button class="icon-btn green-btn" title="Consulter mon rendu" @click="openRenduVisualisation(rendu)">
                  <img :src="iconDossier" alt="Voir dossier" class="action-icon" style="width: 18px; filter: brightness(0) saturate(100%) invert(32%) sepia(85%) saturate(1912%) hue-rotate(120deg) brightness(97%) contrast(93%);" />
                </button>
                <button class="icon-btn blue-btn" title="Télécharger le sujet/modèle" @click="simulateDownload(rendu)">
                  <img :src="iconDocument" alt="Document" class="action-icon" style="width: 18px; filter: brightness(0) saturate(100%) invert(32%) sepia(85%) saturate(1912%) hue-rotate(209deg) brightness(97%) contrast(93%);" />
                </button>
                <button class="icon-btn orange-btn" title="Déposer/Modifier mon travail" @click="openUploadRendu(rendu)">
                   <img :src="iconAjoutDossier" alt="Déposer" class="action-icon" style="width: 18px; filter: brightness(0) invert(1);" />
                </button>
              </div>
            </li>
          </ul>
        </div>

      </div>

      <div class="side-column">
        
        <div class="info-card desc-card">
          <div class="card-header">
            <h3>Project Description</h3>
            <span class="more-options">•••</span>
          </div>
          <p class="desc-text">{{ saeData.description }}</p>
          <div class="desc-meta">
            <p><span>Semestre:</span> {{ saeData.semestre }}</p>
            <p><span>Date d'échéance:</span> <strong>{{ deadlineDisplay }}</strong></p>
          </div>
        </div>

        <div class="info-card consigne-card">
          <div class="card-header">
            <h3>Consigne</h3>
          </div>
          <div class="consigne-content">
            <p class="desc-text">{{ consigneText }}</p>
          </div>
        </div>

        <div class="info-card avancement-card">
          <div class="circular-progress-container">
            <svg class="progress-ring" width="220" height="220">
              <circle class="progress-ring-track" stroke-width="16" fill="transparent" :r="radius" cx="110" cy="110" />
              <circle class="progress-ring-circle student-ring" stroke-width="16" fill="transparent" :r="radius" cx="110" cy="110" :stroke-dasharray="circumference" :stroke-dashoffset="strokeDashoffset" />
            </svg>
            <div class="progress-text">
              <span class="progress-label-top">Temps restant</span>
              <span class="progress-value-main">{{ timeRemaining }}</span> 
            </div>
          </div>
        </div>

      </div>
    </div>

    <Teleport to="body">
      
      <div v-if="modals.annonceView" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content medium-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <div class="annonce-header-modal" style="margin-bottom: 0;">
            <span class="item-icon announcement-icon">
              <img :src="iconAnnonces" alt="Annonce" class="action-icon" style="width: 24px; height: 24px;"/>
            </span>
            <div>
              <h4>{{ selectedAnnonce?.title }}</h4>
              <span class="item-author">{{ selectedAnnonce?.author }} - Le {{ selectedAnnonce?.date }} à {{ selectedAnnonce?.time }}</span>
            </div>
          </div>
          <div style="padding: 20px; background: var(--bg-secondaire); border-radius: 8px; margin-top: 20px;">
            <p style="white-space: pre-wrap; line-height: 1.6; color: var(--texte-principal); margin: 0;">{{ selectedAnnonce?.content }}</p>
          </div>
        </div>
      </div>

      <div v-if="modals.ressourceView" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content medium-modal" style="text-align: center;">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <h3>Visualisation du document</h3>
          
          <div style="padding: 40px; background: var(--bg-secondaire); border-radius: 8px; margin: 20px 0;">
            <img :src="iconDocument" alt="Document" style="width: 64px; height: 64px; margin-bottom: 15px; opacity: 0.6;" />
            <h4>{{ selectedRessource?.title }}</h4>
            <p style="color: #666; font-size: 0.9rem;">Déposé par : {{ selectedRessource?.author }}</p>
          </div>
          
          <button class="icon-btn blue-btn" style="margin: 0 auto; display: flex; align-items: center; gap: 10px; width: auto; padding: 10px 20px;" @click="alert('Téléchargement simulé !')">
            <img :src="iconDownload" alt="Télécharger" class="action-icon" style="filter: brightness(0) invert(1);" /> 
            Télécharger le fichier
          </button>
        </div>
      </div>

      <div v-if="modals.renduUpload" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content small-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <h3>Déposer un travail</h3>
          <p style="font-size: 0.9rem; color: #666; margin-bottom: 20px;">Dépôt pour : <strong>{{ currentRenduTarget?.title }}</strong></p>
          
          <input type="file" class="form-input mb-10" />
          <button class="validate-btn outline mt-15" @click="simulateUpload">Envoyer mon travail</button>
        </div>
      </div>

    </Teleport>
  </div>
</template>

<style scoped>
  @import '../css/StudentSAE_Detail.css';
</style>