<template>
  <van-action-sheet v-model:show="show"> </van-action-sheet>
</template>
<script setup>
defineProps({
  title: {
    type: String,
    required: false,
    default: '请选择'
  }
})
const show = ref(false)
defineExpose({ show })
</script>
<style lang="scss" scoped></style>
