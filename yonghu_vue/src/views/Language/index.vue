<template>
  <div class="container">
    <nav-bar title="语言设置" />
    <refresh-list class="content" :data="refreshData" @refresh="onRefresh" @load="onLoad">
      <van-space fill direction="vertical" size="0.6rem">
        <div
          class="language-card"
          v-for="item in data"
          :key="item.id"
          @click.stop="handlerChoose(item)"
        >
          <div class="left">
            {{ item.chinese_name }}
            <div class="language-name">
              {{ item.language_name }}
              {{ item.file_name }}
            </div>
            <div></div>
          </div>
          <div class="right">
            <div class="custom-check" :class="item.is_default === 1 ? 'chosen' : ''">
              <icon-park v-if="item.is_default === 1" name="check" color="#ffffff" size="1.2rem" />
            </div>
          </div>
        </div>
      </van-space>
    </refresh-list>
  </div>
</template>
<script setup>
import NavBar from '@/components/CustomNavBar/index.vue'
import RefreshList from '@/components/RefreshList/index.vue'
import { languageList as list, setDefaultLanguage } from '@/api/user.js'
import toast from '@/utils/toast.js'
import useUserStore from '@/stores/modules/user.js'
const userStore = useUserStore()
const queryParams = ref({
  page: 1,
  limit: 10
})
const data = ref([])
const count = ref(0)
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
const handlerChoose = (item) => {
  if (item.is_default === 1) {
    return
  }
  toast.loading()
  setDefaultLanguage({ lang_id: item.id })
    .then((res) => {
      toast.success('设置成功')
      userStore.setLanguage(item)
      handleQuery()
    })
    .catch((err) => err)
}
onMounted(handleQuery)
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;
  .content {
    overflow-y: auto;
    height: calc(100dvh - 50px);
    .language-card {
      background: #ffffff;
      padding: 1rem 0.72rem 1rem 1.25rem;
      box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.05);
      border-radius: 0.32rem;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      .left {
        display: flex;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        font-size: 1.2rem;
        font-weight: 500;
        color: #191919;
        line-height: 1.4rem;
        .language-name {
          padding-top: 0.2rem;
          font-weight: 400;
          font-size: 1rem;
          color: #757575;
          line-height: 1.2rem;
        }
      }
      .right {
        padding-left: 1.2rem;
        .custom-check {
          background: #ffffff;
          width: 2rem;
          height: 2rem;
          border-radius: 1rem;
          border: 1px solid #191919;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
        }
        .chosen {
          background: #191919;
          border-width: 0;
        }
      }
    }
  }
}
</style>
