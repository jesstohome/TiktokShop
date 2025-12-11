<template>
  <nav-bar :title="$t('partnership')" />
  <div class="container">
    <div class="logo-row">
      <van-image round width="8rem" height="8rem" :src="platform.logo" />
    </div>
    <div class="platform-name row">
      {{ platform.name }}
    </div>
    <div class="custom-row row">
      <van-button round block color="#191919" @click="router.push('/services')">{{
        $t('serviceLink')
      }}</van-button>
    </div>
    <div class="merchant-row row">
      <van-button round block color="#191919" @click="hanlderLocation(platform.merchant_link)">{{
        $t('platformSell')
      }}</van-button>
    </div>
  </div>
</template>
<script setup>
import { platformInfo } from '@/api/home.js'
import toast from '@/utils/toast.js'
import Navbar from '@/components/CustomNavBar/index.vue'
const router = useRouter();
const platform = ref({})
const hanlderLocation = (val) => {
  // console.log(val)
  window.location.href = val
}
const getPlatformInfo = () => {
  toast.loading({ msg: '加载中...' })
  platformInfo()
    .then((res) => {
      platform.value = res.data
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}

getPlatformInfo()
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  // align-items: center;
  .logo-row {
    text-align: center;
    padding-top: 3rem;
  }
  .row {
    text-align: center;
    padding-top: 2rem;
  }
  .platform-name {
    font-size: 2rem;
    font-weight: 600;
  }
  .custom-row {
    padding-top: 3rem;
  }
  .merchant-row {
  }
}
</style>
