<template>
  <div class="container">
    <nav-bar :title="$t('orderDetail')" />
    <div class="content">
      <div class="address content-item">
        {{ $t('shippingAddress') }}
        <list-tile :title="address?.name" :label="address?.label" @click.stop="showChooseAddress">
          <template v-if="!address" #left>
            <div class="choose-address">
              <icon-park name="plus" size="1.6rem" />
              <div class="choose-address-word">{{ $t('addAddress') }}</div>
            </div>
          </template>
        </list-tile>
      </div>
      <div class="info content-item">
        {{ $t('productDetail') }}
        <cart-item
          class="product"
          v-for="(item, index) in orderData.product"
          :key="index + 'product'"
          :product="item"
          :readonly="true"
          @change-num="changeNum(item)"
        >
        </cart-item>
      </div>
      <div class="promotion content-item">
        <custom-input
          :value="promotion"
          @blur="(val) => (promotion = val)"
          :placeholder="$t('inputYourPromoCode')"
        />
      </div>
      <div class="content-item">
        <list-tile :title="$t('subTotal')" :arrow="false" :value="total + ''" />
      </div>
      <div class="content-item">
        <list-tile :title="$t('freight')" :arrow="false" :value="orderData.delivery + ''" />
      </div>
      <div class="content-item">
        <list-tile :title="$t('total')" :arrow="false" :value="allTotal + ''" />
      </div>
    </div>
    <div class="bottom">
      <div class="total-price">
        {{ $t('total') }}：
        <div class="total-number">${{ allTotal }}</div>
      </div>
      <div class="button">
        <van-button @click.stop="handlerSubmitOrder" block round color="#191919">{{
          $t('payNow')
        }}</van-button>
      </div>
    </div>
    <van-action-sheet :overlay="false" :round="false" v-model:show="showActionSheet">
      <payment ref="paymentRef" :pay-data="payData" @close="() => (showActionSheet = false)" @verify="verifyPwd" />
    </van-action-sheet>
  </div>
  <custom-floating-panel ref="floatingPanel" :title="$t('pleaseChooseShippingAddress')">
    <van-space direction="vertical" size="1rem">
      <div
        class="address-card"
        v-for="(item, index) in addresses"
        :key="index"
        @click="chooseAddress(item)"
      >
        <div class="name">{{ item.name }}</div>
        <div class="address">{{ item.address }}</div>
      </div>
    </van-space>
  </custom-floating-panel>
</template>
<script setup name="Order">
import ListTile from '@/components/ListTile/index.vue'
import CartItem from '@/components/CartItem/index.vue'
import CustomInput from '@/components/Input/index.vue'
import CustomFloatingPanel from '@/components/CustomFloatingPanel/index.vue'
import Payment from '@/components/Peyment/index.vue'
import { multiply, plus } from '@/utils/math.js'
import toast from '@/utils/toast.js'
import { submitOrder, merPay } from '@/api/order.js'
import { verifyPay } from '@/api/user.js'
import useUserStore from '@/stores/modules/user'
import { showConfirmDialog } from 'vant'
const userStore = useUserStore()
const { proxy } = getCurrentInstance()
const orderData = ref({
  product: [],
  delivery: 0
})
const address = computed(() => {
  if (userStore.backData.address) {
    const _address = userStore.backData.address
    orderData.value.address = _address
    return {
      name: _address.name + _address.country,
      label: _address.tag
    }
  } else {
    if (orderData.value.address) {
      const _address = orderData.value.address
      return {
        name: _address.name + _address.country,
        label: _address.tag
      }
    }
  }
  return null
})
const addresses = ref([])
const total = computed(() => {
  let _total = 0
  orderData.value.product.forEach((item) => {
    _total = plus(item.total_price, _total)
  })
  return _total
})
const allTotal = computed(() => {
  return plus(total.value, orderData.value.delivery)
})
const changeNum = (item) => {
  item.total_price = multiply(item.sales_price, item.cart_num)
}
const promotion = ref(undefined)

