<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import { useRoute } from 'vue-router';
import gsap from 'gsap';
import { useAccessibiliteStore } from '@/stores/accessibilite'; // Importation du store accessibilité

const route = useRoute();
// Récupère l'ID depuis l'URL (ex: 401)
const saeId = route.params.id || '401'; 

// Variable pour stocker l'année sélectionnée
const selectedYear = ref('Par année');
const accessStore = useAccessibiliteStore(); // Initialisation du store

// Simulation des données des travaux étudiants (Ajout des champs description, image et video)
const studentWorks = ref([
  { id: 1, student: 'Annissa FDZAFE', year: '2026', gradient: 'linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%)', description: "Création d'une plateforme interactive avec rendu 3D.", image: 'https://picsum.photos/seed/1/800/400', video: '../assets/298812_small.mp4' },
  { id: 2, student: 'Lucas MARTIN', year: '2025', gradient: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', description: "Projet de l'année précédente axé sur le design UI.", image: 'https://picsum.photos/seed/2/800/400', video: null },
  { id: 3, student: 'Benoit Dupont', year: '2026', gradient: 'linear-gradient(135deg, #5ee7df 0%, #b490ca 100%)', description: "Refonte complète du site web avec intégration vidéo.", image: 'https://picsum.photos/seed/3/800/400', video: '../assets/298812_small.mp4' },
  { id: 4, student: 'Emma BERNARD', year: '2025', gradient: 'linear-gradient(135deg, #f6d365 0%, #fda085 100%)', description: "Application mobile de 2025 avec React Native.", image: 'https://picsum.photos/seed/4/800/400', video: null },
  { id: 5, student: 'Sarah lopez', year: '2026', gradient: 'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)', description: "Développement d'un jeu vidéo éducatif.", image: 'https://picsum.photos/seed/5/800/400', video: null },
  { id: 6, student: 'Rayan Lionel', year: '2026', gradient: 'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)', description: "Dashboard interactif pour la gestion de données.", image: 'https://picsum.photos/seed/6/800/400', video: null },
]);

// --- LOGIQUE DE FILTRAGE ---
const filteredWorks = computed(() => {
  if (selectedYear.value === 'Par année') {
    return studentWorks.value;
  }
  return studentWorks.value.filter(work => work.year === selectedYear.value);
});

// --- LOGIQUE DE LA FENÊTRE MODALE ---
const isModalOpen = ref(false);
const selectedWork = ref(null);

const ouvrirModale = (work) => {
  selectedWork.value = work;
  isModalOpen.value = true;
};

const fermerModale = () => {
  isModalOpen.value = false;
  setTimeout(() => { selectedWork.value = null; }, 300); // Délai pour la fluidité de fermeture
};

// --- LOGIQUE D'ACCESSIBILITÉ VOCALE ---
// Lecture globale quand la voix est activée
watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    let desc = `Vous êtes sur la page de détail de la S A E ${saeId}. `;
    desc += `Le projet est le développement d'une application web. `;
    desc += `Actuellement, ${filteredWorks.value.length} travaux étudiants sont affichés. `;
    desc += `Vous pouvez utiliser le menu déroulant pour filtrer par année. `;
    desc += `Cliquez sur une carte pour afficher le détail du projet.`;
    accessStore.lireTexte(desc);
  }
}, { immediate: true });

// Fonction pour lire les actions spécifiques
const lireAction = (texte) => {
  accessStore.lireTexte(texte);
};

// Animation d'apparition en cascade
onMounted(async () => {
  await nextTick();
  
  // Applique l'animation à tous les éléments ayant la classe 'work-card'
  gsap.fromTo('.work-card', 
    { 
      opacity: 0, 
      y: 100, // Commence plus bas (animateFrom="bottom")
      filter: 'blur(10px)' // L'effet blurToFocus du script
    },
    {
      opacity: 1,
      y: 0,
      filter: 'blur(0px)',
      duration: 0.8,
      ease: 'power3.out', // Même ease que le React
      stagger: 0.05 // Délai entre chaque carte pour l'effet cascade
    }
  );
});

