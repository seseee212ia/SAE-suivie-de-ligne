import { defineStore } from 'pinia'

export const useAccessibiliteStore = defineStore('accessibilite', {
  state: () => ({
    voix: false,
    daltonien: false,
    fontScale: 1
  }),
  
  actions: {
    toggleVoix() {
      this.voix = !this.voix;
      if (this.voix) {
        this.lireTexte("Synthèse vocale activée");
      } else {
        window.speechSynthesis.cancel();
      }
    },
    
    toggleDaltonien() {
      this.daltonien = !this.daltonien;
      if (this.daltonien) {
        document.body.style.filter = "contrast(110%) sepia(50%) hue-rotate(180deg) saturate(150%)";
      } else {
        document.body.style.filter = "none";
      }
    },
    
    setPolice(scale) {
      this.fontScale = scale;
      document.documentElement.style.fontSize = `${16 * scale}px`;
    },
    
    lireTexte(texte) {
      if (this.voix && texte) {
        window.speechSynthesis.cancel(); // Coupe le discours précédent
        const utterance = new SpeechSynthesisUtterance(texte);
        utterance.lang = 'fr-FR';
        utterance.rate = 1; // Vitesse de lecture
        window.speechSynthesis.speak(utterance);
      }
    }
  }
})