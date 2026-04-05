import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
// 1. On importe depuis le dossier "router" (il lira automatiquement l'index.js à l'intérieur)
import router from './router' 

const app = createApp(App)

app.use(createPinia())
// 2. On utilise la variable "router" (en anglais, comme tu l'as nommée)
app.use(router)

app.mount('#app')