// Animations au survol
const handleMouseEnter = (e) => {
  gsap.to(e.currentTarget, {
    scale: 0.95, // hoverScale du script
    duration: 0.3,
    ease: 'power2.out'
  });
};

const handleMouseLeave = (e) => {
  gsap.to(e.currentTarget, {
    scale: 1,
    duration: 0.3,
    ease: 'power2.out'
  });
};
</script>

<template>
  <div class="sae-detail-page">
    
    <header class="page-header">
      <div class="header-title">
        <h1>SAE / SAE {{ saeId }}</h1>
        <select 
          class="year-filter" 
          v-model="selectedYear"
          @focus="lireAction('Menu pour filtrer les projets par année')"
          @change="lireAction(`Filtre appliqué. Année ${selectedYear} sélectionnée. ${filteredWorks.length} projets trouvés.`)"
        >
          <option value="Par année">Par année</option>
          <option value="2025">2025</option>
          <option value="2026">2026</option>
        </select>
      </div>
      <hr>
    </header>

    <section class="sae-banner">
      <div class="banner-content">
        <span class="tag">Développement Web</span>
        <h2>SAE {{ saeId }}</h2>
        
        <div class="description">
          <strong>Développement d'une application web</strong>
          <p>Réaliser une application web full-stack avec framework front et back, API REST.</p>
        </div>
      </div>
      <div class="banner-image"></div>
    </section>

    <section class="works-grid">
      <div 
        v-for="work in filteredWorks" 
        :key="work.id" 
        class="work-card"
        @click="ouvrirModale(work); lireAction(`Ouverture des détails du projet de ${work.student}`)"
        @mouseenter="(e) => { handleMouseEnter(e); lireAction(`Aperçu du projet de ${work.student} réalisé en ${work.year}. Cliquez pour voir les détails.`); }"
        @mouseleave="handleMouseLeave"
        tabindex="0"
        @focus="lireAction(`Aperçu du projet de ${work.student} réalisé en ${work.year}. Appuyez sur Entrée pour voir les détails.`)"
        @keydown.enter="ouvrirModale(work); lireAction(`Ouverture des détails du projet de ${work.student}`)"
      >
        <div class="work-visual" :style="{ background: work.gradient }"></div>
        
        <div class="work-info">
          <span class="student-name">{{ work.student }}</span>
          <span class="work-year">{{ work.year }}</span>
        </div>
      </div>
    </section>

    <Teleport to="body">
      <div v-if="isModalOpen" class="modal-overlay" @click.self="fermerModale(); lireAction('Fermeture de la fenêtre de détails');">
        
        <div class="modal-content">
          <button 
            class="close-btn" 
            @click="fermerModale(); lireAction('Fermeture de la fenêtre de détails');" 
            @focus="lireAction('Bouton pour fermer la fenêtre')"
            aria-label="Fermer"
          >✕</button>

          <div class="modal-media">
            <video 
              v-if="selectedWork.video" 
              controls 
              autoplay 
              class="media-element"
              @focus="lireAction('Lecteur vidéo du projet. Utilisez les contrôles pour lire ou mettre en pause.')"
            >
              <source :src="selectedWork.video" type="video/mp4">
              Votre navigateur ne supporte pas la balise vidéo.
            </video>
            
            <img v-else-if="selectedWork.image" :src="selectedWork.image" alt="Aperçu du projet" class="media-element" />
          </div>

          <div class="modal-info">
            <h3 class="modal-student" tabindex="0" @focus="lireAction(`Titre du projet : ${selectedWork.student}, en ${selectedWork.year}`)">
              {{ selectedWork.student }} - {{ selectedWork.year }}
            </h3>
            <hr class="modal-divider" />
            <p class="modal-description" tabindex="0" @focus="lireAction(`Description du projet : ${selectedWork.description}`)">
              {{ selectedWork.description }}
            </p>
          </div>

        </div>
      </div>
    </Teleport>

  </div>
</template>

<style scoped>
@import '../css/PublicSAEDetailView.css';
</style>