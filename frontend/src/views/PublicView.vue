<script setup>
import { ref, computed, watch } from 'vue';
import CardSwap from '../components/CardSwap.vue';
import SAECard from '../components/SAECard.vue';
import { useAccessibiliteStore } from '@/stores/accessibilite';

const accessStore = useAccessibiliteStore();

const selectedSemester = ref('Tous');
const selectedYear = ref('Tous');

const saeList = ref([
  { id: 103, title: 'SAE 103', semester: 'S1', year: '2025', color: 'linear-gradient(135deg, #667eea, #764ba2)' },
  { id: 203, title: 'SAE 203', semester: 'S2', year: '2024', color: 'linear-gradient(135deg, #ff9a9e, #fad0c4)' },
  { id: 301, title: 'SAE 301', semester: 'S3', year: '2026', color: 'linear-gradient(135deg, #a1c4fd, #c2e9fb)' },
  { id: 401, title: 'SAE 401', semester: 'S4', year: '2026', color: 'linear-gradient(135deg, #84fab0, #8fd3f4)' },
  { id: 501, title: 'SAE 501', semester: 'S5', year: '2025', color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe)' },
  { id: 601, title: 'SAE 601', semester: 'S6', year: '2026', color: 'linear-gradient(135deg, #fef08a, #fcd34d)' }
]);

const filteredSaeList = computed(() => {
  return saeList.value.filter(sae => {
    const matchSemester = selectedSemester.value === 'Tous' || sae.semester === selectedSemester.value;
    const matchYear = selectedYear.value === 'Tous' || sae.year === selectedYear.value;
    return matchSemester && matchYear;
  });
});

// Fonction pour centraliser l'appel vocal
const lireAction = (texte) => {
  accessStore.lireTexte(texte);
};

// Description contextuelle de la page quand on active la voix
watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    let desc = `Vous êtes sur le catalogue public des S A E. `;
    desc += `Actuellement, ${filteredSaeList.value.length} projets sont affichés. `;
    desc += `Utilisez les menus déroulants pour filtrer par semestre ou par année.`;
    lireAction(desc);
  }
}, { immediate: true });
</script>

<template>
  <div 
    class="fixed-interface-container" 
    :class="{ 'daltonien-filter': accessStore.daltonien }"
    :style="{ fontSize: accessStore.fontScale + 'rem' }"
  >

    <header class="page-header">
      <h1 
        tabindex="0"
        @mouseenter="lireAction('Titre de la page : S A E')"
        @focus="lireAction('Titre de la page : S A E')"
        :style="{ fontSize: (2.5 * accessStore.fontScale) + 'rem' }"
      >
        SAE
      </h1>
      <hr>
      
      <div class="filters-container">
        <select 
          v-model="selectedSemester" 
          class="filter-select"
          aria-label="Filtrer par semestre"
          @focus="lireAction('Filtre par semestre. Actuellement sur ' + selectedSemester)"
          @change="lireAction(`Filtre appliqué. Semestre sélectionné : ${selectedSemester}. ${filteredSaeList.length} projets trouvés.`)"
        >
          <option value="Tous">Tous les semestres</option>
          <option value="S1">Semestre 1</option>
          <option value="S2">Semestre 2</option>
          <option value="S3">Semestre 3</option>
          <option value="S4">Semestre 4</option>
          <option value="S5">Semestre 5</option>
          <option value="S6">Semestre 6</option>
        </select>

        <select 
          v-model="selectedYear" 
          class="filter-select"
          aria-label="Filtrer par année"
          @focus="lireAction('Filtre par année. Actuellement sur ' + selectedYear)"
          @change="lireAction(`Filtre appliqué. Année sélectionnée : ${selectedYear}. ${filteredSaeList.length} projets trouvés.`)"
        >
          <option value="Tous">Toutes les années</option>
          <option value="2025">2025</option>
          <option value="2026">2026</option>
        </select>
      </div>
    </header>

    <div class="animation-zone">
      <CardSwap 
        :key="selectedSemester + selectedYear" 
        :cards="filteredSaeList" 
        :width="482" 
        :height="361"
      >
        <template #card-content="{ card }">
          <SAECard 
            :id="card.id"
            :title="card.title"
            :semester="card.semester"
            :colorGradient="card.color"
            class="full-size-card"
            tabindex="0"
            @mouseenter="lireAction(`Aperçu du projet ${card.title}, ${card.semester}, année ${card.year}. Cliquez pour voir les détails.`)"
            @focus="lireAction(`Aperçu du projet ${card.title}, ${card.semester}, année ${card.year}. Appuyez sur Entrée pour voir les détails.`)"
          />
        </template>
      </CardSwap>
    </div>
  </div>
</template>

<style scoped>
  @import '../css/PublicView.css';
</style>