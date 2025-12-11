import { defineStore } from 'pinia'
import { login, getInfo, logout } from '@/api/user.js'
import { getToken, setToken, removeToken, getLocalLang, setLocalLang } from '@/utils/auth.js'
import { default_lang } from '@/utils/constants.js'
import i18n from '@/lang/index.js'
const useUserStore = defineStore('user', {
  state: () => ({
    token: getToken(),
    userInfo: null,
    backData: {},
    setting: {
      // 语言
      lang: getLocalLang() || default_lang
    }
  }),
  actions: {
    // 登录
    login(data) {
      return new Promise((resolve, reject) => {
        login(data)
          .then((res) => {
            setToken(res.data.userinfo.token)
            this.token = res.data.userinfo.token
            resolve()
          })
          .catch((error) => {
            reject(error)
          })
      })
    },
    // 获取用户信息
    getInfo() {
      return new Promise((resolve, reject) => {
        getInfo()
          .then((res) => {
            this.userInfo = res.data
            if (res.data.lang) {
              this.setting.lang = res.data.lang
              // 登录后若本地语言和后端语言不一致，以后端为主,重新设置语言
              if (i18n.global.locale !== this.setting.lang.file_name) {
                i18n.global.locale = this.setting.lang.file_name
                setLocalLang(this.setting.lang)
              }
            }
            resolve()
          })
          .catch((error) => {
            console.log(error)
            reject(error)
          })
      })
    },
    // 判断是否登录
    isLogin() {
      return new Promise((resolve, reject) => {
        if (this.token && this.userInfo) {
          resolve(true)
        } else {
          reject(false)
        }
      })
    },
    // 退出系统
    logOut() {
      return new Promise((resolve, reject) => {
        logout(this.token)
          .then(() => {
            this.token = ''
            this.userInfo = null
            removeToken()
            resolve()
          })
          .catch((error) => {
            reject(error)
          })
      })
    },
    invalidToken() {
      return new Promise((resolve, reject) => {
        this.token = ''
        this.userInfo = null
        removeToken()
        resolve()
      })
    },
    afterRePwd() {
      return new Promise((resolve, reject) => {
        this.token = ''
        this.userInfo = null
        removeToken()
        resolve()
      })
    },
    getLanguageName() {
      if (this.setting.lang) {
        return this.setting.lang.language_name
      } else {
        return default_lang.language_name
      }
    },
    setLanguage(lang) {
      this.setting.lang = lang
      //  本地存储语言
      setLocalLang(lang)
    },
    setBackData(data) {
      this.backData = { ...this.backData, ...data }
    }
  }
})

export default useUserStore
