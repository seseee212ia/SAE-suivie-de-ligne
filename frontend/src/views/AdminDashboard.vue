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
          <h3>Ajouter SAE</h3>
          <form @submit.prevent="ajouterSAE" class="add-sae-form">
            <div class="form-row">
              <input v-model="nouvelleSae.titre" type="text" placeholder="Intitulé..." class="input-title" required aria-label="Intitulé de la SAE" />
              
              <select v-model="nouvelleSae.semestre" class="input-select" aria-label="Semestre">
                <option value="S1">Semestre 1</option>
                <option value="S2">Semestre 2</option>
                <option value="S3">Semestre 3</option>
                <option value="S4">Semestre 4</option>
                <option value="S5">Semestre 5</option>
                <option value="S6">Semestre 6</option>
              </select>
              
              <input v-model="nouvelleSae.debut" type="date" title="Date de début" class="input-date" required aria-label="Date de début" />
              <input v-model="nouvelleSae.fin" type="date" title="Date de fin" class="input-date" required aria-label="Date de fin" />
              
              <button type="submit" class="btn-add-circle" aria-label="Ajouter la SAE">+</button>
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

// --- CALENDRIER ET FILTRAGE ---
const listMois = ref([]);
const moisSelectionne = ref('');
const jourSelectionne = ref(1);

// Noms des mois globaux pour la date du jour
const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];
const moisActuelString = nomsMoisGlobaux[new Date().getMonth()];

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

onMounted(() => {
  const dateActuelle = new Date();
  listMois.value = genererListeMois();
  moisSelectionne.value = moisActuelString;
  jourSelectionne.value = dateActuelle.getDate();
});

const moisAbrege = (mois) => {
  if (!mois) return '';
  return mois.substring(0, 3); // Nov, Dec, etc.
};

// --- BASE DE DONNÉES SAE ---
// CORRECTION : Les IDs et les couleurs ont été alignés avec la page de détails !
const listeSAE_BDD = ref([
  { id: 1, mois: moisActuelString, jour: 28, titre: 'SAE 301 - Projet Web', color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)', notifs: 8 },
  { id: 2, mois: moisActuelString, jour: 15, titre: 'SAE 303 - Design UI/UX', color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)', notifs: 5 },
  { id: 3, mois: moisActuelString, jour: 30, titre: 'SAE 302 - Communication', color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)', notifs: 3 },
  { id: 4, mois: 'Novembre', jour: 12, titre: 'SAE 401 - Audiovisuel', color: 'linear-gradient(135deg, #10b981, #059669)', notifs: 2 }
]);

const saesFiltrees = computed(() => {
  return listeSAE_BDD.value.filter(sae => sae.mois === moisSelectionne.value);
});

// --- AJOUT SAE ---
const nouvelleSae = ref({ titre: '', semestre: 'S1', debut: '', fin: '', description: '' });
const messageConfirmation = ref(false);

const ajouterSAE = () => {
  listeSAE_BDD.value.push({ 
    id: Date.now(), 
    mois: moisSelectionne.value, 
    jour: jourSelectionne.value,
    titre: nouvelleSae.value.titre || 'Nouvelle SAE',
    color: '#f59e0b', 
    notifs: 0
  });
  messageConfirmation.value = true;
  
  setTimeout(() => {
    messageConfirmation.value = false;
    nouvelleSae.value = { titre: '', semestre: 'S1', debut: '', fin: '', description: '' };
  }, 3000);
};

// --- GESTION DES POP-UPS ---
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

// Texte descriptif amélioré pour la synthèse vocale
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
    utterance.rate = 0.95; // Un peu plus lent pour la clarté
    window.speechSynthesis.speak(utterance);
  } else {
    window.speechSynthesis.cancel();
  }
});
</script>

