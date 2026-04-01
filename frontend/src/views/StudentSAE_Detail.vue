<template>
  <div class="dashboard-layout">
    
    <StudentSidebar />

    <main class="main-content" id="main-content-area">
      
      <header class="top-header">
        <div class="search-container">
          <div class="search-bar">
            <img src="../assets/icon_loupe.svg" alt="Rechercher" class="search-icon-svg" aria-hidden="true" />
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Rechercher..." 
              class="search-input" 
              aria-label="Rechercher par mot-clé"
              @input="lireTexte('Recherche en cours')"
            />
          </div>
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
              <label class="switch" aria-label="Activer la synthèse vocale">
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
                <button @click="definirPolice(1.3)" :class="{'active-zoom': fontScale === 1.3}" class="btn-toggle-action">x1</button>
                <button @click="definirPolice(1.6)" :class="{'active-zoom': fontScale === 1.6}" class="btn-toggle-action">x2</button>
                <button @click="definirPolice(1.9)" :class="{'active-zoom': fontScale === 1.9}" class="btn-toggle-action">x3</button>
                <button @click="definirPolice(1)" class="btn-toggle-action reset-btn" v-if="fontScale > 1">↺</button>
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
            <li class="notif-item"><span class="notif-icon">📢</span> <span class="notif-text">L'étudiant Annissa a rendu son livrable.</span></li>
            <li class="notif-item"><span class="notif-icon">⚠️</span> <span class="notif-text">Échéance SAE 316 dans 2 jours.</span></li>
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

      <div v-if="annonceOuverte" class="modal-overlay" @click="fermerAnnonce">
        <div class="modal-content-annonce" @click.stop>
          <button class="close-btn-cross" @click="fermerAnnonce" aria-label="Fermer l'annonce">✖</button>
          <div class="annonce-header-flex">
            <span class="icon-megaphone-lg" aria-hidden="true">📢</span>
            <div class="annonce-header-text">
              <h3>{{ annonceOuverte.titre }} Du {{ annonceOuverte.date }}</h3>
              <span class="annonce-meta">PAR {{ annonceOuverte.auteur }} - {{ annonceOuverte.date }} | {{ annonceOuverte.heure }}</span>
            </div>
          </div>
          <div class="annonce-body">
            <p>{{ annonceOuverte.contenu }}</p>
          </div>
        </div>
      </div>

      <div class="content-area" role="main">
        
        <div class="page-welcome">
          <h1>Bienvenue Annissa 👋 
            <span class="breadcrumb" aria-hidden="true">
              <span @click="fermerSae" :class="{'clickable-breadcrumb': saeSelectionnee}" title="Retour au tableau de bord">Page / Mes SAE</span>
              <span v-if="saeSelectionnee"> / {{ saeSelectionnee.titre }}</span>
            </span>
          </h1>
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
                >
                  {{ jour }}
                </button>
              </div>
            </div>
          </div>
        </div>

        
        <div v-if="!saeSelectionnee" class="kanban-board">
          
          <div class="kanban-column">
            <div class="column-header border-todo">
              <span class="dot dot-todo"></span>
              <h3>A faire</h3>
              <span class="badge-count">{{ todoSaes.length }}</span>
            </div>
            
            <article v-for="sae in todoSaes" :key="sae.id" class="kanban-card" :class="{'tilted': sae.tilted}" @click="ouvrirSae(sae)">
              <div class="card-tag" :class="sae.tagClass">{{ sae.tag }}</div>
              <div class="card-header-flex">
                <h4 class="card-title">{{ sae.titre }}</h4>
                <button class="more-options" @click.stop>•••</button>
              </div>
              <p class="card-desc">{{ sae.description }}</p>
              
              <div class="card-footer">
                <span class="card-stat">💬 {{ sae.commentaires }}</span>
                <span class="card-date">📅 {{ sae.dateStr }}</span>
              </div>
            </article>
          </div>

          <div class="kanban-column">
            <div class="column-header border-inprogress">
              <span class="dot dot-inprogress"></span>
              <h3>En cours</h3>
              <span class="badge-count">{{ inProgressSaes.length }}</span>
            </div>
            
            <article v-for="sae in inProgressSaes" :key="sae.id" class="kanban-card" @click="ouvrirSae(sae)">
              <div class="card-tag" :class="sae.tagClass">{{ sae.tag }}</div>
              <div class="card-header-flex">
                <h4 class="card-title">{{ sae.titre }}</h4>
                <button class="more-options" @click.stop>•••</button>
              </div>
              
              <p class="card-desc">{{ sae.description }}</p>
              
              <div v-if="sae.image" class="card-img-placeholder" :class="sae.imgClass"></div>
              
              <div class="card-footer">
                <span class="card-stat">💬 {{ sae.commentaires }}</span>
                <span class="card-date">📅 {{ sae.dateStr }}</span>
              </div>
            </article>
          </div>

          <div class="kanban-column">
            <div class="column-header border-done">
              <span class="dot dot-done"></span>
              <h3>Finis</h3>
              <span class="badge-count">{{ doneSaes.length }}</span>
            </div>
            
            <article v-for="sae in doneSaes" :key="sae.id" class="kanban-card" @click="ouvrirSae(sae)">
              <div class="card-tag" :class="sae.tagClass">{{ sae.tag }}</div>
              <div class="card-header-flex">
                <h4 class="card-title">{{ sae.titre }}</h4>
                <button class="more-options" @click.stop>•••</button>
              </div>
              
              <p class="card-desc">{{ sae.description }}</p>
              <div v-if="sae.image" class="card-img-placeholder gray-placeholder"></div>
              
              <div class="card-footer">
                <span class="card-stat">💬 {{ sae.commentaires }}</span>
                <span class="card-date">📅 {{ sae.dateStr }}</span>
              </div>
            </article>
          </div>

        </div>


        <div v-else class="sae-detail-container">
          
          <div class="sae-main-col">
            <div class="sae-panel banner-panel">
              <div class="card-tag tag-margin" :class="saeSelectionnee.tagClass">{{ saeSelectionnee.tag }}</div>
              <div class="header-options">
                <h2 class="sae-detail-title">{{ saeSelectionnee.titre }}</h2>
                <button class="more-options">•••</button>
              </div>
              <div class="banner-img" :class="saeSelectionnee.imgClass || 'img-flowers'"></div>

              <div class="phases-container">
                <div class="phase-item">
                  <span>Phase 1: Maquette</span>
                  <button class="phase-box" :class="{checked: saeSelectionnee.phase1}" @click="togglePhase('phase1')"></button>
                </div>
                <div class="phase-item">
                  <span>Phase 2: Vue Etudiant</span>
                  <button class="phase-box" :class="{checked: saeSelectionnee.phase2}" @click="togglePhase('phase2')"></button>
                </div>
                <div class="phase-item">
                  <span>Phase 3: Vue Enseignant</span>
                  <button class="phase-box" :class="{checked: saeSelectionnee.phase3}" @click="togglePhase('phase3')"></button>
                </div>
              </div>

            </div>
            
            <div class="split-panels">
              <div class="sae-panel">
                <div class="header-options">
                  <h3 class="panel-title">Ressources</h3>
                  <button v-if="saeSelectionnee.estProprietaire" class="btn-icon-transparent" title="Ajouter une ressource">➕</button>
                </div>
                <ul class="item-list">
                  <li v-for="res in mockRessources" :key="res.id" class="list-item">
                     <div class="item-info">
                       <div class="item-title">
                         <span class="icon" :class="{'icon-orange-text': !res.lu, 'icon-gray-text': res.lu}">{{ res.lu ? '📎' : '📄' }}</span> 
                         {{ res.titre }}
                       </div>
                       <div class="item-meta">DÉPOSÉ PAR {{ res.auteur }}</div>
                     </div>
                     <div class="item-date">📅 {{ res.date }} 🕒 {{ res.heure }}</div>
                  </li>
                </ul>
              </div>
              
              <div class="sae-panel">
                <h3 class="panel-title">Annonces</h3>
                <ul class="item-list">
                  <li v-for="ann in mockAnnonces" :key="ann.id" class="list-item clickable-ann" @click="ouvrirAnnonce(ann)">
                     <div class="item-info">
                       <div class="item-title"><span class="icon-orange-text">📢</span> {{ ann.titre }}</div>
                       <div class="item-meta">M. LE CADET</div>
                     </div>
                     <div class="item-date">📅 {{ ann.date }} 🕒 {{ ann.heure }}</div>
                  </li>
                </ul>
              </div>
            </div>

            <div class="sae-panel">
              <h3 class="panel-title">Dépôt & Rendus</h3>
              
              <div class="dropzone-area" 
                @dragover.prevent="dragover = true" 
                @dragleave.prevent="dragover = false" 
                @drop.prevent="handleDrop" 
                :class="{'dragover-active': dragover}">
                <div class="dropzone-content">
                  <span class="icon-upload" aria-hidden="true">☁️</span>
                  <p>Glissez et déposez vos fichiers de livrables ici<br>ou <span class="text-blue clickable" @click="triggerFileInput">parcourez vos dossiers</span></p>
                </div>
                <input type="file" ref="fileInput" @change="handleFileUpload" style="display: none;" multiple />
              </div>

              <h4 class="sub-title mt-2">Fichiers envoyés ({{ mockRendus.length }})</h4>
              <ul class="rendu-list">
                <li v-for="rendu in mockRendus" :key="rendu.id" class="rendu-item">
                  <div class="rendu-title">📄 {{ rendu.titre }}</div>
                  <div class="rendu-actions">
                    <button class="btn-action btn-blue" title="Consulter le document">👁️</button>
                    <button class="btn-action btn-orange" title="Modifier le fichier">✏️</button>
                    <button class="btn-action btn-red" @click="supprimerRendu(rendu.id)" title="Supprimer le fichier">🗑️</button>
                  </div>
                </li>
              </ul>
            </div>
          </div>

          <div class="sae-side-col">
            
            <div class="sae-panel" v-if="saeSelectionnee.estProprietaire">
              <h3 class="panel-title">Paramètres de publication</h3>
              <div class="feature-item no-border no-padding">
                <div class="feature-label">
                  <span class="icon-wrapper" aria-hidden="true">🌐</span> 
                  <span>Rendre publique (Portfolio)</span>
                </div>
                <label class="switch" aria-label="Rendre cette SAE visible publiquement">
                  <input type="checkbox" v-model="saeSelectionnee.estPublique">
                  <span class="slider round"></span>
                </label>
              </div>
            </div>

            <div class="sae-panel">
              <div class="header-options">
                <h3 class="panel-title">Description du projet</h3>
                <button v-if="saeSelectionnee.estProprietaire" class="btn-icon-transparent" title="Modifier la description">✏️</button>
              </div>
              <p class="text-sm desc-text">{{ saeSelectionnee.description }}</p>
              <div class="info-group">
                <p class="text-sm mt-1"><strong>Semestre:</strong> {{ saeSelectionnee.semestre }}</p>
                <p class="text-sm"><strong>Date d'échéance:</strong> {{ saeSelectionnee.dateStr }}</p>
              </div>
            </div>
            
            <div class="sae-panel">
              <div class="header-options">
                <h3 class="panel-title">Consigne</h3>
                <button v-if="saeSelectionnee.estProprietaire" class="btn-icon-transparent" title="Modifier la consigne">✏️</button>
              </div>
              <p class="text-sm desc-text">Veuillez suivre rigoureusement les consignes de cette évaluation pour valider vos compétences. Assurez-vous de vérifier tous les livrables demandés.</p>
            </div>

            <div class="sae-panel center-content">
              <div class="timer-circle">
                <div class="timer-inner">
                  <span class="timer-label">Temps restant</span>
                  <span class="timer-value">35 j 02 h</span>
                </div>
              </div>
            </div>
          </div>

        </div> 
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, provide } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import StudentSidebar from '../components/StudentSidebar.vue';

