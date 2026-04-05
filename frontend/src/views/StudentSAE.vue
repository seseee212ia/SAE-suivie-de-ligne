<template>
  <div class="dashboard-layout">

    <main class="main-content" id="main-content-area">

      <div class="content-area" role="main">
        
        <div class="page-header">
          <h1>Bienvenue Annissa 👋 <span class="breadcrumb" aria-hidden="true">Page / Mes SAE</span></h1>
        </div>
        <hr class="divider" />

        <div class="filters-toolbar">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Rechercher par mot clé..." 
            class="filter-select flex-1" 
            aria-label="Rechercher par mot-clé"
            @input="accessStore.lireTexte('Recherche en cours')"
          />
          <select v-model="filtreSemestre" aria-label="Filtrer par semestre" class="filter-select" @change="changementSemestre">
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
                >
                  {{ jour }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <div 
          class="kanban-board" 
          ref="kanbanContainer"
          @mousedown="startDrag" 
          @mouseleave="stopDrag" 
          @mouseup="stopDrag" 
          @mousemove="drag"
          :class="{ 'is-dragging': isDragging }"
        >
          
          <div class="kanban-column">
            <div class="column-header">
              <div class="col-title-wrap">
                <span class="dot" style="background-color: #4C6FFF;"></span>
                <h3 class="col-title">A faire</h3>
                <span class="badge">{{ todoSaes.length }}</span>
              </div>
            </div>
            <div class="col-line" style="background-color: #4C6FFF;"></div>
            
            <div class="cards-list">
              <article 
                v-for="sae in todoSaes" 
                :key="sae.id" 
                class="sae-task-card" 
                :class="{'is-dragging-card': sae.tilted}" 
                @click="naviguerVersSAE(sae)"
                tabindex="0"
              >
                <div class="card-top">
                  <span class="category-tag" :style="{ color: sae.categoryColor, backgroundColor: sae.categoryBg }">{{ sae.tag }}</span>
                  <span class="more-options" @click.stop>•••</span>
                </div>
                
                <h4 class="card-title">{{ sae.titre }}</h4>
                <p v-if="sae.description" class="card-desc">{{ sae.description }}</p>
                
                <div class="card-footer">
                  <span class="meta-item"><img :src="iconMeeting" alt="Commentaires" class="small-icon" /> {{ sae.commentaires || sae.notifs || 0 }}</span>
                  <span class="meta-item"><img :src="iconCalendar" alt="Date" class="small-icon" /> {{ sae.date || sae.dateStr }}</span>
                </div>
              </article>
            </div>
          </div>

          <div class="kanban-column">
            <div class="column-header">
              <div class="col-title-wrap">
                <span class="dot" style="background-color: #F59E0B;"></span>
                <h3 class="col-title">En cours</h3>
                <span class="badge">{{ inProgressSaes.length }}</span>
              </div>
            </div>
            <div class="col-line" style="background-color: #FBBF24;"></div>
            
            <div class="cards-list">
              <article v-for="sae in inProgressSaes" :key="sae.id" class="sae-task-card" @click="naviguerVersSAE(sae)" tabindex="0">
                <div class="card-top">
                  <span class="category-tag" :style="{ color: sae.categoryColor, backgroundColor: sae.categoryBg }">{{ sae.tag }}</span>
                  <span class="more-options" @click.stop>•••</span>
                </div>
                
                <h4 class="card-title">{{ sae.titre }}</h4>
                <p v-if="sae.description" class="card-desc">{{ sae.description }}</p>
                
                <div v-if="sae.image" class="img-placeholder flower-img"></div>
                
                <div class="card-footer">
                  <span class="meta-item"><img :src="iconMeeting" alt="Commentaires" class="small-icon" /> {{ sae.commentaires || sae.notifs || 0 }}</span>
                  <span class="meta-item"><img :src="iconCalendar" alt="Date" class="small-icon" /> {{ sae.date || sae.dateStr }}</span>
                </div>
              </article>
            </div>
          </div>

          <div class="kanban-column">
            <div class="column-header">
              <div class="col-title-wrap">
                <span class="dot" style="background-color: #10B981;"></span>
                <h3 class="col-title">Finis</h3>
                <span class="badge">{{ doneSaes.length }}</span>
              </div>
            </div>
            <div class="col-line" style="background-color: #A7F3D0;"></div>
            
            <div class="cards-list">
              <article v-for="sae in doneSaes" :key="sae.id" class="sae-task-card" @click="naviguerVersSAE(sae)" tabindex="0">
                <div class="card-top">
                  <span class="category-tag" :style="{ color: sae.categoryColor, backgroundColor: sae.categoryBg }">{{ sae.tag }}</span>
                  <span class="more-options" @click.stop>•••</span>
                </div>
                
                <h4 class="card-title">{{ sae.titre }}</h4>
                <p v-if="sae.description" class="card-desc">{{ sae.description }}</p>

                <div v-if="sae.grayPlaceholder" class="img-placeholder gray-box"></div>
                
                <div class="card-footer">
                  <span class="meta-item"><img :src="iconMeeting" alt="Commentaires" class="small-icon" /> {{ sae.commentaires || sae.notifs || 0 }}</span>
                  <span class="meta-item"><img :src="iconCalendar" alt="Date" class="small-icon" /> {{ sae.date || sae.dateStr }}</span>
                </div>
              </article>
            </div>
          </div>

        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAccessibiliteStore } from '@/stores/accessibilite';

