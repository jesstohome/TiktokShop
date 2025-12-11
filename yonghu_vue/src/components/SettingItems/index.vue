<template>
  <div class="setting-items-container">
    <div class="setting-title">{{ title }}</div>
    <div class="setting-items">
      <Setting-Item
        v-for="(item, index) in items"
        :key="item"
        :name="item.name"
        :border="index !== items.length - 1"
        @click="handlerSelect(item.name)"
      >
        <template v-if="item.name === selectedLanguage" #right>
          <van-button size="mini" round icon="success" color="#31452e" />
        </template>
      </Setting-Item>
    </div>
  </div>
</template>

<script setup>
import SettingItem from './item.vue'
defineProps({
  title: {
    type: String,
    required: false,
    default: 'setting-title'
  },
  type: {
    type: String,
    required: false,
    default: 'select'
  },
  items: {
    type: Array,
    required: false,
    default: () => []
  },
  selectedLanguage: {
    type: String,
    required: false,
    default: 'English(UK)'
  }
})
const emit = defineEmits('change')
const handlerSelect = (lan) => {
  emit('change', lan)
}
</script>
<style lang="scss" scoped>
.setting-items-container {
  margin-top: 24px;
  padding: 20px 16px 10px 16px;
  border: 1px solid rgba(227, 231, 236, 1);
  display: flex;
  flex-direction: column;
  .setting-title {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.06px;
    line-height: 20px;
    color: rgba(102, 112, 122, 1);
  }
  .setting-items {
    padding-top: 17px;
    .setting-item {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
  }
}
</style>
