<script setup>
import { ref, computed, watch } from 'vue'
import HorizontalCalendar from '../components/HorizontalCalendar.vue' 
import { useAccessibiliteStore } from '@/stores/accessibilite' // Import du store

import iconNotif from '@/assets/icon_meeting.svg'
import iconDelais from '@/assets/icon_calendar.svg'

import iconWarning from '@/assets/icon_warning.svg'
import iconInfo from '@/assets/icon_info.svg'
import iconDanger from '@/assets/icon_danger.svg'

const accessStore = useAccessibiliteStore(); // Initialisation du store

// --- 1. GESTION DU TEMPS ET DU CALENDRIER DYNAMIQUE ---
const today = new Date();
const formattedMonth = today.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
const moisActuel = formattedMonth.charAt(0).toUpperCase() + formattedMonth.slice(1);

const currentMonth = ref(moisActuel);
const currentDay = ref(today.getDate());

const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];

// --- 2. FAUSSES DONNÉES DE TEST AVEC DATE DÉBUT ET FIN ---
const y = today.getFullYear();
const m = today.getMonth();
const d = today.getDate();

const toutesLesSaes = ref([
  { 
    id: 316, 
    title: 'SAE 316', 
    comments: 8, 
    // Commence il y a 2 jours, finit dans 5 jours (EN COURS)
    dateDebut: new Date(y, m, d - 2), 
    dateFin: new Date(y, m, d + 5), 
    gradient: 'linear-gradient(135deg, #d495fc, #7b61ff)' 
  },
  { 
    id: 304, 
    title: 'SAE 304', 
    comments: 2, 
    // Commence demain, finit dans 10 jours (À VENIR)
    dateDebut: new Date(y, m, d + 1), 
    dateFin: new Date(y, m, d + 10), 
    gradient: 'linear-gradient(135deg, #181c5a, #768ebf)' 
  },
  { 
    id: 305, 
    title: 'SAE 305', 
    comments: 14, 
    // A commencé il y a 10 jours, s'est terminée hier (TERMINÉE)
    dateDebut: new Date(y, m, d - 10), 
    dateFin: new Date(y, m, d - 1), 
    gradient: 'linear-gradient(135deg, #6c2619, #8c9eb9)' 
  }
]);

// --- 3. FILTRE LOGIQUE TEMPOREL ---
const saesDuJour = computed(() => {
  if (!currentMonth.value) return [];
  
  // A. Recréer la date exacte sélectionnée sur le calendrier
  const parts = currentMonth.value.split(' ');
  const nomMois = parts[0];
  const anneeStr = parts[1];
  
  const indexMois = nomsMoisGlobaux.findIndex(mois => mois.toLowerCase() === nomMois.toLowerCase());
  const annee = parseInt(anneeStr);

  const dateSelectionnee = new Date(annee, indexMois, currentDay.value);
  // On s'assure d'ignorer les heures pour la comparaison
  dateSelectionnee.setHours(12, 0, 0, 0);

  // B. Filtrer les SAE en cours
  return toutesLesSaes.value.filter(sae => {
    const debut = new Date(sae.dateDebut);
    debut.setHours(0, 0, 0, 0);

    const fin = new Date(sae.dateFin);
    fin.setHours(23, 59, 59, 999);

    // VÉRIFICATION : Est-ce que le jour sélectionné est entre le début et la fin ?
    return dateSelectionnee >= debut && dateSelectionnee <= fin;
  }).map(sae => {
    // C. Formater la date d'affichage (ex: "15 Avr")
    const options = { day: 'numeric', month: 'short' };
    return {
      ...sae,
      dateAffichage: new Date(sae.dateFin).toLocaleDateString('fr-FR', options)
    }
  });
});

const annonces = ref([
  { id: 1, type: 'warning', title: 'Réunion Pédagogique', sae: 'SAE 316', date: 'Aujourd\'hui', text: 'N\'oubliez pas la réunion de coordination pour le projet tutoré à 14h en salle C204.' },
  { id: 2, type: 'info', title: 'Rendus étudiants', sae: 'SAE 304', date: 'Hier', text: 'Les groupes 1 à 4 ont déposé leurs livrables sur la plateforme. La correction peut débuter.' },
  { id: 3, type: 'danger', title: 'Rappel Échéance', sae: 'SAE 305', date: 'Lun. 14', text: 'La date limite de saisie des notes pour cette SAE approche à grands pas.' }
]);

// --- 4. ACCESSIBILITÉ VOCALE DYNAMIQUE ---
const lireAction = (texte) => {
  accessStore.lireTexte(texte);
};

