<template>
  <div class="item-container" :style="styleObject">
    <div class="item-content">
      {{ name }}{{ border }}
      <slot name="right" />
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  border: {
    type: Boolean,
    required: false,
    default: true
  },
  name: {
    type: String,
    required: true
  }
})
const styleObject = reactive({
  'border-bottom-width': props.border ? '1px' : '0',
  'border-bottom-style': 'solid',
  'border-bottom-color': 'rgba(227, 231, 236, 1)'
})
</script>
<style lang="scss" scoped>
.item-container {
  padding-top: 16px;
  display: flex;
  flex-direction: column;
  .item-content {
    padding: 0 13px 15px 13px;
    font-size: 16px;
    font-weight: 600;
    letter-spacing: 0.08px;
    line-height: 24px;
    color: rgba(17, 17, 17, 1);
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
}
</style>
