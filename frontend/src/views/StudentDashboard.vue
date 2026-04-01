<template>
  <div class="dashboard-layout">
    
    <StudentSidebar />

    <main class="main-content" id="main-content-area">
      
      <header class="top-header">
        <div class="search-container">
          <div class="dropdown-filter">
            <select v-model="filtreSemestre" aria-label="Filtrer par semestre" @change="changementSemestre">
              <option value="Par semestre">Par semestre</option>
              <option value="S1">Semestre 1</option>
              <option value="S2">Semestre 2</option>
              <option value="S3">Semestre 3</option>
              <option value="S4">Semestre 4</option>
              <option value="S5">Semestre 5</option>
              <option value="S6">Semestre 6</option>
            </select>
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
          <button @click="toggleParametres" class="icon-btn" title="Paramètres" aria-label="Paramètres étudiant">
            <img src="../assets/icon_parametres.svg" alt="" class="svg-icon" aria-hidden="true" />
          </button>
          
          <div class="user-profile clickable" @click="toggleProfil" role="button" aria-label="Profil de l'étudiant">
            <img src="https://i.pravatar.cc/150?img=32" alt="Avatar" class="avatar-img" />
            <span>Annissa F ⌄</span>
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
              <span class="notif-text">L'étudiant Annissa a rendu son livrable.</span>
            </li>
            <li class="notif-item">
              <span class="notif-icon">⚠️</span> 
              <span class="notif-text">Échéance SAE 316 dans 2 jours.</span>
            </li>
          </ul>
        </div>

        <div v-if="afficherParametres || afficherProfil" class="popup-menu standard-popup">
          <h3 v-if="afficherParametres">Paramètres</h3>
          <h3 v-if="afficherProfil">Profil Étudiant</h3>
          <ul class="action-list">
            <li @click="ouvrirModifMotDePasse" class="action-item">Modifier le mot de passe</li>
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
        
        <div class="page-welcome">
          <h1>Bienvenue Annissa 👋 <span class="breadcrumb" aria-hidden="true">Page / Tableau De Bord</span></h1>
        </div>
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

        <div class="dashboard-grid">
          <section class="main-column">
            <h2>SAE En Cours ({{ saesEnCours.length }})</h2>
            
            <div 
              class="sae-cards-container" 
              ref="saeContainer"
              @mousedown="startDrag" 
              @mouseleave="stopDrag" 
              @mouseup="stopDrag" 
              @mousemove="drag"
              :class="{ 'is-dragging': isDragging }"
            >
              <article v-for="sae in saesEnCours" :key="sae.id" class="sae-card" tabindex="0" @click="naviguerVersSAE(sae)">
                <div class="sae-gradient" :style="{ background: sae.color }" aria-hidden="true"></div>
                <div class="sae-card-body">
                  <h3 class="sae-title">{{ sae.titre }}</h3>
                  <div class="sae-info">
                    <span aria-label="Commentaires"><span class="icon">💬</span> {{ sae.commentaires }}</span>
                    <span aria-label="Date de rendu"><span class="icon">📅</span> {{ sae.dateStr }}</span>
                  </div>
                </div>
              </article>
            </div>
          </section>

          <aside class="side-column">
            <h2>Annonces</h2>
            <div class="annonces-container">
              <div v-for="annonce in annoncesDynamiques" :key="annonce.id" class="annonce-card clickable" @click="naviguerVersSAEDepuisAnnonce(annonce.saeRef)">
                <div class="annonce-icon" :class="'icon-' + annonce.type">
                  {{ getAnnonceIcon(annonce.type) }}
                </div>
                <div class="annonce-content">
                  <h4>{{ annonce.titre }}</h4>
                  <div class="annonce-meta">
                    <span class="sae-ref">{{ annonce.saeRef }}</span>
                    <span class="annonce-date">{{ annonce.date }}</span>
                  </div>
                </div>
              </div>
            </div>
          </aside>
        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, provide } from 'vue';
import { useRouter } from 'vue-router';
import StudentSidebar from '../components/StudentSidebar.vue';

const router = useRouter();

// --- CALENDRIER DYNAMIQUE (SYNCHRO PC) ---
const listMois = ref([]);
const moisSelectionne = ref('');
const jourSelectionne = ref(1);
const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];

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

const selectionnerMois = (mois) => { 
  moisSelectionne.value = mois;
  lireTexte(`Calendrier mis à jour pour ${mois}`);
};
const selectionnerJour = (jour) => { 
  jourSelectionne.value = jour;
  lireTexte(`Jour ${jour} sélectionné`);
};