import iconMeeting from '@/assets/icon_meeting.svg';
import iconCalendar from '@/assets/icon_calendar_today.svg';

const accessStore = useAccessibiliteStore();
const router = useRouter();
const route = useRoute();

// Navigation vers la page de détail
const naviguerVersSAE = (sae) => {
  if(!isDragging.value) { // Evite le clic lors d'un drag
    router.push('/etudiant/sae/' + sae.id);
  }
};

// --- DRAG TO SCROLL POUR LE KANBAN ---
const kanbanContainer = ref(null);
const isDragging = ref(false);
let isMouseDown = false;
let startX = 0;
let scrollLeft = 0;

const startDrag = (e) => {
  isMouseDown = true;
  isDragging.value = false;
  startX = e.pageX - kanbanContainer.value.offsetLeft;
  scrollLeft = kanbanContainer.value.scrollLeft;
};
const stopDrag = () => {
  isMouseDown = false;
  setTimeout(() => { isDragging.value = false; }, 10);
};
const drag = (e) => {
  if (!isMouseDown) return;
  const x = e.pageX - kanbanContainer.value.offsetLeft;
  const walk = (x - startX) * 2; 
  if (Math.abs(walk) > 5) isDragging.value = true;
  if (isDragging.value) {
    e.preventDefault();
    kanbanContainer.value.scrollLeft = scrollLeft - walk;
  }
};


// --- GESTION RECHERCHE & FILTRES ---
const searchQuery = ref('');
const filtreSemestre = ref('Par semestre');

const changementSemestre = () => {
  accessStore.lireTexte(`Filtre modifié sur ${filtreSemestre.value}`);
  if (['S1', 'S3', 'S5'].includes(filtreSemestre.value)) {
    selectionnerMois('Septembre');
  } else if (['S2', 'S4', 'S6'].includes(filtreSemestre.value)) {
    selectionnerMois('Janvier');
  }
};

// --- CALENDRIER DYNAMIQUE ---
const listMois = ref([]);
const moisSelectionne = ref('');
const jourSelectionne = ref(1);
const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];

