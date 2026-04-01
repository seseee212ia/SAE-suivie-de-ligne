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
                <label>Matière / Tag</label>
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
            <label>Modifier le groupe des étudiants</label>
            <div v-for="etu in etudiants" :key="'groupe-'+etu.id" class="status-edit-row">
              <span>{{ etu.nom }}</span>
              <select v-model="etu.groupe" @change="sauvegarderMembres" class="input-select-small">
                <option value="A1">A1</option>
                <option value="A2">A2</option>
                <option value="B1">B1</option>
                <option value="B2">B2</option>
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
                    <select v-model="nouveauMembre.role" class="input-select w-full" required>
                      <option value="" disabled>Rôle</option>
                      <option value="Enseignant">Enseignant</option>
                      <option value="Étudiant">Étudiant</option>
                    </select>

                    <template v-if="nouveauMembre.role === 'Enseignant'">
                       <select v-model="nouveauMembre.nom" class="input-select w-full" required>
                          <option value="" disabled>Sélectionner un enseignant</option>
                          <option v-for="prof in enseignantsProposes" :key="prof" :value="prof">
                              {{ prof }}
                          </option>
                       </select>
                    </template>

                    <template v-if="nouveauMembre.role === 'Étudiant'">
                        <select v-model="nouveauMembre.groupe" class="input-select" required>
                            <option value="" disabled>Groupe</option>
                            <option value="A1">A1</option>
                            <option value="A2">A2</option>
                            <option value="B1">B1</option>
                            <option value="B2">B2</option>
                        </select>
                        <input 
                          type="text" 
                          v-model="nouveauMembre.nom" 
                          placeholder="Nom de l'étudiant..." 
                          class="input-search-member flex-1" 
                          required 
                        />
                    </template>
                    
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
                    <span class="text-right">Groupe</span>
                  </div>
                  <ul class="clean-list list-separated">
                    <li v-for="etudiant in etudiants" :key="etudiant.id" class="table-row">
                      <span>{{ etudiant.nom }}</span>
                      <span class="text-right"><span class="status-badge status-default">{{ etudiant.groupe }}</span></span>
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

const saeData = ref({});
const membresListe = ref([]); 
const afficherModifSaePopup = ref(false);

// --- LOGIQUE LOCALE ET INTELLIGENTE DES ENSEIGNANTS ---
const enseignantsProposes = computed(() => {
    const filiere = (saeData.value.filiere || "").toLowerCase();
    const titre = (saeData.value.titre || "").toLowerCase();
    const semestreStr = saeData.value.semestre || "S1";
    const numSemestre = parseInt(semestreStr.replace('S', ''));

    // Liste par défaut exhaustive
    let professeurs = [
        'Olivier Le Cadet (Dev Web)', 
        'Soufian Ben Amor (Dev Web & Ergonomie)', 
        'Sylvie Fabre (Communication)', 
        'Therese Lepage (Anglais)', 
        'Sophia Kourkoulakou (VR & 3D)',
        'Vincent Wable (Audiovisuel)', 
        'Fred Pirat (Audiovisuel)',
        'Xavier hautbois (Développement web Animation 2D/3D )',
        'Brigitte Neveu-Derotrie (Marketing)',
        'Michel Pinoza (Graphisme)',
        'Jean-Marie Clech (Graphisme)',
    ];

   // Filtrage basé sur les mots clés de la SAE
    if (filiere.includes('web') || filiere.includes('dev') || titre.includes('web') || titre.includes('back')) {
        professeurs = ['Olivier Le Cadet (Dev Web)', 'Soufian Ben Amor (Dev Web & Ergonomie)'];
    } else if (filiere.includes('design') || filiere.includes('graphisme') || filiere.includes('ergo') || titre.includes('ui')) {
        professeurs = ['Jean-Marie Clech (Graphisme)', 'Michel Pinoza (Graphisme)', 'Soufian Ben Amor (Dev Web & Ergonomie)'];
    } else if (filiere.includes('com') || titre.includes('marketing')) {
        professeurs = ['Brigitte Neveu-Derotrie (Marketing)', 'Sylvie Fabre (Communication)'];
    }

    // Le prof de VR n'est disponible qu'à partir du semestre 3
    if (numSemestre >= 3 && !professeurs.includes('Sophia Kourkoulakou (VR & 3D)')) {
        professeurs.push('Sophia Kourkoulakou (VR & 3D)');
    }

    return professeurs;
});

