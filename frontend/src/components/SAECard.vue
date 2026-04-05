<script setup>
defineProps({
  id: Number,
  title: String,
  semester: String,
  colorGradient: {
    type: String,
    default: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
  }
})
</script>

<template>
  <RouterLink :to="'/sae/' + id" class="sae-card">
    <div class="card-visual-wrapper">
      <div class="card-visual" :style="{ background: colorGradient }"></div>
    </div>
    
    <div class="card-info">
      <h3 class="sae-title">{{ title }}</h3>
      <span class="sae-semester">{{ semester }}</span> 
    </div>
  </RouterLink>
</template>

<style scoped>
 @import '../css/SAECard.css';
</style>