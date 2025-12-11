import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useSettingStore = defineStore('setting', () => {
  const language = ref('English(UK)')
  function setLanguage(val) {
    language.value = val
  }
  return { language, setLanguage }
})
