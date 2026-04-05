<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAccessibiliteStore } from '@/stores/accessibilite' // Ajout du store accessibilité

import iconAjoutDossier from '@/assets/icon_dossierAjout.svg'
import iconAgraffe from '@/assets/icon_agraffe.svg'
import iconCalendrier from '@/assets/icon_calendar_today.svg'
import iconTime from '@/assets/icon_access_time.svg'
import iconAnnonces from '@/assets/icon_meeting.svg'
import iconEdit from '@/assets/icon_modif.svg'
import iconDossier from '@/assets/icon_dossier.svg'
import iconDocument from '@/assets/icon_document.svg'
import iconDownload from '@/assets/icon_download.svg'

const route = useRoute()
const saeId = computed(() => route.params.id || '401')
const accessStore = useAccessibiliteStore()

// --- COHÉRENCE DES DATES ---
// Dictionnaire pour garantir l'exacte correspondance de la date d'échéance avec le Kanban
const saeDatesMatch = {
  '302': '2025-11-30',
  '303': '2026-11-30',
  '202': '2025-11-15',
  '203': '2026-10-20',
  '101': '2025-09-10'
}

// --- DONNÉES DE LA SAE ---
const saeData = ref({
  title: `SAE ${saeId.value}`,
  category: 'Développement Web',
  categoryColor: '#D97706',
  categoryBg: '#FEF3C7',
  description: 'Projet de fin de semestre pour valider les compétences en développement.',
  semestre: 'S3',
  deadlineDate: saeDatesMatch[saeId.value] || '2025-11-30', // Application de la correction des dates
  totalStudents: 40, 
  phasesAdded: false 
})

const deadlineDisplay = computed(() => {
  const d = new Date(saeData.value.deadlineDate)
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
})

// Modifié pour n'afficher que les jours "25 j" comme sur ta capture
const timeRemaining = computed(() => {
  const targetDate = new Date(saeData.value.deadlineDate + 'T23:59:59')
  const now = new Date()
  const diffMs = targetDate - now

  if (diffMs <= 0) return "Terminé"
  const days = Math.floor(diffMs / (1000 * 60 * 60 * 24))
  return `${days} j`
})

// --- ACCESSIBILITÉ VOCALE ---
const lireAction = (texte) => {
  accessStore.lireTexte(texte);
}

watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    let desc = `Page de détail de la ${saeData.value.title}. `;
    desc += `Date d'échéance : ${deadlineDisplay.value}. `;
    desc += `Vous pouvez modifier la description, les consignes, et gérer les ressources, annonces et le groupe de travail.`;
    lireAction(desc);
  }
}, { immediate: true });


// --- GESTION DES PHASES ---
const phases = ref([])
const editablePhases = ref([]) 

const openPhasesModal = () => {
  if (phases.value.length > 0) {
    editablePhases.value = phases.value.map(p => ({ ...p }))
  } else {
    editablePhases.value = [{ id: Date.now(), name: '', validatedCount: 0 }]
  }
  modals.value.phases = true
  lireAction(saeData.value.phasesAdded ? 'Ouverture de la fenêtre de modification des phases' : 'Ouverture de la fenêtre de définition des phases');
}

const addEditablePhase = () => {
  editablePhases.value.push({ id: Date.now(), name: '', validatedCount: 0 })
  lireAction('Nouvelle phase ajoutée au formulaire');
}

const removeEditablePhase = (index) => { 
  if (editablePhases.value.length > 1) {
    editablePhases.value.splice(index, 1)
    lireAction('Phase supprimée');
  } 
}

const validerPhases = () => {
  phases.value = editablePhases.value.filter(p => p.name.trim() !== '')
  saeData.value.phasesAdded = phases.value.length > 0
  fermerModales()
  lireAction('Phases de progression validées et sauvegardées avec succès');
}

const getPhasePercent = (phase) => {
  if (saeData.value.totalStudents === 0) return 0
  return Math.round((phase.validatedCount / saeData.value.totalStudents) * 100)
}

