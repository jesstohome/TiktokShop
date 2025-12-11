<template>
  <div class="container">
    <nav-bar :title="$t('allProducts')" />
    <div class="top">
      <div class="filter-row">
        <custom-input style="flex: 1" :value="queryParams.title" @blur="blurTitleInput">
          <template #left>
            <icon-park name="search" size="1.8rem" />
          </template>
          <template #right>
            <icon-park name="close" size="1.6rem" @click.stop="clearSearch" />
          </template>
        </custom-input>
      </div>
      <div class="tag-row">
        <van-space size="1rem">
          <div
            class="tag"
            v-for="item in keys"
            :key="item.keyword"
            :class="item.keyword === selectKey?.keyword ? 'selected-tag' : ''"
            @click.stop="handlerTagSelect(item)"
          >
            {{ item.keyword }}
          </div>
        </van-space>
      </div>
    </div>
    <refresh-list
      class="content"
      v-if="data.length"
      :data="refreshData"
      @refresh="onRefresh"
      @load="onLoad"
    >
      <van-space direction="vertical" size="0.9rem">
        <product-card
          v-for="(item, index) in data"
          :key="index"
          :product="item"
          @click="goDetail(item)"
        />
      </van-space>
    </refresh-list>
    <div v-else>
      <van-image :src="empty" />
    </div>
  </div>
</template>
<script setup>
import throttle from 'lodash/throttle'
import NavBar from '@/components/CustomNavBar/index.vue'
import empty from '@/assets/image/empty.png'
import CustomInput from '@/components/Input/index.vue'
import RefreshList from '@/components/RefreshList/index.vue'
import ProductCard from '@/components/ProductCard/horiz.vue'
import { topTenKey, search as list } from '@/api/product.js'
import toast from '@/utils/toast.js'
import { useRouter } from 'vue-router'
const keys = ref([])
const queryParams = ref({
  page: 1,
  limit: 10,
  title: undefined,
  category_id: undefined
})
const selectKey = ref(null)
const blurTitleInput = throttle(
  (val) => {
    selectKey.value = null
    queryParams.value.page = 1
    queryParams.value.title = val
    handleQuery()
  },
  1000,
  { trailing: true }
)
const clearSearch = () => {
  queryParams.value.page = 1
  queryParams.value.title = null
  selectKey.value = null
  // handleQuery()
}

const handlerTagSelect = (key) => {
  selectKey.value = key
  queryParams.value.title = key.keyword
  queryParams.value.page = 1
  handleQuery()
}
const refreshData = ref({
  loading: false,
  listLoading: false,
  finished: false,
  disabled: true
})
const data = ref([])
const total = ref(0)
const listData = async () => {
  const res = await list(queryParams.value)
  total.value = res.data.total
  if (res.data.list.length < queryParams.value.limit) {
    refreshData.value.finished = true
  }else{
    refreshData.value.finished = false
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
const router = useRouter()
const goDetail = (product) => {
  router.push({
    path: `/detail/${product.product_id}/${product.mer_id}`
  })
}
const init = async () => {
  toast.loading({ msg: '加载中...' })
  Promise.all([
    topTenKey()
      .then((res) => res)
      .catch((err) => err),
    list(queryParams.value)
      .then((res) => res)
      .catch((err) => err)
  ])
    .then((responses) => {
      keys.value = responses[0].data
      total.value = responses[1].data.total
      if (responses[1].data.list.length < queryParams.value.limit) {
        refreshData.value.finished = true
      }
      data.value = responses[1].data.list
      refreshData.value.disabled = false
    })
    .finally(() => {
      toast.close()
    })
}
init()
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  .top {
    padding: 0 1rem 0 1rem;
    height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: stretch;
    .filter-row {
      padding-top: 1rem;
      display: flex;
      flex-direction: row;
      align-items: center;
      .clear-filter {
        padding-left: 0.8rem;
        font-weight: 600;
        font-size: 1.2rem;
        line-height: 1.4rem;
      }
    }
    .tag-row {
      overflow-x: auto;
      display: flex;
      flex-direction: row;
      flex-wrap: wrap;
      align-items: center;
      overflow-x: auto;
      .tag {
        white-space: nowrap;
        background: #ffffff;
        color: #a7a7a7;
        font-weight: 600;
        font-size: 1rem;
        line-height: 15px;
        padding: 0.6rem 1.4rem;
        border-radius: 1.6rem;
        letter-spacing: 2px;
      }
      .selected-tag {
        background: #191919;
        color: #ffffff;
      }
    }
  }
  .content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    overflow-y: auto;
    height: calc(100dvh - 120px);
  }
}
</style>
