<template>
  <van-floating-panel v-if="show" :height="height">
    <div class="floating-panel-container">
      <div v-if="title" class="title-row">
        {{ title }}
      </div>
      <div v-if="tip" class="tip-row">
        {{ tip }}
      </div>
      <slot />
    </div>
  </van-floating-panel>
  <van-overlay :show="show" @click="handlerClickOverlay" />
</template>
<script setup>
defineProps({
  height: {
    type: [Number, String],
    required: false,
    default: 500
  },
  title: {
    type: String,
    required: false,
    default: undefined
  },
  tip: {
    typeof: String,
    required: false,
    default: undefined
  }
})
const show = ref(false)
const handlerClickOverlay = () => {
  show.value = false
}
defineExpose({ show })
</script>
<style lang="scss" scoped>
.floating-panel-container {
  padding: 0 1rem 1rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  .title-row {
    padding: 1rem;
    text-align: center;
    font-weight: 500;
    font-size: 1.2rem;
    color: #191919;
    line-height: 1rem;
  }
  .tip-row {
    padding: 1rem 0;
    display: flex;
    flex-direction: row;
    align-items: center;
    font-weight: 400;
    font-size: 0.8rem;
    color: #191919;
    line-height: 1rem;
  }
}
</style>
