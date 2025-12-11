<template>
  <div class="container">
    <nav-bar :title="$t('setting')" />
    <div class="top">
      <list-menus :menus="menus" @click="handlerMenuClick" />
    </div>
    <div class="bottom">
      <van-button @click="handlerCancel" block round color="#191919"> {{ $t('quit') }} </van-button>
    </div>
  </div>
</template>
<script setup>
import ListMenus from '@/components/ListMenus/index.vue'
import useUserStore from '@/stores/modules/user.js'
import { showConfirmDialog } from 'vant'
import toast from '@/utils/toast.js'
const { proxy } = getCurrentInstance()
const menus = ref([
  // {
  //   name: '忘记密码',
  //   iconName: 'setting-two',
  //   routeName: 'ForgotPassword'
  // },
  {
    name: proxy.t('changePassword'),
    iconName: 'shield',
    routeName: 'Passwords'
  },
  {
    name: proxy.t('helpAndSupport'),
    iconName: 'help',
    routeName: 'HelpAndSupport'
  },
  {
    name: proxy.t('customerService'),
    iconName: 'headset-one',
    routeName: 'Services'
  }
])
const router = useRouter()
const userStore = useUserStore()
const handlerMenuClick = (menu) => {
  if (menu.routeName) {
    router.push({ name: menu.routeName })
    return
  }
}
const handlerCancel = () => {
  showConfirmDialog({
    title: proxy.t('quit'),
    message: proxy.t('isSureToQuit'),
    cancelButtonText: proxy.t('cancel'),
    confirmButtonText: proxy.t('confirm')
  })
    .then(() => {
      toast.loading()
      userStore
        .logOut()
        .then(() => {
          router.push({ name: 'Login' })
        })
        .finally(() => {
          toast.close()
        })
    })
    .catch(() => {})
}
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  position: relative;
  .top {
    padding: 1rem 1rem 0 1rem;
  }
  .bottom {
    padding: 1rem;
    width: 100%;
    position: fixed;
    bottom: 0;
  }
}
</style>
