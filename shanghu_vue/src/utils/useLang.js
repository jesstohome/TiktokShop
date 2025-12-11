// // 控制语言
// import {ref, watchEffect} from 'vue';
// import {useI18n} from 'vue-i18n';
//
// export function useLang() {
//     const {locale, setLocale} = useI18n(); // 在setup函数内部调用useI18n
//
//     const Lang = ref('en');
//
//     watchEffect(() => {
//         locale.value = Lang.value;
//         localStorage.setItem('lang', Lang.value);
//     });
//
//     return {
//         Lang,
//     };
// }
