<template>
  <div class="container">
    <nav-bar :title="$t('lawAndPolicy')" />
    <div class="content" v-html="rickText" />
  </div>
</template>
<script setup>
import NavBar from '@/components/CustomNavBar/index.vue'
import { law } from '@/api/user.js'
import toast from '@/utils/toast.js'
const rickText = ref('')
const getData = () => {
  toast.loading({ msg: '加载中...' })
  law()
    .then((res) => {
      rickText.value = res.data
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
getData()
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;
  .content {
    overflow-y: auto;
    height: calc(100dvh - 50px);
  }
}
</style>
