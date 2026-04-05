<template>
  <div class="dashboard-layout">

    <main class="main-content" id="main-content-area" @mouseenter="lireAction('Zone des détails de la SAE')">

      <div v-if="afficherModifSaePopup" class="modal-overlay" @click="afficherModifSaePopup = false; lireAction('Fermeture de la fenêtre de modification de la SAE')">
        <div class="modal-content-center modal-xl" @click.stop @mouseenter="lireAction('Fenêtre de modification des détails de la SAE')">
          <h3 tabindex="0" @focus="lireAction('Modifier les détails de la SAE')" @mouseenter="lireAction('Titre de la fenêtre : Modifier les détails')">Modifier les détails de la SAE</h3>
          
          <form @submit.prevent="validerModifSAE" class="ajout-sae-form">
            <div class="form-row-modal">
              <div class="form-group-modal flex-1">
                <label @mouseenter="lireAction('Label : Intitulé')">Intitulé</label>
                <input v-model="saeData.titre" type="text" class="input-full" required @focus="lireAction('Champ de saisie : Intitulé')" @mouseenter="lireAction('Champ : Intitulé')" @click="lireAction('Saisie de l\'intitulé')" />
              </div>
              <div class="form-group-modal flex-1">
                <label @mouseenter="lireAction('Label : Matière ou Tag')">Matière / Tag</label>
                <input v-model="saeData.filiere" type="text" class="input-full" required @focus="lireAction('Champ de saisie : Matière')" @mouseenter="lireAction('Champ : Matière ou Tag')" @click="lireAction('Saisie de la matière')" />
              </div>
            </div>
            
            <div class="form-row-modal">
               <div class="form-group-modal flex-1">
                <label @mouseenter="lireAction('Label : Date limite ou Rendu')">Date limite / Rendu</label>
                <input v-model="saeData.dateInput" type="date" class="input-full" @focus="lireAction('Champ de saisie : Date limite')" @mouseenter="lireAction('Champ : Date limite')" @click="lireAction('Saisie de la date limite')" />
              </div>
              <div class="form-group-modal flex-1">
                <label @mouseenter="lireAction('Label : Semestre')">Semestre</label>
                <select v-model="saeData.semestre" class="input-full" @focus="lireAction('Menu déroulant : Semestre')" @mouseenter="lireAction('Menu déroulant : Semestre')" @click="lireAction('Sélection du semestre')">
                   <option value="S1">S1</option>
                   <option value="S2">S2</option>
                   <option value="S3">S3</option>
                   <option value="S4">S4</option>
                   <option value="S5">S5</option>
                   <option value="S6">S6</option>
                </select>
              </div>
            </div>

            <div class="form-group-modal">
              <label @mouseenter="lireAction('Label : Description')">Description</label>
              <input v-model="saeData.description" type="text" class="input-full" @focus="lireAction('Champ de saisie : Description')" @mouseenter="lireAction('Champ : Description')" @click="lireAction('Saisie de la description')" />
            </div>
            
            <div class="modal-actions">
              <button type="button" @click="afficherModifSaePopup = false; lireAction('Bouton Annuler la modification')" class="btn-cancel" @focus="lireAction('Bouton Annuler')" @mouseenter="lireAction('Bouton Annuler les modifications')">Annuler</button>
              <button type="submit" class="btn-confirm" @focus="lireAction('Bouton Enregistrer les modifications')" @mouseenter="lireAction('Bouton Enregistrer')" @click="lireAction('Validation des modifications')">Enregistrer</button>
            </div>
          </form>

        </div>
      </div>

      <div v-if="afficherModifMembresPopup" class="modal-overlay" @click="afficherModifMembresPopup = false; lireAction('Fermeture de la fenêtre des membres')">
        <div class="modal-content-center modal-lg" @click.stop @mouseenter="lireAction('Fenêtre de gestion des membres')">
          <h3 tabindex="0" @focus="lireAction('Gérer la liste des membres')" @mouseenter="lireAction('Titre de la fenêtre : Gérer la liste des membres')">Gérer la liste des membres</h3>
          
          <div class="form-group-modal">
            <label tabindex="0" @focus="lireAction('Membres assignés. Cliquez pour retirer un membre.')" @mouseenter="lireAction('Label : Membres assignés')">Membres assignés (Cliquez pour retirer)</label>
            <div class="members-tags-container" @mouseenter="lireAction('Conteneur des membres assignés')">
              <span 
                v-for="m in membresListe" 
                :key="m.id" 
                @click="supprimerMembre(m.id)" 
                class="clickable-member-tag" 
                title="Supprimer ce membre"
                tabindex="0"
                @mouseenter="lireAction(`Membre assigné : ${m.nom}. Cliquez pour retirer ce membre.`)"
                @focus="lireAction(`Membre assigné : ${m.nom}. Appuyez sur Entrée pour retirer.`)"
              >
                {{ m.nom }} ({{ m.role }}) ✕
              </span>
              <span v-if="membresListe.length === 0" class="text-gray-500 italic text-sm" tabindex="0" @focus="lireAction('Aucun membre assigné pour le moment')" @mouseenter="lireAction('Aucun membre assigné')">Aucun membre assigné.</span>
            </div>
          </div>

          <div v-if="etudiants.length > 0" class="form-group-modal mt-15">
            <label tabindex="0" @focus="lireAction('Modifier le groupe des étudiants assignés')" @mouseenter="lireAction('Label : Modifier le groupe des étudiants')">Modifier le groupe des étudiants</label>
            <div v-for="etu in etudiants" :key="'groupe-'+etu.id" class="status-edit-row">
              <span tabindex="0" @focus="lireAction(`Étudiant : ${etu.nom}`)" @mouseenter="lireAction(`Étudiant : ${etu.nom}`)">{{ etu.nom }}</span>
              <select v-model="etu.groupe" @change="sauvegarderMembres(); lireAction(`Nouveau groupe assigné pour ${etu.nom}`)" class="input-select-small" @focus="lireAction(`Changer le groupe de ${etu.nom}`)" @mouseenter="lireAction('Menu pour changer le groupe de l\'étudiant')" @click="lireAction('Sélection du groupe')">
                <option value="A1">A1</option>
                <option value="A2">A2</option>
                <option value="B1">B1</option>
                <option value="B2">B2</option>
              </select>
            </div>
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="afficherModifMembresPopup = false; lireAction('Bouton Terminer la gestion des membres')" class="btn-confirm" @focus="lireAction('Bouton Terminer')" @mouseenter="lireAction('Bouton Terminer')">Terminer</button>
          </div>

        </div>
      </div>

      <div class="content-area" role="main">
        
        <div class="page-header">
          <h1 tabindex="0" @focus="lireAction('Vous êtes sur la page des paramètres de la ' + saeData.titre)" @mouseenter="lireAction('Titre : Page des paramètres de la ' + saeData.titre)">Page / <span class="breadcrumb-active">{{ saeData.titre }}</span></h1>
        </div>
        <hr class="divider" />

        <div class="detail-grid">
          
          <div class="main-column">
            
            <section class="sae-detail-card card-container" @mouseenter="lireAction('Détails généraux de la SAE')">
              <div class="sae-header-card">
                <span class="tag-filiere" tabindex="0" @focus="lireAction('Filière : ' + saeData.filiere)" @mouseenter="lireAction('Tag Filière : ' + saeData.filiere)">{{ saeData.filiere }}</span>
                <button class="action-btn" @click="ouvrirModifSAE" title="Modifier la SAE" @mouseenter="lireAction('Bouton Modifier les détails globaux de la SAE')" @focus="lireAction('Bouton Modifier les détails de la SAE')">
                  <img src="../assets/icon_modifier.svg" alt="Modifier" class="action-svg" />
                </button>
              </div>
              
              <h2 class="sae-title-large" tabindex="0" @focus="lireAction('Titre de la SAE : ' + saeData.titre)" @mouseenter="lireAction('Titre de la SAE : ' + saeData.titre)">{{ saeData.titre }}</h2>
              
              <div class="sae-banner" :style="{ background: saeData.color }"></div>
              
              <div class="sae-description-block">
                <h3 tabindex="0" @focus="lireAction('Sous-titre : ' + saeData.sousTitre)" @mouseenter="lireAction('Sous-titre : ' + saeData.sousTitre)">{{ saeData.sousTitre }}</h3>
                <p tabindex="0" @focus="lireAction('Description : ' + saeData.description)" @mouseenter="lireAction('Description : ' + saeData.description)">{{ saeData.description }}</p>
              </div>
            </section>

            <div class="members-management-row">
              
              <section class="add-member-card card-container" @mouseenter="lireAction('Section pour ajouter un membre')">
                <h3 tabindex="0" @focus="lireAction('Formulaire pour ajouter un membre')" @mouseenter="lireAction('Titre : Ajouter membre')">Ajouter membre</h3>
                <form @submit.prevent="ajouterMembre" class="add-member-form">
                  <div class="form-row flex-col-mobile">
                    <select v-model="nouveauMembre.role" class="input-select w-full" required @focus="lireAction('Sélectionner le rôle du membre')" @mouseenter="lireAction('Menu déroulant : Rôle du membre')" @click="lireAction('Choix du rôle')">
                      <option value="" disabled>Rôle</option>
                      <option value="Enseignant">Enseignant</option>
                      <option value="Étudiant">Étudiant</option>
                    </select>

                    <template v-if="nouveauMembre.role === 'Enseignant'">
                        <input 
                          type="text" 
                          v-model="nouveauMembre.nom" 
                          placeholder="Nom de l'enseignant..." 
                          class="input-search-member flex-1" 
                          required 
                          @focus="lireAction('Champ : Nom de l\'enseignant')"
                          @mouseenter="lireAction('Champ de saisie : Nom de l\'enseignant')"
                          @click="lireAction('Saisie du nom de l\'enseignant')"
                        />
                    </template>

                    <template v-if="nouveauMembre.role === 'Étudiant'">
                        <select v-model="nouveauMembre.groupe" class="input-select" required @focus="lireAction('Sélectionner le groupe de l\'étudiant')" @mouseenter="lireAction('Menu déroulant : Groupe de l\'étudiant')" @click="lireAction('Sélection du groupe')">
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
                          @focus="lireAction('Champ : Nom de l\'étudiant')"
                          @mouseenter="lireAction('Champ de saisie : Nom de l\'étudiant')"
                          @click="lireAction('Saisie du nom de l\'étudiant')"
                        />
                    </template>
                    
                    <button type="submit" class="btn-add-circle" @mouseenter="lireAction('Bouton Valider l\'ajout du membre')" @focus="lireAction('Bouton Valider l\'ajout du membre')" @click="lireAction('Validation de l\'ajout du membre')">+</button>
                  </div>
                </form>
              </section>

              <section class="members-list-card card-container flex-3" @mouseenter="lireAction('Section de la liste des membres actuels')">
                <div class="card-header-flex">
                  <h3 tabindex="0" @focus="lireAction('Liste Des Membres actuellement assignés')" @mouseenter="lireAction('Titre : Liste Des Membres')">Liste Des Membres</h3>
                  <button class="action-btn" @click="ouvrirModifMembres" title="Modifier la liste des membres" @mouseenter="lireAction('Bouton Ouvrir l\'outil pour gérer la liste des membres')" @focus="lireAction('Bouton Gérer la liste des membres')">
                    <img src="../assets/icon_modifier.svg" alt="Modifier" class="action-svg" />
                  </button>
                </div>
                
                <div class="members-section">
                  <h4 tabindex="0" @focus="lireAction(`Section : Enseignants Responsables. Il y a ${enseignants.length} enseignants.`)" @mouseenter="lireAction('Section : Enseignants Responsables')">Enseignants Responsables</h4>
                  <ul class="clean-list">
                    <li v-for="prof in enseignants" :key="prof.id" tabindex="0" @focus="lireAction(`Enseignant : ${prof.nom}`)" @mouseenter="lireAction(`Enseignant : ${prof.nom}`)">{{ prof.nom }}</li>
                    <li v-if="enseignants.length === 0" class="italic-text" tabindex="0" @focus="lireAction('Aucun enseignant assigné')" @mouseenter="lireAction('Aucun enseignant assigné')">Aucun enseignant</li>
                  </ul>
                </div>
                
                <div class="members-section mt-20">
                  <div class="table-header">
                    <span tabindex="0" @focus="lireAction('Colonne : Nom de l\'étudiant')" @mouseenter="lireAction('En-tête de colonne : Étudiant')">Étudiant</span>
                    <span class="text-right" tabindex="0" @focus="lireAction('Colonne : Groupe de l\'étudiant')" @mouseenter="lireAction('En-tête de colonne : Groupe')">Groupe</span>
                  </div>
                  <ul class="clean-list list-separated">
                    <li v-for="etudiant in etudiants" :key="etudiant.id" class="table-row" tabindex="0" @focus="lireAction(`Étudiant : ${etudiant.nom}, assigné au groupe ${etudiant.groupe}`)" @mouseenter="lireAction(`Étudiant : ${etudiant.nom}, Groupe : ${etudiant.groupe}`)">
                      <span>{{ etudiant.nom }}</span>
                      <span class="text-right"><span class="status-badge status-default">{{ etudiant.groupe }}</span></span>
                    </li>
                    <li v-if="etudiants.length === 0" class="italic-text mt-15" tabindex="0" @focus="lireAction('Aucun étudiant assigné pour le moment')" @mouseenter="lireAction('Aucun étudiant assigné')">Aucun étudiant assigné</li>
                  </ul>
                </div>
              </section>
              
            </div>
          </div>

          <div class="side-column">
            
            <section class="info-card card-container" @mouseenter="lireAction('Section Détails du projet')">
              <h3 tabindex="0" @focus="lireAction('Détails supplémentaires du projet')" @mouseenter="lireAction('Titre : Détails du projet')">Détails du projet</h3>
              <div class="info-content">
                <p tabindex="0" @focus="lireAction(`Semestre affecté : ${saeData.semestre}`)" @mouseenter="lireAction(`Semestre affecté : ${saeData.semestre}`)">Semestre affecté: <strong>{{ saeData.semestre }}</strong></p>
                <p tabindex="0" @focus="lireAction(`Date d'échéance : ${saeData.dateEcheance}`)" @mouseenter="lireAction(`Date d'échéance : ${saeData.dateEcheance}`)">Date d'échéance: <br><strong class="date-highlight">{{ saeData.dateEcheance }}</strong></p>
              </div>
            </section>
            
            <section class="consigne-card card-container flex-1" @mouseenter="lireAction('Section Consignes de la SAE')">
              <h3 tabindex="0" @focus="lireAction('Consignes générales de la SAE')" @mouseenter="lireAction('Titre : Consignes de la SAE')">Consignes de la SAE</h3>
              <div class="consigne-content">
                <p tabindex="0" @focus="lireAction(saeData.consignes || 'Aucune consigne n\'a été rédigée pour le moment.')" @mouseenter="lireAction('Texte de la consigne')">{{ saeData.consignes || "Aucune consigne n'a été rédigée pour le moment." }}</p>
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
import { useAccessibiliteStore } from '@/stores/accessibilite'

