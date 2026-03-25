import { createApp } from 'vue'
import App from './App.vue'
import routeur from './routeur.js' // On importe bien "routeur.js"

const app = createApp(App)

app.use(routeur) // On dit à l'application d'utiliser le routeur
app.mount('#app') // On monte l'application UNE SEULE FOIS