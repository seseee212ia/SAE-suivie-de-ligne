import { createRouter, createWebHistory } from 'vue-router'

// IMPORTS ADMIN
import AdminLogin from './views/Adminlogin.vue' // Page de login admin
import AdminDashboard from './views/AdminDashboard.vue' // Page d'accueil admin
import AdminDashboard_SAE from './views/AdminDashboard_SAE.vue' // Page dashboard SAE admin
import AdminSAEDetail from './views/Admin_SAE_Detail.vue' // Page de détail SAE admin

// IMPORTS ETUDIANT
import StudentLogin from './views/StudentLogin.vue' // Page de login étudiant
import StudentDashboard from './views/StudentDashboard.vue' // Page d'accueil étudiant
import StudentSAE from './views/StudentSAE.vue' // Page dashboard SAE étudiant
import StudentSAEDetail from './views/StudentSAE_Detail.vue' // Page de détail SAE étudiant

const routes = [
  // Routes Admin
  { path: '/', component: AdminLogin }, // Page de démarrage (Admin)
  { path: '/admin/dashboard', component: AdminDashboard }, // Page d'accueil (Admin)
  { path: '/admin/sae', component: AdminDashboard_SAE }, // Page dashboard SAE (Admin)
  { path: '/admin/sae/:id', component: AdminSAEDetail, name: 'SaeDetail' }, // Page de détail SAE (Admin)

  // Routes Étudiant
  { path: '/etudiant', component: StudentLogin }, // Raccourci pour le login étudiant
  { path: '/etudiant/login', component: StudentLogin }, // Page de login étudiant
  { path: '/etudiant/dashboard', component: StudentDashboard }, // Page d'accueil étudiant
  { path: '/etudiant/sae', component: StudentSAE }, // Page dashboard SAE étudiant
  { path: '/etudiant/sae/:id', component: StudentSAEDetail, name: 'StudentSaeDetail' } // Page de détail SAE étudiant
]

const router = createRouter({ 
  history: createWebHistory(),
  routes
})

export default router