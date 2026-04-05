<template>
  <div class="dashboard-layout">

    <main class="main-content" id="main-content-area">
        
      <div class="content-area" role="main">
        
        <div class="page-header">
          <h1>Bienvenue Annissa 👋 <span class="breadcrumb" aria-hidden="true">Page / Tableau De Bord</span></h1>
        </div>
        <hr class="divider" />

        <div class="filters-toolbar">
          <select v-model="filtreSemestre" class="filter-select" aria-label="Filtrer par semestre" @change="changementSemestre">
            <option value="Par semestre">Par semestre</option>
            <option value="S1">Semestre 1</option>
            <option value="S2">Semestre 2</option>
            <option value="S3">Semestre 3</option>
            <option value="S4">Semestre 4</option>
            <option value="S5">Semestre 5</option>
            <option value="S6">Semestre 6</option>
          </select>
        </div>

        <div class="timeline" aria-label="Calendrier">
          <div class="calendar-container">
            <div class="months-container">
              <div class="months" role="tablist">
                <button 
                  v-for="mois in listMois" 
                  :key="mois.id"
                  @click="selectionnerMois(mois.nom)"
                  :class="{ 'active-month': moisSelectionne === mois.nom }"
                  class="clickable month-pill"
                  role="tab"
                  :aria-selected="moisSelectionne === mois.nom"
                >
                  {{ mois.nom }} {{ mois.annee }}
                </button>
              </div>
            </div>
            
            <div class="days-container">
              <div class="days">
                <button 
                  v-for="jour in joursDuMois" 
                  :key="jour"
                  @click="selectionnerJour(jour)"
                  :class="{ 'active-day': jourSelectionne === jour }"
                  class="clickable day-circle"
                  :aria-label="`Sélectionner le jour ${jour}`"
                  :aria-pressed="jourSelectionne === jour"
                >
                  {{ jour }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="dashboard-grid">
          <section class="main-column">
            <h2>SAE En Cours ({{ saesEnCours.length }})</h2>
            
            <div 
              class="sae-cards-container" 
              ref="saeContainer"
              @mousedown="startDrag" 
              @mouseleave="stopDrag" 
              @mouseup="stopDrag" 
              @mousemove="drag"
              :class="{ 'is-dragging': isDragging }"
            >
              <article v-for="sae in saesEnCours" :key="sae.id" class="sae-card" tabindex="0" @click="naviguerVersSAE(sae)">
                <div class="sae-gradient" :style="{ background: sae.color }" aria-hidden="true"></div>
                <div class="sae-card-body">
                  <h3 class="sae-title">{{ sae.titre }}</h3>
                  <div class="sae-info">
                    <span aria-label="Commentaires"><span class="icon">💬</span> {{ sae.commentaires || sae.notifs || 0 }}</span>
                    <span aria-label="Date de rendu"><span class="icon">📅</span> {{ sae.date || sae.dateStr }}</span>
                  </div>
                </div>
              </article>
              
              <p v-if="saesEnCours.length === 0" class="no-sae" style="color: #6b7280; font-style: italic; margin-top: 1rem; padding-left: 0.5rem;">Aucune SAE arrivant à échéance pour cette date.</p>
            </div>
          </section>

          <aside class="side-column">
            <h2>Annonces</h2>
            <div class="annonces-container">
              <div v-for="annonce in annoncesDynamiques" :key="annonce.id" class="annonce-card clickable" @click="naviguerVersSAEDepuisAnnonce(annonce.saeRef)">
                <div class="annonce-icon" :class="'icon-' + annonce.type">
                  {{ getAnnonceIcon(annonce.type) }}
                </div>
                <div class="annonce-content">
                  <h4>{{ annonce.titre }}</h4>
                  <div class="annonce-meta">
                    <span class="sae-ref">{{ annonce.saeRef }}</span>
                    <span class="annonce-date">{{ annonce.date }}</span>
                  </div>
                </div>
              </div>
            </div>
          </aside>
        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAccessibiliteStore } from '@/stores/accessibilite';

