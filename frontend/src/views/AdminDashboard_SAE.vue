<template>
  <div class="dashboard-layout">
    
    <SidebarMenu />

    <main class="main-content" id="main-content-area">
      
      <header class="top-header">
        <div class="search-container">
          <div class="search-bar">
            <img src="../assets/icon_loupe.svg" alt="Rechercher" class="search-icon-svg" aria-hidden="true" />
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Rechercher une SAE..." 
              class="search-input" 
              aria-label="Rechercher une SAE par mot-clé" 
            />
          </div>
        </div>
        
        <div class="top-icons">
          <button @click="toggleMenuAccessibilite" class="icon-btn" title="Accessibilité" aria-label="Menu Accessibilité">
            <img src="../assets/icon_accessibilites.svg" alt="" class="svg-icon" aria-hidden="true" />
          </button>
          <button @click="toggleNotifications" class="icon-btn" title="Notifications" aria-label="Notifications">
            <img src="../assets/icon_annonces.svg" alt="" class="svg-icon" aria-hidden="true" />
          </button>
          <button @click="toggleModeSombre" class="icon-btn" title="Mode Sombre" aria-label="Activer ou désactiver le mode sombre">
            <img src="../assets/icon_dark_mode.svg" alt="" class="svg-icon" aria-hidden="true" />
          </button>
          <button @click="toggleParametres" class="icon-btn" title="Paramètres" aria-label="Paramètres administrateur">
            <img src="../assets/icon_parametres.svg" alt="" class="svg-icon" aria-hidden="true" />
          </button>
          
          <div class="user-profile clickable" @click="toggleProfil" role="button" aria-label="Profil administrateur">
            <span>Admin ⌄</span>
          </div>
        </div>

        <div v-if="afficherAccessibilite" class="popup-menu accessibilite-menu" role="dialog" aria-labelledby="acc-title">
          <div class="popup-header-black">
            <h3 id="acc-title">Accessibilité</h3>
            <button @click="toggleMenuAccessibilite" class="close-btn-white" aria-label="Fermer le menu accessibilité">✕</button>
          </div>
          <div class="popup-body">
            <div class="feature-item">
              <div class="feature-label">
                <span class="icon-wrapper" aria-hidden="true">🔊</span> 
                <span>Synthèse vocale</span>
              </div>
              <label class="switch" aria-label="Activer la synthèse vocale de la page">
                <input type="checkbox" v-model="accessibilite.voix">
                <span class="slider round"></span>
              </label>
            </div>
            <div class="feature-item zoom-feature">
              <div class="feature-label">
                <span class="icon-wrapper font-bold" aria-hidden="true">A+</span> 
                <span>Agrandir le texte</span>
              </div>
              <div class="zoom-buttons">
                <button @click="definirPolice(1.3)" :class="{'active-zoom': fontScale === 1.3}" class="btn-toggle-action" aria-label="Taille de texte normale">x1</button>
                <button @click="definirPolice(1.6)" :class="{'active-zoom': fontScale === 1.6}" class="btn-toggle-action" aria-label="Taille de texte moyenne">x2</button>
                <button @click="definirPolice(1.9)" :class="{'active-zoom': fontScale === 1.9}" class="btn-toggle-action" aria-label="Taille de texte très grande">x3</button>
                <button @click="definirPolice(1)" class="btn-toggle-action reset-btn" v-if="fontScale > 1" aria-label="Réinitialiser la taille du texte">↺</button>
              </div>
            </div>
            <div class="feature-item">
              <div class="feature-label">
                <span class="icon-wrapper" aria-hidden="true">👁️</span> 
                <span>Mode Daltonien</span>
              </div>
              <label class="switch" aria-label="Activer le mode daltonien">
                <input type="checkbox" v-model="accessibilite.daltonien" @change="appliquerFiltreDaltonien">
                <span class="slider round"></span>
              </label>
            </div>
          </div>
        </div>

        <div v-if="afficherNotifications" class="popup-menu standard-popup">
          <h3>Notifications</h3>
          <ul class="notif-list">
            <li class="notif-item">
              <span class="notif-icon">📢</span> 
              <span class="notif-text">L'étudiant Dubois a rendu son livrable.</span>
            </li>
            <li class="notif-item">
              <span class="notif-icon">⚠️</span> 
              <span class="notif-text">Échéance SAE 316 dans 2 jours.</span>
            </li>
          </ul>
        </div>

        <div v-if="afficherParametres || afficherProfil" class="popup-menu standard-popup">
          <h3 v-if="afficherParametres">Paramètres</h3>
          <h3 v-if="afficherProfil">Profil Administrateur</h3>
          <ul class="action-list">
            <li @click="ouvrirModifMotDePasse" class="action-item">Modifier le mot de passe</li>
            <li @click="allerGererDroits" class="action-item">Gérer les droits</li>
            <li v-if="afficherProfil" @click="deconnexion" class="action-item text-red">Déconnexion</li>
          </ul>
        </div>

      </header>

      <div v-if="afficherMotDePassePopup" class="modal-overlay" @click="afficherMotDePassePopup = false">
        <div class="modal-content-center" @click.stop>
          <h3>Réinitialiser le mot de passe</h3>
          <div class="form-group-modal">
            <label>Nouveau mot de passe</label>
            <input type="password" placeholder="••••••••" class="input-full" />
          </div>
          <div class="form-group-modal">
            <label>Confirmer le mot de passe</label>
            <input type="password" placeholder="••••••••" class="input-full" />
          </div>
          <div class="modal-actions">
            <button @click="afficherMotDePassePopup = false" class="btn-cancel">Annuler</button>
            <button @click="validerNouveauMotDePasse" class="btn-confirm">Enregistrer</button>
          </div>
        </div>
      </div>

      <div v-if="afficherAjoutSaePopup" class="modal-overlay" @click="afficherAjoutSaePopup = false">
        <div class="modal-content-center modal-xl" @click.stop>
          <h3>Ajouter une nouvelle S.A.E</h3>
          <form @submit.prevent="validerAjoutSAE" class="ajout-sae-form">
            <div class="form-group-modal">
              <label>Intitulé de la S.A.E</label>
              <input v-model="nouvelleSae.titre" type="text" placeholder="Ex: SAE 301 - Projet Web..." class="input-full" required />
            </div>
            
            <div class="form-row-modal">
              <div class="form-group-modal flex-1">
                <label>Semestre</label>
                <select v-model="nouvelleSae.semestre" class="input-full" required>
                  <option value="S1">Semestre 1</option>
                  <option value="S2">Semestre 2</option>
                  <option value="S3">Semestre 3</option>
                  <option value="S4">Semestre 4</option>
                  <option value="S5">Semestre 5</option>
                  <option value="S6">Semestre 6</option>
                </select>
              </div>
              <div class="form-group-modal flex-1">
                <label>Groupe</label>
                <select v-model="nouvelleSae.groupe" class="input-full" required>
                  <option value="g1">A1</option>
                  <option value="g2">A2</option>
                  <option value="g3">B1</option>
                  <option value="g4">B2</option>
                </select>
              </div>
            </div>

            <div class="form-row-modal">
              <div class="form-group-modal flex-1">
                <label>Date limite / Rendu</label>
                <input v-model="nouvelleSae.dateInput" type="date" class="input-full" required />
              </div>
              <div class="form-group-modal flex-1">
                <label>Ressources (Sujet, annexes...)</label>
                <input type="file" @change="gererUploadFichier" class="input-full file-input" multiple />
              </div>
            </div>

            <div class="form-group-modal">
              <label>Consignes / Description de la SAE</label>
              <textarea v-model="nouvelleSae.description" placeholder="Saisissez les consignes ici..." class="input-full textarea-consigne" required></textarea>
            </div>
            
            <div class="modal-actions">
              <button type="button" @click="afficherAjoutSaePopup = false" class="btn-cancel">Annuler</button>
              <button type="submit" class="btn-confirm">Ajouter la SAE</button>
            </div>
          </form>
        </div>
      </div>

      <div class="content-area" role="main">
        
        <div class="page-header">
          <h1>Bienvenue <span class="breadcrumb" aria-hidden="true">Page / SAE</span></h1>
          <button class="btn-add-main" @click="ouvrirAjoutSAE" aria-label="Ajouter une nouvelle S.A.E">
            ➕
          </button>
        </div>
        <hr class="divider" />

        <div class="filters-toolbar">
          <select v-model="filtreSemestre" class="filter-select" aria-label="Filtrer par semestre">
            <option value="">Semestre</option>
            <option value="S1">Semestre 1</option>
            <option value="S2">Semestre 2</option>
            <option value="S3">Semestre 3</option>
            <option value="S4">Semestre 4</option>
            <option value="S5">Semestre 5</option>
            <option value="S6">Semestre 6</option>
          </select>
          <select v-model="filtreGroupe" class="filter-select" aria-label="Filtrer par groupe">
            <option value="">Groupe</option>
            <option value="g1">A1</option>
            <option value="g2">A2</option>
            <option value="g3">B1</option>
            <option value="g4">B2</option>
          </select>
        </div>

        <section class="sae-grid">
          <article v-for="sae in saesFiltrees" :key="sae.id" class="sae-card" tabindex="0" @click="router.push('/admin/sae/' + sae.id)">
            <div class="sae-image-container">
              <div class="sae-gradient" :style="{ background: sae.color }" aria-hidden="true"></div>
              
              <div class="sae-card-actions">
                <button class="action-btn" @click.stop="modifierSAE(sae.id)" title="Modifier" aria-label="Modifier la S.A.E">
                  <img src="../assets/icon_modifier.svg" alt="Modifier" class="action-svg" />
                </button>
                <button class="action-btn" @click.stop="supprimerSAE(sae.id)" title="Supprimer" aria-label="Supprimer la S.A.E">
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

          <p v-if="saesFiltrees.length === 0" class="no-sae" role="status">Aucune S.A.E trouvée avec ces critères.</p>
        </section>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import SidebarMenu from '../components/SidebarMenu.vue';