const accessStore = useAccessibiliteStore()

const router = useRouter();
const route = useRoute();

const saeData = ref({});
const membresListe = ref([]); 
const afficherModifSaePopup = ref(false);

const nomsMoisGlobaux = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre'];

// --- FONCTION D'ACCESSIBILITÉ VOCALE ---
const lireAction = (texte) => {
  if (accessStore.voix) {
    accessStore.lireTexte(texte);
  }
};

onMounted(() => {
  const saeId = parseInt(route.params.id);
  
  // Utilisation de mmi_data_sae_v2 pour unifier la base de données !
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
      // Si aucune consigne précise n'est donnée, on utilise la description renseignée dans le formulaire
      consignes: foundSae.consignes || foundSae.description || "Veuillez vous référer au document qui sera transmis par l'enseignant responsable.",
      dateEcheance: foundSae.dateEcheance || foundSae.date || "À définir",
      dateInput: foundSae.fin || ''
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
      color: "linear-gradient(135deg, #9ca3af, #4b5563)",
      dateInput: ''
    };
    membresListe.value = []; 
  }

  if (route.query.edit === 'true') {
    afficherModifSaePopup.value = true;
  }
});

const ouvrirModifSAE = () => { 
  afficherModifSaePopup.value = true; 
  lireAction('Ouverture de la fenêtre pour modifier les détails de la S A E');
};

