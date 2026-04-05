<script setup>
import { ref, computed, watch } from 'vue'
import HorizontalCalendar from '../components/HorizontalCalendar.vue' 
import { useAccessibiliteStore } from '@/stores/accessibilite' // Ajout du store accessibilité

import iconMeeting from '@/assets/icon_meeting.svg' 
import iconCalendar from '@/assets/icon_calendar_today.svg' 

const accessStore = useAccessibiliteStore()

// RÉCUPÉRATION DYNAMIQUE DE LA DATE DU JOUR
const today = new Date();
const formattedMonth = today.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
const moisActuel = formattedMonth.charAt(0).toUpperCase() + formattedMonth.slice(1);

const activeMonth = ref(moisActuel);
const activeDay = ref(today.getDate());

// --- FILTRES DE RECHERCHE ---
const selectedSemester = ref('Tous');
const selectedYear = ref('Tous');

// --- BASE DE DONNÉES DES CARTES KANBAN ---
// Ajout des champs "semester", "year" et dates formatées pour la cohérence
const baseColumns = ref([
  {
    id: 'todo', title: 'A faire', dotColor: '#4C6FFF', lineColor: '#4C6FFF',
    cards: [
      { id: 302, category: 'Développement web', categoryColor: '#D97706', categoryBg: '#FEF3C7', title: 'SAE 302', description: "Brainstorming brings team members' diverse experience into play.", comments: 8, date: '30 Nov 2025', semester: 'S3', year: '2025' },
      { id: 303, category: 'Communication', categoryColor: '#E11D48', categoryBg: '#FFE4E6', title: 'SAE 303', description: "User research helps you to create an optimal product for users.", comments: 8, date: '30 Nov 2026', semester: 'S3', year: '2026'}
    ]
  },
  {
    id: 'inprogress', title: 'En cours', dotColor: '#F59E0B', lineColor: '#FBBF24',
    cards: [
      { id: 202, category: 'Graphisme', categoryColor: '#D97706', categoryBg: '#FEF3C7', title: 'SAE 202', description: "", imagePlaceholder: true, comments: 2, date: '15 Nov 2025', semester: 'S2', year: '2025' },
      { id: 203, category: 'Développement web', categoryColor: '#059669', categoryBg: '#D1FAE5', title: 'SAE 203', description: "Notion is a workspace that adapts to your needs. It's as minimal or as powerful as you need it to be.", comments: 5, date: '20 Oct 2026', semester: 'S2', year: '2026' }
    ]
  },
  {
    id: 'done', title: 'Finis', dotColor: '#10B981', lineColor: '#A7F3D0',
    cards: [
      { id: 101, category: 'Communication', categoryColor: '#D97706', categoryBg: '#FEF3C7', title: 'SAE 101', description: "", grayPlaceholder: true, comments: 0, date: '10 Sep 2025', semester: 'S1', year: '2025' }
    ]
  }
])

// --- FILTRAGE DYNAMIQUE DU KANBAN ---
const columns = computed(() => {
  return baseColumns.value.map(col => {
    // On filtre les cartes de la colonne selon les select
    const filteredCards = col.cards.filter(card => {
      const matchSemester = selectedSemester.value === 'Tous' || card.semester === selectedSemester.value;
      const matchYear = selectedYear.value === 'Tous' || card.year === selectedYear.value;
      return matchSemester && matchYear;
    });
    
    // On renvoie la colonne avec ses cartes filtrées et son compte exact
    return {
      ...col,
      cards: filteredCards,
      count: filteredCards.length
    };
  });
});

// --- ACCESSIBILITÉ VOCALE ---
const lireAction = (texte) => {
  accessStore.lireTexte(texte);
};

// Description globale de la page à l'activation
watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    let desc = `Vous êtes sur la page Mes S A E. `;
    desc += `Utilisez les filtres déroulants pour affiner l'affichage par semestre et par année. `;
    desc += `Le tableau d'avancement Kanban se trouve en dessous avec 3 colonnes : À faire, En cours, et Finis.`;
    lireAction(desc);
  }
}, { immediate: true });
</script>

