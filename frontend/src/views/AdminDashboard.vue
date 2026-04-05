<template>
  <div class="dashboard-layout">

    <main class="main-content" id="main-content-area" @mouseenter="lireAction('Zone de contenu principal du tableau de bord')">

      <div class="content-area" role="main">
        <h1 @mouseenter="lireAction('Titre de la page : Bienvenue sur le Tableau De Bord')">Bienvenue <span class="breadcrumb" aria-hidden="true">Page / Tableau De Bord</span></h1>
        <hr class="divider" />

        <div class="timeline" aria-label="Calendrier" @mouseenter="lireAction('Section Calendrier')">
          <div class="calendar-container">
            <div class="months-container">
              <div class="months" role="tablist">
                <button 
                  v-for="mois in listMois" 
                  :key="mois.id"
                  @click="selectionnerMois(mois.nom); lireAction('Mois sélectionné : ' + mois.nom + ' ' + mois.annee)"
                  @mouseenter="lireAction('Mois : ' + mois.nom + ' ' + mois.annee)"
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
                  @click="selectionnerJour(jour); lireAction('Jour sélectionné : ' + jour + ' ' + moisSelectionne)"
                  @mouseenter="lireAction('Jour ' + jour)"
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

        <h2 @mouseenter="lireAction('Liste des SAE en cours pour la date sélectionnée. Total : ' + saesFiltrees.length)">SAE En Cours ({{ saesFiltrees.length }})</h2>
        
        <section class="sae-grid">
          <article 
            v-for="sae in saesFiltrees" 
            :key="sae.id" 
            class="sae-card" 
            tabindex="0" 
            @click="router.push('/admin/sae/' + sae.id); lireAction('Ouverture de la SAE : ' + sae.titre)"
            @mouseenter="lireAction('SAE : ' + sae.titre + '. ' + sae.notifs + ' notifications. Cliquez pour voir les détails.')"
          >
            <div class="sae-gradient" :style="{ background: sae.color }" aria-hidden="true"></div>
            <h3 class="sae-title">{{ sae.titre }}</h3>
            <div class="sae-info">
              <span aria-label="Nombre de notifications">📢 {{ sae.notifs }}</span>
              <span aria-label="Date limite">📅 {{ moisAbrege(sae.mois) }} {{ sae.jour }}</span>
            </div>
          </article>
          <p v-if="saesFiltrees.length === 0" class="no-sae" role="status" @mouseenter="lireAction('Aucune SAE arrivant à échéance pour ce jour.')">Aucune SAE arrivant à échéance pour ce jour.</p>
        </section>

        <section class="add-sae-section" @mouseenter="lireAction('Section Ajouter une SAE')">
          <form @submit.prevent="ajouterSAE" class="add-sae-form">
            <div class="add-sae-header">
              <h3 @mouseenter="lireAction('Formulaire d\'ajout de SAE')">Ajouter SAE</h3>
              <button type="submit" class="btn-add-circle" aria-label="Ajouter la SAE" @mouseenter="lireAction('Bouton de validation pour ajouter la SAE')" @click="lireAction('Validation de l\'ajout de la SAE')">+</button>
            </div>

            <div class="form-row">
              <input v-model="nouvelleSae.titre" type="text" placeholder="Ex: SAE 301 - Intitulé..." class="input-title" required aria-label="Intitulé de la SAE" @mouseenter="lireAction('Champ de saisie : Intitulé de la SAE')" @click="lireAction('Saisie de l\'intitulé')" />
              
              <select v-model="nouvelleSae.semestre" class="input-select" aria-label="Semestre" @mouseenter="lireAction('Menu déroulant : Choix du Semestre')" @click="lireAction('Sélection du semestre')">
                <option value="S1">Semestre 1</option>
                <option value="S2">Semestre 2</option>
                <option value="S3">Semestre 3</option>
                <option value="S4">Semestre 4</option>
                <option value="S5">Semestre 5</option>
                <option value="S6">Semestre 6</option>
              </select>

              <select v-model="nouvelleSae.groupe" class="input-select" aria-label="Groupe" @mouseenter="lireAction('Menu déroulant : Choix du Groupe')" @click="lireAction('Sélection du groupe')">
                <option value="Tous">Tous</option>
                <option value="Dévelopemment web">Dévelopemment web</option>
                <option value="Création numérique">Création numérique</option>
              </select>
              
              <div class="date-group">
                <span class="date-label" @mouseenter="lireAction('Sélection de la date de début')">Date de début</span>
                <input v-model="nouvelleSae.debut" type="date" title="Date de début" class="input-date" required aria-label="Date de début" @mouseenter="lireAction('Champ : Date de début de la SAE')" @click="lireAction('Saisie de la date de début')" />
              </div>
              
              <div class="date-group">
                <span class="date-label" @mouseenter="lireAction('Sélection de la date de fin')">Date de fin</span>
                <input v-model="nouvelleSae.fin" type="date" title="Date de fin" class="input-date" required aria-label="Date de fin" @mouseenter="lireAction('Champ : Date de fin de la SAE')" @click="lireAction('Saisie de la date de fin')" />
              </div>
            </div>
            
            <textarea v-model="nouvelleSae.description" placeholder="Description..." class="input-desc" aria-label="Description de la SAE" @mouseenter="lireAction('Zone de texte : Description de la SAE')" @click="lireAction('Saisie de la description')"></textarea>
          </form>
          <p v-if="messageConfirmation" class="success-msg" role="alert" @mouseenter="lireAction('Message de succès : SAE ajoutée avec succès')">✅ SAE ajoutée avec succès !</p>
        </section>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAccessibiliteStore } from '@/stores/accessibilite'

