<template>
  <div class="container">
    <nav-bar :title="$t('orderDetail')" />
    <div class="content">
      <div class="content-item">
        <list-tile :title="$t('orderNo')" :arrow="false" :value="orderData.order_sn" />
      </div>
      <div class="address content-item">
        {{ $t('shippingAddress') }}
        <list-tile :title="address?.name" :label="address?.label" :arrow="false">
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
          v-for="(item, index) in orderData.products"
          :key="index + 'product'"
          :product="item"
          :comment="orderData.status===3||orderData.status===4"
          @add-comment="handlerComment"
        >
        </cart-item>
      </div>
      <!-- <div class="promotion content-item">
          <custom-input
            :value="promotion"
            @blur="(val) => (promotion = val)"
            placeholder="输入您的促销码"
          />
        </div> -->
      <div class="content-item">
        <list-tile :title="$t('sutTotal')" :arrow="false" :value="allTotal + ''" />
      </div>
      <div class="content-item">
        <list-tile :title="$t('total')" :arrow="false" :value="allTotal + ''" />
      </div>
      <div class="content-item">
        <list-tile :title="$t('orderStatus')" :arrow="false" :value="status" />
      </div>
      <div class="content-item delivery">
        <div class="delivery-title">
          {{ hasDeliveryInfo ? $t('logisticsInformation') : $t('noLogisticsInformation') }}
          <div>
            {{ _deliveries.length ? _deliveries[0]['delivery_no']:'' }}
          </div>
        </div>
        <van-steps 
          v-if="_deliveries.length" direction="vertical" 
          active-color="#191919" :active="0"
        >
          <van-step
            v-for="item in _deliveries"
            :key="item.id"
          >
            <h3>{{ item.mark }}</h3>
            <p>{{ item.updatetime }}</p>
          </van-step>
        </van-steps>
      </div>
    </div>
    <div class="bottom">
      <div class="total-price">
        {{ $t('total') }}：
        <div class="total-number">${{ allTotal }}</div>
      </div>
      <div class="buttons">
        <van-space size="0.25rem">
          <van-button
            block
            round
            v-for="btn in _buttons"
            :key="btn.value"
            :type="btn.type"
            :color="btn.color"
            @click="handlerDeal(btn.value)"
          >
            {{ btn.name }}
          </van-button>
        </van-space>
      </div>
    </div>
    <van-action-sheet :overlay="false" :round="false" v-model:show="showActionSheet">
      <payment 
        ref="paymentRef" :pay-data="orderData" 
        @close="() => (showActionSheet = false)" @verify="verifyPwd" 
      />
    </van-action-sheet>
  </div>
  <custom-floating-panel ref="floatingPanel" :title="$t('pleaseChooseShippingAddress')">
    <van-space direction="vertical" size="1rem">
      <div class="address-card" v-for="(item, index) in addresses" :key="index">
        <div class="name">{{ item.name }}</div>
        <div class="address">{{ item.address }}</div>
      </div>
    </van-space>
  </custom-floating-panel>
