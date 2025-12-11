<template>
  <div class="list-tile-container">
    <div class="list-tile-left">
      <slot name="left">
        <div class="title">{{ title }}</div>
        <div v-if="label" class="label">{{ label }}</div>
      </slot>
    </div>
    <div class="list-tile-right">
      <div class="value">{{ value }}</div>
      <icon-park v-if="arrow" :name="arrow" size="2rem" />
    </div>
  </div>
</template>
<script setup>
defineProps({
  title: {
    type: String,
    required: false,
    default: ''
  },
  label: {
    type: String,
    required: false,
    default: undefined
  },
  value: {
    type: String,
    required: false,
    default: undefined
  },
  arrow: {
    type: [String, Boolean],
    required: false,
    default: 'right'
  }
})
</script>
<style lang="scss" scoped>
.list-tile-container {
  margin-top: 0.5rem;
  background: #ffffff;
  box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.05);
  border-radius: 1rem;
  padding: 0.85rem 1.2rem;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  .list-tile-left {
    display: flex;
    flex-direction: column;
    .title {
      font-size: 1rem;
      font-weight: 400;
      color: #191919;
      line-height: 1.2rem;
    }
    .label {
      padding-top: 0.2rem;
      width: 16rem;
      font-weight: 400;
      font-size: 1rem;
      color: #757575;
      line-height: 1.2rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }
  .list-tile-right {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    align-items: center;
    .value {
      font-size: 1rem;
      font-weight: 400;
      color: #191919;
      line-height: 1.2rem;
    }
  }
}
</style>
