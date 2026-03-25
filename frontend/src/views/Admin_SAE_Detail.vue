<template>
  <div class="dashboard-layout">
    
    <SidebarMenu />

    <main class="main-content" id="main-content-area">
      
      <header class="top-header">
        <div class="search-container"></div>
        
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
            <li class="notif-item"><span class="notif-icon">📢</span> <span class="notif-text">L'étudiant Dubois a rendu son livrable.</span></li>
          </ul>
        </div>

        <div v-if="afficherParametres || afficherProfil" class="popup-menu standard-popup">
          <h3 v-if="afficherParametres">Paramètres</h3>
          <h3 v-if="afficherProfil">Profil Administrateur</h3>
          <ul class="action-list">
            <li class="action-item">Modifier le mot de passe</li>
            <li class="action-item">Gérer les droits</li>
            <li @click="deconnexion" class="action-item text-red">Déconnexion</li>
          </ul>
        </div>
      </header>

      <div v-if="afficherModifSaePopup" class="modal-overlay" @click="afficherModifSaePopup = false">
        <div class="modal-content-center modal-xl" @click.stop>
          <h3>Modifier les détails de la SAE</h3>
          <form @submit.prevent="validerModifSAE" class="ajout-sae-form">
            <div class="form-row-modal">
              <div class="form-group-modal flex-1">
                <label>Intitulé</label>
                <input v-model="saeData.titre" type="text" class="input-full" required />
              </div>
              <div class="form-group-modal flex-1">
                <label>Filière / Tag</label>
                <input v-model="saeData.filiere" type="text" class="input-full" required />
              </div>
            </div>
            
            <div class="form-group-modal">
              <label>Description courte</label>
              <input v-model="saeData.description" type="text" class="input-full" />
            </div>

            <div class="form-group-modal">
              <label>Consignes détaillées</label>
              <textarea v-model="saeData.consignes" class="input-full textarea-consigne"></textarea>
            </div>
            
            <div class="modal-actions">
              <button type="button" @click="afficherModifSaePopup = false" class="btn-cancel">Annuler</button>
              <button type="submit" class="btn-confirm">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>

      <div v-if="afficherModifMembresPopup" class="modal-overlay" @click="afficherModifMembresPopup = false">
        <div class="modal-content-center modal-lg" @click.stop>
          <h3>Gérer la liste des membres</h3>
          
          <div class="form-group-modal">
            <label>Membres assignés (Cliquez pour retirer)</label>
            <div class="members-tags-container">
              <span v-for="m in membresListe" :key="m.id" @click="supprimerMembre(m.id)" class="clickable-member-tag" title="Supprimer ce membre">
                {{ m.nom }} ({{ m.role }}) ✕
              </span>
              <span v-if="membresListe.length === 0" class="text-gray-500 italic text-sm">Aucun membre assigné.</span>
            </div>
          </div>

          <div v-if="etudiants.length > 0" class="form-group-modal mt-15">
            <label>Mettre à jour le statut des rendus</label>
            <div v-for="etu in etudiants" :key="'status-'+etu.id" class="status-edit-row">
              <span>{{ etu.nom }}</span>
              <select v-model="etu.status" class="input-select-small">
                <option value="Reçu">Reçu</option>
                <option value="En attente">En attente</option>
                <option value="Non rendu">Non rendu</option>
              </select>
            </div>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="afficherModifMembresPopup = false" class="btn-confirm">Terminer</button>
          </div>
        </div>
      </div>

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
        
        <div class="page-header">
          <h1>Page / <span class="breadcrumb-active">{{ saeData.titre }}</span></h1>
        </div>
        <hr class="divider" />

        <div class="detail-grid">
          
          <div class="main-column">
            
            <section class="sae-detail-card card-container">
              <div class="sae-header-card">
                <span class="tag-filiere">{{ saeData.filiere }}</span>
                <button class="action-btn" @click="ouvrirModifSAE" title="Modifier la SAE">
                  <img src="../assets/icon_modifier.svg" alt="Modifier" class="action-svg" />
                </button>
              </div>
              
              <h2 class="sae-title-large">{{ saeData.titre }}</h2>
              
              <div class="sae-banner" :style="{ background: saeData.color }"></div>
              
              <div class="sae-description-block">
                <h3>{{ saeData.sousTitre }}</h3>
                <p>{{ saeData.description }}</p>
              </div>
            </section>

            <div class="members-management-row">
              
              <section class="add-member-card card-container">
                <h3>Ajouter membre</h3>
                <form @submit.prevent="ajouterMembre" class="add-member-form">
                  <div class="form-row flex-col-mobile">
                    <select v-model="nouveauMembre.role" class="input-select" required>
                      <option value="" disabled>Rôle</option>
                      <option value="Enseignant">Enseignant</option>
                      <option value="Étudiant">Étudiant</option>
                    </select>
                    
                    <input 
                      type="text" 
                      v-model="nouveauMembre.nom" 
                      placeholder="Rechercher / Ajouter..." 
                      class="input-search-member" 
                      required 
                    />
                    
                    <button type="submit" class="btn-add-circle">+</button>
                  </div>
                </form>
              </section>

              <section class="members-list-card card-container flex-3">
                <div class="card-header-flex">
                  <h3>Liste Des Membres</h3>
                  <button class="action-btn" @click="ouvrirModifMembres" title="Modifier la liste des membres">
                    <img src="../assets/icon_modifier.svg" alt="Modifier" class="action-svg" />
                  </button>
                </div>
                
                <div class="members-section">
                  <h4>Enseignants Responsables</h4>
                  <ul class="clean-list">
                    <li v-for="prof in enseignants" :key="prof.id">{{ prof.nom }}</li>
                    <li v-if="enseignants.length === 0" class="italic-text">Aucun enseignant</li>
                  </ul>
                </div>
                
                <div class="members-section mt-20">
                  <div class="table-header">
                    <span>Étudiant</span>
                    <span>Groupe</span>
                    <span class="text-right">Statut du rendu</span>
                  </div>
                  <ul class="clean-list list-separated">
                    <li v-for="etudiant in etudiants" :key="etudiant.id" class="table-row">
                      <span>{{ etudiant.nom }}</span>
                      <span>{{ etudiant.groupe }}</span>
                      <span class="text-right"><span :class="['status-badge', getStatusClass(etudiant.status)]">{{ etudiant.status }}</span></span>
                    </li>
                    <li v-if="etudiants.length === 0" class="italic-text mt-15">Aucun étudiant assigné</li>
                  </ul>
                </div>
              </section>
              
            </div>
          </div>

          <div class="side-column">
            
            <section class="info-card card-container">
              <h3>Détails du projet</h3>
              <div class="info-content">
                <p>Semestre affecté: <strong>{{ saeData.semestre }}</strong></p>
                <p>Date d'échéance: <br><strong class="date-highlight">{{ saeData.dateEcheance }}</strong></p>
              </div>
            </section>
            
            <section class="consigne-card card-container flex-1">
              <h3>Consignes de la SAE</h3>
              <div class="consigne-content">
                <p>{{ saeData.consignes || "Aucune consigne n'a été rédigée pour le moment." }}</p>
              </div>
            </section>

          </div>

        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import SidebarMenu from '../components/SidebarMenu.vue';

