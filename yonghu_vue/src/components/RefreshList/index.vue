<template>
  <van-pull-refresh v-model="_data.loading" @refresh="onRefresh">
    <van-list
      :loading="_data.listLoading"
      :finished="_data.finished"
      :disabled="_data.disabled"
      :loading-text="$t('refreshing')"
      :finished-text="$t('noMore')"
      :immediate-check="false"
      @load="loadList"
    >
      <slot />
    </van-list>
  </van-pull-refresh>
</template>
<script setup>
import { computed } from 'vue'
const props = defineProps({
  data: {
    type: Object,
    required: true,
    default: () => {
      return {
        loading: false,
        listLoading: false,
        finished: false,
        disabled: true
      }
    }
  }
})
const _data = computed(() => {
  return props.data
})
const emit = defineEmits(['refresh', 'load'])
//  下拉刷新
const onRefresh = () => {
  emit('refresh')
}
//  上拉加载
const loadList = () => {
  if (!props.data.finished && !props.data.loading) {
    emit('load')
  }
}
</script>
<style lang="scss" scoped></style>
