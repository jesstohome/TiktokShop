<template>
  <nav-bar :title="$t('fundRecord')" />
  <div class="container">
    <div class="tabs">
      <van-tabs v-model:active="tabActive" @change="statusChange">
        <van-tab v-for="item in tabs" :key="item.value" :name="item.value" :title="item.name" />
      </van-tabs>
    </div>
    <refresh-list class="content" :data="refreshData" @refresh="onRefresh" @load="onLoad">
      <div
        v-if="data.length"
        style="box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08)"
        class="bg-white mx-3 mt-3 h-20 rounded-md flex"
        v-for="item in data"
        :key="item.id"
      >
        <div class="flex justify-center items-center mx-3">
          <icon-park name="finance" size="2.5rem" />
        </div>
        <div class="flex-auto flex flex-col justify-center">
          <span class="text-base font-normal">
            {{ item.title }}
          </span>
          <span class="text-sm opacity-80">
            {{ item.createtime }}
          </span>
        </div>
        <div class="flex justify-center items-center mx-3">
          <icon-park
            :color="item.type === 'recharge' ? '#00B557' : '#fe4857'"
            name="plus"
            size="1.6rem"
          />
          <span
            class="text-base font-medium"
            :class="item.type === 'recharge' ? 'text-green' : 'text-red'"
          >
            {{ item.money }}
          </span>
        </div>
      </div>
      <van-empty v-else :description="$t('noRecord')"> </van-empty>
    </refresh-list>
  </div>
</template>
<script setup>
import NavBar from '@/components/CustomNavBar/index.vue'
import RefreshList from '@/components/RefreshList/index.vue'
import { billList as list } from '@/api/user.js'
import toast from '@/utils/toast.js'
const { proxy } = getCurrentInstance()
const tabActive = ref('all')
const tabs = ref([
  { value: 'all', name: proxy.t('all') },
  { value: 'recharge', name: proxy.t('recharge') },
  { value: 'extract', name: proxy.t('draw') }
])
const data = ref([])
const count = ref(0)
const queryParams = ref({
  page: 1,
  limit: 10,
  type: 'all'
})
const refreshData = ref({
  loading: false,
  listLoading: false,
  finished: false,
  disabled: true
})
const listData = async () => {
  const res = await list(queryParams.value)
  count.value = res.data.count
  if (res.data.list.length < queryParams.value.limit) {
    refreshData.value.finished = true
  }
  return res
}
const onRefresh = async () => {
  queryParams.value.page = 1
  refreshData.value.finished = false
  const res = await listData()
  data.value = res.data.list
  refreshData.value.loading = false
}
const onLoad = async () => {
  refreshData.value.listLoading = true
  queryParams.value.page += 1
  const res = await listData()
  data.value.push(...res.data.list)
  refreshData.value.listLoading = false
}
const handleQuery = async () => {
  toast.loading({ msg: '加载中...' })
  const res = await listData()
  data.value = res.data.list
  refreshData.value.disabled = false
  toast.close()
}
const statusChange = (val) => {
  queryParams.value.type = val
  queryParams.value.page = 1
  handleQuery()
}
handleQuery()
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  overflow-y: hidden;
  padding: 0;
  .tabs {
    height: 50px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
  }
  .content {
    padding: 0;
    overflow-y: auto;
    height: calc(100dvh - 100px);
    .text-green {
      color: #00b557;
    }
    .text-red {
      color: #fe4857;
    }
  }
}
</style>
