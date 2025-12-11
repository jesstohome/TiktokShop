<template>
  <div class="custom-nav-container">
    <div class="custom-nav-left">
      <icon-park name="left" size="2.5rem" v-if="canBack" @click="handleBack" />
      <slot v-else name="left">
        <div />
      </slot>
    </div>
    <slot>
      <div class="custom-nav-middle">
        <div class="custom-nav-title">{{ title }}</div>
      </div>
    </slot>
    <div class="custom-nav-right" @click="handlerRightClick">
      <slot name="right">
        <div />
      </slot>
    </div>
  </div>
</template>
<script setup>
defineProps({
  title: {
    type: String,
    required: false,
    default: '标题'
  },
  canBack: {
    type: Boolean,
    default: true
  }
})
const router = useRouter()
const handleBack = () => {
  router.back()
}
const emit = defineEmits(['rightClick'])
const handlerRightClick = () => {
  emit('rightClick')
}
</script>
<style lang="scss" scoped>
.custom-nav-container {
  height: 50px;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  position: relative;
  .custom-nav-left {
    display: flex;
    justify-content: flex-start;
    z-index: 1;
  }
  .custom-nav-middle {
    pointer-events: none;
    z-index: 999;
    position: absolute;
    width: 100%;
    font-weight: 700;
    letter-spacing: 0.09px;
    color: #191919;
    text-align: center;
    .custom-nav-title {
      font-size: 1.2rem;
      font-weight: 600;
      letter-spacing: 2px;
      color: #191919;
      text-align: center;
    }
  }
  .custom-nav-right {
    z-index: 1;
    display: flex;
    justify-content: flex-end;
    font-size: 1.2rem;
    font-weight: 500;
    letter-spacing: 2px;
    color: #191919;
  }
}
</style>
