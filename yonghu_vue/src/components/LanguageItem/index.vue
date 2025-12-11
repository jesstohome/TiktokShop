<template>
  <div class="language-container">
    <div class="left">
      {{ language.language_name }}
      <div class="chn">{{ language.file_name }}</div>
    </div>
    <div v-if="language.is_default === 1" class="right">
      <icon-park name="check" size="1.6rem" />
    </div>
  </div>
</template>
<script setup>
defineProps({
  language: {
    type: Object,
    required: true
  }
})
</script>
<style lang="scss" scoped>
.language-container {
  background: #ffffff;
  border-bottom: 1px solid #e5e5e5;
  padding: 0.72rem;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  .left {
    font-size: 1rem;
    font-weight: 500;
    color: #191919;
    line-height: 1.4rem;
    .chn {
      font-size: 0.8rem;
      font-weight: 400;
      line-height: 1.4rem;
    }
  }
}
</style>
