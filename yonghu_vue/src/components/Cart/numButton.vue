<template>
  <div class="num-button-container">
    <van-stepper v-model="_num" :min="0" />
  </div>
</template>
<script setup>
const props = defineProps({
  num: {
    type: Number,
    required: false,
    default: 0
  }
})
const emit = defineEmits(['changeNum'])
const _num = computed({
  get() {
    return props.num
  },
  set(val) {
    emit('changeNum', val)
  }
})
</script>
<style lang="scss" scoped>
.num-button-container {
  background: #f5f5f5;
  border-radius: 8px;
  padding: 6px;
  display: flex;
  flex-direction: row;
  align-items: center;
  ::v-deep(.van-stepper__minus) {
    background: #ffffff;
    border-radius: 12px;
  }
  ::v-deep(.van-stepper__plus) {
    background: #ffffff;
    border-radius: 12px;
  }
}
</style>