const router = useRouter();
const route = useRoute();

// --- DONNÉES DYNAMIQUES DE LA SAE ET DES MEMBRES ---
const bddSaeMock = [
  { 
    id: 1, 
    titre: 'Projet Web', 
    filiere: "Développement Web", 
    sousTitre: "Développement d'une application web", 
    description: "Réaliser une application web full-stack avec framework front et back, API REST.", 
    semestre: 'S1', 
    dateEcheance: '24 Mars 2026', 
    consignes: "Fournir le code source complet sur un dépôt Git.\n\nLa documentation technique doit inclure le diagramme de base de données.\n\nLe rendu est individuel.", 
    color: 'linear-gradient(135deg, #fbcfe8, #d8b4fe, #818cf8)',
    membres: [
      { id: 101, role: 'Enseignant', nom: 'Olivier Le cadet' },
      { id: 102, role: 'Enseignant', nom: 'Soufian Ben Amor' },
      { id: 102, role: 'Étudiant', nom: 'Thomas Morel', groupe: 'MMI1 A1', status: 'Reçu' },
      { id: 103, role: 'Étudiant', nom: 'Sarah Blanc', groupe: 'MMI1 A2', status: 'Non rendu' }
    ]
  },
  { 
    id: 2, 
    titre: 'Design UX/UI', 
    filiere: "Création Numérique", 
    sousTitre: "Maquettage interactif Figma", 
    description: "Concevoir l'interface utilisateur d'une application mobile en respectant les principes UX.", 
    semestre: 'S1', 
    dateEcheance: '15 Avril 2026', 
    consignes: 'Le livrable doit inclure le lien du prototype interactif Figma avec tous les écrans connectés. L\'UI Kit doit être fourni.', 
    color: 'linear-gradient(135deg, #1e3a8a, #3b82f6, #93c5fd)',
    membres: [
      { id: 201, role: 'Enseignant', nom: 'Soufian Ben Amor' },
      { id: 202, role: 'Étudiant', nom: 'Sofiia Poloz', groupe: 'MMI2 B2', status: 'Reçu' },
      { id: 203, role: 'Étudiant', nom: 'Jean Martin', groupe: 'MMI2 B2', status: 'En attente' },
      { id: 204, role: 'Étudiant', nom: 'Lucas Bernard', groupe: 'MMI2 A1', status: 'Non rendu' }
    ]
  },
  { 
    id: 3, 
    titre: 'Communication', 
    filiere: "Communication", 
    sousTitre: "Plan de communication", 
    description: "Établir une stratégie de communication digitale pour un lancement de produit.", 
    semestre: 'S2', 
    dateEcheance: '02 Mai 2026', 
    consignes: 'Rédiger un dossier PDF de 15 pages minimum incluant le budget et le planning de publication. Un support de présentation oral est requis.', 
    color: 'linear-gradient(135deg, #7f1d1d, #14532d, #a78bfa)',
    membres: [
      { id: 301, role: 'Enseignant', nom: 'Sylvie Fabre' },
      { id: 302, role: 'Étudiant', nom: 'Emma Leroy', groupe: 'MMI1 B1', status: 'En attente' },
      { id: 303, role: 'Étudiant', nom: 'Hugo Dubois', groupe: 'MMI1 B1', status: 'Reçu' }
    ]
  }
];