// Description globale quand on active la voix
watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    let desc = `Bienvenue sur votre tableau de bord Enseignant. `;
    desc += `Le calendrier est positionné sur le ${currentDay.value} ${currentMonth.value}. `;
    desc += saesDuJour.value.length === 0 ? `Il n'y a aucune S A E en cours à cette date. ` : `Il y a ${saesDuJour.value.length} S A E en cours. `;
    desc += `Vous avez également ${annonces.value.length} annonces à consulter.`;
    lireAction(desc);
  }
}, { immediate: true });

// Avertir l'utilisateur quand il change la date du calendrier
watch(currentDay, (newDay) => {
  if (accessStore.voix) {
    let text = `Date modifiée : ${newDay} ${currentMonth.value}. `;
    text += saesDuJour.value.length > 0 ? `${saesDuJour.value.length} S A E en cours.` : `Aucune S A E en cours.`;
    lireAction(text);
  }
});

</script>

<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <div class="title-section">
        <h2 
          tabindex="0" 
          @focus="lireAction('Bienvenue Madame XXXXXX')"
        >
          Bienvenue Madame XXXXXX 👋
        </h2>
        <span 
          class="breadcrumb" 
          tabindex="0" 
          @focus="lireAction('Vous êtes sur la page Tableau De Bord')"
        >
          <strong>Tableau De Bord</strong>
        </span>
      </div>
      <hr class="separator" />
    </header>

    <HorizontalCalendar 
      v-model:activeMonth="currentMonth"
      v-model:activeDay="currentDay"
    />

    <div class="main-content">
      <section class="sae-section">
        <h3 
          class="section-title" 
          tabindex="0" 
          @focus="lireAction('Section : S A E En Cours à cette date')"
        >
          SAE En Cours à cette date
        </h3>
        
        <div class="sae-grid" v-if="saesDuJour.length > 0">
          <RouterLink 
            v-for="sae in saesDuJour" 
            :key="sae.id" 
            :to="'/enseignant/sae/' + sae.id"
            class="sae-card"
            style="text-decoration: none;"
            @mouseenter="lireAction(`Projet ${sae.title}. ${sae.comments} commentaires. Échéance le ${sae.dateAffichage}. Cliquez pour ouvrir.`)"
            @focus="lireAction(`Projet ${sae.title}. ${sae.comments} commentaires. Échéance le ${sae.dateAffichage}. Appuyez sur Entrée pour ouvrir.`)"
          >
            <div class="sae-visual" :style="{ background: sae.gradient }"></div>
            <div class="sae-info">
              <h4 style="color: var(--texte-principal);">{{ sae.title }}</h4>
            </div>
            <div class="sae-footer">
              <div class="icons-container">
                <img :src="iconNotif" alt="Icon Notif" class="action-icon" /> {{ sae.comments }}
              </div>
              <div class="icons-container">
                <img :src="iconDelais" alt="Icon Delais" class="action-icon" /> {{ sae.dateAffichage }}
              </div>
            </div>
          </RouterLink>
        </div>
        <p 
          v-else 
          class="no-sae-msg" 
          tabindex="0" 
          @focus="lireAction('Aucune S A E en cours pour cette date.')"
        >
          Aucune SAE en cours pour cette date.
        </p>
       </section>

      <aside class="annonces-section">
        <h3 
          class="section-title" 
          tabindex="0" 
          @focus="lireAction('Section : Annonces')"
        >
          Annonces
        </h3>
        <div class="annonces-list">
          <div 
            v-for="annonce in annonces" 
            :key="annonce.id" 
            class="annonce-card"
            tabindex="0"
            @mouseenter="lireAction(`Annonce : ${annonce.title}, concernant la ${annonce.sae}. Reçu ${annonce.date}. Message : ${annonce.text}`)"
            @focus="lireAction(`Annonce : ${annonce.title}, concernant la ${annonce.sae}. Reçu ${annonce.date}. Message : ${annonce.text}`)"
          >
            <div class="annonce-header">
              <div class="annonce-title-wrap">
                <span class="annonce-icon" :class="annonce.type">
                  <img v-if="annonce.type === 'warning'" :src="iconWarning" alt="Warning" class="action-icon" />
                  <img v-if="annonce.type === 'info'" :src="iconInfo" alt="Info" class="action-icon" />
                  <img v-if="annonce.type === 'danger'" :src="iconDanger" alt="Danger" class="action-icon" />
                </span>
                <h4 class="annonce-title">{{ annonce.title }}</h4>
              </div>
              <div class="annonce-meta">
                <span class="annonce-sae">{{ annonce.sae }}</span>
                <span class="annonce-date">{{ annonce.date }}</span>
              </div>
            </div>
            <p v-if="annonce.text" class="annonce-text">{{ annonce.text }}</p>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<style scoped>
  @import '../css/EnseignantDashbordView.css';
</style>