const globalProgress = computed(() => {
  if (!saeData.value.phasesAdded || phases.value.length === 0) return 0
  let totalPercent = 0
  phases.value.forEach(p => { totalPercent += getPhasePercent(p) })
  return Math.round(totalPercent / phases.value.length)
})

const simulerValidationEtudiant = (phase) => {
  if (phase.validatedCount < saeData.value.totalStudents) {
    phase.validatedCount++
    lireAction(`Un étudiant a validé la phase ${phase.name}. Avancement actuel: ${phase.validatedCount} sur ${saeData.value.totalStudents}`);
  }
}

// Cercle agrandi pour faire rentrer le texte (Rayon de 85 au lieu de 60)
const radius = 85
const circumference = 2 * Math.PI * radius
const strokeDashoffset = computed(() => {
  return circumference - (globalProgress.value / 100) * circumference
})

// --- LISTES ---
const ressources = ref([
  { id: 1, title: 'Charte graphique', author: 'PAR VOUS', date: '21 Juin', time: '14:23', status: 'unread' },
  { id: 2, title: 'Maquette Figma', author: 'PAR VOUS', date: '22 Juin', time: '09:00', status: 'read' },
])

const annonces = ref([
  { id: 1, title: 'Annonce Du 12/03/2026', author: 'PAR VOUS', date: '12 Mars', time: '14:23', content: "Contenu de l'annonce détaillée ici...\n\nIl est très important de lire ce message." },
  { id: 2, title: 'Annonce Du 10/03/2026', author: 'PAR VOUS', date: '10 Mars', time: '09:00', content: "N'oubliez pas vos rendus pour demain !" }
])

const rendus = ref([
  { id: 1, title: 'Maquette', count: 12 },
  { id: 2, title: 'Code Source', count: 12 }
])

const consigneText = ref("Voici les consignes pour cette SAE...")

// --- MODALES ---
const modals = ref({
  desc: false, consigne: false, annonce: false, renduAdd: false, renduView: false, 
  phases: false, ressourceAdd: false, ressourceView: false, annonceView: false
})

const fermerModales = () => { 
  for (let key in modals.value) { modals.value[key] = false } 
  lireAction('Fenêtre fermée');
}

// LOGIQUE D'ANNONCE 
const newAnnonce = ref('')
const validerAnnonce = () => {
  if (newAnnonce.value.trim() === '') return
  annonces.value.unshift({
    id: Date.now(), title: `Annonce Du ${new Date().toLocaleDateString('fr-FR')}`, author: 'PAR VOUS',
    date: new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }),
    time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }), content: newAnnonce.value
  })
  newAnnonce.value = ''
  fermerModales()
  lireAction('Annonce publiée avec succès');
}

const selectedAnnonce = ref(null)
const openAnnonceView = (ann) => {
  selectedAnnonce.value = ann
  modals.value.annonceView = true
  lireAction(`Lecture de l'annonce : ${ann.title}`);
}

// LOGIQUE DE RESSOURCE
const newRessourceTitle = ref('')
const validerAjoutRessource = () => {
  if (newRessourceTitle.value.trim() === '') return
  ressources.value.unshift({
    id: Date.now(), title: newRessourceTitle.value, author: 'PAR VOUS',
    date: new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }),
    time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }), status: 'read'
  })
  newRessourceTitle.value = ''; fermerModales()
  lireAction('Nouvelle ressource ajoutée avec succès');
}

const selectedRessource = ref(null)
const openRessourceView = (res) => { 
  res.status = 'read'; 
  selectedRessource.value = res; 
  modals.value.ressourceView = true 
  lireAction(`Visualisation de la ressource : ${res.title}`);
}

// AUTRES LOGIQUES
const editDesc = ref({ description: saeData.value.description, semestre: saeData.value.semestre, deadlineDate: saeData.value.deadlineDate })
const validerDesc = () => { 
  saeData.value.description = editDesc.value.description; 
  saeData.value.semestre = editDesc.value.semestre; 
  saeData.value.deadlineDate = editDesc.value.deadlineDate; 
  fermerModales();
  lireAction("Description du projet mise à jour avec succès");
}

