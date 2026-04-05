<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import gsap from 'gsap';

const props = defineProps({
  width: { type: Number, default: 500 },
  height: { type: Number, default: 350 },
  cardDistance: { type: Number, default: 60 },
  verticalDistance: { type: Number, default: 70 },
  // On garde cette prop au cas où, mais on ne l'utilisera plus dans le code
  skewAmount: { type: Number, default: 6 }, 
  cards: { type: Array, required: true }
});

const cardRefs = ref([]);
let order = []; // Stocke l'ordre actuel des index (ex: [0, 1, 2, 3])
let isAnimating = false; // Verrou pour empêcher de scroller pendant une animation

// Calcule les coordonnées (x, y, z) pour une position donnée dans la pile
const makeSlot = (position, total) => ({
  x: 0, // Alignement horizontal parfait
  y: -position * props.verticalDistance, // Empilement vertical
  z: -position * props.cardDistance * 1.5, // Profondeur pour la 3D
  zIndex: total - position // Ordre de superposition
});

// Animation pour passer à la carte SUIVANTE (Molette vers le bas)
const nextCard = () => {
  if (isAnimating) return; 
  isAnimating = true;

  const total = props.cards.length;
  const frontIndex = order[0]; 
  const frontEl = cardRefs.value[frontIndex];

  const tl = gsap.timeline({
    onComplete: () => {
      order.push(order.shift());
      isAnimating = false;
    }
  });

  // Retrait de rotationZ: -10 ---
  tl.to(frontEl, {
    y: '+=300', opacity: 0, // La carte tombe droite
    duration: 0.4, ease: 'power2.in'
  }, 0);

  // Les autres cartes avancent d'un cran
  for (let i = 1; i < total; i++) {
    const el = cardRefs.value[order[i]];
    const newSlot = makeSlot(i - 1, total);
    tl.to(el, {
      x: newSlot.x, y: newSlot.y, z: newSlot.z, zIndex: newSlot.zIndex,
      duration: 0.5, ease: 'power2.out'
    }, 0.2);
  }

  // La carte tombée est téléportée au fond de la pile et réapparait doucement
  const backSlot = makeSlot(total - 1, total);
  tl.set(frontEl, {
    x: backSlot.x, y: backSlot.y, z: backSlot.z, 
    zIndex: backSlot.zIndex
    // rotationZ: 0 a été retiré ici, c'est inutile maintenant
  }, 0.4);
  tl.to(frontEl, { opacity: 1, duration: 0.3 }, 0.4);
};

// Animation pour revenir à la carte PRÉCÉDENTE (Molette vers le haut)
const prevCard = () => {
  if (isAnimating) return;
  isAnimating = true;

  const total = props.cards.length;
  order.unshift(order.pop());
  const newFrontIndex = order[0];
  const newFrontEl = cardRefs.value[newFrontIndex];

  const tl = gsap.timeline({
    onComplete: () => { isAnimating = false; }
  });

  const frontSlot = makeSlot(0, total);
  
  // Retrait de rotationZ: -10 ---
  gsap.set(newFrontEl, {
    x: frontSlot.x, y: frontSlot.y + 300, z: frontSlot.z,
    zIndex: frontSlot.zIndex, opacity: 0 // Reste droite
  });

  // Les autres cartes reculent d'un cran
  for (let i = 1; i < total; i++) {
    const el = cardRefs.value[order[i]];
    const newSlot = makeSlot(i, total);
    tl.to(el, {
      x: newSlot.x, y: newSlot.y, z: newSlot.z, zIndex: newSlot.zIndex,
      duration: 0.5, ease: 'power2.out'
    }, 0);
  }

  // Retrait de rotationZ: 0
  tl.to(newFrontEl, {
    y: frontSlot.y, opacity: 1, // La carte remonte droite
    duration: 0.5, ease: 'power2.out'
  }, 0.2);
};

// L'écouteur de la molette 
const onWheel = (e) => {
  e.preventDefault(); 
  if (Math.abs(e.deltaY) > 20) {
    if (e.deltaY > 0) {
      nextCard(); 
    } else {
      prevCard(); 
    }
  }
};

onMounted(async () => {
  await nextTick();
  const total = props.cards.length;
  if (total === 0) return;

  order = Array.from({ length: total }, (_, i) => i);

  order.forEach((cardIndex, pileIndex) => {
    const el = cardRefs.value[cardIndex];
    const slot = makeSlot(pileIndex, total);
    gsap.set(el, {
      ...slot,
      xPercent: -50, yPercent: -50,
      transformOrigin: 'center center',
      opacity: 1, force3D: true
    });
  });

  window.addEventListener('wheel', onWheel, { passive: false });
});

onUnmounted(() => {
  window.removeEventListener('wheel', onWheel);
});
</script>

<template>
  <div class="card-swap-container" :style="{ width: `${width}px`, height: `${height}px` }">
    <div
      v-for="(card, index) in cards"
      :key="card.id"
      class="card-gsap"
      :ref="el => { if (el) cardRefs[index] = el }"
      :style="{ width: `${width}px`, height: `${height}px` }"
    >
      <slot name="card-content" :card="card"></slot>
    </div>
  </div>
</template>

<style scoped>
@import '../css/CardSwap.css';
</style>