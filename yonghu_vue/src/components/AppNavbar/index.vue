<template>
  <van-nav-bar class="py-3" :title="title" @click-left="onClickLeft" border="false" fixed>
    <template #left>
      <slot name="left"></slot>
    </template>
    <template #right>
      <slot name="right"></slot>
    </template>
  </van-nav-bar>
  <div style="height: 1rem"></div>
</template>

<script setup>
const { title } = defineProps({
  title: String
})
// 返回上级
const onClickLeft = () => history.back()
</script>

<style scoped></style>