const editConsigne = ref(consigneText.value)
const validerConsigne = () => { 
  consigneText.value = editConsigne.value; 
  fermerModales();
  lireAction("Consignes de la S A E mises à jour avec succès");
}

const newRenduTitle = ref('')
const validerRendu = () => { 
  if (newRenduTitle.value.trim() === '') return; 
  rendus.value.push({ id: Date.now(), title: newRenduTitle.value, count: 0 }); 
  newRenduTitle.value = ''; 
  fermerModales();
  lireAction('Nouvel espace de rendu créé avec succès');
}

const groupeNom = ref('')
const etudiantsInputs = ref(['']) 
const addEtudiantInput = () => {
  etudiantsInputs.value.push('')
  lireAction("Champ ajouté pour un nouvel étudiant");
}

const validerGroupe = () => { 
  alert("Groupe validé avec succès !") 
  lireAction("Groupe de travail et étudiants assignés avec succès");
}
</script>

<template>
  <div class="sae-detail-container">
    
    <header class="page-header">
      <div class="title-section">
        <h2 tabindex="0" @focus="lireAction('Bienvenue Monsieur FDZAFE')">Bienvenue Monsieur FDZAFE 👋</h2>
        <span class="breadcrumb" tabindex="0" @focus="lireAction('Vous êtes sur la page ' + saeData.title)">Page / Mes SAE / <strong>{{ saeData.title }}</strong></span>
      </div>
      <hr class="separator" />
    </header>

    <div class="dashboard-grid">
      
      <div class="main-column">
        
        <div class="banner-card">
          <div class="banner-content">
            <div class="banner-top-row">
              <span class="category-tag" tabindex="0" @focus="lireAction('Catégorie : ' + saeData.category)" :style="{ color: saeData.categoryColor, backgroundColor: saeData.categoryBg }">{{ saeData.category }}</span>
            </div>
            
            <h1 class="sae-main-title" tabindex="0" @focus="lireAction('Titre : ' + saeData.title)">{{ saeData.title }}</h1>

            <button v-if="!saeData.phasesAdded" @click="openPhasesModal" class="action-btn orange-btn banner-btn" @mouseenter="lireAction('Définir les phases de progression')" @focus="lireAction('Bouton Définir les phases de progression')">
              <span class="icon">+</span> Définir les phases de progression
            </button>
            <button v-else @click="openPhasesModal" class="action-btn orange-btn banner-btn" @mouseenter="lireAction('Modifier les phases de progression')" @focus="lireAction('Bouton Modifier les phases')">
              <span class="icon">✎</span> Modifier les phases
            </button>

            <div v-if="saeData.phasesAdded" class="phases-container">
              <div v-for="phase in phases" :key="phase.id" class="phase-item">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                  <span class="phase-name" tabindex="0" @focus="lireAction(`Phase ${phase.name}, validée par ${phase.validatedCount} étudiants sur ${saeData.totalStudents}`)">{{ phase.name }} <small>({{ phase.validatedCount }} / {{ saeData.totalStudents }} étudiants)</small></span>
                  <button @click="simulerValidationEtudiant(phase)" class="icon-btn blue-btn" style="width: 24px; height: 24px; padding: 0;" title="Simuler validation d'un étudiant">+</button>
                </div>
                
                <div class="progress-bar-bg" style="margin-top: 5px;">
                  <div class="progress-bar-fill" :style="{ width: getPhasePercent(phase) + '%', backgroundColor: getPhasePercent(phase) > 0 ? '#F59E0B' : 'transparent' }"></div>
                </div>
                <span class="phase-percent">{{ getPhasePercent(phase) }}%</span>
              </div>
            </div>
          </div>
        </div>

        <div class="split-row">
          
          <div class="info-card">
            <div class="card-header">
              <h3 tabindex="0" @focus="lireAction('Section Ressources')">Ressources</h3>
              <button class="icon-btn green-btn" @click="modals.ressourceAdd = true; lireAction('Ouverture du formulaire d\'ajout de ressource')" @mouseenter="lireAction('Ajouter une ressource')" @focus="lireAction('Bouton Ajouter une ressource')">
                <img :src="iconAjoutDossier" alt="Icon AjoutDossier" class="action-icon" />
              </button>
            </div>
            <ul class="item-list">
              <li v-for="res in ressources" :key="res.id">
                <button class="item-icon agrafe-btn" :class="{ 'unread': res.status === 'unread' }" @click="openRessourceView(res)" @mouseenter="lireAction(`Ressource : ${res.title}. Cliquez pour visualiser.`)" @focus="lireAction(`Ressource : ${res.title}. Appuyez sur Entrée pour visualiser.`)">
                  <img :src="iconAgraffe" alt="Icon Agraffe" class="action-icon" />
                </button>
                <div class="item-details">
                  <span class="item-title">{{ res.title }}</span>
                  <span class="item-author">{{ res.author }}</span>
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
              <h3 tabindex="0" @focus="lireAction('Section Annonces')">Annonces</h3>
              <button class="icon-btn orange-btn" @click="modals.annonce = true; lireAction('Ouverture du formulaire de création d\'annonce')" @mouseenter="lireAction('Ajouter une annonce')" @focus="lireAction('Bouton Ajouter une annonce')">+</button>
            </div>
            <ul class="item-list">
              <li v-for="ann in annonces" :key="ann.id">
                <button class="item-icon agrafe-btn" @click="openAnnonceView(ann)" @mouseenter="lireAction(`Annonce : ${ann.title}. Cliquez pour lire.`)" @focus="lireAction(`Annonce : ${ann.title}. Appuyez sur Entrée pour lire.`)">
                  <img :src="iconAnnonces" alt="Icon Notif" class="action-icon" />
                </button>
                <div class="item-details">
                  <span class="item-title">{{ ann.title }}</span>
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

        <div class="split-row">
          <div class="info-card rendus-card">
            <div class="card-header">
              <h3 tabindex="0" @focus="lireAction('Section Rendus')">Rendus</h3>
            </div>
            <ul class="item-list">
              <li v-for="rendu in rendus" :key="rendu.id" class="rendu-item">
                <div class="rendu-left">
                  <div class="file-icon folder">
                    <img :src="iconDossier" alt="Dossier" class="action-icon" style="width: 24px; height: 24px; opacity: 0.8;" />
                  </div>
                  <span class="item-title">{{ rendu.title }}</span>
                </div>
                <div class="rendu-right">
                  <button class="icon-btn blue-btn" @click="modals.renduView = true; lireAction(`Ouverture de l'espace de rendu ${rendu.title}`)" @mouseenter="lireAction(`Voir l'espace de rendu ${rendu.title}`)" @focus="lireAction(`Bouton pour voir l'espace de rendu ${rendu.title}`)">
                    <img :src="iconDocument" alt="Voir Rendu" class="action-icon" style="width: 18px; height: 18px; filter: brightness(0) saturate(100%) invert(32%) sepia(85%) saturate(1912%) hue-rotate(209deg) brightness(97%) contrast(93%);" />
                  </button>
                  <span class="score-badge" tabindex="0" @focus="lireAction(`${rendu.count} fichiers rendus`)">{{ rendu.count }}</span>
                </div>
              </li>
            </ul>
          </div>

          <div class="info-card groupe-card">
            <h3 tabindex="0" @focus="lireAction('Section Groupe de travail')">Groupe de travail</h3>
            <div class="groupe-form">
              <input type="text" v-model="groupeNom" placeholder="Groupe ....." class="form-input" @focus="lireAction('Champ de saisie pour le nom du groupe')" />
              <div v-for="(etud, index) in etudiantsInputs" :key="index" class="input-with-btn">
                <input type="text" v-model="etudiantsInputs[index]" placeholder="N° étudiant ....." class="form-input" @focus="lireAction(`Champ de saisie Numéro étudiant ${index + 1}`)" />
                <button v-if="index === etudiantsInputs.length - 1" @click="addEtudiantInput" class="icon-btn orange-btn small-btn" @mouseenter="lireAction('Ajouter un champ étudiant')" @focus="lireAction('Bouton Ajouter un champ étudiant')">+</button>
              </div>
              <button class="validate-btn" @click="validerGroupe" @mouseenter="lireAction('Valider le groupe de travail')" @focus="lireAction('Bouton Valider le groupe')">Valider</button>
            </div>
          </div>
        </div>

      </div>

      <div class="side-column">
        
        <div class="info-card desc-card">
          <div class="card-header">
            <h3 tabindex="0" @focus="lireAction('Section Description du projet')">Description du projet</h3>
            <button class="icon-btn orange-btn" @click="modals.desc = true; lireAction('Ouverture du formulaire de modification de la description')" @mouseenter="lireAction('Modifier la description')" @focus="lireAction('Bouton Modifier la description')">
              <img :src="iconEdit" alt="Modifier" class="action-icon" style="width: 18px; height: 18px; filter: brightness(0) invert(1);" />
            </button>
          </div>
          <p class="desc-text" tabindex="0" @focus="lireAction(saeData.description)">{{ saeData.description }}</p>
          <div class="desc-meta">
            <p tabindex="0" @focus="lireAction('Semestre affecté : ' + saeData.semestre)"><span>Semestre:</span> {{ saeData.semestre }}</p>
            <p tabindex="0" @focus="lireAction('Date d\'échéance : ' + deadlineDisplay)"><span>Date d'échéance:</span> {{ deadlineDisplay }}</p>
          </div>
        </div>

        <div class="info-card consigne-card">
          <div class="card-header">
            <h3 tabindex="0" @focus="lireAction('Section Consigne')">Consigne</h3>
            <button class="icon-btn orange-btn" @click="modals.consigne = true; lireAction('Ouverture du formulaire de modification de consigne')" @mouseenter="lireAction('Modifier la consigne')" @focus="lireAction('Bouton Modifier la consigne')">
               <img :src="iconEdit" alt="Modifier" class="action-icon" style="width: 18px; height: 18px; filter: brightness(0) invert(1);" />
            </button>
          </div>
          <div class="consigne-content">
            <p class="desc-text" tabindex="0" @focus="lireAction(consigneText)">{{ consigneText }}</p>
          </div>
        </div>

        <div class="info-card avancement-card">
          <div class="circular-progress-container" tabindex="0" @focus="lireAction(`Avancement global ${globalProgress} pour cent. Temps restant ${timeRemaining}.`)">
            <svg class="progress-ring" width="220" height="220">
              <circle class="progress-ring-track" stroke-width="16" fill="transparent" :r="radius" cx="110" cy="110" />
              <circle class="progress-ring-circle" stroke-width="16" fill="transparent" :r="radius" cx="110" cy="110" :stroke-dasharray="circumference" :stroke-dashoffset="strokeDashoffset" />
            </svg>
            <div class="progress-text">
              <span class="progress-label-top">Avancement</span>
              <span class="progress-value-main">{{ globalProgress }} %</span>
              <span class="progress-label-bottom">Temps restant</span>
              <span class="progress-value-sub">{{ timeRemaining }}</span>
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

      <div v-if="modals.annonce" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content annonce-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <div class="annonce-header-modal">
            <span class="item-icon announcement-icon">
              <img :src="iconAnnonces" alt="Annonce" class="action-icon" style="width: 24px; height: 24px;"/>
            </span>
            <div>
              <h4>Annonce Du {{ new Date().toLocaleDateString('fr-FR') }}</h4>
              <span class="item-author">PAR VOUS - {{ new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'}) }}</span>
            </div>
          </div>
          <textarea v-model="newAnnonce" placeholder="Contenu de l'annonce....." class="form-textarea hidden-borders"></textarea>
          <div class="annonce-footer">
            <button class="send-btn green-bg" @click="validerAnnonce">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
            </button>
          </div>
        </div>
      </div>

      <div v-if="modals.ressourceAdd" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content small-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <h3>Ajouter une ressource</h3>
          <input type="text" v-model="newRessourceTitle" placeholder="Nom du fichier..." class="form-input rounded-input mb-10" />
          <input type="file" class="form-input mb-10" />
          <button class="validate-btn outline mt-15" @click="validerAjoutRessource">Uploader</button>
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
            <p style="color: #666; font-size: 0.9rem;">Le {{ selectedRessource?.date }} à {{ selectedRessource?.time }}</p>
          </div>
          
          <button class="icon-btn blue-btn" style="margin: 0 auto; display: flex; align-items: center; gap: 10px; width: auto; padding: 10px 20px;" @click="alert('Téléchargement simulé !')">
            <img :src="iconDownload" alt="Télécharger" class="action-icon" style="filter: brightness(0) invert(1);" /> 
            Télécharger le fichier
          </button>
        </div>
      </div>

      <div v-if="modals.phases" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content small-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <h3>{{ saeData.phasesAdded ? 'Modifier les phases' : 'Ajouter des phases' }}</h3>
          <div class="phases-form-list">
            <div v-for="(phase, index) in editablePhases" :key="phase.id" class="phase-input-row" style="margin-bottom: 10px;">
              <input type="text" v-model="phase.name" :placeholder="`Intitulé de phase ${index + 1}....`" class="form-input rounded-input" />
              <div class="phase-actions" style="display:flex; gap:5px;">
                <button v-if="index === editablePhases.length - 1" class="icon-btn orange-btn" @click="addEditablePhase">+</button>
                <button class="icon-btn blue-btn" @click="removeEditablePhase(index)">-</button>
              </div>
            </div>
          </div>
          <button class="validate-btn outline mt-15" @click="validerPhases">Valider les phases</button>
        </div>
      </div>

      <div v-if="modals.renduView" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content medium-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <h3>Fichiers Rendus</h3>
          <ul class="item-list">
            <li>
              <div class="item-details"><span class="item-title">Rapport_Final_GroupeA.pdf</span></div>
              <button class="icon-btn blue-btn">
                <img :src="iconDownload" alt="Télécharger" class="action-icon" style="width: 18px; height: 18px; filter: brightness(0) saturate(100%) invert(32%) sepia(85%) saturate(1912%) hue-rotate(209deg) brightness(97%) contrast(93%);" />
              </button>
            </li>
            <li>
              <div class="item-details"><span class="item-title">Maquettes_Figma.zip</span></div>
              <button class="icon-btn blue-btn">
                 <img :src="iconDownload" alt="Télécharger" class="action-icon" style="width: 18px; height: 18px; filter: brightness(0) saturate(100%) invert(32%) sepia(85%) saturate(1912%) hue-rotate(209deg) brightness(97%) contrast(93%);" />
              </button>
            </li>
          </ul>
        </div>
      </div>

      <div v-if="modals.renduAdd" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content small-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <h3>Créer un espace de rendu</h3>
          <input type="text" v-model="newRenduTitle" placeholder="Nom du rendu..." class="form-input rounded-input" />
          <button class="validate-btn outline mt-15" @click="validerRendu">Valider</button>
        </div>
      </div>

      <div v-if="modals.consigne" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content medium-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <h3>Modifier les consignes</h3>
          <textarea v-model="editConsigne" class="form-textarea" rows="5"></textarea>
          <button class="validate-btn outline mt-15" @click="validerConsigne">Valider</button>
        </div>
      </div>

      <div v-if="modals.desc" class="modal-overlay" @click.self="fermerModales">
        <div class="modal-content medium-modal">
          <button class="close-btn-black" @click="fermerModales">✕</button>
          <h3>Modifier la description</h3>
          <input type="text" v-model="editDesc.semestre" placeholder="Semestre" class="form-input mb-10" />
          <input type="date" v-model="editDesc.deadlineDate" class="form-input mb-10" />
          <textarea v-model="editDesc.description" class="form-textarea" rows="4"></textarea>
          <button class="validate-btn outline mt-15" @click="validerDesc">Valider</button>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
  @import '../css/EnseignantSAEDetailView.css';
</style>