const router = useRouter();
const route = useRoute();

// --- GESTION VUE DÉTAILLÉE ---
const saeSelectionnee = ref(null);
const annonceOuverte = ref(null);

// Surveille l'URL pour afficher la SAE correspondante si un ID est présent
const checkRouteForSae = () => {
  if (route.params && route.params.id) {
    const idSae = parseInt(route.params.id);
    const foundSae = toutAvecStatusDynamique.value.find(s => s.id === idSae);
    if (foundSae) {
      saeSelectionnee.value = foundSae;
      lireTexte(`Ouverture des détails du projet ${foundSae.titre}`);
    } else {
      saeSelectionnee.value = null;
    }
  } else {
    saeSelectionnee.value = null;
  }
};

const ouvrirSae = (sae) => {
  // Au lieu de la stocker uniquement en local, on change l'URL
  router.push('/etudiant/sae/' + sae.id);
};

const fermerSae = () => {
  // Retour vers le Kanban principal
  router.push('/etudiant/sae');
};

const ouvrirAnnonce = (annonce) => {
  annonceOuverte.value = annonce;
  lireTexte(`Annonce : ${annonce.titre}`);
};

const fermerAnnonce = () => {
  annonceOuverte.value = null;
};

// Modification des phases d'avancement
const togglePhase = (phaseName) => {
  if (saeSelectionnee.value) {
    saeSelectionnee.value[phaseName] = !saeSelectionnee.value[phaseName];
    lireTexte(`Progression modifiée : ${phaseName} est maintenant ${saeSelectionnee.value[phaseName] ? 'validée' : 'non validée'}`);
  }
};

