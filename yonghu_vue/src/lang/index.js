import { createI18n } from 'vue-i18n'
import { getLocalLang } from '@/utils/auth.js'

//  引入需要的语言包
import en from '@/lang/en.js'
import zh from '@/lang/zh.js'
import jap from '@/lang/jap.js'
import kor from '@/lang/kor.js'
import thai from '@/lang/thai.js'
import vn from '@/lang/vn.js'

//  系统默认语言
export const default_lang = {
  language_name: 'English',
  chinese_name: '英文',
  file_name: 'en'
}

// 本地语言环境获取
const lang = getLocalLang()?.file_name || default_lang.file_name || navigator.language

const i18n = createI18n({
  locale: lang, // 语言标识
  fallbackLocale: 'zh-CN', // 失败时默认语言
  silentTranslationWarn: true, // 设置为true 会屏蔽翻译在控制台报出的警告
  messages: {
    en: en,
    'zh-CN': zh,
    'ri':jap,
    'han':kor,
    'tai':thai,
	'vn':vn
  }
})

// 设置语言
// localeLib.i18n((key, value) => i18n.t(key, value));

export default i18n