const router = useRouter();

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
  { id: 1, titre: 'SAE 301 - Projet Web', semestre: 'S1', groupe: 'g1', mois: moisActuelString, jour: jourActuel, date: moisActuelAbrege, color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 8, description: "Réaliser une application web full-stack avec framework front et back, API REST.", filiere: "Développement Web", consignes: "Fournir le code source complet sur un dépôt Git.\n\nLa documentation technique doit inclure le diagramme de base de données.\n\nLe rendu est individuel." },
  { id: 2, titre: 'SAE 303 - Design UX/UI', semestre: 'S1', groupe: 'g2', mois: moisActuelString, jour: jourActuel, date: 'Avr 15', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 2, description: "Concevoir l'interface utilisateur d'une application mobile en respectant les principes UX.", filiere: "Création Numérique", consignes: "Le livrable doit inclure le lien du prototype interactif Figma avec tous les écrans connectés. L'UI Kit doit être fourni." },
  { id: 3, titre: 'SAE 302 - Communication', semestre: 'S2', groupe: 'g1', mois: moisActuelString, jour: jourActuel, date: 'Mai 02', color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 5, description: "Établir une stratégie de communication digitale pour un lancement de produit.", filiere: "Communication", consignes: "Rédiger un dossier PDF de 15 pages minimum incluant le budget et le planning de publication. Un support de presentation oral est requis." },
  { id: 4, titre: 'SAE 401 - Audiovisuel', semestre: 'S3', groupe: 'g3', mois: 'Novembre', jour: 12, date: 'Nov 12', color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 1 },
  { id: 5, titre: 'SAE 201 - Intégration', semestre: 'S1', groupe: 'g1', mois: 'Avril', jour: 30, date: 'Avr 30', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 9 },
  { id: 6, titre: 'SAE 402 - Développement Back', semestre: 'S2', groupe: 'g3', mois: 'Decembre', jour: 15, date: 'Dec 15', color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 0 },
  { id: 7, titre: 'SAE 304 - Stratégie Marketing', semestre: 'S3', groupe: 'g2', mois: 'Juin', jour: 10, date: 'Juin 10', color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 4 },
  { id: 8, titre: 'SAE 403 - Motion Design', semestre: 'S4', groupe: 'g1', mois: 'Janvier', jour: 20, date: 'Jan 20', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 2 },
  { id: 9, titre: 'SAE 202 - Gestion de Projet', semestre: 'S4', groupe: 'g4', mois: 'Juillet', jour: 5, date: 'Juil 05', color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 6 },
  { id: 10, titre: 'SAE 203 - Ergonomie', semestre: 'S2', groupe: 'g2', mois: 'Fevrier', jour: 28, date: 'Fev 28', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 3 },
  { id: 11, titre: 'SAE 501 - Hébergement Web', semestre: 'S5', groupe: 'g1', mois: 'Septembre', jour: 1, date: 'Sep 01', color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 1 }
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
  nouvelleSae.value = { titre: '', semestre: 'S1', groupe: 'g1', dateInput: '', description: '', fichiers: [] };
  alert("S.A.E ajoutée avec succès !");
};

