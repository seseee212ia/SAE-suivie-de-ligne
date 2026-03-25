import { createRouter, createWebHistory } from 'vue-router'
import AdminLogin from './views/Adminlogin.vue'
import AdminDashboard from './views/AdminDashboard.vue'
import AdminDashboard_SAE from './views/AdminDashboard_SAE.vue'
import AdminSAEDetail from './views/Admin_SAE_Detail.vue'

const routes = [
  { path: '/', component: AdminLogin }, // Page de démarrage
  { path: '/admin/dashboard', component: AdminDashboard }, // Page de destination (Tableau de bord)
  { path: '/admin/sae', component: AdminDashboard_SAE }, // Page liste des SAEs
  { path: '/admin/sae/:id', component: AdminSAEDetail, name: 'SaeDetail' } // Page de détail d'une SAE
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router