onMounted(() => {
  const saeId = parseInt(route.params.id);
  
  const stored = localStorage.getItem('mmi_data_sae_v2');
  let bddSaeGlobale = [];
  if (stored) {
    bddSaeGlobale = JSON.parse(stored);
  }
  
  const foundSae = bddSaeGlobale.find(s => s.id === saeId);
  
  if (foundSae) {
    saeData.value = { 
      ...foundSae,
      filiere: foundSae.filiere || "Générique",
      sousTitre: foundSae.sousTitre || "Nouvelle mission en cours",
      description: foundSae.description || "Aucune description détaillée n'a été fournie pour le moment.",
      consignes: foundSae.consignes || "Veuillez vous référer au document qui sera transmis par l'enseignant responsable.",
      dateEcheance: foundSae.dateEcheance || "À définir"
    };
    membresListe.value = [...(foundSae.membres || [])]; 
  } else {
    saeData.value = {
      id: saeId,
      titre: "SAE " + (saeId || "Inconnue"),
      filiere: "Générique",
      sousTitre: "Sujet en cours de rédaction",
      description: "La description de cette SAE n'est pas encore disponible.",
      semestre: "S1",
      dateEcheance: "À définir",
      consignes: "Veuillez vous référer au document qui sera transmis par l'enseignant responsable.",
      color: "linear-gradient(135deg, #9ca3af, #4b5563)"
    };
    membresListe.value = []; 
  }

  if (route.query.edit === 'true') {
    afficherModifSaePopup.value = true;
  }
});

const ouvrirModifSAE = () => { afficherModifSaePopup.value = true; };

const validerModifSAE = () => {
  const stored = localStorage.getItem('mmi_data_sae_v2');
  if (stored) {
    let bddSaeGlobale = JSON.parse(stored);
    const index = bddSaeGlobale.findIndex(s => s.id === saeData.value.id);
    if (index !== -1) {
      bddSaeGlobale[index].titre = saeData.value.titre;
      bddSaeGlobale[index].filiere = saeData.value.filiere;
      bddSaeGlobale[index].description = saeData.value.description;
      bddSaeGlobale[index].consignes = saeData.value.consignes;
      
      localStorage.setItem('mmi_data_sae_v2', JSON.stringify(bddSaeGlobale));
    }
  }

  afficherModifSaePopup.value = false;
};

const afficherModifMembresPopup = ref(false);
const ouvrirModifMembres = () => { afficherModifMembresPopup.value = true; };

const nouveauMembre = ref({ role: '', nom: '', groupe: '' });
const enseignants = computed(() => membresListe.value.filter(m => m.role === 'Enseignant'));
const etudiants = computed(() => membresListe.value.filter(m => m.role === 'Étudiant'));

const ajouterMembre = () => {
  if (nouveauMembre.value.role && nouveauMembre.value.nom) {
    membresListe.value.push({
      id: Date.now(),
      role: nouveauMembre.value.role,
      nom: nouveauMembre.value.nom,
      groupe: nouveauMembre.value.role === 'Étudiant' ? nouveauMembre.value.groupe : ''
    });
    
    sauvegarderMembres();
    
    nouveauMembre.value.nom = ''; 
    nouveauMembre.value.groupe = '';
  }
};

const supprimerMembre = (id) => { 
    membresListe.value = membresListe.value.filter(m => m.id !== id); 
    sauvegarderMembres();
};

const sauvegarderMembres = () => {
    const stored = localStorage.getItem('mmi_data_sae_v2');
    if(stored) {
        let bddSaeGlobale = JSON.parse(stored);
        const index = bddSaeGlobale.findIndex(s => s.id === saeData.value.id);
        if(index !== -1) {
            bddSaeGlobale[index].membres = [...membresListe.value];
            localStorage.setItem('mmi_data_sae_v2', JSON.stringify(bddSaeGlobale));
        }
    }
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

// SYNTHÈSE VOCALE DESCRIPTIVE
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
      etudiants.value.forEach(etu => { texteALire += `L'étudiant ${etu.nom} est dans le groupe ${etu.groupe}. `; });
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

<style scoped src="../css/Admin_SAE_Detail.css"></style>