<template>
  <div class="mes-sae-container">
    
    <header class="page-header">
      <div class="title-section">
        <h2 tabindex="0" @focus="lireAction('Bienvenue Monsieur FDZAFE')">Bienvenue Monsieur FDZAFE 👋</h2>
        <span class="breadcrumb" tabindex="0" @focus="lireAction('Vous êtes sur la page Mes S A E')">Page / <strong>Mes SAE</strong></span>
      </div>
      <hr class="separator" />
    </header>

    <HorizontalCalendar 
      v-model:activeMonth="activeMonth"
      v-model:activeDay="activeDay"
    />

    <div class="filters-row">
      <select 
        v-model="selectedSemester" 
        class="custom-select"
        @change="lireAction(`Filtre appliqué. Semestre sélectionné : ${selectedSemester}.`)"
        @focus="lireAction('Filtre par semestre. Actuellement sur ' + selectedSemester)"
        aria-label="Filtrer par semestre"
      >
        <option value="Tous">Par semestre</option>
        <option value="S1">Semestre 1</option>
        <option value="S2">Semestre 2</option>
        <option value="S3">Semestre 3</option>
        <option value="S4">Semestre 4</option>
        <option value="S5">Semestre 5</option>
        <option value="S6">Semestre 6</option>
      </select>
      
      <select 
        v-model="selectedYear" 
        class="custom-select"
        @change="lireAction(`Filtre appliqué. Année sélectionnée : ${selectedYear}.`)"
        @focus="lireAction('Filtre par année. Actuellement sur ' + selectedYear)"
        aria-label="Filtrer par année"
      >
        <option value="Tous">Par année</option>
        <option value="2025">2025</option>
        <option value="2026">2026</option>
      </select>
    </div>

    <div class="kanban-board">
      <div v-for="col in columns" :key="col.id" class="kanban-column">
        
        <div class="column-header">
          <div class="col-title-wrap">
            <span class="dot" :style="{ backgroundColor: col.dotColor }"></span>
            <h3 
              class="col-title" 
              tabindex="0" 
              @focus="lireAction(`Colonne ${col.title}, contenant ${col.count} tâches`)"
            >
              {{ col.title }}
            </h3>
            <span class="badge" :aria-label="`${col.count} tâches`">{{ col.count }}</span>
          </div>
        </div>
        
        <div class="col-line" :style="{ backgroundColor: col.lineColor }"></div>

        <div class="cards-list">
          <template v-for="card in col.cards" :key="card.id">
            
            <div v-if="card.isDragging" class="drop-zone-placeholder"></div>

            <RouterLink 
              :to="'/enseignant/sae/' + card.id"
              :class="['sae-task-card', { 'is-dragging': card.isDragging }]"
              style="text-decoration: none; color: inherit; display: block;"
              tabindex="0"
              @mouseenter="lireAction(`Carte ${card.title}. Catégorie ${card.category}. Échéance le ${card.date}. Cliquez pour voir les détails.`)"
              @focus="lireAction(`Carte ${card.title}. Catégorie ${card.category}. Échéance le ${card.date}. Appuyez sur Entrée pour voir les détails.`)"
            >
              <div class="card-top">
                <span class="category-tag" :style="{ color: card.categoryColor, backgroundColor: card.categoryBg }">{{ card.category }}</span>
                <span class="more-options" @click.prevent>•••</span>
              </div>

              <h4 class="card-title">{{ card.title }}</h4>
              <p v-if="card.description" class="card-desc">{{ card.description }}</p>
              
              <div v-if="card.imagePlaceholder" class="img-placeholder flower-img"></div>
              <div v-if="card.grayPlaceholder" class="img-placeholder gray-box"></div>

              <div class="card-footer">
                <span class="meta-item"><img :src="iconMeeting" alt="Commentaires" class="small-icon" /> {{ card.comments }}</span>
                <span class="meta-item"><img :src="iconCalendar" alt="Date" class="small-icon" /> {{ card.date }}</span>
              </div>
            </RouterLink>

          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  @import '../css/EnseignantMesSAEView.css';
  .small-icon { width: 16px; height: 16px; opacity: 0.7; }
</style>