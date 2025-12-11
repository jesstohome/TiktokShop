<script setup>
const router = useRouter();
import {showSuccessToast} from "vant";
import { useI18n } from 'vue-i18n';
const onClickLeft = () => {
  router.back();
};
const list = [
  { name: t("bankCardRecharge.name") },
  { name: t("bankCardRecharge.card") },
  { name: t("bankCardRecharge.bankname") }
]
</script>

<template>
<!--  <NavBar title="充值" />-->
  <header>
    <van-nav-bar
        :title="$t('bankCardRecharge.Recharge')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft">
      <template #right>
        <span @click="router.push('/rechargeRecord')" class="text-white">{{ $t("bankCardRecharge.rechargeRecord") }}</span>
      </template>
    </van-nav-bar>
  </header>
  <main class="mx-3">
    <h5 class="mt-3 text-base font-semibold">{{ $t("bankCardRecharge.rechargeMethod") }}</h5>
    <div v-for="(item,index) in list" :key="index" class="bg-white rounded-md mt-3 flex items-center py-3">
      <div class="mx-3">
        <span class="font-normal">{{ item.name }}</span>
      </div>
    </div>
    <h5 class="mt-6 text-base font-semibold">{{ $t("bankCardRecharge.rechargeMoney") }}</h5>
    <input class="bg-white w-full rounded-md mt-3 flex items-center py-3 px-3" :placeholder="$t('bankCardRecharge.inputrechargeMoney')" />
    <div class="flex mt-3">
      <div>
        <icon-park name="info" size="1.4rem" />
      </div>
      <div class="leading-">
        <span class="opacity-80">{{ $t("bankCardRecharge.title") }}</span>
      </div>
    </div>
    <div class="pt-10">
      <van-button round block color="#191919">{{ $t("bankCardRecharge.submit") }}</van-button>
    </div>
  </main>

</template>

<style lang="scss" scoped>

</style>