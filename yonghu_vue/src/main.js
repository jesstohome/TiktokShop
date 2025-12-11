import { createApp } from 'vue'

import App from './App.vue'

import './style.css' //清除全局默认样式
import '@/assets/style/main.css' //tailwindcss入口文件
import '@/assets/style/gj.scss'
import '@/assets/style/iconfont.css'

import '@/assets/icons/svg.js'

import router from './router'

//  router controll
import './permission.js'

import { Lazyload } from 'vant'

// store
import store from './stores'

import NavBar from '@/components/CustomNavBar/index.vue'
import IconPark from '@/components/IconPark/index.vue'
import RefreshList from '@/components/RefreshList/index.vue'

// vant toast dialog

import 'vant/es/toast/style'
import 'vant/es/dialog/style'
import { Toast, Dialog } from 'vant'

import i18n from '@/lang/index.js'

const app = createApp(App)

//  全局方法
app.config.globalProperties.t = i18n.global.t

app.component('IconPark', IconPark)
app.component('NavBar', NavBar)
app.component('RefreshList', RefreshList)

app.use(router)
app.use(Lazyload)
app.use(store)

app.use(Toast)
app.use(Dialog)
app.use(i18n)

app.mount('#app')
