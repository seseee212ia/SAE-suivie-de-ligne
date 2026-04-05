<template>
  <div class="dashboard-layout">

    <main class="main-content" id="main-content-area" @mouseenter="lireAction('Zone principale des SAE')">

      <div v-if="afficherAjoutSaePopup" class="modal-overlay" @click="afficherAjoutSaePopup = false; lireAction('Fermeture de la fenêtre d\'ajout')">
        <div class="modal-content-center modal-xl" @click.stop @mouseenter="lireAction('Fenêtre modale pour ajouter une SAE')">
          <h3>Ajouter une nouvelle SAE</h3>
          
          <form @submit.prevent="validerAjoutSAE" class="ajout-sae-form">
            <div class="form-group-modal">
              <label @mouseenter="lireAction('Label : Intitulé de la SAE')">Intitulé de la SAE</label>
              <input v-model="nouvelleSae.titre" type="text" placeholder="Ex: SAE 301 - Projet Web..." class="input-full" required @mouseenter="lireAction('Champ : Intitulé de la SAE')" @click="lireAction('Saisie de l\'intitulé')" />
            </div>
            
            <div class="form-row-modal">
              <div class="form-group-modal flex-1">
                <label @mouseenter="lireAction('Label : Semestre')">Semestre</label>
                <select v-model="nouvelleSae.semestre" class="input-full" required @mouseenter="lireAction('Menu : Choix du Semestre')" @click="lireAction('Sélection du semestre')">
                  <option value="S1">Semestre 1</option>
                  <option value="S2">Semestre 2</option>
                  <option value="S3">Semestre 3</option>
                  <option value="S4">Semestre 4</option>
                  <option value="S5">Semestre 5</option>
                  <option value="S6">Semestre 6</option>
                </select>
              </div>
            </div>

            <div class="form-row-modal">
              <div class="form-group-modal flex-1">
                <label @mouseenter="lireAction('Label : Date limite de rendu')">Date limite / Rendu</label>
                <input v-model="nouvelleSae.dateInput" type="date" class="input-full" required @mouseenter="lireAction('Champ : Date limite')" @click="lireAction('Saisie de la date limite')" />
              </div>
              <div class="form-group-modal flex-1">
                <label @mouseenter="lireAction('Label : Ressources et Annexes')">Ressources (Sujet, annexes...)</label>
                <input type="file" @change="gererUploadFichier" class="input-full file-input" multiple @mouseenter="lireAction('Bouton d\'ajout de fichiers')" @click="lireAction('Sélection de fichiers')" />
              </div>
            </div>

            <div class="form-group-modal">
              <label @mouseenter="lireAction('Label : Description de la SAE')">Description de la SAE</label>
              <textarea v-model="nouvelleSae.description" placeholder="Saisissez les consignes ici..." class="input-full textarea-consigne" required @mouseenter="lireAction('Zone de texte : Consignes de la SAE')" @click="lireAction('Saisie des consignes')"></textarea>
            </div>
            
            <div class="modal-actions">
              <button type="button" @click="afficherAjoutSaePopup = false; lireAction('Annulation et fermeture de la fenêtre')" class="btn-cancel" @mouseenter="lireAction('Bouton Annuler')">Annuler</button>
              <button type="submit" class="btn-confirm" @mouseenter="lireAction('Bouton Valider l\'ajout de la SAE')" @click="lireAction('Validation du formulaire')">Ajouter la SAE</button>
            </div>
          </form>

        </div>
      </div>

      <div class="content-area" role="main">
        
        <div class="page-header">
          <h1 @mouseenter="lireAction('Page de gestion des SAE')">Bienvenue <span class="breadcrumb" aria-hidden="true">Page / SAE</span></h1>
          <button class="btn-add-main" @click="ouvrirAjoutSAE" aria-label="Ajouter une nouvelle SAE" @mouseenter="lireAction('Bouton pour ouvrir le formulaire d\'ajout d\'une SAE')">
            ➕
          </button>
        </div>
        <hr class="divider" />

        <div class="filters-toolbar" @mouseenter="lireAction('Barre d\'outils de recherche et de filtrage')">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Rechercher une SAE..." 
            class="filter-select" 
            aria-label="Rechercher une SAE par mot-clé" 
            @mouseenter="lireAction('Barre de recherche pour filtrer les SAE')"
            @click="lireAction('Saisie dans la barre de recherche')"
          />
          <select v-model="filtreSemestre" class="filter-select" aria-label="Filtrer par semestre" @mouseenter="lireAction('Filtre par semestre')" @click="lireAction('Sélection d\'un semestre pour le filtre')">
            <option value="">Semestre</option>
            <option value="S1">Semestre 1</option>
            <option value="S2">Semestre 2</option>
            <option value="S3">Semestre 3</option>
            <option value="S4">Semestre 4</option>
            <option value="S5">Semestre 5</option>
            <option value="S6">Semestre 6</option>
          </select>
          <select v-model="filtreGroupe" class="filter-select" aria-label="Filtrer par groupe" @mouseenter="lireAction('Filtre par groupe')" @click="lireAction('Sélection d\'un groupe pour le filtre')">
            <option value="">Groupe</option>
            <option value="g1">A1</option>
            <option value="g2">A2</option>
            <option value="g3">B1</option>
            <option value="g4">B2</option>
          </select>
        </div>

        <section class="sae-grid">
          <article 
            v-for="sae in saesFiltrees" 
            :key="sae.id" 
            class="sae-card" 
            tabindex="0" 
            @click="router.push('/admin/sae/' + sae.id); lireAction('Ouverture des détails de la SAE ' + sae.titre)"
            @mouseenter="lireAction('Carte de la SAE : ' + sae.titre + '. ' + sae.notifs + ' notifications. Échéance le : ' + sae.date)"
          >
            <div class="sae-image-container">
              <div class="sae-gradient" :style="{ background: sae.color }" aria-hidden="true"></div>
              
              <div class="sae-card-actions">
                <button class="action-btn" @click.stop="modifierSAE(sae.id)" title="Modifier" aria-label="Modifier la SAE" @mouseenter="lireAction('Bouton Modifier la SAE ' + sae.titre)">
                  <img src="../assets/icon_modifier.svg" alt="Modifier" class="action-svg" />
                </button>
                <button class="action-btn" @click.stop="supprimerSAE(sae.id)" title="Supprimer" aria-label="Supprimer la SAE" @mouseenter="lireAction('Bouton Supprimer la SAE ' + sae.titre)">
                  <img src="../assets/icon_supprimer.svg" alt="Supprimer" class="action-svg" />
                </button>
              </div>
            </div>
            
            <h3 class="sae-title">{{ sae.titre }}</h3>

            <div class="sae-info">
              <span aria-label="Nombre de notifications">📢 {{ sae.notifs }}</span>
              <span aria-label="Date limite" :class="{'urgent-date': sae.date === moisActuelAbrege}">📅 {{ sae.date }}</span>
            </div>
          </article>

          <p v-if="saesFiltrees.length === 0" class="no-sae" role="status" @mouseenter="lireAction('Aucune SAE trouvée avec ces critères.')">Aucune SAE trouvée avec ces critères.</p>
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