// --- GESTION DU DRAG & DROP DES RENDUS ---
const dragover = ref(false);
const fileInput = ref(null);

const triggerFileInput = () => {
  if(fileInput.value) fileInput.value.click();
};

const handleDrop = (e) => {
  dragover.value = false;
  if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
    Array.from(e.dataTransfer.files).forEach(file => {
      mockRendus.value.push({ id: Date.now() + Math.random(), titre: file.name });
    });
    lireTexte("Fichiers déposés avec succès");
  }
};

const handleFileUpload = (e) => {
  if (e.target.files && e.target.files.length > 0) {
    Array.from(e.target.files).forEach(file => {
      mockRendus.value.push({ id: Date.now() + Math.random(), titre: file.name });
    });
    lireTexte("Fichiers importés avec succès");
  }
};

const supprimerRendu = (id) => {
  mockRendus.value = mockRendus.value.filter(r => r.id !== id);
  lireTexte("Fichier supprimé");
};

// MOCK DATA POUR LA VUE DETAIL
const mockRessources = [
  { id: 1, titre: "Document Synthèse SAE", auteur: "M. LE CADET", date: "10/03/2026", heure: "11:30", lu: false },
  { id: 2, titre: "Grille d'évaluation", auteur: "M. LE CADET", date: "10/03/2026", heure: "11:30", lu: true }
];

const mockAnnonces = [
  { id: 1, titre: "Annonce Générale", auteur: "M. LE CADET", date: "10/03/2026", heure: "11:30", contenu: "N'oubliez pas de rendre le rapport final avant la fin du mois." },
  { id: 2, titre: "Précision Livrables", auteur: "M. LE CADET", date: "12/03/2026", heure: "09:00", contenu: "Les maquettes visuelles doivent inclure une déclinaison mobile et tablette." }
];

const mockRendus = ref([
  { id: 1, titre: "Rapport_Final_Annissa.pdf" },
  { id: 2, titre: "Maquettes_Visuelles_V1.zip" }
]);

// --- GESTION RECHERCHE & FILTRES ---
const searchQuery = ref('');
const filtreSemestre = ref('Par semestre');

const changementSemestre = () => {
  lireTexte(`Filtre modifié sur ${filtreSemestre.value}`);
  jourSelectionne.value = null; 
  if (['S1', 'S3', 'S5'].includes(filtreSemestre.value)) {
    selectionnerMois('Septembre');
  } else if (['S2', 'S4', 'S6'].includes(filtreSemestre.value)) {
    selectionnerMois('Janvier');
  }
};

