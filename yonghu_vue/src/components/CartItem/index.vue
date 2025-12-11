<template>
  <div class="cart-item-container">
    <div class="item-img">
      <van-image width="6.5rem" height="auto" :src="product.goods?.image" />
    </div>
    <div class="item-info">
      <span class="name">
        {{ product.goods?.title }}
      </span>
      <div class="price">
        {{ product.goods?.sales_price }}
        <div class="spec">{{ product.goods?.spec || product.spec || $t('noSpec') }}</div>
      </div>
      <div v-if="readonly && product.cart_id">{{ $t('quantity') }}:&nbsp;&nbsp;{{ value }}</div>
      <div v-else class="number-ctl">
        <van-stepper v-model="value" :min="1" />
      </div>
      <div class="total">{{ $t('total') }}: &nbsp;${{ product.total_price }}</div>
    </div>
    <van-checkbox
      v-if="!readonly"
      icon-size="2.2rem"
      v-model="product.checked"
      checked-color="#191919"
    />
  </div>
</template>
<script setup>
import { setCartNum } from '@/api/cart.js'
import toast from '@/utils/toast.js'
const { proxy } = getCurrentInstance()
const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  readonly: {
    type: Boolean,
    required: false,
    default: false
  }
})
const emit = defineEmits(['changeNum'])
const value = computed({
  get() {
    return props.product.cart_num
  },
  set(val) {
    if (val <= 0 || isNaN(val)) {
      return
    }
    if (props.product.cart_id) {
      emit('changeNum', {
        cart_id: props.product.cart_id,
        num: val
      })
    } else {
      props.product.cart_num = val
      emit('changeNum')
    }
  }
})
const changeCartNum = async (num) => {
  if (props.product.cart_id) {
    toast.loading({
      msg: proxy.t('quantityChanging')
    })
    return new Promise((resolve) => {
      setCartNum({
        cart_id: props.product.cart_id,
        num: num
      })
        .then((res) => {
          resolve(true)
          emit('changeNum')
        })
        .catch((err) => {})
        .finally(() => {
          toast.close()
        })
    })
  } else {
    props.product.cart_num = num
    emit('changeNum')
  }
}
</script>
<style lang="scss" scoped>
.cart-item-container {
  background: #ffffff;
  padding: 0.72rem;
  border-radius: 0.6rem;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.05);
  .item-img {
    background: #f7f7f7;
    border-radius: 0.56rem;
    box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.05);
  }
  .item-info {
    padding-left: 1.42rem;
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: space-around;
    align-items: stretch;
    font-size: 1rem;
    font-weight: 600;
    color: #191919;
    line-height: 1.2rem;
    .name {
      width: 12rem;
      padding-bottom: 0.5rem;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
    .price {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      .spec {
        font-size: 0.85rem;
        color: #868686;
      }
    }
    .number-ctl {
      width: 80%;
      padding: 0.4rem 0.8rem;
      border-radius: 1.6rem;
      background: #f5f5f5;
      display: flex;
      flex-direction: row;
      justify-content: space-around;
      align-items: center;
      .number-word {
        padding: 0 1.2rem;
        font-size: 1.2rem;
        font-weight: 600;
        color: #191919;
        line-height: 1.6rem;
      }
      ::v-deep(.van-cell) {
        padding: 0;
        width: 4rem;
      }
      ::v-deep(.van-field__control) {
        text-align: center;
        padding: 0;
        background: #f5f5f5;
      }
    }
    .total {
      padding-top: 0.8rem;
    }
  }
}
</style>