const searchQuery = ref('');
const filtreSemestre = ref('');
const filtreGroupe = ref('');

// Configuration partagée globale (dates)
const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
const dateAujourdhui = new Date();
const moisActuelString = nomsMoisGlobaux[dateAujourdhui.getMonth()];
const jourActuel = dateAujourdhui.getDate();
const moisActuelAbrege = moisActuelString.substring(0, 3) + ' ' + jourActuel;

// --- BASE DE DONNÉES SAE PARTAGÉE (LocalStorage) ---
const defaultSAEs = [
  { id: 1, titre: 'SAE 301 - Projet Web', semestre: 'S1', groupe: 'g1', mois: moisActuelString, jour: jourActuel, date: moisActuelAbrege, dateEcheance: `${jourActuel} ${moisActuelString} 2026`, color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 8, description: "Réaliser une application web full-stack avec framework front et back, API REST.", filiere: "Développement Web", consignes: "Fournir le code source complet sur un dépôt Git.\n\nLa documentation technique doit inclure le diagramme de base de données.\n\nLe rendu est individuel." },
  { id: 2, titre: 'SAE 303 - Design UX/UI', semestre: 'S1', groupe: 'g2', mois: 'Avril', jour: 15, date: 'Avr 15', dateEcheance: "15 Avril 2026", color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 2, description: "Concevoir l'interface utilisateur d'une application mobile en respectant les principes UX.", filiere: "Création Numérique", consignes: "Le livrable doit inclure le lien du prototype interactif Figma avec tous les écrans connectés. L'UI Kit doit être fourni." },
  { id: 3, titre: 'SAE 302 - Communication', semestre: 'S2', groupe: 'g1', mois: 'Mai', jour: 2, date: 'Mai 02', dateEcheance: "02 Mai 2026", color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 5, description: "Établir une stratégie de communication digitale pour un lancement de produit.", filiere: "Communication", consignes: "Rédiger un dossier PDF de 15 pages minimum incluant le budget et le planning de publication. Un support de presentation oral est requis." },
  { id: 4, titre: 'SAE 401 - Audiovisuel', semestre: 'S3', groupe: 'g3', mois: 'Novembre', jour: 12, date: 'Nov 12', dateEcheance: "12 Novembre 2026", color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 1 },
  { id: 5, titre: 'SAE 201 - Intégration', semestre: 'S1', groupe: 'g1', mois: 'Avril', jour: 30, date: 'Avr 30', dateEcheance: "30 Avril 2026", color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 9 },
  { id: 6, titre: 'SAE 402 - Développement Back', semestre: 'S2', groupe: 'g3', mois: 'Decembre', jour: 15, date: 'Dec 15', dateEcheance: "15 Decembre 2026", color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 0 },
  { id: 7, titre: 'SAE 304 - Stratégie Marketing', semestre: 'S3', groupe: 'g2', mois: 'Juin', jour: 10, date: 'Juin 10', dateEcheance: "10 Juin 2026", color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 4 },
  { id: 8, titre: 'SAE 403 - Motion Design', semestre: 'S4', groupe: 'g1', mois: 'Janvier', jour: 20, date: 'Jan 20', dateEcheance: "20 Janvier 2026", color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 2 },
  { id: 9, titre: 'SAE 202 - Gestion de Projet', semestre: 'S4', groupe: 'g4', mois: 'Juillet', jour: 5, date: 'Juil 05', dateEcheance: "05 Juillet 2026", color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 6 },
  { id: 10, titre: 'SAE 203 - Ergonomie', semestre: 'S2', groupe: 'g2', mois: 'Fevrier', jour: 28, date: 'Fev 28', dateEcheance: "28 Fevrier 2026", color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 3 },
  { id: 11, titre: 'SAE 501 - Hébergement Web', semestre: 'S5', groupe: 'g1', mois: 'Septembre', jour: 1, date: 'Sep 01', dateEcheance: "01 Septembre 2026", color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 1 }
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

const toutesLesSAE = ref([]);

onMounted(() => {
  toutesLesSAE.value = loadSAEs();
});

const saesFiltrees = computed(() => {
  return toutesLesSAE.value.filter(sae => {
    const matchSearch = sae.titre.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchSemestre = filtreSemestre.value === '' || sae.semestre === filtreSemestre.value;
    const matchGroupe = filtreGroupe.value === '' || sae.groupe === filtreGroupe.value;
    
    return matchSearch && matchSemestre && matchGroupe;
  });
});

const afficherAjoutSaePopup = ref(false);
const nouvelleSae = ref({ titre: '', semestre: 'S1', groupe: 'g1', dateInput: '', description: '', fichiers: [] });

const ouvrirAjoutSAE = () => {
  afficherAjoutSaePopup.value = true;
  lireAction('Ouverture du formulaire d\'ajout de SAE');
};

const gererUploadFichier = (event) => {
  nouvelleSae.value.fichiers = Array.from(event.target.files);
};

const validerAjoutSAE = () => {
  const objDate = new Date(nouvelleSae.value.dateInput);
  
  let moisSae = moisActuelString;
  let jourSae = jourActuel;
  let formattedDate = moisActuelAbrege; 
  let dateFinaleFull = "À définir";
  
  if (!isNaN(objDate)) {
    moisSae = nomsMoisGlobaux[objDate.getMonth()];
    jourSae = objDate.getDate();
    formattedDate = `${moisSae.substring(0, 3)} ${jourSae}`;
    dateFinaleFull = `${jourSae} ${moisSae} ${objDate.getFullYear()}`;
  }

  toutesLesSAE.value.push({
    id: Date.now(),
    titre: nouvelleSae.value.titre,
    semestre: nouvelleSae.value.semestre,
    groupe: nouvelleSae.value.groupe,
    mois: moisSae,
    jour: jourSae,
    date: formattedDate,
    dateEcheance: dateFinaleFull,
    description: nouvelleSae.value.description || "Aucune description fournie.",
    color: '#f59e0b', 
    notifs: 0
  });

  saveSAEs(toutesLesSAE.value);
  afficherAjoutSaePopup.value = false;
  
  lireAction(`La SAE ${nouvelleSae.value.titre} a été ajoutée et la vue a été mise à jour. Échéance prévue le ${dateFinaleFull}. Description : ${nouvelleSae.value.description}`);
  
  nouvelleSae.value = { titre: '', semestre: 'S1', groupe: 'g1', dateInput: '', description: '', fichiers: [] };
  alert("SAE ajoutée avec succès !");
};

const modifierSAE = (id) => {
  lireAction('Redirection vers la modification de la SAE');
  router.push({ path: '/admin/sae/' + id, query: { edit: 'true' } });
};

const supprimerSAE = (id) => {
  lireAction('Demande de suppression de la SAE');
  if (confirm("Êtes-vous sûr de vouloir supprimer cette SAE ?")) {
    toutesLesSAE.value = toutesLesSAE.value.filter(sae => sae.id !== id);
    saveSAEs(toutesLesSAE.value);
    lireAction('La SAE a été supprimée avec succès.');
  }
};

watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    const nbSae = saesFiltrees.value.length;
    let texteALire = `Bonjour. Vous êtes sur la page de gestion globale des SAE. En haut de la page, vous trouverez une barre de recherche, des filtres par Semestre et par Groupe, ainsi qu'un bouton pour ajouter une nouvelle SAE. `;
    if (nbSae === 0) {
      texteALire += `Actuellement, aucune SAE ne correspond à vos critères de recherche. `;
    } else {
      texteALire += `La grille affiche actuellement ${nbSae} SAE. Fin de la description.`;
    }
    
    accessStore.lireTexte(texteALire);
  }
});
</script>

<style scoped src="../css/AdminDashboard_SAE.css"></style>