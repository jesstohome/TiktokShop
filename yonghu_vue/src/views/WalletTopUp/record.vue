<template>
  <nav-bar :title="$t('rechargeRecord')" />
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
        class="bg-white mx-3 mt-3 h-30 pt-3 pb-3 rounded-md flex"
        v-for="item in data"
        :key="item.id"
      >
        <div class="flex justify-center items-center mx-3">
          <icon-park name="finance" size="2.5rem" />
        </div>
        <div class="flex-auto flex flex-col justify-center">
          <span class="text-base font-normal text-gray-900">
            {{ item.network }}
          </span>
          <span class="text-sm opacity-80">
            {{ item.createtime }}
          </span>
          <span class="text-sm opacity-80"> {{ $t('auditResult') }}: {{ item.admin_msg }} </span>
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
            {{ item.price }}
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
import { rechargeRecord as list } from '@/api/user.js'
import toast from '@/utils/toast.js'
import { fund_record_statuses } from '@/utils/constants.js'
const tabActive = ref('all')
const tabs = ref(fund_record_statuses)
const data = ref([])
const count = ref(0)
const queryParams = ref({
  page: 1,
  limit: 10,
  status: 'all'
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
  toast.loading()
  const res = await listData()
  data.value = res.data.list
  refreshData.value.disabled = false
  toast.close()
}
const statusChange = (val) => {
  queryParams.value.status = val
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
  .content {
    padding: 0;
    overflow-y: auto;
    height: calc(100dvh - 50px);
    .text-green {
      color: #00b557;
    }
    .text-red {
      color: #fe4857;
    }
  }
}
</style>
