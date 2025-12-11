<template>
  <van-floating-panel v-if="show" :height="500">
    <language-item
      v-for="item in languages"
      :key="item.file_name"
      :language="item"
      @click="handlerLanguageChoose(item)"
    />
  </van-floating-panel>
  <van-overlay :show="show" @click="handlerClickOverlay" />
</template>
<script setup>
import i18n from '@/lang/index.js'
import to from 'await-to-js'
import useUserStore from '@/stores/modules/user.js'
import useBasicData from '@/stores/modules/basicData.js'
import LanguageItem from '@/components/LanguageItem/index.vue'
import { setDefaultLanguage } from '@/api/user.js'
import toast from '@/utils/toast.js'
const userStore = useUserStore()
const basicData = useBasicData()
const lang = computed(() => {
  return userStore.setting.lang
})
const show = ref(false)
const languages = ref([])
const handlerLanguageChoose = async (language) => {
  if (language.file_name === lang.value.file_name) {
    return
  }
  //  更改后端用户语言
  const [err, res] = await to(
    setDefaultLanguage({
      lang_id: language.id,
      lang: language.file_name
    })
)
  if (err) {
    //  后端默认语言失败
    toast.fail({ msg: err.msg })
    show.value = false
    return
  }
  // const [err, isLogin] = await to(userStore.isLogin())
  // if (isLogin) {
  //   //  更改后端用户语言
  //   const [err, res] = await to(
  //     setDefaultLanguage({
  //       lang_id: language.id,
  //       lang: language.file_name
  //     })
  //   )
  //   if (err) {
  //     //  后端默认语言失败
  //     toast.fail({ msg: err.msg })
  //     show.value = false
  //     return
  //   }
  // }
  //  更改store
  userStore.setLanguage(language)
  setDetault(language)
  //  更改前端语言
  i18n.global.locale = language.file_name
  location.reload()
  show.value = false
}
defineExpose({
  show
})
const setDetault = (language) => {
  languages.value.forEach((item) => {
    if (item.file_name === language.file_name) {
      item.is_default = 1
    } else {
      item.is_default = 0
    }
  })
}
const _getLanguages = () => {
  basicData
    .getLanguages()
    .then((res) => {
      languages.value = res
      //  如果本地存储了语言,则默认语言以本地为准
      if (lang) {
        languages.value.forEach((item) => {
          if (item.file_name === lang.value.file_name) {
            item.is_default = 1
          } else {
            item.is_default = 0
          }
        })
      }
    })
    .catch((err) => err)
}
const handlerClickOverlay = () => {
  show.value = false
}
_getLanguages()
</script>
<style lang="scss" scoped></style>
