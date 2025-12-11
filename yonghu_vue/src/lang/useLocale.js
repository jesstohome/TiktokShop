import { useI18n } from 'vue-i18n'

export function usei18n() {
  const { locale } = useI18n()
  // 定义一个函数来改变locale
  function changeLocale(newLocale) {
    locale.value = newLocale
  }

  return { changeLocale }
}

export default useLocale
