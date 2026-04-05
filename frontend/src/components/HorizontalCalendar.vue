<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  activeMonth: { type: String, required: true },
  activeDay: { type: Number, required: true }
})

const emit = defineEmits(['update:activeMonth', 'update:activeDay'])

// Tableaux réactifs qui seront remplis dynamiquement
const monthsList = ref([])
const daysList = ref([])

// 1. Fonction pour formater un mois (ex: "Avril 2026")
const formatMonthYear = (date) => {
  const formatter = new Intl.DateTimeFormat('fr-FR', { month: 'long', year: 'numeric' })
  const formatted = formatter.format(date)
  // Met la première lettre en majuscule
  return formatted.charAt(0).toUpperCase() + formatted.slice(1)
}

// 2. Générer les 12 mois à partir d'aujourd'hui
const generateMonths = () => {
  const result = []
  const today = new Date()
  
  for (let i = 0; i < 12; i++) {
    // Crée une date pour le mois actuel + i
    const d = new Date(today.getFullYear(), today.getMonth() + i, 1)
    result.push({
      label: formatMonthYear(d),
      month: d.getMonth(),
      year: d.getFullYear()
    })
  }
  monthsList.value = result
}

// 3. Générer les jours (1 à 28/30/31) selon le mois sélectionné
const generateDays = (monthLabel) => {
  const selected = monthsList.value.find(m => m.label === monthLabel)
  if (!selected) return

  // Astuce JS : le jour '0' du mois suivant donne le dernier jour du mois actuel
  const daysInMonth = new Date(selected.year, selected.month + 1, 0).getDate()
  
  const result = []
  for (let i = 1; i <= daysInMonth; i++) {
    result.push(i)
  }
  daysList.value = result

  // Sécurité : si on était le 31 et qu'on passe à un mois de 30 jours, on ramène la sélection au 30
  if (props.activeDay > daysInMonth) {
    emit('update:activeDay', daysInMonth)
  }
}

// 4. On écoute les changements de mois pour mettre à jour les jours
watch(() => props.activeMonth, (newMonth) => {
  if (newMonth) generateDays(newMonth)
})

// Au chargement du composant
onMounted(() => {
  generateMonths()
  
  // Si aucun mois n'est défini (ou qu'il est obsolète), on force le mois actuel
  if (!props.activeMonth || !monthsList.value.some(m => m.label === props.activeMonth)) {
    const currentMonthLabel = monthsList.value[0].label
    emit('update:activeMonth', currentMonthLabel)
    generateDays(currentMonthLabel)
  } else {
    generateDays(props.activeMonth)
  }
})
</script>

<template>
  <section class="calendar-section">
    <div class="months-list">
      <button 
        v-for="monthObj in monthsList" 
        :key="monthObj.label"
        :class="['month-btn', { active: activeMonth === monthObj.label }]"
        @click="emit('update:activeMonth', monthObj.label)"
      >
        {{ monthObj.label }}
      </button>
    </div>
    
    <div class="days-list">
      <button 
        v-for="day in daysList" 
        :key="day"
        :class="['day-btn', { active: activeDay === day }]"
        @click="emit('update:activeDay', day)"
      >
        {{ day }}
      </button>
    </div>
  </section>
</template>

<style scoped>
 @import '../css/HorizontalCalendar.css';
</style>