// --- FILTRE SEMESTRE SYNCHRONISÉ ---
const filtreSemestre = ref('Par semestre');

const changementSemestre = () => {
  lireTexte(`Filtre modifié sur ${filtreSemestre.value}`);
  
  if (['S1', 'S3', 'S5'].includes(filtreSemestre.value)) {
    selectionnerMois('Septembre');
  } else if (['S2', 'S4', 'S6'].includes(filtreSemestre.value)) {
    selectionnerMois('Janvier');
  }
};

// --- DRAG TO SCROLL (SWIPE HORIZONTAL) AVEC FIX POUR LE CLIC ---
const saeContainer = ref(null);
const isDragging = ref(false);
let isMouseDown = false;
let startX = 0;
let scrollLeft = 0;

const startDrag = (e) => {
  isMouseDown = true;
  isDragging.value = false; // Ne pas bloquer le clic immédiatement
  startX = e.pageX - saeContainer.value.offsetLeft;
  scrollLeft = saeContainer.value.scrollLeft;
};
const stopDrag = () => {
  isMouseDown = false;
  // Délai très court pour laisser passer l'événement click si on n'a pas bougé
  setTimeout(() => { isDragging.value = false; }, 10);
};
const drag = (e) => {
  if (!isMouseDown) return;
  
  const x = e.pageX - saeContainer.value.offsetLeft;
  const walk = (x - startX) * 2; // Multiplicateur de vitesse de scroll
  
  // Différencier un simple clic d'un "drag" intentionnel (> 5px de mouvement)
  if (Math.abs(walk) > 5) {
    isDragging.value = true;
  }

  if (isDragging.value) {
    e.preventDefault();
    saeContainer.value.scrollLeft = scrollLeft - walk;
  }
};

// --- ACCESSIBILITÉ & SYNTHÈSE VOCALE ---
const accessibilite = ref({ voix: false, daltonien: false });
const fontScale = ref(1);

const lireTexte = (texte) => {
  if (!accessibilite.value.voix) return;
  window.speechSynthesis.cancel();
  const utterance = new SpeechSynthesisUtterance(texte);
  utterance.lang = 'fr-FR'; 
  utterance.rate = 0.95;
  window.speechSynthesis.speak(utterance);
};
provide('lireTexte', lireTexte);

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
  const isSombre = document.body.classList.toggle('theme-sombre');
  lireTexte(isSombre ? "Mode sombre activé" : "Mode clair activé");
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

const deconnexion = () => { router.push('/'); };

// --- DONNÉES SAE ---
const saesEnCours = ref([
  { id: 316, titre: 'SAE 316', color: 'linear-gradient(135deg, #e879f9, #818cf8)', commentaires: 8, dateStr: 'Nov 30' },
  { id: 304, titre: 'SAE 304', color: 'linear-gradient(135deg, #1e3a8a, #818cf8)', commentaires: 8, dateStr: 'Oct 15' },
  { id: 311, titre: 'SAE 311', color: 'linear-gradient(135deg, #7f1d1d, #14532d)', commentaires: 2, dateStr: 'Dec 12' }
]);

// --- ANNONCES DYNAMIQUES (Liées au calendrier) ---
const annoncesDynamiques = computed(() => {
  let moisAbrev = moisSelectionne.value ? moisSelectionne.value.substring(0, 3).toLowerCase() + '.' : '';
  if (moisSelectionne.value === 'Fevrier') moisAbrev = 'févr.';
  
  const dateJour = jourSelectionne.value;
  const echeance = dateJour <= 26 ? dateJour + 2 : 1; 

  return [
    { id: 1, type: 'warning', titre: 'Rappel Échéance SAE Design', saeRef: 'SAE 311', date: `${echeance} ${moisAbrev}` },
    { id: 2, type: 'info', titre: 'Ressources Disponibles', saeRef: 'SAE 316', date: `${dateJour} ${moisAbrev}` }
  ];
});

const getAnnonceIcon = (type) => type === 'warning' ? '🔔' : (type === 'info' ? 'ℹ️' : '⚠️');

// --- NAVIGATION VERS VUE DETAILLÉE ---
const naviguerVersSAE = (sae) => {
  router.push('/etudiant/sae/' + sae.id);
};

const naviguerVersSAEDepuisAnnonce = (saeRef) => {
  // Extrait le numéro (ex: "311" de "SAE 311") et redirige vers la page détail de cette SAE
  const saeId = saeRef.replace(/\D/g, ''); 
  if (saeId) {
    router.push('/etudiant/sae/' + saeId);
  }
};

