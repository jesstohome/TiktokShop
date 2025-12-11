<template>
  <div class="menu-container">
    <div class="menu-side">
      <icon-park :name="menu.iconName" class="ml-2" size="1.8rem" style="padding-right: 0.8rem" />
      {{ $t(menu.name) }}
    </div>
    <div class="menu-side">
      <span style="padding-right: 0.8rem">{{ menu.rightName }}</span>
      <van-icon color="#191919" name="arrow" size="1.4rem" />
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  menu: {
    type: Object,
    required: true
  }
})
</script>
<style lang="scss" scoped>
.menu-container {
  padding: 1rem 0;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e5e5e5;
  font-weight: 400;
  font-size: 1rem;
  color: #303030;
  line-height: 1.2rem;

  .menu-side {
    display: flex;
    flex-direction: row;
    align-items: center;
  }
}
</style>
