<script setup>
import {useRouter, useRoute} from 'vue-router'
import {orderManagement} from "@/api/index.js";
import {useUserStore} from '@/store/modules/user.js';

const router = useRouter();
import {useI18n} from 'vue-i18n';
//多语言
const {t} = useI18n();
//仓库中获取商户信息请求函数
const userStore = useUserStore();
const active = ref(0);

const route = useRoute();

const list = [
  {
    text: t("tabbar.home"),
    icon_1: new URL('@/assets/image/tabbar/未选中/首页.png', import.meta.url).href,
    icon_2: new URL('@/assets/image/tabbar/选中/首页.png', import.meta.url).href,
    path: '/home'
  },
  {
    text: t("tabbar.product"),
    icon_1: new URL('@/assets/image/tabbar/未选中/产品.png', import.meta.url).href,
    icon_2: new URL('@/assets/image/tabbar/选中/产品.png', import.meta.url).href,
    path: '/product'
  },
  {
    text: t("router.service"),
    icon_1: new URL('@/assets/image/home1/创业联盟.png', import.meta.url).href,
    icon_2: new URL('@/assets/image/tabbar/选中/我的.png', import.meta.url).href,
    path: '/services'
  },
  {
    text: t("tabbar.order"),
    icon_1: new URL('@/assets/image/tabbar/未选中/订单.png', import.meta.url).href,
    icon_2: new URL('@/assets/image/tabbar/选中/订单.png', import.meta.url).href,
    path: '/order',
  },
  {
    text: t("tabbar.my"),
    icon_1: new URL('@/assets/image/tabbar/未选中/我的.png', import.meta.url).href,
    icon_2: new URL('@/assets/image/tabbar/选中/我的.png', import.meta.url).href,
    path: '/my'
  }
];
list.forEach((item, index) => {
  if (item.path === route.path) {
    active.value = index;
  }
});
userStore.ordertotal()
//定时获取订单列表未提货的总数
const intervalId = setInterval(() => {
  userStore.ordertotal()
}, 10000); //
userStore.msgtotal()
//定时获取消息的总数
const intervalIds = setInterval(() => {
  userStore.msgtotal()
}, 5000); //
onBeforeUnmount(() => {
  // 在组件销毁之前清除定时器
  clearInterval(intervalId);
  clearInterval(intervalIds);
})



</script>

<template>
  <!--<div class="h-20"></div>-->
  <footer class="grid grid-flow-row grid-cols-3 footer ">
    <van-tabbar class="pt-3" v-model="active">
      <van-tabbar-item replace :to="item.path" v-for="(item, index) in list" :key="index">
        <template #icon="{ active }">
          <div class="!flex !flex-col !justify-center">
			  <van-badge :content="userStore.msg_total" v-if="item.text===$t('router.service')">
			    <!--              <div class="child" />-->
			  </van-badge>
            <van-badge :content="userStore.total" v-if="item.text===$t('tabbar.order')">
              <!--              <div class="child" />-->
            </van-badge>
            <img :src="active ? item.icon_1 : item.icon_2"/>
            <span class="text-center mt-2">{{ item.text }}</span>
          </div>
        </template>
      </van-tabbar-item>
    </van-tabbar>
  </footer>
</template>

<style scoped lang="scss">
.van-tabbar-item {
  img {
    margin: 0 auto;
  }
}

.van-tabbar-item--active {
  color: black !important;
}

.van-tabbar-item {
  color: #999999
}

:deep(.van-tabbar-item__icon .van-badge) {
  position: absolute!important;
}


</style>