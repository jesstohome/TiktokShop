<template>
  <div class="product-img-container" @click="goDetail">
    <VanImage :src="url" />
  </div>
</template>
<script setup>
defineProps({
  url: {
    type: String,
    required: true
  }
})
const route = useRoute()
const router = useRouter()
const goDetail = () => {
  if (route.name === 'Detail') {
    return
  }
  router.push({ path: '/detail' })
}
</script>
<style lang="scss" scoped>
.product-img-container {
  background: #f6f8fe;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}
</style>