const genererListeMois = () => {
  const dateInitiale = new Date();
  const moisGeneres = [];
  for (let i = 0; i < 12; i++) {
    const d = new Date(dateInitiale.getFullYear(), dateInitiale.getMonth() + i, 1);
    moisGeneres.push({ id: i + 1, nom: nomsMoisGlobaux[d.getMonth()], annee: d.getFullYear().toString() });
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

// --- BASE DE DONNÉES SYNCHRONISÉE ---
const baseSaes = ref([]);

// Logique de filtrage combiné (Recherche + Semestre)
const saesFiltrees = computed(() => {
  return baseSaes.value.filter(sae => {
    const matchSearch = sae.titre.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                       (sae.filiere && sae.filiere.toLowerCase().includes(searchQuery.value.toLowerCase()));
    const matchSemestre = filtreSemestre.value === 'Par semestre' || sae.semestre === filtreSemestre.value;
    return matchSearch && matchSemestre;
  });
});

// --- LOGIQUE KANBAN RÉELLE BASÉE SUR LES DATES ---
// Utilitaire pour convertir une SAE en un objet Date JavaScript réel
const getSaeDate = (sae) => {
  const today = new Date();
  if (sae.dateEcheance && sae.dateEcheance !== "À définir") {
    const parts = sae.dateEcheance.split(' ');
    if (parts.length >= 3) {
       const day = parseInt(parts[0]);
       const monthIndex = nomsMoisGlobaux.findIndex(m => m.toLowerCase() === parts[1].toLowerCase());
       const year = parseInt(parts[2]);
       if (monthIndex !== -1 && !isNaN(day) && !isNaN(year)) {
           const d = new Date(year, monthIndex, day);
           d.setHours(0,0,0,0);
           return d;
       }
    }
  }
  const moisIndex = nomsMoisGlobaux.indexOf(sae.mois);
  const d = new Date(today.getFullYear(), moisIndex, sae.jour);
  d.setHours(0,0,0,0);
  return d;
};

// Date réelle d'aujourd'hui
const realToday = new Date();
realToday.setHours(0,0,0,0);

const inProgressSaes = computed(() => {
  return saesFiltrees.value.filter(sae => sae.mois === moisSelectionne.value && sae.jour === jourSelectionne.value);
});

const doneSaes = computed(() => {
  return saesFiltrees.value.filter(sae => {
    if (sae.mois === moisSelectionne.value && sae.jour === jourSelectionne.value) return false;
    const saeDate = getSaeDate(sae);
    return saeDate < realToday;
  });
});

const todoSaes = computed(() => {
  return saesFiltrees.value.filter(sae => {
    if (sae.mois === moisSelectionne.value && sae.jour === jourSelectionne.value) return false;
    const saeDate = getSaeDate(sae);
    return saeDate >= realToday;
  });
});

const getColorForCategory = (filiere) => {
    if (filiere === 'Développement Web') return { categoryColor: '#059669', categoryBg: '#D1FAE5' };
    if (filiere === 'Création Numérique') return { categoryColor: '#E11D48', categoryBg: '#FFE4E6' };
    if (filiere === 'Communication') return { categoryColor: '#D97706', categoryBg: '#FEF3C7' };
    return { categoryColor: '#4C6FFF', categoryBg: '#E0E7FF' };
};

// --- CHARGEMENT DES DONNÉES ---
onMounted(() => {
  const now = new Date();
  listMois.value = genererListeMois();
  moisSelectionne.value = nomsMoisGlobaux[now.getMonth()];
  jourSelectionne.value = now.getDate();

  const stored = localStorage.getItem('mmi_data_sae_v2');
  if (stored) {
    let parsedData = JSON.parse(stored);
    
    baseSaes.value = parsedData.map((sae, index) => {
      const tagColors = getColorForCategory(sae.filiere);
      return {
        ...sae,
        tag: sae.filiere || 'Générique',
        ...tagColors,
        tilted: false,
        image: index % 2 === 0, 
        grayPlaceholder: index % 2 !== 0 
      };
    });
  } else {
    const moisAbrev = moisSelectionne.value.substring(0, 3);
    const dateActuelleStr = `${moisAbrev} ${now.getDate()}`;
    
    baseSaes.value = [
      { id: 302, titre: 'SAE 302', tag: 'Développement Web', filiere: 'Développement Web', categoryColor: '#059669', categoryBg: '#D1FAE5', description: "Outil de gestion.", dateStr: dateActuelleStr, semestre: 'S3', tilted: false, mois: moisSelectionne.value, jour: jourSelectionne.value + 5 },
      { id: 303, titre: 'SAE 303', tag: 'Communication', filiere: 'Communication', categoryColor: '#E11D48', categoryBg: '#FFE4E6', description: "Stratégie de comm.", dateStr: dateActuelleStr, semestre: 'S3', tilted: false, mois: moisSelectionne.value, jour: jourSelectionne.value },
    ];
  }
});

// Utilisation propre du Store 
watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    let texteALire = `Vous êtes sur la page Mes S A E. Le calendrier est sur le ${jourSelectionne.value} ${moisSelectionne.value}. `;
    texteALire += `Pour cette date, il y a ${todoSaes.value.length} projets à faire, ${inProgressSaes.value.length} en cours, et ${doneSaes.value.length} terminés.`;
    accessStore.lireTexte(texteALire);
  }
});
</script>

<style scoped src="../css/StudentSAE.css"></style>