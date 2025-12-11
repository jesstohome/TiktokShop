<template>
  <div class="list-menus-container">
    <menu-item 
      v-for="item in menus" :key="item.name" 
      :menu="item" @click="hanlderClick(item)" 
    />
  </div>
</template>
<script setup>
import MenuItem from './MenuItem.vue'
const props = defineProps({
  menus: {
    type: Array,
    required: true
  }
})
const emit = defineEmits(['click'])
const hanlderClick = (menu) => {
  emit('click', menu)
}
</script>
<style lang="scss" scoped>
.list-menus-container {
  background: #ffffff;
  padding: 0 0.6rem 1.1rem 0.6rem;
  border-radius: 0.6rem;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}
</style>
