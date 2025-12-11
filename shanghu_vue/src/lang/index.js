// index.ts
import {createI18n} from 'vue-i18n';
import zh from './zh';
import en from './en';
import han from './han';
import ri from './ri';
import tai from './tai';
import ft from './ft';
import vn from './vn';

const messages = {
    en,
    "zh-CN": zh,
    'zh-Hant': ft,
    han,
    ri,
    tai,
	vn
};
// const language = localStorage.getItem('lang') || 'zh' // 这是获取浏览器的语言
const i18n = createI18n({
    locale: localStorage.getItem('lang') || 'zh-CN', // 首先从缓存里拿，没有的话就用浏览器语言，
    // locale: language || 'zh', // 首先从缓存里拿，没有的话就用浏览器语言，
    fallbackLocale: 'zh-CN', // 设置备用语言
    messages,
    legacy: false,
    globalInjection: true,
    silentTranslationWarn: true,
});

// localStorage.setItem('lang', language);
export default i18n;