<style scoped>
.dashboard-layout { display: flex; height: 100vh; width: 100vw; background-color: #f3f4f6; color: #111827; font-family: 'Inter', sans-serif; }
.main-content { flex: 1; display: flex; flex-direction: column; position: relative; overflow-x: hidden; }

/* Header */
.top-header { display: flex; justify-content: space-between; padding: 0 2rem; background: #B4D4FE; align-items: center; border-bottom: 1px solid #e5e7eb; height: 80px; box-sizing: border-box;}
.top-icons { display: flex; gap: 15px; align-items: center; }
.icon-btn { background: none; border: none; cursor: pointer; color: #6b7280; padding: 5px; display: flex; align-items: center; justify-content: center; }
.svg-icon { width: 22px; height: 22px; transition: transform 0.2s; }
.svg-icon:hover { transform: scale(1.1); }
.user-profile { font-weight: 600; color: #1e40af; margin-left: 20px;}
.clickable { cursor: pointer; transition: color 0.2s; }
.clickable:hover { color: #3b82f6; }

/* POP-UPS GÉNÉRAUX */
.popup-menu { position: absolute; top: 75px; right: 20px; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 340px; z-index: 100; overflow: hidden;}
.standard-popup { padding: 1.5rem; }
.standard-popup h3 { margin-top: 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.8rem; font-size: 1.1rem;}

/* DESIGN NOTIFICATIONS & MESSAGES */
.notif-list { padding-left: 0; list-style: none; margin: 0;}
.notif-item { display: flex; align-items: flex-start; gap: 12px; padding: 0.8rem 0; border-bottom: 1px solid #f3f4f6; }
.notif-item:last-child { border-bottom: none; padding-bottom: 0;}
.notif-icon { font-size: 1.1rem; }
.notif-text { font-size: 0.9rem; color: #374151; line-height: 1.4;}

/* DESIGN PARAMÈTRES & PROFIL */
.action-list { list-style: none; padding: 0; margin: 0;}
.action-item { padding: 0.8rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: 0.95rem; cursor: pointer; transition: 0.2s;}
.action-item:hover { background-color: #f8fafc; color: #3b82f6; }
.action-item:last-child { border-bottom: none; }
.text-red { color: #ef4444; font-weight: 600;}
.text-red:hover { background-color: #fef2f2; color: #dc2626; }

/* POP-UP ACCESSIBILITÉ */
.accessibilite-menu { width: 340px; }
.popup-header-black { background-color: black; color: white; display: flex; justify-content: space-between; align-items: center; padding: 1rem; }
.popup-header-black h3 { margin: 0; font-size: 1.1rem; font-weight: normal;}
.close-btn-white { background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; }
.popup-body { padding: 0.5rem 0; }
.feature-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem; border-bottom: 1px solid #f3f4f6; }
.zoom-feature { flex-direction: column; align-items: flex-start; gap: 15px; }
.zoom-buttons { display: flex; gap: 5px; }
.feature-label { display: flex; align-items: center; gap: 12px; font-size: 1rem; color: #374151; font-weight: 500;}
.icon-wrapper { display: flex; align-items: center; justify-content: center; width: 24px; font-size: 1.2rem; }
.font-bold { font-weight: bold; font-family: serif; }

.btn-toggle-action { background: #e5e7eb; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; font-size: 0.9rem; transition: 0.2s;}
.btn-toggle-action:hover { background: #d1d5db; }
.active-zoom { background: #3b82f6 !important; color: white; border-color: #3b82f6; }
.reset-btn { background: #ef4444 !important; color: white; }
.reset-btn:hover { background: #dc2626 !important; }

/* MODAL MOT DE PASSE */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-content-center { background: white; padding: 2rem; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.modal-content-center h3 { margin-top: 0; color: #111827; margin-bottom: 1.5rem;}
.form-group-modal { margin-bottom: 1rem; display: flex; flex-direction: column; gap: 0.5rem;}
.form-group-modal label { font-size: 0.9rem; color: #374151;}
.input-full { width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;}
.modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;}
.btn-cancel { background: transparent; border: none; color: #6b7280; cursor: pointer;}
.btn-confirm { background: #3b82f6; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer;}

/* Switch */
.switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #2196F3; }
input:checked + .slider:before { transform: translateX(20px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

/* CONTENU DE LA PAGE */
.content-area { padding: 2rem 3rem; overflow-y: auto; width: 100%; box-sizing: border-box;}
.breadcrumb { font-size: 0.8rem; color: #6b7280; font-weight: normal; margin-left: 10px;}
.divider { border: 0; height: 1px; background: #d1d5db; margin-bottom: 2rem; }

/* CALENDRIER */
.timeline { margin-bottom: 3rem; width: 100%; }

.calendar-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem; 
}

.months-container, .days-container {
  width: 100%;
}

.months, .days { 
  display: flex; 
  flex-wrap: wrap; 
  gap: 1rem; 
  align-items: center; 
}

.months { font-weight: 600; color: #9ca3af; font-size: 0.9rem; }
.month-pill { background: none; border: none; padding: 0.4rem 0.2rem; margin-right: 0.5rem; transition: 0.2s; border-bottom: 2px solid transparent; font-family: inherit;}
.active-month { color: #312e81; border-bottom: 2px solid #312e81; }

.days { color: #6b7280; font-size: 0.9rem; }
.day-circle { background: none; font-family: inherit; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 2px solid transparent; transition: 0.2s; flex-shrink: 0;}
.active-day { color: #312e81; font-weight: bold; border: 2px solid #312e81; background: transparent;}

/* GRILLE SAE */
h2 { font-size: 1.2rem; color: #111827; margin-bottom: 1rem;}
.sae-grid { display: flex; gap: 1.5rem; margin-bottom: 3rem; flex-wrap: wrap; }
/* Modifications ici: ajout du pointer, de la transition et de l'effet de survol */
.sae-card { background: white; padding: 1rem; border-radius: 12px; width: 220px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #f3f4f6; transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer;}
.sae-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
.sae-title { font-size: 1rem; font-weight: 600; margin: 0 0 10px 0; color: #1f2937; } /* Ajout du style pour le titre SAE */
.sae-gradient { height: 130px; border-radius: 8px; margin-bottom: 1rem; }
.sae-info { display: flex; justify-content: space-between; font-size: 0.8rem; color: #6b7280; border-top: 1px solid #f3f4f6; padding-top: 10px;}
.no-sae { color: #6b7280; font-style: italic; }

/* FORMULAIRE AJOUT SAE */
.add-sae-section { background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); max-width: 900px; margin-bottom: 2rem;}
.add-sae-section h3 { margin-top: 0; font-size: 1.1rem; color: #111827; margin-bottom: 1.5rem;}
.form-row { display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;}
.input-title { flex: 1; min-width: 200px; padding: 0.8rem 1rem; border: 1px solid #d1d5db; border-radius: 8px; font-family: 'Inter', sans-serif;}
.input-select { padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 8px; background: white; cursor: pointer; color: #111827;} 
.input-date { padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 8px; color: #4b5563;}
.btn-add-circle { background-color: #3b82f6; color: white; border: none; width: 45px; height: 45px; border-radius: 50%; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);}
.btn-add-circle:hover { background-color: #2563eb; transform: scale(1.05);}
.input-desc { width: 100%; box-sizing: border-box; padding: 1rem; border: 1px solid #d1d5db; border-radius: 8px; min-height: 120px; font-family: 'Inter', sans-serif; resize: vertical;}
.success-msg { color: #059669; background: #d1fae5; padding: 0.8rem; border-radius: 6px; margin-top: 1rem; font-weight: 500;}

/* ==========================================================
   MODE SOMBRE GLOBAL
   ========================================================== */
:global(body.theme-sombre) { background-color: #111827 !important; color: #ffffff !important; }
:global(body.theme-sombre .dashboard-layout) { background-color: #111827 !important; color: #ffffff !important; }
:global(body.theme-sombre .top-header) { background: #1f2937 !important; border-bottom-color: #374151 !important; }

:global(body.theme-sombre aside),
:global(body.theme-sombre .sidebar-container),
:global(body.theme-sombre .sidebar),
:global(body.theme-sombre .sidebar-menu) {
  background-color: #111827 !important;
  border-right-color: #374151 !important;
}
:global(body.theme-sombre aside > div),
:global(body.theme-sombre .sidebar > div),
:global(body.theme-sombre .sidebar-container > div) {
  background-color: #111827 !important;
}

:global(body.theme-sombre .sae-card), 
:global(body.theme-sombre .add-sae-section), 
:global(body.theme-sombre .popup-menu),
:global(body.theme-sombre .modal-content-center) { 
  background: #1f2937 !important; border-color: #374151 !important; color: #ffffff !important; 
}

:global(body.theme-sombre h1), 
:global(body.theme-sombre h2), 
:global(body.theme-sombre h3),
:global(body.theme-sombre span),
:global(body.theme-sombre p),
:global(body.theme-sombre div:not(.sae-gradient)), 
:global(body.theme-sombre li),
:global(body.theme-sombre a),
:global(body.theme-sombre button) { color: #ffffff !important; }

:global(body.theme-sombre input:not([type="checkbox"])), 
:global(body.theme-sombre select), 
:global(body.theme-sombre textarea) { 
  background-color: #374151 !important; 
  color: #ffffff !important; 
  border-color: #4b5563 !important; 
}

:global(body.theme-sombre select option) {
  background-color: #374151 !important; color: #ffffff !important;
}

:global(body.theme-sombre input::placeholder), 
:global(body.theme-sombre textarea::placeholder) { color: #ffffff !important; }

:global(body.theme-sombre .feature-item), 
:global(body.theme-sombre .notif-item), 
:global(body.theme-sombre .action-item),
:global(body.theme-sombre .sae-info) { border-color: #374151 !important; color: #ffffff !important;}

:global(body.theme-sombre .action-item:hover) { background-color: #374151 !important; color: #60a5fa !important; }
:global(body.theme-sombre .text-red:hover) { background-color: #7f1d1d !important; color: #f87171 !important; }

:global(body.theme-sombre .icon-btn) { color: #ffffff !important; }
:global(body.theme-sombre .svg-icon) { filter: brightness(0) invert(1) !important; }
:global(body.theme-sombre .user-profile span) { color: #60a5fa !important; }
:global(body.theme-sombre .text-red) { color: #ef4444 !important; }

:global(body.theme-sombre img[src*="logo-mmi"]) {
  content: url("../assets/logo-mmi (1).svg") !important;
}

:global(body.theme-sombre .months .month-pill) { color: #9ca3af !important; }
:global(body.theme-sombre .active-month) { color: #60a5fa !important; border-bottom: 2px solid #60a5fa !important; background: transparent !important; }

:global(body.theme-sombre .days .day-circle) { color: #d1d5db !important; }
:global(body.theme-sombre .active-day) { color: #60a5fa !important; border: 2px solid #60a5fa !important; background: transparent !important; }

:global(body.theme-sombre .btn-toggle-action:not(.active-zoom):not(.reset-btn)) { background-color: #4b5563 !important; color: white !important;}
:global(body.theme-sombre .btn-toggle-action:hover:not(.active-zoom):not(.reset-btn)) { background-color: #6b7280 !important;}
</style>