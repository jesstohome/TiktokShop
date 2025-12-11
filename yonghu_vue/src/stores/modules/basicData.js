import { defineStore } from 'pinia'

import { category } from '@/api/home.js'
import { languageList } from '@/api/user.js'

const useBasicData = defineStore('basicData', {
  state: () => ({
    categories: [],
    languages: []
  }),
  actions: {
    getCategories() {
      return new Promise((resolve, reject) => {
        if (this.categories.length) {
          resolve(this.categories)
        } else {
          category()
            .then((res) => {
              this.categories = res.data
              resolve(this.categories)
            })
            .catch((err) => {
              reject(err)
            })
        }
      })
    },
    getLanguages() {
      return new Promise((resolve, reject) => {
        if (this.languages.length) {
          resolve(this.languages)
        } else {
          languageList()
            .then((res) => {
              this.languages = res.data.list
              resolve(this.languages)
            })
            .catch((err) => {
              reject(err)
            })
        }
      })
    }
  }
})

export default useBasicData