const saeData = ref({});
const membresListe = ref([]); // Initialisé vide, se remplit au montage

onMounted(() => {
  const saeId = parseInt(route.params.id);
  const foundSae = bddSaeMock.find(s => s.id === saeId);
  
  if (foundSae) {
    saeData.value = { ...foundSae };
    // Remplissage dynamique des membres depuis l'objet SAE
    membresListe.value = [...(foundSae.membres || [])]; 
  } else {
    saeData.value = {
      titre: "SAE " + (saeId || "Inconnue"),
      filiere: "Générique",
      sousTitre: "Sujet en cours de rédaction",
      description: "La description de cette SAE n'est pas encore disponible.",
      semestre: "S-",
      dateEcheance: "À définir",
      consignes: "Veuillez vous référer au document qui sera transmis par l'enseignant responsable.",
      color: "linear-gradient(135deg, #9ca3af, #4b5563)"
    };
    membresListe.value = []; // Aucun membre si SAE non trouvée
  }
});

const afficherModifSaePopup = ref(false);
const ouvrirModifSAE = () => { afficherModifSaePopup.value = true; };
const validerModifSAE = () => {
  afficherModifSaePopup.value = false;
  alert("Les modifications de la S.A.E ont été enregistrées !");
};

// --- NOUVEAU : POPUP POUR MEMBRES ---
const afficherModifMembresPopup = ref(false);
const ouvrirModifMembres = () => { afficherModifMembresPopup.value = true; };

