<template>
  <nav-bar :title="$t('myOrders')" />
  <div class="container">
    <div class="tabs">
      <van-tabs v-model:active="tabActive" @change="statusChange">
        <van-tab v-for="item in tabs" :key="item.value" :name="item.value" :title="item.name" />
      </van-tabs>
    </div>
    <refresh-list class="content" :data="refreshData" @refresh="onRefresh" @load="onLoad">
      <van-swipe-cell
        v-if="data.length"
        class="order-container"
        v-for="item in data"
        :key="item.order_id"
      >
        <div class="bg-white mx-3 mt-3 rounded-md back_3 back_4" @click="goDetail(item.order_id)">
          <div class="flex items-center pt-1.5">
            <van-icon class="mx-3" name="ellipsis" size="24" />
            <span class="ml-auto mr-3">{{ item.status_text }}</span>
          </div>
          <div class="flex px-3">
            <div>
              <van-image
                :src="item.product[0].image"
                height="100"
                lazy-load
                radius="10px"
                width="100"
              >
                <template v-slot:loading>
                  <van-loading size="20" type="spinner" />
                </template>
              </van-image>
            </div>
            <div class="ml-3 mt-3 flex-auto">
              <div class="w-60">
                <span class="van-ellipsis" style="display: block">
                  {{ item.product[0].title }}
                </span>
              </div>
              <div>
                <span class="text-neutral-500 text-sm"> {{ $t('orderNo') }}: </span>
                <span class="font-semibold">
                  {{ item.order_sn }}
                </span>
              </div>
              <div class="flex">
                <div class="text-right py-1">
                  <span class="text-neutral-500 text-sm">{{ $t('quantity') }}</span>
                  <span class="text-lg font-semibold pl-3">{{ item.total_num }}</span>
                </div>
                <div class="text-right py-1 ml-auto">
                  <span class="text-neutral-500 text-sm">{{ $t('total') }}</span>
                  <span class="text-lg font-semibold pl-3">{{ item.total_price }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <template #right>
          <div class="order-item-del" @click="handlerDel(item)">{{ $t('delete') }}</div>
        </template>
      </van-swipe-cell>
      <van-empty v-else :description="$t('noRecord')"> </van-empty>
    </refresh-list>
  </div>
</template>
<script setup>
import NavBar from '@/components/CustomNavBar/index.vue'
import RefreshList from '@/components/RefreshList/index.vue'
import { orderList as list } from '@/api/user.js'
import { deleteOrder } from '@/api/order.js'
import toast from '@/utils/toast.js'
import { order_statuses } from '@/utils/constants.js'
const { proxy } = getCurrentInstance()
const tabActive = ref('all')
const tabs = ref(order_statuses)
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
  data.value = res.data.list.map((item) => {
    if (item.refund_status > 0) {
      item.status_text = item.refund_status === 1 ? proxy.t('drawBacking') : proxy.t('refunded')
    } else {
      item.status_text = tabs.value.find((s) => s.value === item.status + '').name
    }
    return item
  })
  refreshData.value.disabled = false
  toast.close()
}
const statusChange = (val) => {
  queryParams.value.status = val
  queryParams.value.page = 1
  handleQuery()
}
const router = useRouter()
const route = useRoute()
const goDetail = (id) => {
  router.push({ name: 'OrderInfo', params: { id } })
}
const handlerDel = ({ order_id }) => {
  toast.loading()
  deleteOrder({ order_id })
    .then((res) => {
      console.log(res)
    })
    .catch((err) => err)
}
onMounted(() => {
  if (route.params.status) {
    tabActive.value = route.params.status
    queryParams.value.status = route.params.status
  }
  handleQuery()
})
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
    .order-container {
      .order-item-del {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        background: #fe4857;
        font-weight: 600;
        font-size: 1.2rem;
        color: #ffffff;
        line-height: 1.4rem;
        letter-spacing: 2px;
        height: 100%;
        width: 6rem;
      }
    }
  }
}
</style>
