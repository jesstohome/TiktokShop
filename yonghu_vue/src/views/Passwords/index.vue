<template>
  <nav-bar :title="$t('changePassword')" />
  <div class="container">
    <list-tile @click="goRoute('ChangePassword')" :title="$t('loginPassword')" />
    <div style="height: 1rem" />
    <list-tile @click="goRoute('ChangePayPwd')" :title="$t('paymentCode')" />
  </div>
</template>
<script setup>
import ListTile from '@/components/ListTile/index.vue'
const router = useRouter()
const goRoute = (routeName) => {
  router.push({ name: routeName })
}
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 1rem;
}
</style>