const accessStore = useAccessibiliteStore();
const router = useRouter();

const lireAction = (texte) => {
  if (accessStore.voix) {
    accessStore.lireTexte(texte);
  }
};

// Affichage du calendrier 
const listMois = ref([]);
const moisSelectionne = ref('');
const jourSelectionne = ref(1);

// Configuration partagée globale (dates)
const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
const dateAujourdhui = new Date();
const moisActuelString = nomsMoisGlobaux[dateAujourdhui.getMonth()];
const jourActuel = dateAujourdhui.getDate();
const moisActuelAbrege = moisActuelString.substring(0, 3) + ' ' + jourActuel;

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

const selectionnerMois = (mois) => { moisSelectionne.value = mois; };
const selectionnerJour = (jour) => { jourSelectionne.value = jour; };

// Base de données des SAE (LocalStorage)
const defaultSAEs = [
  { id: 1, titre: 'SAE 301 - Projet Web', semestre: 'S1', groupe: 'A1', mois: moisActuelString, jour: jourActuel, date: moisActuelAbrege, color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 8, description: "Réaliser une application web full-stack avec framework front et back, API REST.", filiere: "Développement Web", consignes: "Fournir le code source complet sur un dépôt Git.\n\nLa documentation technique doit inclure le diagramme de base de données.\n\nLe rendu est individuel." },
  { id: 2, titre: 'SAE 303 - Design UX/UI', semestre: 'S1', groupe: 'A2', mois: moisActuelString, jour: jourActuel, date: 'Avr 15', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 2, description: "Concevoir l'interface utilisateur d'une application mobile en respectant les principes UX.", filiere: "Création Numérique", consignes: "Le livrable doit inclure le lien du prototype interactif Figma avec tous les écrans connectés. L'UI Kit doit être fourni." },
  { id: 3, titre: 'SAE 302 - Communication', semestre: 'S2', groupe: 'B1', mois: moisActuelString, jour: jourActuel, date: 'Mai 02', color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 5, description: "Établir une stratégie de communication digitale pour un lancement de produit.", filiere: "Communication", consignes: "Rédiger un dossier PDF de 15 pages minimum incluant le budget et le planning de publication. Un support de présentation oral est requis." },
  { id: 4, titre: 'SAE 401 - Audiovisuel', semestre: 'S3', groupe: 'B2', mois: 'Novembre', jour: 12, date: 'Nov 12', color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 1 },
  { id: 5, titre: 'SAE 201 - Intégration', semestre: 'S1', groupe: 'A1', mois: 'Avril', jour: 30, date: 'Avr 30', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 9 },
  { id: 6, titre: 'SAE 402 - Développement Back', semestre: 'S2', groupe: 'B1', mois: 'Decembre', jour: 15, date: 'Dec 15', color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 0 },
  { id: 7, titre: 'SAE 304 - Stratégie Marketing', semestre: 'S3', groupe: 'A2', mois: 'Juin', jour: 10, date: 'Juin 10', color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 4 },
  { id: 8, titre: 'SAE 403 - Motion Design', semestre: 'S4', groupe: 'B2', mois: 'Janvier', jour: 20, date: 'Jan 20', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 2 },
  { id: 9, titre: 'SAE 202 - Gestion de Projet', semestre: 'S4', groupe: 'A', mois: 'Juillet', jour: 5, date: 'Juil 05', color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 6 },
  { id: 10, titre: 'SAE 203 - Ergonomie', semestre: 'S2', groupe: 'B', mois: 'Fevrier', jour: 28, date: 'Fev 28', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 3 },
  { id: 11, titre: 'SAE 501 - Hébergement Web', semestre: 'S5', groupe: 'A1', mois: 'Septembre', jour: 1, date: 'Sep 01', color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 1 }
];

const loadSAEs = () => {
  const stored = localStorage.getItem('mmi_data_sae_v2');
  if (stored) return JSON.parse(stored);
  localStorage.setItem('mmi_data_sae_v2', JSON.stringify(defaultSAEs));
  return defaultSAEs;
};

const saveSAEs = (data) => {
  localStorage.setItem('mmi_data_sae_v2', JSON.stringify(data));
};

const listeSAE_BDD = ref([]);

