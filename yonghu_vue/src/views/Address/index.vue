<template>
  <div class="container">
    <nav-bar :title="$t('shippingAddress')" />
    <refresh-list class="content" :data="refreshData" @refresh="onRefresh" @load="onLoad">
      <van-space v-if="data.length" fill direction="vertical" size="0.6rem">
        <van-swipe-cell class="address-container" v-for="item in data" :key="item.address_id">
          <address-card :address="item" @click="handleAddressClick(item)" @edit="handleEdit">
            <template v-if="isChoose" #icon>
              <icon-park name="local" size="2rem" />
            </template>
          </address-card>
          <template #right>
            <div class="cart-item-del" @click="handlerDel(item)">{{ $t('delete') }}</div>
          </template>
        </van-swipe-cell>
      </van-space>
    </refresh-list>
    <div class="bottom">
      <van-button block round color="#191919" @click="handlerAdd">{{
        $t('addShippingAddress')
      }}</van-button>
    </div>
  </div>
</template>
<script setup name="Address">
import NavBar from '@/components/CustomNavBar/index.vue'
import RefreshList from '@/components/RefreshList/index.vue'
import AddressCard from './components/AddressCard.vue'
import { addressList as list, setDefaultAddress as setDefault, delAddress } from '@/api/user.js'
import toast from '@/utils/toast.js'
import { showConfirmDialog } from 'vant'
import useUserStore from '@/stores/modules/user.js'
const { proxy } = getCurrentInstance()
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
  toast.loading()
  const res = await listData()
  data.value = res.data.list
  refreshData.value.disabled = false
  toast.close()
}
const handleAddressClick = (address) => {
  if (isChoose.value) {
    userStore.setBackData({ address })
    router.back()
    return
  }
  if (address.is_default === 1) {
    return
  }
  showConfirmDialog({
    title: proxy.t('operateConfirm'),
    message: proxy.t('weigherToSetDefaultAddress') + '?',
    cancelButtonText: proxy.t('cancel'),
    confirmButtonText: proxy.t('confirm')
  })
    .then(() => {
      setDefaultAddress(address)
    })
    .catch(() => {})
}
const setDefaultAddress = (address) => {
  toast.loading()
  setDefault({ address_id: address.address_id })
    .then((res) => {
      toast.success({ msg: proxy.t('settingSuccess') })
      userStore.userInfo.address = address
      handleQuery()
    })
    .catch((err) => err)
}
const route = useRoute()
const router = useRouter()
const handleEdit = (address) => {
  router.push({ name: 'AddressForm', params: { id: address.address_id } })
}
const handlerAdd = () => {
  router.push({ name: 'AddressForm' })
}
const handlerDel = ({ address_id }) => {
  toast.loading()
  delAddress({ address_id: address_id })
    .then((res) => {
      toast.success({ msg: proxy.t('deleteSuccess') })
      handleQuery()
    })
    .catch((err) => err)
}
const isChoose = computed(() => {
  return route.query.type === 'choosen'
})
onMounted(handleQuery)
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;
  .content {
    height: calc(100dvh - 120px);
    overflow-y: auto;
    .address-container {
      .cart-item-del {
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
