import { createRouter, createWebHistory } from 'vue-router'

// importations de vues
import LandingPage from '../views/LandingPage.vue'
import PublicView from '../views/PublicView.vue' 
import PublicSAEDetailView from '../views/PublicSAEDetailView.vue'
import EnseignantDashbordView from '../views/EnseignantDashbordView.vue'
import EnseignantMesSaeView from '../views/EnseignantMesSaeView.vue'
import EnseignantSAEDetailView from '../views/EnseignantSAEDetailView.vue'
import PageConnexion from '../views/PageConnexion.vue'
import PageConnexionSession from '../views/PageConnexionSession.vue'
import Adminlogin from '../views/Adminlogin.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import AdminDashboard_SAE from '../views/AdminDashboard_SAE.vue'
import Admin_SAE_Detail from '../views/Admin_SAE_Detail.vue'
import StudentLogin from '../views/StudentLogin.vue'
import StudentDashboard from '../views/StudentDashboard.vue'
import StudentSAE from '../views/StudentSAE.vue'
import StudentSAE_Detail from '../views/StudentSAE_Detail.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'landing',
      component: LandingPage // page qui s'affiche au lancement
    },
    
    {
      path: '/public-home',
      name: 'public-home',
      component: PublicView 
    },

    {
      path: '/sae/:id', 
      name: 'sae-detail',
      component: PublicSAEDetailView
    },

    {
      path: '/connexion',
      name: 'connexion',
      component: PageConnexion
    },

    {
      // Le ":role" permet de savoir quelle session est demandée
      path: '/connexion/:role',
      name: 'connexion-session',
      component: PageConnexionSession
    },

    {
      path: '/enseignant/dashboard',
      name: 'enseignant-dashboard',
      component: EnseignantDashbordView
    },

    {
      path: '/enseignant/mes-sae',
      name: 'mes-sae',
      component: EnseignantMesSaeView
    },

    {
      path: '/enseignant/sae/:id',
      name: 'ens-sae-detail',
      component: EnseignantSAEDetailView
    },

    {
      path: '/connexion/admin',
      name: 'ConnexionAdmin',
      component: Adminlogin
    },

    {
      path: '/admin/dashboard',
      name: 'AdminDashboard',
      component: AdminDashboard
    },

    {
      path: '/admin/sae',
      name: 'admin-sae',
      component: AdminDashboard_SAE
    },

    {
      // Le ":id" est crucial, il permet d'ouvrir une SAE spécifique
      path: '/admin/sae/:id',
      name: 'admin-sae-detail',
      component: Admin_SAE_Detail
    },
  
    {
      path: '/connexion/etudiant',
      name: 'ConnexionEtudiant',
      component: StudentLogin
    },

    {
      path: '/etudiant/dashboard',
      name: 'StudentDashboard',
      component: StudentDashboard
    },

    {
      path: '/etudiant/sae',
      name: 'StudentSAE',
      component: StudentSAE
    },

    {
      path: '/etudiant/sae/:id',
      name: 'StudentSAE_Detail',
      component: StudentSAE_Detail
    }

  ]
})

export default router