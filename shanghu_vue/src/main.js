import {createApp} from 'vue';
import App from './App.vue';
import './style.css';
import "@/assets/style/mian.css";
import router from './router';
import store from './store';
import {Lazyload} from 'vant';
import i18n from '@/lang/index'
import '@/assets/icons/svg.js'
import '@/assets/style/theme.css';
import IconPark from '@/components/IconPark/index.vue'


const app = createApp(App);
app.config.globalProperties.$base_url = import.meta.env.VITE_APP_BASE_URL;
app.use(router);
app.use(Lazyload);
app.use(store);
app.use(i18n)
app.component('IconPark',IconPark)
app.mount('#app');