// --- GESTION DES MEMBRES ---
const nouveauMembre = ref({ role: '', nom: '' });
const enseignants = computed(() => membresListe.value.filter(m => m.role === 'Enseignant'));
const etudiants = computed(() => membresListe.value.filter(m => m.role === 'Étudiant'));

const ajouterMembre = () => {
  if (nouveauMembre.value.role && nouveauMembre.value.nom) {
    membresListe.value.push({
      id: Date.now(),
      role: nouveauMembre.value.role,
      nom: nouveauMembre.value.nom,
      groupe: nouveauMembre.value.role === 'Étudiant' ? 'À définir' : '',
      status: nouveauMembre.value.role === 'Étudiant' ? 'En attente' : ''
    });
    nouveauMembre.value.nom = ''; 
  }
};

const supprimerMembre = (id) => { membresListe.value = membresListe.value.filter(m => m.id !== id); };

const getStatusClass = (status) => {
  if (status === 'Reçu') return 'status-success';
  if (status === 'En attente') return 'status-warning';
  return 'status-default';
};

// --- GESTION HEADER / ACCESSIBILITE ---
const afficherAccessibilite = ref(false);
const afficherNotifications = ref(false);
const afficherParametres = ref(false);
const afficherProfil = ref(false);
const afficherMotDePassePopup = ref(false);
const fontScale = ref(1);
const accessibilite = ref({ voix: false, daltonien: false });

const fermerTout = () => { afficherAccessibilite.value = false; afficherNotifications.value = false; afficherParametres.value = false; afficherProfil.value = false; };
const toggleMenuAccessibilite = () => { const etat = afficherAccessibilite.value; fermerTout(); afficherAccessibilite.value = !etat; };
const toggleNotifications = () => { const etat = afficherNotifications.value; fermerTout(); afficherNotifications.value = !etat; };
const toggleParametres = () => { const etat = afficherParametres.value; fermerTout(); afficherParametres.value = !etat; };
const toggleProfil = () => { const etat = afficherProfil.value; fermerTout(); afficherProfil.value = !etat; };
const ouvrirModifMotDePasse = () => { fermerTout(); afficherMotDePassePopup.value = true; };
const validerNouveauMotDePasse = () => { alert("Mot de passe mis à jour !"); afficherMotDePassePopup.value = false; };
const allerGererDroits = () => { fermerTout(); router.push('/admin/sae/202'); };
const deconnexion = () => { router.push('/'); };

const definirPolice = (scale) => {
  fontScale.value = scale;
  document.documentElement.style.fontSize = `${16 * fontScale.value}px`;
};
const appliquerFiltreDaltonien = () => {
  if (accessibilite.value.daltonien) { document.body.style.filter = "contrast(110%) sepia(50%) hue-rotate(180deg) saturate(150%)"; } 
  else { document.body.style.filter = "none"; }
};
const toggleModeSombre = () => { document.body.classList.toggle('theme-sombre'); };

// SYNTHÈSE VOCALE DESCRIPTIVE DE LA SAE
watch(() => accessibilite.value.voix, (estActive) => {
  if (estActive) {
    let texteALire = `Vous êtes sur la page de détail de la S.A.E intitulée ${saeData.value.titre}. `;
    texteALire += `Le projet est : ${saeData.value.sousTitre}. `;
    texteALire += `Description : ${saeData.value.description} `;
    texteALire += `Cette SAE concerne le semestre ${saeData.value.semestre} et la date d'échéance est fixée au ${saeData.value.dateEcheance}. `;
    
    texteALire += `Voici les consignes données par l'enseignant : ${saeData.value.consignes} `;

    texteALire += `Concernant l'équipe : Il y a ${enseignants.value.length} enseignants affectés. `;
    if (enseignants.value.length > 0) {
      enseignants.value.forEach(prof => { texteALire += `${prof.nom}. `; });
    }
    
    texteALire += `Et il y a ${etudiants.value.length} étudiants affectés. `;
    if (etudiants.value.length > 0) {
      etudiants.value.forEach(etu => { texteALire += `L'étudiant ${etu.nom} est en statut : ${etu.status}. `; });
    }
    
    texteALire += `Fin de la lecture de la page.`;

    const utterance = new SpeechSynthesisUtterance(texteALire);
    utterance.lang = 'fr-FR'; 
    utterance.rate = 0.95; 
    window.speechSynthesis.speak(utterance);
  } else {
    window.speechSynthesis.cancel();
  }
});
</script>

