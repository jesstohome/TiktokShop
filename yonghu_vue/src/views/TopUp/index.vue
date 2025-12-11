<script setup>
import NavBar from '@/components/CustomNavBar/index.vue'
const { proxy } = getCurrentInstance()
const list = [
  { path: '/walletTopUp', name: proxy.t('rechargeOnChain'), icon: 'bitcoin' },
  // { path: '/cardTopUp', name: '银行卡充值', icon: 'bank-card' },
  { path: '/service', name: proxy.t('manualRecharge'), icon: 'headset-one' }
]
const router = useRouter()
const goRoute = (item) => {
  router.push({ path: item.path })
}
const goRecord = () => {
  router.push({ name: 'TopUpRecord' })
}
</script>
<template>
  <nav-bar :title="$t('recharge')">
    <template #right>
      <div @click="goRecord" style="font-size: 1.2rem; padding-right: 1rem;">
        {{ $t('record') }}
      </div>
    </template>
  </nav-bar>
  <main class="mx-3">
    <div class="flex justify-center items-center mt-10">
      <img alt="" class="w-3/4" src="@/assets/icons/yan.png" />
    </div>
    <h5 class="mt-3 text-base font-semibold">{{ $t('rechargeMethod') }}</h5>
    <div
      v-for="(item, index) in list"
      :key="index"
      class="bg-white rounded-md mt-3 flex items-center py-3"
      @click="goRoute(item)"
    >
      <div class="mx-3">
        <icon-park :name="item.icon" size="1.8rem" />
      </div>
      <div>
        <span>{{ item.name }}</span>
      </div>
    </div>
  </main>
</template>
<style lang="scss" scoped></style>