const validerModifSAE = () => {
  // Utilisation de mmi_data_sae_v2
  const stored = localStorage.getItem('mmi_data_sae_v2');
  if (stored) {
    let bddSaeGlobale = JSON.parse(stored);
    const index = bddSaeGlobale.findIndex(s => s.id === saeData.value.id);
    if (index !== -1) {
      bddSaeGlobale[index].titre = saeData.value.titre;
      bddSaeGlobale[index].filiere = saeData.value.filiere;
      bddSaeGlobale[index].description = saeData.value.description;
      bddSaeGlobale[index].consignes = saeData.value.consignes;
      bddSaeGlobale[index].semestre = saeData.value.semestre;

      if (saeData.value.dateInput) {
        const objDate = new Date(saeData.value.dateInput);
        if (!isNaN(objDate)) {
           const moisNom = nomsMoisGlobaux[objDate.getMonth()];
           const jour = objDate.getDate();
           const annee = objDate.getFullYear();
           
           const dDebut = new Date(objDate);
           dDebut.setDate(dDebut.getDate() - 14);

           bddSaeGlobale[index].debut = dDebut.toISOString().split('T')[0];
           bddSaeGlobale[index].fin = objDate.toISOString().split('T')[0];
           
           bddSaeGlobale[index].mois = moisNom;
           bddSaeGlobale[index].jour = jour;
           bddSaeGlobale[index].date = `${moisNom.substring(0, 3)} ${jour}`;
           bddSaeGlobale[index].dateEcheance = `${jour} ${moisNom} ${annee}`;
           
           saeData.value.dateEcheance = bddSaeGlobale[index].dateEcheance;
        }
      }
      
      localStorage.setItem('mmi_data_sae_v2', JSON.stringify(bddSaeGlobale));
    }
  }

  afficherModifSaePopup.value = false;
  lireAction(`Les modifications ont été appliquées et l'interface est mise à jour. Nouvel intitulé de la SAE : ${saeData.value.titre}. Date limite : ${saeData.value.dateEcheance}.`);
};