<style scoped>
.dashboard-layout { display: flex; height: 100vh; width: 100vw; background-color: #f3f4f6; color: #111827; font-family: 'Inter', sans-serif; }
.main-content { flex: 1; display: flex; flex-direction: column; position: relative; overflow-x: hidden; }

/* HEADER */
.top-header { display: flex; justify-content: space-between; padding: 0 2rem; background: #B4D4FE; align-items: center; border-bottom: 1px solid #e5e7eb; height: 80px; box-sizing: border-box;}
.search-container { flex: 1; display: flex; align-items: center; margin-left: 20px;}
.top-icons { display: flex; gap: 15px; align-items: center; }
.icon-btn { background: none; border: none; cursor: pointer; color: #6b7280; padding: 5px; display: flex; align-items: center; justify-content: center; }
.svg-icon { width: 22px; height: 22px; transition: transform 0.2s; }
.svg-icon:hover { transform: scale(1.1); }
.user-profile { font-weight: 600; color: #1e40af; margin-left: 20px;}
.clickable { cursor: pointer; transition: color 0.2s; }
.clickable:hover { color: #3b82f6; }

/* POP-UPS */
.popup-menu { position: absolute; top: 75px; right: 20px; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); width: 340px; z-index: 100; overflow: hidden;}
.standard-popup { padding: 1.5rem; }
.standard-popup h3 { margin-top: 0; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.8rem; font-size: 1.1rem;}
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

/* ACCESSIBILITE MENU */
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

.switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; }
input:checked + .slider { background-color: #2196F3; }
input:checked + .slider:before { transform: translateX(20px); }
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }

/* MODALS */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal-content-center { background: white; padding: 2.5rem; border-radius: 12px; width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;}
.modal-lg { width: 500px; }
.modal-xl { width: 600px; } 
.modal-content-center h3 { margin-top: 0; color: #111827; margin-bottom: 1.5rem; font-size: 1.3rem;}
.form-group-modal { margin-bottom: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem;}
.form-row-modal { display: flex; gap: 1rem; }
.flex-1 { flex: 1; }
.form-group-modal label { font-size: 0.9rem; color: #374151; font-weight: 500;}
.input-full { width: 100%; padding: 0.8rem; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box; font-family: 'Inter', sans-serif;}
.textarea-consigne { min-height: 100px; resize: vertical; }
.modal-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;}
.btn-cancel { background: transparent; border: none; color: #6b7280; cursor: pointer; font-weight: 500;}
.btn-confirm { background: #3b82f6; color: white; border: none; padding: 0.6rem 1.2rem; border-radius: 6px; cursor: pointer; font-weight: 500;}

.status-edit-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding: 8px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb;}
.input-select-small { padding: 4px; border-radius: 4px; border: 1px solid #d1d5db;}

.members-tags-container { display: flex; flex-wrap: wrap; gap: 8px; padding: 10px; background: #f9fafb; border-radius: 8px; border: 1px dashed #d1d5db; min-height: 50px;}
.clickable-member-tag { background: #fee2e2; color: #b91c1c; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; cursor: pointer; transition: 0.2s; display: flex; align-items: center; font-weight: 500;}
.clickable-member-tag:hover { background: #fca5a5; transform: scale(1.05);}

/* ZONE CONTENU */
.content-area { padding: 2rem 3rem; overflow-y: auto; width: 100%; box-sizing: border-box;}
.page-header h1 { margin: 0; font-size: 1.5rem;}
.breadcrumb-active { color: #111827; font-weight: 700; }
.divider { border: 0; height: 2px; background: #e5e7eb; margin-top: 10px; margin-bottom: 2rem; }
.italic-text { color: #9ca3af; font-style: italic; font-size: 0.85rem;}

/* GRILLE PRINCIPALE (Gauche / Droite) */
.detail-grid {
  display: flex;
  gap: 2rem;
  align-items: stretch; /* Étire les colonnes à la même hauteur */
}
.main-column {
  flex: 3;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.side-column {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* CARTES GENERIQUES */
.card-container {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 10px rgba(0,0,0,0.03);
  border: 1px solid #f3f4f6;
}

/* CARTE SAE PRINCIPALE */
.sae-header-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}
.tag-filiere {
  background-color: #fef08a;
  color: #92400e;
  padding: 0.3rem 0.8rem;
  border-radius: 6px;
  font-size: 0.85rem;
  font-weight: 600;
}
.sae-title-large {
  margin: 0 0 1.5rem 0;
  font-size: 1.6rem;
  color: #1f2937;
}
.sae-banner {
  width: 100%;
  height: 220px;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}
.sae-description-block h3 {
  font-size: 1.2rem;
  margin: 0 0 0.5rem 0;
  color: #111827;
}
.sae-description-block p {
  color: #4b5563;
  font-size: 1rem;
  line-height: 1.6;
  margin: 0;
}

/* LIGNE AJOUT / LISTE MEMBRES */
.members-management-row {
  display: flex;
  gap: 1.5rem;
  align-items: stretch; /* Étire les cartes enfants (Ajout & Liste) */
}
.flex-3 { flex: 3; } /* Elargit la liste des membres */

/* Formulaire Ajout Membre */
.add-member-card h3 { margin-top: 0; font-size: 1.1rem; margin-bottom: 1.5rem;}
.form-row {
  display: flex;
  gap: 0.8rem;
  align-items: center;
}
.flex-col-mobile { flex-direction: column; align-items: stretch;}
.input-select {
  padding: 0.8rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  font-size: 0.95rem;
}
.input-search-member {
  flex: 1;
  padding: 0.8rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 0.95rem;
  outline: none;
}
.btn-add-circle {
  background-color: #3b82f6; 
  color: white;
  border: none;
  height: 42px;
  border-radius: 8px;
  font-size: 1.2rem;
  font-weight: bold;
  cursor: pointer;
  transition: 0.2s;
}
.btn-add-circle:hover { background-color: #2563eb; }

/* Liste des Membres */
.members-list-card {
  display: flex;
  flex-direction: column; 
}
.card-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}
.card-header-flex h3 { margin: 0; font-size: 1.2rem; }
.members-section h4 {
  font-size: 1rem;
  color: #111827;
  margin: 0 0 0.8rem 0;
  border-bottom: 1px solid #f3f4f6;
  padding-bottom: 5px;
}
.clean-list {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 0.95rem;
  color: #4b5563;
}
.clean-list li {
  padding: 0.4rem 0;
  font-weight: 500;
}
.list-separated li { border-bottom: 1px solid #f9fafb; padding: 0.6rem 0;}
.list-separated li:last-child { border-bottom: none;}
.mt-20 { margin-top: 20px; }

/* Table Etudiants */
.table-header {
  display: flex;
  font-size: 0.85rem;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 0.5rem;
  text-transform: uppercase;
}
.table-header span { flex: 1; }
.table-row {
  display: flex;
  align-items: center;
}
.table-row span { flex: 1; }
.text-right { text-align: right; }
.status-badge {
  display: inline-block;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-align: center;
}
.status-success { background-color: #dcfce7; color: #166534; }
.status-warning { background-color: #ffedd5; color: #9a3412; }
.status-default { background-color: #f3f4f6; color: #4b5563; }

/* COLONNE DROITE (Infos / Consignes) */
.info-card h3, .consigne-card h3 { margin-top: 0; font-size: 1.2rem; margin-bottom: 1rem; }
.info-content {
  background-color: #f8fafc;
  padding: 1.2rem;
  border-radius: 8px;
  font-size: 0.95rem;
  color: #4b5563;
}
.info-content p { margin: 0.8rem 0; }
.info-content strong { color: #111827; }
.date-highlight { font-size: 1.1rem; color: #1e40af;}

.consigne-card { flex: 1; display: flex; flex-direction: column;}
.consigne-content { flex: 1; background: #f8fafc; padding: 1.2rem; border-radius: 8px;}
.consigne-content p {
  font-size: 0.95rem;
  color: #4b5563;
  margin: 0;
  white-space: pre-line; 
  line-height: 1.6;
}

/* Boutons SVG d'action */
.action-btn {
  width: 32px;
  height: 32px;
  background: transparent; 
  border: none;
  padding: 0;
  cursor: pointer;
  transition: transform 0.2s;
}
.action-btn:hover { transform: scale(1.1); }
.action-svg {
  width: 100%;
  height: 100%;
  border-radius: 6px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

/* ==========================================================
   MODE SOMBRE GLOBAL
   ========================================================== */
:global(body.theme-sombre) { background-color: #111827 !important; color: #ffffff !important; }
:global(body.theme-sombre .dashboard-layout) { background-color: #111827 !important; color: #ffffff !important; }
:global(body.theme-sombre .top-header) { background: #1f2937 !important; border-bottom-color: #374151 !important; }

:global(body.theme-sombre aside),
:global(body.theme-sombre .sidebar-container),
:global(body.theme-sombre .sidebar) { background-color: #111827 !important; border-right-color: #374151 !important; }
:global(body.theme-sombre aside > div) { background-color: #111827 !important; }

:global(body.theme-sombre .card-container),
:global(body.theme-sombre .popup-menu),
:global(body.theme-sombre .modal-content-center) { background: #1f2937 !important; border-color: #374151 !important; color: #ffffff !important; }

:global(body.theme-sombre .info-content),
:global(body.theme-sombre .consigne-content) { background: #374151 !important; color: #d1d5db !important;}
:global(body.theme-sombre .info-content strong), :global(body.theme-sombre .date-highlight) { color: #ffffff !important; }
:global(body.theme-sombre .clean-list) { color: #d1d5db !important; }
:global(body.theme-sombre .consigne-content p),
:global(body.theme-sombre .sae-description-block p) { color: #9ca3af !important; }
:global(body.theme-sombre .list-separated li) { border-bottom-color: #4b5563 !important;}
:global(body.theme-sombre .members-section h4) { border-bottom-color: #4b5563 !important;}
:global(body.theme-sombre .status-edit-row) { background: #374151 !important; border-color: #4b5563 !important;}

:global(body.theme-sombre .members-tags-container) { background: #374151 !important; border-color: #4b5563 !important; }
:global(body.theme-sombre .clickable-member-tag) { background: #7f1d1d !important; color: #fca5a5 !important; }

:global(body.theme-sombre h1), :global(body.theme-sombre h2), :global(body.theme-sombre h3), :global(body.theme-sombre h4), :global(body.theme-sombre span:not(.tag-filiere):not(.status-badge)) { color: #ffffff !important; }
:global(body.theme-sombre input:not([type="checkbox"])), :global(body.theme-sombre select), :global(body.theme-sombre textarea) { background-color: #374151 !important; color: #ffffff !important; border-color: #4b5563 !important; }
:global(body.theme-sombre select option) { background-color: #374151 !important; color: #ffffff !important; }

:global(body.theme-sombre .status-success) { background-color: #064e3b !important; color: #a7f3d0 !important; }
:global(body.theme-sombre .status-warning) { background-color: #7c2d12 !important; color: #fed7aa !important; }
/* Ajout d'une couleur visible en mode sombre pour le "Non Rendu" */
:global(body.theme-sombre .status-default) { background-color: #4b5563 !important; color: #e5e7eb !important; }
:global(body.theme-sombre .tag-filiere) { background-color: #a16207 !important; color: #fef08a !important;}

:global(body.theme-sombre .icon-btn) { color: #ffffff !important; }
:global(body.theme-sombre .svg-icon) { filter: brightness(0) invert(1) !important; }
:global(body.theme-sombre .user-profile span) { color: #60a5fa !important; }
:global(body.theme-sombre img[src*="logo-mmi"]) { content: url("../assets/logo-mmi (1).svg") !important; }
</style>