const accessStore = useAccessibiliteStore();
const router = useRouter();

// --- CALENDRIER DYNAMIQUE (SYNCHRO PC) ---
const listMois = ref([]);
const moisSelectionne = ref('');
const jourSelectionne = ref(1);
const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];

const genererListeMois = () => {
  const dateInitiale = new Date();
  const moisGeneres = [];
  for (let i = 0; i < 12; i++) {
    const d = new Date(dateInitiale.getFullYear(), dateInitiale.getMonth() + i, 1);
    moisGeneres.push({
      id: i + 1,
      nom: nomsMoisGlobaux[d.getMonth()],
      annee: d.getFullYear().toString()
    });
  }
  return moisGeneres;
};

const joursDuMois = computed(() => {
  const moisTrouve = listMois.value.find(m => m.nom === moisSelectionne.value);
  const annee = moisTrouve ? parseInt(moisTrouve.annee) : new Date().getFullYear();
  const indexMois = nomsMoisGlobaux.indexOf(moisSelectionne.value);
  const nbJours = new Date(annee, indexMois + 1, 0).getDate();
  return Array.from({ length: nbJours }, (_, i) => i + 1);
});

const selectionnerMois = (mois) => { 
  moisSelectionne.value = mois;
  accessStore.lireTexte(`Calendrier mis à jour pour ${mois}`);
};
const selectionnerJour = (jour) => { 
  jourSelectionne.value = jour;
  accessStore.lireTexte(`Jour ${jour} sélectionné`);
};

// --- FILTRE SEMESTRE SYNCHRONISÉ ---
const filtreSemestre = ref('Par semestre');

const changementSemestre = () => {
  accessStore.lireTexte(`Filtre modifié sur ${filtreSemestre.value}`);
  
  if (['S1', 'S3', 'S5'].includes(filtreSemestre.value)) {
    selectionnerMois('Septembre');
  } else if (['S2', 'S4', 'S6'].includes(filtreSemestre.value)) {
    selectionnerMois('Janvier');
  }
};

// --- DRAG TO SCROLL (SWIPE HORIZONTAL) AVEC FIX POUR LE CLIC ---
const saeContainer = ref(null);
const isDragging = ref(false);
let isMouseDown = false;
let startX = 0;
let scrollLeft = 0;

const startDrag = (e) => {
  isMouseDown = true;
  isDragging.value = false; // Ne pas bloquer le clic immédiatement
  startX = e.pageX - saeContainer.value.offsetLeft;
  scrollLeft = saeContainer.value.scrollLeft;
};
const stopDrag = () => {
  isMouseDown = false;
  // Délai très court pour laisser passer l'événement click si on n'a pas bougé
  setTimeout(() => { isDragging.value = false; }, 10);
};
const drag = (e) => {
  if (!isMouseDown) return;
  
  const x = e.pageX - saeContainer.value.offsetLeft;
  const walk = (x - startX) * 2; // Multiplicateur de vitesse de scroll
  
  // Différencier un simple clic d'un "drag" intentionnel (> 5px de mouvement)
  if (Math.abs(walk) > 5) {
    isDragging.value = true;
  }

  if (isDragging.value) {
    e.preventDefault();
    saeContainer.value.scrollLeft = scrollLeft - walk;
  }
};

// --- DONNÉES SAE SYNCHRONISÉES ---
const baseSaes = ref([]);

// Propriété calculée pour filtrer les SAE en fonction du calendrier
const saesEnCours = computed(() => {
  return baseSaes.value.filter(sae => sae.mois === moisSelectionne.value && sae.jour === jourSelectionne.value);
});