const afficherModifMembresPopup = ref(false);
const ouvrirModifMembres = () => { 
  afficherModifMembresPopup.value = true; 
  lireAction('Ouverture de la fenêtre de gestion des membres de la S A E');
};

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
    lireAction(`Mise à jour effectuée. ${nouveauMembre.value.nom} a été ajouté à la liste avec le rôle de ${nouveauMembre.value.role}.`);
    
    nouveauMembre.value.nom = ''; 
    nouveauMembre.value.groupe = '';
  }
};

const supprimerMembre = (id) => { 
    membresListe.value = membresListe.value.filter(m => m.id !== id); 
    sauvegarderMembres();
    lireAction('Le membre sélectionné a été retiré de la liste avec succès. La liste est mise à jour.');
};

const sauvegarderMembres = () => {
    // Utilisation de mmi_data_sae_v2
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

watch(() => accessStore.voix, (estActive) => {
  if (estActive) {
    let texteALire = `Vous êtes sur la page de paramétrage de la S.A.E ${saeData.value.titre}. `;
    texteALire += `Cette S.A.E concerne le semestre ${saeData.value.semestre} et la date d'échéance est fixée au ${saeData.value.dateEcheance}. `;
    
    texteALire += `Concernant l'équipe actuelle : Il y a ${enseignants.value.length} enseignants et ${etudiants.value.length} étudiants affectés. `;
    texteALire += `Vous pouvez utiliser les boutons pour modifier la S A E, ajouter des membres, ou gérer les groupes.`;

    accessStore.lireTexte(texteALire);
  }
});

</script>

<style scoped src="../css/Admin_SAE_Detail.css"></style>