const floatingPanel = ref(null)
const router = useRouter()
const showChooseAddress = () => {
  router.push({ name: 'Address', query: { type: 'choosen' } })
}
const chooseAddress = (item) => {
  address.value = item
  floatingPanel.value.show = false
}
const showActionSheet = ref(false)
const paymentRef=ref(null)
const payData = ref({})
const handlerSubmitOrder = () => {
  if (!orderData.value.address) {
    toast.show({ msg: proxy.t('pleaseChooseShippingAddress') })
    return
  }
  if (!userStore.userInfo.have_pay) {
    showConfirmDialog({
      message: proxy.t('notSetPaymentPassword'),
      confirmButtonText: proxy.t('goSetting'),
      cancelButtonText: proxy.t('cancel')
    })
      .then(() => {
        router.push({ name: 'ChangePayPwd' })
      })
      .catch(() => {})
    return
  }
  toast.loading()
  const _data = {
    address_id: orderData.value.address.address_id,
	chu_id: orderData.value.chu_id,
    cart_ids: orderData.value.product.map((item) => item.cart_id).join(','),
    product_id: orderData.value.product.map((item) => item.product_id).join(','),
    number: orderData.value.product.map((item) => item.number).join(','),
    mer_id: orderData.value.product.map((item) => item.mer_id).join(','),
    spec: orderData.value.product.map((item) => item.spec).join(',')
  }
  if (_data.cart_ids.length) {
    delete _data.product_id
    delete _data.number
    delete _data.mer_id
  } else {
    delete _data.cart_ids
  }
  submitOrder(_data)
    .then((res) => {
      payData.value = res.data
      toast.success({ msg: proxy.t('submitOrderSuccess') })
      payNow()
    })
    .catch((err) => {
      console.log(err)
    })
}
const payNow = () => {
  showActionSheet.value = true
}
const route = useRoute()
const verifyPwd = (pwd) => {
  toast.loading()
  verifyPay({
    password_pay: pwd
  })
    .then((res) => {
      payAction()
    })
    .catch((err) => {
      paymentRef.value.cleanPwd()
    })
}
const payAction = () => {
  toast.loading()
  merPay({
    out_trade_no: payData.value.out_trade_no,
    total_price: payData.value.total_price
  })
    .then((res) => {
      showActionSheet.value = false
      router.back()
      toast.success({ msg: proxy.t('paySuccess') })
    })
    .catch(err=>{
      paymentRef.value.cleanPwd()
    })
}
onMounted(() => {
  const _data = JSON.parse(route.query.orderData)
  _data.product = _data.product.map((item) => {
    return {
      ...item,
      checked: true,
      cart_num: item.number,
      total_price: multiply(item.sales_price, item.number),
      goods: {
        spec: item.spec,
        image: item.image,
        title: item.title,
        sales_price: item.sales_price
      }
    }
  })
  orderData.value = _data
})
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  overflow-y: hidden;
  padding: 0;
  .content {
    height: calc(100dvh - (50px + 70px));
    .content-item {
      padding-top: 0.25rem;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      font-size: 1rem;
      font-weight: 500;
      color: #191919;
      line-height: 1.2rem;
    }
    .address {
      padding: 0;
      .choose-address {
        display: flex;
        flex-direction: row;
        align-items: center;
        .choose-address-word {
          padding-left: 0.6rem;
          font-weight: 400;
          font-size: 1rem;
          color: #191919;
          line-height: 2rem;
        }
      }
    }
    .info {
      padding-top: 1rem;
      .product {
        margin-top: 0.75rem;
      }
    }
    .promotion {
      padding-top: 1rem;
    }
  }
  .bottom {
    .total-price {
      flex: 3;
      font-weight: 500;
      font-size: 1rem;
      color: #191919;
      line-height: 1.2rem;
      display: flex;
      flex-direction: row;
      align-items: center;
      .total-number {
        padding-left: 0.25rem;
        font-weight: 500;
        font-size: 2rem;
        color: #fe4857;
        line-height: 2rem;
      }
    }
    .button {
      flex: 2;
    }
  }
  ::v-deep(.van-action-sheet) {
    max-height: 100dvh;
  }
}
.address-card {
  background: #ffffff;
  box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.08);
  border-radius: 0.25rem;
  padding: 0.5rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  .name {
    font-size: 1rem;
    font-weight: 400;
    color: #191919;
    line-height: 1.2rem;
  }
  .address {
    padding-top: 0.2rem;
    width: 16rem;
    font-weight: 400;
    font-size: 1rem;
    color: #757575;
    line-height: 1.2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
</style>
