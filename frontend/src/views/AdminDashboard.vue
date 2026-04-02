<template>
  <div class="dashboard-layout">
    
    <SidebarMenu />

    <main class="main-content" id="main-content-area">
      
      <header class="top-header">
        <div class="search-bar"></div>
        
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
            <span>Admin</span>
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

      <div class="content-area" role="main">
        <h1>Bienvenue <span class="breadcrumb" aria-hidden="true">Page / Tableau De Bord</span></h1>
        <hr class="divider" />

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

        <h2>SAE En Cours ({{ saesFiltrees.length }})</h2>
        
        <section class="sae-grid">
          <article v-for="sae in saesFiltrees" :key="sae.id" class="sae-card" tabindex="0" @click="router.push('/admin/sae/' + sae.id)">
            <div class="sae-gradient" :style="{ background: sae.color }" aria-hidden="true"></div>
            <h3 class="sae-title">{{ sae.titre }}</h3>
            <div class="sae-info">
              <span aria-label="Nombre de notifications">📢 {{ sae.notifs }}</span>
              <span aria-label="Date limite">📅 {{ moisAbrege(sae.mois) }} {{ sae.jour }}</span>
            </div>
          </article>
          <p v-if="saesFiltrees.length === 0" class="no-sae" role="status">Aucune SAE trouvée pour ce mois.</p>
        </section>

        <section class="add-sae-section">
          <form @submit.prevent="ajouterSAE" class="add-sae-form">
            <div class="add-sae-header">
              <h3>Ajouter SAE</h3>
              <button type="submit" class="btn-add-circle" aria-label="Ajouter la SAE">+</button>
            </div>

            <div class="form-row">
              <input v-model="nouvelleSae.titre" type="text" placeholder="Ex: SAE 301 - Intitulé..." class="input-title" required aria-label="Intitulé de la SAE" />
              
              <select v-model="nouvelleSae.semestre" class="input-select" aria-label="Semestre">
                <option value="S1">Semestre 1</option>
                <option value="S2">Semestre 2</option>
                <option value="S3">Semestre 3</option>
                <option value="S4">Semestre 4</option>
                <option value="S5">Semestre 5</option>
                <option value="S6">Semestre 6</option>
              </select>

              <select v-model="nouvelleSae.groupe" class="input-select" aria-label="Groupe">
                <option value="Tous">Tous</option>
                <option value="Dévelopemment web">Dévelopemment web</option>
                <option value="Création numérique">Création numérique</option>
              </select>
              
              <div class="date-group">
                <span class="date-label">Date de début</span>
                <input v-model="nouvelleSae.debut" type="date" title="Date de début" class="input-date" required aria-label="Date de début" />
              </div>
              
              <div class="date-group">
                <span class="date-label">Date de fin</span>
                <input v-model="nouvelleSae.fin" type="date" title="Date de fin" class="input-date" required aria-label="Date de fin" />
              </div>
            </div>
            
            <textarea v-model="nouvelleSae.description" placeholder="Description..." class="input-desc" aria-label="Description de la SAE"></textarea>
          </form>
          <p v-if="messageConfirmation" class="success-msg" role="alert">✅ SAE ajoutée avec succès !</p>
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
  return mois.substring(0, 3); // Nov, Dec, etc.
};

// Filtrage des SAE en fonction du mois sélectionné et du jour sélectionné
const saesFiltrees = computed(() => {
  return listeSAE_BDD.value.filter(sae => sae.mois === moisSelectionne.value);
});

// Ajout d'une nouvelle SAE
const nouvelleSae = ref({ titre: '', semestre: 'S1', groupe: 'A', debut: '', fin: '', description: '' });
const messageConfirmation = ref(false);

const ajouterSAE = () => {
  let moisSae = moisSelectionne.value;
  let jourSae = jourSelectionne.value;
  let dateAbregee = moisAbrege(moisSae) + ' ' + jourSae;

  if (nouvelleSae.value.debut) {
    const objDate = new Date(nouvelleSae.value.debut);
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
    semestre: nouvelleSae.value.semestre,
    groupe: nouvelleSae.value.groupe,
    titre: nouvelleSae.value.titre || 'Nouvelle SAE',
    description: nouvelleSae.value.description || "Aucune description fournie.", 
    color: '#f59e0b', 
    notifs: 0
  });

  saveSAEs(listeSAE_BDD.value);
  messageConfirmation.value = true;
  
  setTimeout(() => {
    messageConfirmation.value = false;
    nouvelleSae.value = { titre: '', semestre: 'S1', groupe: 'A', debut: '', fin: '', description: '' };
  }, 3000);
};

// Gestion des menus et actions du profil
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

// --- ACCESSIBILITÉ ET SYNTHÈSE VOCALE ---
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
    
    let texteALire = `Bonjour. Vous êtes sur la page d'accueil du tableau de bord administrateur des métiers du multimédia et de l'internet. `;
    texteALire += `Le calendrier est actuellement positionné sur le mois de ${moisSelectionne.value}. `;
    
    if (nbSae === 0) {
      texteALire += `Il n'y a aucune S.A.E en cours pour ce mois. `;
    } else if (nbSae === 1) {
      texteALire += `Il y a une S.A.E en cours ce mois-ci. `;
    } else {
      texteALire += `Il y a ${nbSae} S.A.E en cours ce mois-ci. `;
    }

    if (nbSae > 0) {
      texteALire += `Voici la liste des S.A.E : `;
      saesFiltrees.value.forEach((sae, index) => {
        texteALire += `Numéro ${index + 1} : ${sae.titre}, avec ${sae.notifs} notifications. La date clé est le ${sae.jour} ${sae.mois}. `;
      });
    }
    
    texteALire += `Plus bas sur la page, vous trouverez un formulaire pour ajouter une nouvelle S.A.E. Fin de la description.`;

    const utterance = new SpeechSynthesisUtterance(texteALire);
    utterance.lang = 'fr-FR'; 
    utterance.rate = 0.95; 
    window.speechSynthesis.speak(utterance);
  } else {
    window.speechSynthesis.cancel();
  }
});
</script>

<style scoped src="../css/AdminDashboard.css"></style> 