// --- MODIFICATION DÉCLENCHÉE ICI ---
const modifierSAE = (id) => {
  // Redirige vers la page de détail avec un paramètre "edit" pour ouvrir la modale directement
  router.push({ path: '/admin/sae/' + id, query: { edit: 'true' } });
};

const supprimerSAE = (id) => {
  if (confirm("Êtes-vous sûr de vouloir supprimer cette S.A.E ?")) {
    toutesLesSAE.value = toutesLesSAE.value.filter(sae => sae.id !== id);
    saveSAEs(toutesLesSAE.value);
  }
};

const afficherAccessibilite = ref(false);
const afficherNotifications = ref(false);
const afficherParametres = ref(false);
const afficherProfil = ref(false);
const afficherMotDePassePopup = ref(false);

const fermerTout = () => {
  afficherAccessibilite.value = false;
  afficherNotifications.value = false;
  afficherParametres.value = false;
  afficherProfil.value = false;
};

const toggleMenuAccessibilite = () => { const etat = afficherAccessibilite.value; fermerTout(); afficherAccessibilite.value = !etat; };
const toggleNotifications = () => { const etat = afficherNotifications.value; fermerTout(); afficherNotifications.value = !etat; };
const toggleParametres = () => { const etat = afficherParametres.value; fermerTout(); afficherParametres.value = !etat; };
const toggleProfil = () => { const etat = afficherProfil.value; fermerTout(); afficherProfil.value = !etat; };