// --- ANNONCES DYNAMIQUES (Liées au calendrier) ---
const annoncesDynamiques = computed(() => {
  let moisAbrev = moisSelectionne.value ? moisSelectionne.value.substring(0, 3).toLowerCase() + '.' : '';
  if (moisSelectionne.value === 'Fevrier') moisAbrev = 'févr.';
  
  const dateJour = jourSelectionne.value;
  const echeance = dateJour <= 26 ? dateJour + 2 : 1; 

  return [
    { id: 1, type: 'warning', titre: 'Rappel Échéance SAE Design', saeRef: 'SAE 311', date: `${echeance} ${moisAbrev}` },
    { id: 2, type: 'info', titre: 'Ressources Disponibles', saeRef: 'SAE 316', date: `${dateJour} ${moisAbrev}` }
  ];
});

const getAnnonceIcon = (type) => type === 'warning' ? '🔔' : (type === 'info' ? 'ℹ️' : '⚠️');

// --- NAVIGATION VERS VUE DETAILLÉE ---
const naviguerVersSAE = (sae) => {
  router.push('/etudiant/sae/' + sae.id);
};

const naviguerVersSAEDepuisAnnonce = (saeRef) => {
  const saeId = saeRef.replace(/\D/g, ''); 
  if (saeId) {
    router.push('/etudiant/sae/' + saeId);
  }
};

// --- SYNCHRONISATION DATE & VOCAL ENRICHIE ---
onMounted(() => {
  const now = new Date();
  listMois.value = genererListeMois();
  moisSelectionne.value = nomsMoisGlobaux[now.getMonth()];
  jourSelectionne.value = now.getDate();
  
  const moisAbrev = moisSelectionne.value.substring(0, 3);
  const dateActuelleStr = `${moisAbrev} ${now.getDate()}`;

  // Récupération de la BDD du local storage (pour se synchroniser avec l'Admin)
  const stored = localStorage.getItem('mmi_data_sae_v2');
  if (stored) {
    baseSaes.value = JSON.parse(stored);
  } else {
    // S'il n'y a pas de LocalStorage, on génère un mock par défaut
    // J'inclus une SAE assignée à la date d'aujourd'hui pour que l'écran ne soit pas vide à l'ouverture
    baseSaes.value = [
      { id: 316, titre: 'SAE 316', color: 'linear-gradient(135deg, #e879f9, #818cf8)', commentaires: 8, mois: moisSelectionne.value, jour: jourSelectionne.value, dateStr: dateActuelleStr },
      { id: 304, titre: 'SAE 304', color: 'linear-gradient(135deg, #1e3a8a, #818cf8)', commentaires: 8, mois: 'Octobre', jour: 15, dateStr: 'Oct 15' },
      { id: 311, titre: 'SAE 311', color: 'linear-gradient(135deg, #7f1d1d, #14532d)', commentaires: 2, mois: 'Decembre', jour: 12, dateStr: 'Dec 12' }
    ];
  }
});

// Utilisation du store d'accessibilité centralisé
watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    const nbSae = saesEnCours.value.length;
    const nomsSae = saesEnCours.value.map(s => s.titre).join(' et ');
    const nbAnnonces = annoncesDynamiques.value.length;
    const nomsAnnonces = annoncesDynamiques.value.map(a => a.titre).join(' puis ');

    let texteALire = `Bonjour Annissa. Bienvenue sur votre tableau de bord étudiant. `;
    texteALire += `Le calendrier est actuellement positionné sur le ${jourSelectionne.value} ${moisSelectionne.value}. `;
    
    if (filtreSemestre.value !== 'Par semestre') {
        texteALire += `Le filtre affiche actuellement les données du ${filtreSemestre.value}. `;
    }

    if (nbSae === 0) {
        texteALire += `Vous n'avez aucune S A E arrivant à échéance pour cette date. `;
    } else {
        texteALire += `Vous avez ${nbSae} S A E en cours pour cette date, qui sont : ${nomsSae}. `;
    }

    if (nbAnnonces > 0) {
        texteALire += `Vous avez également ${nbAnnonces} annonces importantes à lire : ${nomsAnnonces}. `;
    }

    texteALire += `Vous pouvez naviguer vers ces éléments ou utiliser le menu latéral à gauche.`;

    accessStore.lireTexte(texteALire);
  }
});
</script>

<style scoped src="../css/StudentDashboard.css"></style>