onMounted(() => {
  const dateActuelle = new Date();
  listMois.value = genererListeMois();
  moisSelectionne.value = moisActuelString;
  jourSelectionne.value = dateActuelle.getDate();
  listeSAE_BDD.value = loadSAEs();
});

const moisAbrege = (mois) => {
  if (!mois) return '';
  return mois.substring(0, 3); 
};

// Filtrage pour s'afficher à la date de début OU à la date de fin
const saesFiltrees = computed(() => {
  return listeSAE_BDD.value.filter(sae => {
    let match = false;
    
    // Correspondance historique ou par défaut
    if (sae.mois === moisSelectionne.value && sae.jour === jourSelectionne.value) {
      match = true;
    }
    
    // Correspondance sur la date de début
    if (sae.debut) {
      const dDebut = new Date(sae.debut);
      if (!isNaN(dDebut) && nomsMoisGlobaux[dDebut.getMonth()] === moisSelectionne.value && dDebut.getDate() === jourSelectionne.value) {
        match = true;
      }
    }

    // Correspondance sur la date de fin
    if (sae.fin) {
      const dFin = new Date(sae.fin);
      if (!isNaN(dFin) && nomsMoisGlobaux[dFin.getMonth()] === moisSelectionne.value && dFin.getDate() === jourSelectionne.value) {
        match = true;
      }
    }
    
    return match;
  });
});

// Ajout d'une nouvelle SAE
const nouvelleSae = ref({ titre: '', semestre: 'S1', groupe: 'A', debut: '', fin: '', description: '' });
const messageConfirmation = ref(false);

const ajouterSAE = () => {
  let moisSae = moisSelectionne.value;
  let jourSae = jourSelectionne.value;
  let dateAbregee = moisAbrege(moisSae) + ' ' + jourSae;

  if (nouvelleSae.value.fin) {
    const objDate = new Date(nouvelleSae.value.fin);
    if (!isNaN(objDate)) {
      moisSae = nomsMoisGlobaux[objDate.getMonth()];
      jourSae = objDate.getDate();
      dateAbregee = moisAbrege(moisSae) + ' ' + jourSae;
    }
  }

  let dateFinaleFull = "À définir";
  if (nouvelleSae.value.fin) {
      const finDate = new Date(nouvelleSae.value.fin);
      if(!isNaN(finDate)) {
          dateFinaleFull = `${finDate.getDate()} ${nomsMoisGlobaux[finDate.getMonth()]} ${finDate.getFullYear()}`;
      }
  }

  listeSAE_BDD.value.push({ 
    id: Date.now(), 
    mois: moisSae, 
    jour: jourSae,
    date: dateAbregee,
    dateEcheance: dateFinaleFull, 
    debut: nouvelleSae.value.debut,
    fin: nouvelleSae.value.fin,
    semestre: nouvelleSae.value.semestre,
    groupe: nouvelleSae.value.groupe,
    titre: nouvelleSae.value.titre || 'Nouvelle SAE',
    description: nouvelleSae.value.description || "Aucune description fournie.", 
    color: 'linear-gradient(135deg, #f59e0b, #d97706, #b45309)', 
    notifs: 0
  });

  saveSAEs(listeSAE_BDD.value);
  messageConfirmation.value = true;
  
  lireAction(`Mise à jour effectuée. La SAE ${nouvelleSae.value.titre} a été ajoutée. Elle commence le ${nouvelleSae.value.debut} et se termine le ${nouvelleSae.value.fin}. La description est : ${nouvelleSae.value.description}`);
  
  setTimeout(() => {
    messageConfirmation.value = false;
    nouvelleSae.value = { titre: '', semestre: 'S1', groupe: 'A', debut: '', fin: '', description: '' };
  }, 3000);
};

watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    const nbSae = saesFiltrees.value.length;
    let texteALire = `Bonjour. Vous êtes sur la page d'accueil du tableau de bord administrateur. `;
    texteALire += `Le calendrier est actuellement positionné sur le mois de ${moisSelectionne.value} et le jour ${jourSelectionne.value}. `;
    
    if (nbSae === 0) {
      texteALire += `Il n'y a aucune S.A.E arrivant à échéance pour cette date. `;
    } else if (nbSae === 1) {
      texteALire += `Il y a une S.A.E arrivant à échéance aujourd'hui. `;
    } else {
      texteALire += `Il y a ${nbSae} S.A.E arrivant à échéance aujourd'hui. `;
    }

    if (nbSae > 0) {
      texteALire += `Voici la liste des S.A.E : `;
      saesFiltrees.value.forEach((sae, index) => {
        texteALire += `Numéro ${index + 1} : ${sae.titre}, avec ${sae.notifs} notifications. La date d'échéance est le ${sae.jour} ${sae.mois}. `;
      });
    }
    texteALire += `Plus bas sur la page, vous trouverez un formulaire pour ajouter une nouvelle S.A.E. Fin de la description.`;

    accessStore.lireTexte(texteALire);
  }
});
</script>

<style scoped src="../css/AdminDashboard.css"></style>