const ouvrirModifMotDePasse = () => {
  fermerTout();
  afficherMotDePassePopup.value = true;
};

const validerNouveauMotDePasse = () => {
  alert("Mot de passe mis à jour avec succès !");
  afficherMotDePassePopup.value = false;
};

const allerGererDroits = () => {
  fermerTout();
  router.push('/admin/sae/202'); 
};

const deconnexion = () => {
  router.push('/');
};

const fontScale = ref(1);
const accessibilite = ref({ voix: false, daltonien: false });

const definirPolice = (scale) => {
  fontScale.value = scale;
  document.documentElement.style.fontSize = `${16 * fontScale.value}px`;
};

const appliquerFiltreDaltonien = () => {
  if (accessibilite.value.daltonien) {
    document.body.style.filter = "contrast(110%) sepia(50%) hue-rotate(180deg) saturate(150%)";
  } else {
    document.body.style.filter = "none";
  }
};

const toggleModeSombre = () => {
  document.body.classList.toggle('theme-sombre');
};

watch(() => accessibilite.value.voix, (estActive) => {
  if (estActive) {
    const nbSae = saesFiltrees.value.length;
    let texteALire = `Bonjour. Vous êtes sur la page de gestion globale des S.A.E. En haut de la page, vous trouverez une barre de recherche, des filtres par Semestre et par Groupe, ainsi qu'un bouton pour ajouter une nouvelle S.A.E. `;
    if (nbSae === 0) {
      texteALire += `Actuellement, aucune S.A.E ne correspond à vos critères de recherche. `;
    } else {
      texteALire += `La grille affiche actuellement ${nbSae} S.A.E. Fin de la description.`;
    }
    const utterance = new SpeechSynthesisUtterance(texteALire);
    utterance.lang = 'fr-FR'; 
    utterance.rate = 0.95; 
    window.speechSynthesis.speak(utterance);
  } else {
    window.speechSynthesis.cancel();
  }
});
</script>

<style scoped src="../css/AdminDashboard_SAE.css"></style>