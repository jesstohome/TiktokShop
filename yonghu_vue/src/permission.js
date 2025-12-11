import router from './router'
import NProgress from 'nprogress'
import 'nprogress/nprogress.css'
import toast from '@/utils/toast.js'
import { isRelogin } from '@/utils/request.js'
import useUserStore from '@/stores/modules/user.js'
import { isContainWhite } from '@/utils/tool.js'
import i18n from '@/lang/index.js'
NProgress.configure({ showSpinner: false })

const whiteList = [
  '/login',
  '/register',
  '/home',
  '/shop',
  '/explore',
  '/detail',
  '/search',
  '/forgotPassword',
  '/changePassword'
]

router.beforeEach((to, from, next) => {
  NProgress.start()
  const token = useUserStore().token
  if (token) {
    if (to.path === '/login') {
      next({ path: '/' })
      NProgress.done()
    } else {
      if (useUserStore().userInfo === null) {
        isRelogin.show = true
        // 判断当前用户是否已拉取完user_info信息
        useUserStore()
          .getInfo()
          .then(() => {
            isRelogin.show = false
            next({ ...to, replace: true })
          })
          .catch((err) => {
            useUserStore()
              .logOut()
              .then(() => {
                next({ path: '/login' })
              })
              .catch((err) => {
                useUserStore()
                  .invalidToken()
                  .then(() => {
                    next({ path: '/' })
                  })
              })
          })
      } else {
        next()
      }
    }
  } else {
    // 没有token
    if (whiteList.indexOf(to.path) !== -1 || isContainWhite(whiteList, to.path)) {
      // 在免登录白名单，直接进入
      next()
    } else {
      toast.show({
        msg: i18n.global.t('loginFirst')
      })
      // next(`/login?redirect=${to.fullPath}`) // 否则全部重定向到登录页
      next('/login')
      NProgress.done()
    }
  }
})

router.afterEach(() => {
  NProgress.done()
})