// --- SYNCHRONISATION DATE & VOCAL ENRICHIE ---
onMounted(() => {
  const now = new Date();
  listMois.value = genererListeMois();
  moisSelectionne.value = nomsMoisGlobaux[now.getMonth()];
  jourSelectionne.value = now.getDate();
  
  // Synchronisation des dates sur les cartes SAE (Date de l'ordinateur)
  const moisAbrev = moisSelectionne.value.substring(0, 3);
  const dateActuelleStr = `${moisAbrev} ${now.getDate()}`;
  saesEnCours.value.forEach(sae => {
    sae.dateStr = dateActuelleStr;
  });

  if (localStorage.getItem('voix_active') === 'true') {
    accessibilite.value.voix = true;
  }
});

watch(() => accessibilite.value.voix, (estActive) => {
  if (estActive) {
    localStorage.setItem('voix_active', 'true');
    
    // Variables dynamiques pour la lecture
    const nbSae = saesEnCours.value.length;
    const nomsSae = saesEnCours.value.map(s => s.titre).join(' et ');
    const nbAnnonces = annoncesDynamiques.value.length;
    const nomsAnnonces = annoncesDynamiques.value.map(a => a.titre).join(' puis ');

    // Construction du texte enrichi
    let texteALire = `Bonjour Annissa. Bienvenue sur votre tableau de bord étudiant. `;
    texteALire += `Le calendrier est actuellement positionné sur le ${jourSelectionne.value} ${moisSelectionne.value}. `;
    
    if (filtreSemestre.value !== 'Par semestre') {
        texteALire += `Le filtre affiche actuellement les données du ${filtreSemestre.value}. `;
    }

    if (nbSae === 0) {
        texteALire += `Vous n'avez aucune S A E en cours. `;
    } else {
        texteALire += `Vous avez ${nbSae} S A E en cours, qui sont : ${nomsSae}. `;
    }

    if (nbAnnonces > 0) {
        texteALire += `Vous avez également ${nbAnnonces} annonces importantes à lire : ${nomsAnnonces}. `;
    }

    texteALire += `Vous pouvez naviguer vers ces éléments ou utiliser le menu latéral à gauche.`;

    const utterance = new SpeechSynthesisUtterance(texteALire);
    utterance.lang = 'fr-FR'; 
    utterance.rate = 0.95;
    window.speechSynthesis.speak(utterance);
  } else {
    localStorage.setItem('voix_active', 'false');
    window.speechSynthesis.cancel();
  }
});
</script>