</template>
<script setup name="OrderInfo">
import ListTile from '@/components/ListTile/index.vue'
import CartItem from '@/components/CartItem/info.vue'
import CustomFloatingPanel from '@/components/CustomFloatingPanel/index.vue'
import Payment from '@/components/Peyment/index.vue'
import toast from '@/utils/toast.js'
import { detail, pay, cancelOrder, received as confirmReceived, refundOrder } from '@/api/order.js'
import { verifyPay } from '@/api/user.js'
import { order_statuses } from '@/utils/constants.js'
import useUserStore from '@/stores/modules/user.js'
const { proxy } = getCurrentInstance()
const userStore = useUserStore()
const orderData = ref({
})
const _deliveries=ref([])
const status = computed(() => {
  const _status = order_statuses.find((item) => item.value === orderData.value.status + '')
  return _status?.name
})
const buttons = ref([
  { name: proxy.t('payNow'), statuses: ['0'], value: 'pay', color: '#191919' },
  { name: proxy.t('cancel'), statuses: ['0'], value: 'cancel', type: 'default' },
  { name: proxy.t('confirmReceived'), statuses: ['2'], value: 'confirmReceived', type: 'success' },
  { name: proxy.t('appDrawBack'), statuses: ['1', '2', '3', '4'], value: 'refund', type: 'danger' },
])
const _buttons = computed(() => {
  return buttons.value.filter((item) => {
    return item.statuses.includes(orderData.value.status + '')
  })
})
const handlerDeal = (type) => {
  switch (type) {
    case 'pay': // 付款
      handlerPay()
      break
    case 'cancel': //  取消订单
      handlerCancel()
      break
    case 'confirmReceived': //  确认收货
      handlerConfirmReceived()
      break
    case 'refund': //  申请退款
      confirmRefund()
      break
    default:
      break
  }
}
const canPay = computed(() => {
  return orderData.value.status === 0
})
const canReceived = computed(() => {
  return orderData.value.status === 2
})
const hasDeliveryInfo = computed(() => {
  return !!orderData.value.delivery_id
})
const address = computed(() => {
  return {
    name: orderData.value.user_address,
    label: orderData.value.real_name + '  ' + orderData.value.user_phone
  }
})
const allTotal = computed(() => {
  return orderData.value.total_price || 0
})
const floatingPanel = ref(null)
const router = useRouter()
const showActionSheet = ref(false)
const paymentRef=ref(null)
const handlerPay = () => {
  if (!userStore.userInfo.have_pay) {
    showConfirmDialog({
      message: proxy.t('noYetSetPaymentPassowrd'),
      confirmButtonText: proxy.t('goSetting'),
      cancelButtonText: proxy.t('cancel')
    })
      .then(() => {
        router.push({ name: 'ChangePayPwd' })
      })
      .catch(() => {})
    return
  }
  showActionSheet.value = true
}
const handlerCancel = () => {
  showConfirmDialog({
    message: proxy.t('whetherToCancel'),
    cancelButtonText: proxy.t('cancel'),
    confirmButtonText: proxy.t('confirm')
  })
    .then(() => {
      cancel()
    })
    .catch(() => {})
}
const handlerConfirmReceived = () => {
  showConfirmDialog({
    message: proxy.t('isConfirmReceived'),
    cancelButtonText: proxy.t('cancel'),
    confirmButtonText: proxy.t('confirm')
  })
    .then(() => {
      received()
    })
    .catch(() => {})
}
const confirmRefund = () => {
  if (orderData.value.refund_status !== 0) {
    toast.show({ msg: proxy.t('youSubmittedRefund') })
    return
  }
  showConfirmDialog({
    message: proxy.t('confirmToApplyRefund'),
    cancelButtonText: proxy.t('cancel'),
    confirmButtonText: proxy.t('confirm')
  })
    .then(() => {
      const product_ids = orderData.value.products.map((item) => item.order_product_id).join(',')
      router.push({
        name: 'Refund',
        params: {
          id: orderData.value.order_id,
          sn: orderData.value.order_sn,
          product_ids: product_ids,
          amount: orderData.value.total_price
        }
      })
    })
    .catch(() => {})
}
const handlerComment=item=>{
  router.push(
    { 
      name: 'Comment', 
      query:{data: JSON.stringify({
        order_id:orderData.value.order_id,
        good:item
      }) }
    }
  )
}
const cancel = () => {
  toast.loading()
  cancelOrder({ order_id: orderData.value.order_id })
    .then((res) => {
      toast.success({ msg: res.msg })
      router.back()
    })
    .catch((err) => err)
}
const received = () => {
  toast.loading()
  confirmReceived({ order_id: orderData.value.order_id })
    .then((res) => {
      toast.success({ msg: res.msg })
      router.back()
    })
    .catch((err) => err)
}
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
  pay({
    order_id: orderData.value.order_id
  })
    .then((res) => {
      showActionSheet.value = false
      router.back()
      toast.success({ msg: proxy.t('paySuccess') })
    })
    .catch((err) => {
      paymentRef.value.cleanPwd()
    })
}
const route = useRoute()
onMounted(() => {
  toast.loading()
  detail({ order_id: route.params.id })
    .then((res) => {
      orderData.value = res.data
      _deliveries.value=res.data.delivery
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
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
      padding-top: 1rem;
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
    .delivery {
      padding-top: 1rem;
      .delivery-title{
        padding-bottom: 0.5rem;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
      }
    }
  }
  .bottom {
    .total-price {
      flex: 2;
      font-weight: 500;
      font-size: 1rem;
      color: #191919;
      line-height: 1.2rem;
      display: flex;
      flex-direction: row;
      align-items: center;
      .total-number {
        padding-left: 0.25rem;
        font-weight: 600;
        font-size: 1.4rem;
        color: #fe4857;
        line-height: 1.6rem;
      }
    }
    .buttons {
      flex: 3;
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
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
