<script setup>
// import NavBar from '@/components/CustomNavBar/index.vue'
import {useUserStore} from "@/store/modules/user.js";
import {useI18n} from 'vue-i18n';
import {showFailToast} from "vant";
//多语言
const {t} = useI18n();
const router = useRouter();
const userStore = useUserStore();
const onClickLeft = () => {
  router.back();
};

const list = [
  {path: '/walletRecharge', name: t("recharge.chainDeposit"), icon: 'bitcoin'},
  // { path: 'bankCardRecharge', name: '银行卡', icon: 'bank-card' },
  {path: '/service', name: t("recharge.manualDeposit"), icon: 'headset-one'}
]
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('recharge.deposit')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft">
      <template #right>
        <span @click="router.push('/rechargeRecord')" class="text-white">{{ $t("recharge.depositHistory") }}</span>
      </template>
    </van-nav-bar>
  </header>
  <!--  <NavBar title="充值" />-->
  <main class="mx-3">
    <div class="flex justify-center items-center mt-10">
      <img alt="" class="w-3/4" src="@/assets/icons/yan.png">
    </div>
    <h5 class="mt-3 text-base font-semibold">{{ $t("recharge.depositMethod") }}</h5>
    <div v-for="(item,index) in list" :key="index" class="bg-white rounded-md mt-3 flex items-center py-3"
         @click="userStore.MerInfo.status===1?router.push(item.path):showFailToast($t('over'))">
      <div class="mx-3">
        <icon-park :name="item.icon" size="1.8rem"/>
      </div>
      <div>
        <span>{{ item.name }}</span>
      </div>
    </div>
  </main>

</template>

<style lang="scss" scoped>

</style>