<style scoped>
.dashboard-layout { display: flex; height: 100vh; width: 100vw; background-color: #f8fafc; color: #111827; font-family: 'Inter', sans-serif; overflow: hidden; }
.main-content { flex: 1; display: flex; flex-direction: column; overflow-x: hidden; }

/* HEADER GLOBAL */
.top-header { display: flex; justify-content: space-between; padding: 0 2rem; background: #b4d4fe; align-items: center; border-bottom: 1px solid #e5e7eb; height: 80px; box-sizing: border-box; flex-shrink: 0; position: relative;}
.search-container { display: flex; align-items: center; }
.dropdown-filter select { padding: 0.5rem 1rem; border-radius: 8px; border: none; outline: none; font-family: 'Inter'; font-weight: 500; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

.top-icons { display: flex; gap: 15px; align-items: center; }
.icon-btn { background: none; border: none; cursor: pointer; padding: 5px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s; }
.icon-btn:hover { transform: scale(1.1); }
.svg-icon { width: 22px; height: 22px; transition: transform 0.2s; }
.user-profile { display: flex; align-items: center; gap: 10px; font-weight: 600; color: #1e40af; cursor: pointer; padding: 0.3rem 0.8rem; border-radius: 20px;}
.avatar-img { width: 35px; height: 35px; border-radius: 50%; border: 2px solid white; }

/* POPUPS */
.popup-menu { position: absolute; top: 75px; right: 20px; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 340px; z-index: 100; overflow: hidden;}
.standard-popup { padding: 1.5rem; }
.standard-popup h3 { margin-top: 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.8rem; font-size: 1.1rem;}

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

.notif-list { padding-left: 0; list-style: none; margin: 0;}
.notif-item { display: flex; align-items: flex-start; gap: 12px; padding: 0.8rem 0; border-bottom: 1px solid #f3f4f6; }
.notif-item:last-child { border-bottom: none; padding-bottom: 0;}
.notif-icon { font-size: 1.1rem; }
.notif-text { font-size: 0.9rem; color: #374151; line-height: 1.4;}

.action-list { list-style: none; padding: 0; margin: 0;}
.action-item { padding: 0.8rem 1rem; border-bottom: 1px solid #f3f4f6; font-size: 0.95rem; cursor: pointer; transition: 0.2s;}
.action-item:hover { background-color: #f8fafc; color: #3b82f6; }
.action-item:last-child { border-bottom: none; }
.text-red { color: #ef4444; font-weight: 600;}
.text-red:hover { background-color: #fef2f2; color: #dc2626; }

/* MODAL MOT DE PASSE (IDENTIQUE ADMIN) */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-content-center { background: white; padding: 2.5rem; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.modal-content-center h3 { margin-top: 0; color: #111827; margin-bottom: 1.5rem; font-size: 1.3rem;}
.form-group-modal { margin-bottom: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem;}
.form-group-modal label { font-size: 0.9rem; color: #374151; font-weight: 500;}
.input-full { width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-family: 'Inter', sans-serif;}
.modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;}
.btn-cancel { background: transparent; border: none; color: #6b7280; cursor: pointer; font-weight: 500;}
.btn-confirm { background: #3b82f6; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: 500;}

/* SWITCH */
.switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: #3b82f6; }
input:checked + .slider:before { transform: translateX(20px); }

/* CONTENU PRINCIPAL & FIX OVERFLOW PAGE ENTIERE */
.content-area { padding: 1.5rem 2rem; overflow-y: auto; overflow-x: hidden; width: 100%; box-sizing: border-box; }
.page-welcome h1 { margin: 0; font-size: 1.5rem; color: #1f2937; display: flex; align-items: center;}
.breadcrumb { font-size: 0.9rem; color: #6b7280; font-weight: normal; margin-left: 15px; }
.divider { border: 0; height: 2px; background: #e5e7eb; margin-top: 1rem; margin-bottom: 2rem; }

/* TIMELINE CALENDRIER SUR PLUSIEURS LIGNES */
.timeline { margin-bottom: 2rem; width: 100%; }
.calendar-container { display: flex; flex-direction: column; gap: 2rem; }
.months-container, .days-container { width: 100%; }
.months { display: flex; flex-wrap: wrap; column-gap: 2rem; row-gap: 1.5rem; align-items: center; font-weight: 600; color: #9ca3af; font-size: 0.9rem; }
.month-pill { background: none; border: none; padding: 0.4rem 0.2rem; transition: 0.2s; border-bottom: 2px solid transparent; font-family: inherit; cursor: pointer;}
.active-month { color: #312e81; border-bottom: 2px solid #312e81; }
.days { display: flex; flex-wrap: wrap; column-gap: 1.2rem; row-gap: 1rem; align-items: center; color: #6b7280; font-size: 0.9rem; }
.day-circle { background: none; font-family: inherit; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 2px solid transparent; transition: 0.2s; flex-shrink: 0; cursor: pointer;}
.active-day { color: #312e81; font-weight: bold; border: 2px solid #312e81; background: transparent;}

/* GRILLE PRINCIPALE AVEC MINMAX POUR ÉVITER LE DÉBORDEMENT */
.dashboard-grid { 
  display: grid; 
  grid-template-columns: minmax(0, 2.3fr) minmax(0, 1fr); 
  gap: 2rem; 
  width: 100%;
}

/* SAE EN COURS - SWIPE HORIZONTAL (SOURIS) */
.main-column { min-width: 0; }
.main-column h2 { font-size: 1.2rem; margin-top: 0; margin-bottom: 1.5rem; }
.sae-cards-container { 
  display: flex; 
  flex-wrap: nowrap; 
  gap: 1.5rem; 
  overflow-x: auto; 
  padding-bottom: 1rem;
  scrollbar-width: none; 
  -ms-overflow-style: none; 
  cursor: grab;
}
.sae-cards-container::-webkit-scrollbar {
  display: none; 
}
.sae-cards-container.is-dragging {
  cursor: grabbing;
}
.sae-cards-container.is-dragging .sae-card {
  pointer-events: none; 
}

.sae-card { 
  background: white; 
  border-radius: 12px; 
  min-width: 240px; 
  flex-shrink: 0; 
  box-shadow: 0 4px 10px rgba(0,0,0,0.05); 
  cursor: pointer; 
  transition: transform 0.3s; 
  border: 1px solid #f3f4f6;
  user-select: none;
}
.sae-card:hover { transform: translateY(-5px); }
.sae-gradient { height: 130px; border-radius: 8px 8px 0 0; }
.sae-card-body { padding: 1.2rem; }
.sae-title { margin: 0 0 1rem 0; font-size: 1.1rem; color: #111827;}
.sae-info { display: flex; justify-content: space-between; color: #6b7280; font-size: 0.85rem; font-weight: 500;}
.icon { opacity: 0.6; margin-right: 4px;}

/* ANNONCES (Panneau latéral) */
.side-column { min-width: 0; background: #f8fafc; padding: 1.2rem 1.5rem; border-radius: 16px; border: 1px solid #e5e7eb;}
.side-column h2 { font-size: 1.2rem; margin-top: 0; margin-bottom: 1.5rem; }
.annonces-container { display: flex; flex-direction: column; gap: 1rem; }
.annonce-card { display: flex; gap: 1rem; background: white; padding: 1rem; border-radius: 12px; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.02); transition: transform 0.2s, box-shadow 0.2s; }
.annonce-card.clickable { cursor: pointer; }
.annonce-card.clickable:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.annonce-icon { font-size: 1.5rem; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0; }
.icon-warning { color: #d97706; background: #fef3c7; }
.icon-info { color: #2563eb; background: #dbeafe; }
.icon-alert { color: #dc2626; background: #fee2e2; }
.annonce-content { flex: 1; }
.annonce-content h4 { margin: 0 0 5px 0; font-size: 0.95rem; color: #1f2937; line-height: 1.3;}
.annonce-meta { display: flex; justify-content: space-between; font-size: 0.75rem; color: #6b7280; }
.sae-ref { font-weight: 600; color: #9ca3af; }

/* ==========================================================
   MODE SOMBRE GLOBAL ET AMÉLIORÉ
   ========================================================== */
:global(body.theme-sombre) { background-color: #0f172a !important; color: #f8fafc !important; }
:global(body.theme-sombre .dashboard-layout) { background-color: #0f172a !important; }
:global(body.theme-sombre .top-header) { background: #1e293b !important; border-bottom: 1px solid #334155 !important; }
:global(body.theme-sombre .dropdown-filter select) { background: #334155 !important; color: white !important; border-color: #475569 !important;}
:global(body.theme-sombre .page-welcome h1), :global(body.theme-sombre h2), :global(body.theme-sombre h3), :global(body.theme-sombre h4) { color: #f8fafc !important; }

/* Amélioration du mode sombre pour les cartes et annonces */
:global(body.theme-sombre .sae-card), 
:global(body.theme-sombre .popup-menu),
:global(body.theme-sombre .modal-content-center) { 
  background: #1e293b !important; border-color: #334155 !important; color: #ffffff !important; 
}
:global(body.theme-sombre .side-column) { 
  background-color: #1e293b !important; 
  border-color: #334155 !important; 
}
:global(body.theme-sombre .annonce-card) { 
  background-color: #0f172a !important; 
  border-color: transparent !important; 
  box-shadow: 0 4px 6px rgba(0,0,0,0.3) !important; 
}
:global(body.theme-sombre .annonce-card.clickable:hover) {
  box-shadow: 0 6px 12px rgba(0,0,0,0.5) !important; 
}

:global(body.theme-sombre .months .month-pill) { color: #94a3b8 !important; }
:global(body.theme-sombre .active-month) { color: #60a5fa !important; border-bottom: 2px solid #60a5fa !important; background: transparent !important; }
:global(body.theme-sombre .days .day-circle) { color: #cbd5e1 !important; }
:global(body.theme-sombre .active-day) { color: #60a5fa !important; border: 2px solid #60a5fa !important; background: transparent !important; }

:global(body.theme-sombre .sae-title), :global(body.theme-sombre .annonce-content h4) { color: #f8fafc !important; }
:global(body.theme-sombre .sae-info), :global(body.theme-sombre .annonce-meta), :global(body.theme-sombre .sae-ref) { color: #94a3b8 !important; }
:global(body.theme-sombre .divider) { background-color: #334155 !important; }

/* Icônes et inputs sombres */
:global(body.theme-sombre .svg-icon) { filter: brightness(0) invert(1) !important; }
:global(body.theme-sombre .feature-item), :global(body.theme-sombre .notif-item), :global(body.theme-sombre .action-item) { border-color: #334155 !important; color: #f8fafc !important;}
:global(body.theme-sombre .btn-toggle-action:not(.active-zoom):not(.reset-btn)) { background-color: #475569 !important; color: white !important;}
:global(body.theme-sombre input:not([type="checkbox"])) { background-color: #334155 !important; color: #f8fafc !important; border-color: #475569 !important; }
</style>