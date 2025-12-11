<script setup>
const router = useRouter();

const onClickLeft = () => {
  router.back();
};
</script>

<template>
  <header>
    <van-nav-bar
        title="充值"
        left-text="返回"
        left-arrow
        @click-left="onClickLeft">
      <template #right>
        <span @click="router.push('/rechargeRecord')" class="text-white">充值记录</span>
      </template>
    </van-nav-bar>
  </header>
  <main>
    <img src="@/assets/image/name-40e02de4.png" alt="">
    <div class="flex justify-between items-center py-3 mx-3 border-b border-slate-50 mt-3" v-for="item in 5"
         @click="router.push('/blockchainRecharge')">
      <div class="flex justify-center items-center">
        <van-icon size="24px" name="gem"/>
        <span class="ml-3">银行卡充值</span>
      </div>
      <div>
        <van-icon name="arrow"/>
      </div>
    </div>
  </main>

</template>

<style scoped lang="scss">

</style>