// --- CALENDRIER DYNAMIQUE ---
const listMois = ref([]);
const moisSelectionne = ref('');
const jourSelectionne = ref(null);
const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];

const genererListeMois = () => {
  const dateInitiale = new Date();
  const moisGeneres = [];
  for (let i = 0; i < 12; i++) {
    const d = new Date(dateInitiale.getFullYear(), dateInitiale.getMonth() + i, 1);
    moisGeneres.push({ id: i + 1, nom: nomsMoisGlobaux[d.getMonth()], annee: d.getFullYear().toString() });
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
  jourSelectionne.value = null; 
  lireTexte(`Calendrier mis à jour pour ${mois}`);
};

const selectionnerJour = (jour) => { 
  if (jourSelectionne.value === jour) {
    jourSelectionne.value = null; 
    lireTexte(`Vue mensuelle réactivée`);
  } else {
    jourSelectionne.value = jour;
    lireTexte(`Jour ${jour} sélectionné`);
  }
};

// --- DONNÉES MOCKÉES (KANBAN) AVEC PHASES ---
const toutesLesSaes = ref([
  { id: 302, titre: 'SAE 302', tag: 'Développement web', tagClass: 'tag-orange', description: "Réalisation d'un outil de gestion de projet en ligne.", commentaires: 8, dateStr: 'Nov 30', status: 'todo', semestre: 'S3', tilted: false, estProprietaire: true, estPublique: false, phase1: true, phase2: false, phase3: false },
  { id: 303, titre: 'SAE 303', tag: 'Communication', tagClass: 'tag-red', description: "Élaboration d'une stratégie de communication pour un événement local.", commentaires: 8, dateStr: 'Nov 30', status: 'todo', semestre: 'S3', tilted: true, estProprietaire: false, estPublique: false, phase1: false, phase2: false, phase3: false },
  { id: 304, titre: 'SAE 304', tag: 'Audiovisuel', tagClass: 'tag-pink', description: "Création, tournage et montage d'un court-métrage promotionnel.", commentaires: 5, dateStr: 'Dec 15', status: 'todo', semestre: 'S3', tilted: false, estProprietaire: false, estPublique: false, phase1: true, phase2: false, phase3: false },
  
  { id: 202, titre: 'SAE 202', tag: 'Graphisme', tagClass: 'tag-orange', description: "Conception d'une charte graphique complète pour une nouvelle marque.", image: true, imgClass: 'img-flowers', commentaires: 8, dateStr: 'Nov 30', status: 'in_progress', semestre: 'S2', estProprietaire: false, estPublique: false, phase1: true, phase2: false, phase3: false },
  { id: 203, titre: 'SAE 203', tag: 'Portfolio', tagClass: 'tag-orange', description: "Mise à jour du portfolio professionnel avec vos dernières réalisations.", image: false, commentaires: 8, dateStr: 'Nov 30', status: 'in_progress', semestre: 'S2', estProprietaire: true, estPublique: true, phase1: true, phase2: true, phase3: false },
  
  { id: 403, titre: 'SAE 403', tag: 'UX/UI design', tagClass: 'tag-green', description: "Refonte de l'interface d'une application mobile.", image: true, commentaires: 8, dateStr: 'Nov 30', status: 'done', semestre: 'S4', estProprietaire: false, estPublique: false, phase1: true, phase2: true, phase3: true },
  { id: 404, titre: 'SAE 404', tag: 'Marketing', tagClass: 'tag-green', description: "Élaboration d'une campagne de marketing pour des marques locales.", image: false, commentaires: 8, dateStr: 'Nov 30', status: 'done', semestre: 'S4', estProprietaire: false, estPublique: false, phase1: true, phase2: true, phase3: true },
  { id: 316, titre: 'SAE 316', tag: 'Design web', tagClass: 'tag-pink', description: "Création de maquettes interactives.", commentaires: 8, dateStr: 'Nov 30', status: 'in_progress', semestre: 'S3', estProprietaire: false, estPublique: false, phase1: true, phase2: false, phase3: false },
  { id: 311, titre: 'SAE 311', tag: 'Communication', tagClass: 'tag-red', description: "Élaboration d'une stratégie de communication pour un événement local.", commentaires: 2, dateStr: 'Dec 12', status: 'todo', semestre: 'S3', tilted: false, estProprietaire: false, estPublique: false, phase1: false, phase2: false, phase3: false }
]);

// --- CALCUL DYNAMIQUE DES STATUTS SELON LE CALENDRIER ---
const getMoisIndex = (abrev) => {
  const map = { 'Jan': 0, 'Fev': 1, 'Mar': 2, 'Avr': 3, 'Mai': 4, 'Juin': 5, 'Juil': 6, 'Aout': 7, 'Sep': 8, 'Oct': 9, 'Nov': 10, 'Dec': 11 };
  return map[abrev] !== undefined ? map[abrev] : 0;
};

const getDynamicStatus = (sae) => {
  const monthIndex = nomsMoisGlobaux.indexOf(moisSelectionne.value);
  const day = jourSelectionne.value || 15; 
  const currentSelectedDate = new Date(2026, monthIndex, day);

  const [abrev, jourStr] = sae.dateStr.split(' ');
  const deadlineDate = new Date(2026, getMoisIndex(abrev), parseInt(jourStr));

  const startDate = new Date(deadlineDate);
  startDate.setDate(deadlineDate.getDate() - 14);

  if (currentSelectedDate < startDate) {
    return 'todo'; 
  } else if (currentSelectedDate >= startDate && currentSelectedDate <= deadlineDate) {
    return 'in_progress'; 
  } else {
    return 'done'; 
  }
};

const toutAvecStatusDynamique = computed(() => {
  return toutesLesSaes.value.map(sae => ({
    ...sae,
    statusDynamique: getDynamicStatus(sae)
  }));
});

// Logique de filtrage (Recherche + Semestre)
const filtrerSaes = (liste) => {
  return liste.filter(sae => {
    const matchSearch = sae.titre.toLowerCase().includes(searchQuery.value.toLowerCase()) || sae.tag.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchSemestre = filtreSemestre.value === 'Par semestre' || sae.semestre === filtreSemestre.value;
    return matchSearch && matchSemestre;
  });
};

const todoSaes = computed(() => filtrerSaes(toutAvecStatusDynamique.value.filter(s => s.statusDynamique === 'todo')));
const inProgressSaes = computed(() => filtrerSaes(toutAvecStatusDynamique.value.filter(s => s.statusDynamique === 'in_progress')));
const doneSaes = computed(() => filtrerSaes(toutAvecStatusDynamique.value.filter(s => s.statusDynamique === 'done')));


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
const ouvrirModifMotDePasse = () => { fermerTout(); afficherMotDePassePopup.value = true; };
const validerNouveauMotDePasse = () => { alert("Mot de passe mis à jour !"); afficherMotDePassePopup.value = false; };
const deconnexion = () => { router.push('/'); };

// --- SYNCHRONISATION DATE & VOCAL ENRICHIE ---
onMounted(() => {
  const now = new Date();
  listMois.value = genererListeMois();
  moisSelectionne.value = nomsMoisGlobaux[now.getMonth()];
  jourSelectionne.value = null; 
  
  if (localStorage.getItem('voix_active') === 'true') {
    accessibilite.value.voix = true;
  }

  // Vérifier la route lors de la première initialisation
  checkRouteForSae();
});

// Watcher pour mettre à jour la page détaillée si l'URL change (ex : bouton "Précédent" du navigateur)
watch(() => route.params.id, () => {
  checkRouteForSae();
});

watch(() => accessibilite.value.voix, (estActive) => {
  if (estActive) {
    localStorage.setItem('voix_active', 'true');
    let texteALire = `Vous êtes sur la page Mes S A E. `;
    texteALire += `Il y a ${todoSaes.value.length} projets à faire, ${inProgressSaes.value.length} en cours, et ${doneSaes.value.length} terminés.`;
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

/* HEADER GLOBAL AVEC RECHERCHE */
.top-header { display: flex; justify-content: space-between; padding: 0 2rem; background: #b4d4fe; align-items: center; border-bottom: 1px solid #e5e7eb; height: 80px; box-sizing: border-box; flex-shrink: 0; position: relative;}
.search-container { display: flex; align-items: center; gap: 1rem; }
.search-bar { display: flex; align-items: center; background: white; border-radius: 8px; padding: 0.5rem 1rem; width: 250px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.search-icon-svg { width: 16px; height: 16px; margin-right: 10px; filter: invert(0.6); } 
.search-input { border: none; outline: none; width: 100%; font-size: 0.9rem; font-family: 'Inter', sans-serif; background: transparent; }
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

/* MODALS GLOBAUX */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-content-center { background: white; padding: 2.5rem; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
.modal-content-center h3 { margin-top: 0; color: #111827; margin-bottom: 1.5rem; font-size: 1.3rem;}
.form-group-modal { margin-bottom: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem;}
.form-group-modal label { font-size: 0.9rem; color: #374151; font-weight: 500;}
.input-full { width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-family: 'Inter', sans-serif;}
.modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;}
.btn-cancel { background: transparent; border: none; color: #6b7280; cursor: pointer; font-weight: 500;}
.btn-confirm { background: #3b82f6; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: 500;}

/* MODAL ANNONCE SPECIFIQUE */
.modal-content-annonce { background: white; padding: 2.5rem; border-radius: 16px; width: 550px; max-width: 90%; box-shadow: 0 15px 35px rgba(0,0,0,0.2); position: relative;}
.close-btn-cross { position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; transition: 0.2s;}
.close-btn-cross:hover { color: #111827; transform: scale(1.1);}
.annonce-header-flex { display: flex; align-items: center; gap: 15px; margin-bottom: 1.5rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 1rem;}
.icon-megaphone-lg { font-size: 2rem; color: #f97316; }
.annonce-header-text h3 { margin: 0 0 5px 0; font-size: 1.2rem; color: #1f2937; }
.annonce-header-text .annonce-meta { font-size: 0.75rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px;}
.annonce-body p { color: #4b5563; line-height: 1.6; font-size: 1rem; margin: 0;}


/* SWITCH */
.switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: #3b82f6; }
input:checked + .slider:before { transform: translateX(20px); }

/* CONTENU PRINCIPAL */
.content-area { padding: 1.5rem 2rem; overflow-y: auto; overflow-x: hidden; width: 100%; box-sizing: border-box; }
.page-welcome h1 { margin: 0; font-size: 1.5rem; color: #1f2937; display: flex; align-items: center;}
.breadcrumb { font-size: 0.9rem; color: #6b7280; font-weight: normal; margin-left: 15px; }
.clickable-breadcrumb { cursor: pointer; transition: 0.2s;}
.clickable-breadcrumb:hover { color: #3b82f6; text-decoration: underline;}
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

/* BOUTON FILTRE LOCAL */
.local-filters { margin-bottom: 1.5rem; }
.btn-filter-outline { display: flex; align-items: center; gap: 8px; background: transparent; border: 1px solid #d1d5db; color: #4b5563; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-family: 'Inter'; font-weight: 500; transition: 0.2s;}
.btn-filter-outline:hover { background: #f3f4f6; }
.filter-icon-small { width: 16px; height: 16px; filter: opacity(0.6); }

/* BOARD KANBAN */
.kanban-board { 
  display: flex; 
  gap: 2rem; 
  overflow-x: auto; 
  padding-bottom: 2rem;
  scrollbar-width: thin; 
}
.kanban-column { 
  flex: 1; 
  min-width: 300px; 
  display: flex; 
  flex-direction: column; 
  gap: 1.2rem; 
}
.column-header { 
  display: flex; 
  align-items: center; 
  gap: 10px; 
  padding-bottom: 0.5rem;
}
.column-header h3 { margin: 0; font-size: 1.1rem; color: #111827; }
.border-todo { border-bottom: 2px solid #8b5cf6; }
.border-inprogress { border-bottom: 2px solid #f59e0b; }
.border-done { border-bottom: 2px solid #10b981; }

.dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block;}
.dot-todo { background-color: #8b5cf6; }
.dot-inprogress { background-color: #f59e0b; }
.dot-done { background-color: #10b981; }

.badge-count { background: #e5e7eb; color: #4b5563; font-size: 0.8rem; padding: 2px 8px; border-radius: 12px; font-weight: bold;}

/* CARTES KANBAN */
.kanban-card { 
  background: white; 
  border-radius: 12px; 
  padding: 1.2rem; 
  box-shadow: 0 4px 6px rgba(0,0,0,0.02); 
  border: 1px solid #f3f4f6;
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}
.kanban-card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
.kanban-card.tilted { transform: rotate(-3deg); box-shadow: 0 15px 25px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;}

.card-tag { display: inline-block; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; margin-bottom: 10px;}
.tag-orange { background-color: #ffedd5; color: #c2410c; }
.tag-red { background-color: #fee2e2; color: #b91c1c; }
.tag-pink { background-color: #fce7f3; color: #be185d; }
.tag-green { background-color: #dcfce7; color: #15803d; }

.card-header-flex { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;}
.card-title { margin: 0; font-size: 1.1rem; color: #111827; }
.more-options { background: none; border: none; font-size: 1.2rem; color: #9ca3af; cursor: pointer; padding: 0; line-height: 1;}

.card-desc { color: #6b7280; font-size: 0.85rem; line-height: 1.4; margin: 0 0 1rem 0;}

.card-img-placeholder { width: 100%; height: 120px; border-radius: 8px; margin-bottom: 1rem; background-size: cover; background-position: center;}
.gray-placeholder { background-color: #d1d5db; }
.img-flowers { background-image: url('https://images.unsplash.com/photo-1572688484432-260b99484437?q=80&w=400&auto=format&fit=crop'); }

.card-footer { display: flex; justify-content: space-between; align-items: center; color: #9ca3af; font-size: 0.8rem; font-weight: 500;}

/* ==========================================================
   VUE DÉTAILLÉE ET GESTION
   ========================================================== */
.sae-detail-container { display: flex; gap: 2rem; width: 100%; align-items: flex-start;}
.sae-main-col { flex: 1; display: flex; flex-direction: column; gap: 1.5rem; min-width: 0;} 
.sae-side-col { width: 320px; display: flex; flex-direction: column; gap: 1.5rem; flex-shrink: 0;}

.sae-panel { background: white; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.02); border: 1px solid #f3f4f6;}
.panel-title { margin: 0 0 1rem 0; font-size: 1.1rem; color: #111827;}

.banner-panel { padding-top: 1rem; }
.tag-margin { margin-bottom: 1rem; }
.sae-detail-title { margin: 0 0 1rem 0; font-size: 1.5rem; color: #111827;}
.banner-img { width: 100%; height: 180px; border-radius: 12px; background-size: cover; background-position: center;}

/* CSS DES PHASES D'AVANCEMENT (MATCH FIGMA) */
.phases-container { display: flex; gap: 2.5rem; margin-top: 1.5rem; align-items: center; justify-content: flex-start; padding: 0 1rem;}
.phase-item { display: flex; align-items: center; gap: 10px; font-size: 0.85rem; font-weight: 500; color: #374151;}
.phase-box { width: 22px; height: 22px; border: 2px solid #cbd5e1; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; background: white;}
.phase-box:hover { border-color: #94a3b8; }
.phase-box.checked { background-color: #dcfce7; border-color: #22c55e; }
.phase-box.checked::after { content: '✓'; color: #166534; font-weight: bold; font-size: 14px;}

.split-panels { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

/* Nouveaux éléments Dropzone et Boutons édition */
.dropzone-area { border: 2px dashed #cbd5e1; border-radius: 12px; padding: 2rem; text-align: center; background-color: #f8fafc; transition: all 0.3s ease; margin-bottom: 1.5rem; }
.dropzone-area.dragover-active { border-color: #3b82f6; background-color: #eff6ff; }
.dropzone-content { display: flex; flex-direction: column; align-items: center; gap: 10px; color: #64748b; }
.icon-upload { font-size: 2.5rem; }
.text-blue { color: #3b82f6; font-weight: 500; cursor: pointer; }
.text-blue:hover { text-decoration: underline; }
.btn-red { background-color: #fee2e2; color: #dc2626; }
.btn-icon-transparent { background: transparent; border: none; font-size: 1.1rem; cursor: pointer; opacity: 0.6; transition: opacity 0.2s; padding: 0; }
.btn-icon-transparent:hover { opacity: 1; }
.sub-title { font-size: 1rem; color: #374151; margin-bottom: 1rem; margin-top: 0;}
.no-border { border: none !important; }
.no-padding { padding: 0.5rem 0 !important; }

/* Listes d'items (Ressources / Annonces) */
.item-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;}
.list-item { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 1rem; border-bottom: 1px solid #f3f4f6;}
.list-item:last-child { border-bottom: none; padding-bottom: 0;}
.clickable-ann { cursor: pointer; transition: background-color 0.2s; padding: 0.5rem; border-radius: 8px; margin: -0.5rem; border-bottom: none !important;}
.clickable-ann:hover { background-color: #f9fafb; }

.item-info { display: flex; flex-direction: column; gap: 5px; }
.item-title { font-size: 0.9rem; font-weight: 500; color: #374151; display: flex; align-items: center; gap: 8px;}
.icon-orange-text { color: #f97316; font-size: 1.1rem;}
.icon-gray-text { color: #9ca3af; font-size: 1.1rem;}
.item-meta { font-size: 0.7rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px;}
.item-date { font-size: 0.75rem; color: #6b7280; display: flex; align-items: center; gap: 5px;}

/* Liste Rendus */
.rendu-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 1rem;}
.rendu-item { display: flex; justify-content: space-between; align-items: center; padding: 0.8rem 0; border-bottom: 1px solid #f3f4f6;}
.rendu-item:last-child { border-bottom: none; padding-bottom: 0;}
.rendu-title { font-size: 0.95rem; font-weight: 500; color: #374151;}
.rendu-actions { display: flex; gap: 10px;}

.btn-action { width: 35px; height: 35px; border-radius: 8px; border: none; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; cursor: pointer; transition: transform 0.2s;}
.btn-action:hover { transform: translateY(-2px);}
.btn-green { background-color: #dcfce7; color: #15803d;}
.btn-blue { background-color: #dbeafe; color: #1d4ed8;}
.btn-orange { background-color: #ffedd5; color: #c2410c;}

/* Textes Side Column */
.desc-text { color: #4b5563; line-height: 1.5; margin: 0;}
.text-sm { font-size: 0.9rem;}
.mt-1 { margin-top: 1rem;}
.info-group { margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;}

/* Timer / Progress Circle */
.center-content { display: flex; justify-content: center; align-items: center; padding: 2.5rem 1rem;}
.timer-circle { width: 180px; height: 180px; border-radius: 50%; background: conic-gradient(#f59e0b 0% 75%, #e5e7eb 75% 100%); display: flex; align-items: center; justify-content: center;}
.timer-inner { width: 150px; height: 150px; border-radius: 50%; background: white; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);}
.timer-label { font-size: 0.8rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;}
.timer-value { font-size: 1.3rem; font-weight: 700; color: #111827;}


/* ==========================================================
   MODE SOMBRE GLOBAL ET AMÉLIORÉ
   ========================================================== */
:global(body.theme-sombre) { background-color: #0f172a !important; color: #f8fafc !important; }
:global(body.theme-sombre .dashboard-layout) { background-color: #0f172a !important; }
:global(body.theme-sombre .top-header) { background: #1e293b !important; border-bottom: 1px solid #334155 !important; }
:global(body.theme-sombre .search-bar) { background: #334155 !important; border-color: #475569 !important; }
:global(body.theme-sombre .search-input) { color: #ffffff !important; }
:global(body.theme-sombre .search-icon-svg) { filter: brightness(0) invert(1) opacity(0.5) !important; }
:global(body.theme-sombre .dropdown-filter select) { background: #334155 !important; color: white !important; border-color: #475569 !important;}
:global(body.theme-sombre .page-welcome h1), :global(body.theme-sombre h2), :global(body.theme-sombre h3), :global(body.theme-sombre h4) { color: #f8fafc !important; }

/* Mode sombre Kanban */
:global(body.theme-sombre .kanban-card) { background: #1e293b !important; border-color: #334155 !important; }
:global(body.theme-sombre .card-title) { color: #f8fafc !important; }
:global(body.theme-sombre .card-desc) { color: #94a3b8 !important; }
:global(body.theme-sombre .badge-count) { background: #334155 !important; color: #cbd5e1 !important; }
:global(body.theme-sombre .btn-filter-outline) { border-color: #475569 !important; color: #cbd5e1 !important; }
:global(body.theme-sombre .btn-filter-outline:hover) { background: #334155 !important; }

:global(body.theme-sombre .popup-menu),
:global(body.theme-sombre .modal-content-center) { 
  background: #1e293b !important; border-color: #334155 !important; color: #ffffff !important; 
}

:global(body.theme-sombre .months .month-pill) { color: #94a3b8 !important; }
:global(body.theme-sombre .active-month) { color: #60a5fa !important; border-bottom: 2px solid #60a5fa !important; background: transparent !important; }
:global(body.theme-sombre .days .day-circle) { color: #cbd5e1 !important; }
:global(body.theme-sombre .active-day) { color: #60a5fa !important; border: 2px solid #60a5fa !important; background: transparent !important; }
:global(body.theme-sombre .divider) { background-color: #334155 !important; }

:global(body.theme-sombre .svg-icon) { filter: brightness(0) invert(1) !important; }
:global(body.theme-sombre .feature-item), :global(body.theme-sombre .notif-item), :global(body.theme-sombre .action-item) { border-color: #334155 !important; color: #f8fafc !important;}
:global(body.theme-sombre .btn-toggle-action:not(.active-zoom):not(.reset-btn)) { background-color: #475569 !important; color: white !important;}
:global(body.theme-sombre input:not([type="checkbox"])) { background-color: #334155 !important; color: #f8fafc !important; border-color: #475569 !important; }

/* AMÉLIORATION DE LA VISIBILITÉ DES LABELS (TAGS) EN MODE SOMBRE */
:global(body.theme-sombre .tag-orange) { background-color: rgba(234, 88, 12, 0.2) !important; color: #fdba74 !important; border: 1px solid rgba(234, 88, 12, 0.3) !important; }
:global(body.theme-sombre .tag-red) { background-color: rgba(220, 38, 38, 0.2) !important; color: #fca5a5 !important; border: 1px solid rgba(220, 38, 38, 0.3) !important; }
:global(body.theme-sombre .tag-pink) { background-color: rgba(219, 39, 119, 0.2) !important; color: #f9a8d4 !important; border: 1px solid rgba(219, 39, 119, 0.3) !important; }
:global(body.theme-sombre .tag-green) { background-color: rgba(22, 163, 74, 0.2) !important; color: #86efac !important; border: 1px solid rgba(22, 163, 74, 0.3) !important; }

/* MODE SOMBRE POUR LA VUE DÉTAILLÉE ET DEPOT */
:global(body.theme-sombre .sae-panel) { background: #1e293b !important; border-color: #334155 !important; }
:global(body.theme-sombre .sae-detail-title), :global(body.theme-sombre .panel-title) { color: #f8fafc !important; }
:global(body.theme-sombre .list-item), :global(body.theme-sombre .rendu-item), :global(body.theme-sombre .info-group) { border-color: #334155 !important; }
:global(body.theme-sombre .item-title), :global(body.theme-sombre .rendu-title) { color: #e2e8f0 !important; }
:global(body.theme-sombre .clickable-ann:hover) { background-color: #334155 !important; }
:global(body.theme-sombre .desc-text) { color: #cbd5e1 !important; }
:global(body.theme-sombre .timer-circle) { background: conic-gradient(#f59e0b 0% 75%, #334155 75% 100%) !important; }
:global(body.theme-sombre .timer-inner) { background: #1e293b !important; }
:global(body.theme-sombre .timer-value) { color: #f8fafc !important; }
:global(body.theme-sombre .phase-item) { color: #cbd5e1 !important; }
:global(body.theme-sombre .phase-box) { background: #334155 !important; border-color: #475569 !important; }
:global(body.theme-sombre .phase-box.checked) { background: rgba(22, 163, 74, 0.2) !important; border-color: #22c55e !important; }
:global(body.theme-sombre .phase-box.checked::after) { color: #86efac !important; }

:global(body.theme-sombre .dropzone-area) { background-color: #0f172a !important; border-color: #475569 !important; }
:global(body.theme-sombre .dropzone-area.dragover-active) { border-color: #60a5fa !important; background-color: #1e293b !important; }
:global(body.theme-sombre .dropzone-content) { color: #94a3b8 !important; }
:global(body.theme-sombre .sub-title) { color: #f8fafc !important; }
:global(body.theme-sombre .btn-red) { background-color: rgba(220, 38, 38, 0.2) !important; color: #fca5a5 !important; }

/* MODE SOMBRE POUR POPUP ANNONCE */
:global(body.theme-sombre .modal-content-annonce) { background: #1e293b !important; border-color: #334155 !important; }
:global(body.theme-sombre .annonce-header-flex) { border-color: #334155 !important; }
:global(body.theme-sombre .annonce-header-text h3) { color: #f8fafc !important; }
:global(body.theme-sombre .annonce-body p) { color: #cbd5e1 !important; }
:global(body.theme-sombre .close-btn-cross) { color: #94a3b8 !important; }
:global(body.theme-sombre .close-btn-cross:hover) { color: #f8fafc !important; }

/* Boutons d'actions rendus mode sombre */
:global(body.theme-sombre .btn-green) { background-color: rgba(22, 163, 74, 0.2) !important; color: #86efac !important; }
:global(body.theme-sombre .btn-blue) { background-color: rgba(29, 78, 216, 0.2) !important; color: #93c5fd !important; }
:global(body.theme-sombre .btn-orange) { background-color: rgba(234, 88, 12, 0.2) !important; color: #fdba74 !important; }

</style>