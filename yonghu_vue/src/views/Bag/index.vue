<template>
  <div class="container">
    <nav-bar :title="$t('shopping')" :can-back="false" />
    <div class="content">
      <div v-if="carts.length" class="products">
        <van-swipe-cell class="cart-item-container" v-for="(item, index) in carts" :key="index">
          <cart-item :product="item" @changeNum="changeNum" />
          <template #right>
            <div class="cart-item-del" @click="removeItem(item.cart_id)">{{ $t('delete') }}</div>
          </template>
        </van-swipe-cell>
      </div>
      <van-empty v-else :description="$t('noProducts')" />
    </div>
    <div class="bottom">
      <div class="checkout-left">
        {{ $t('total') }}:
        <div class="total">${{ total }}</div>
      </div>
      <div class="checkout-right">
        <van-button color="#191919" round block @click="handlerCreateOrder">
          <span class="button-words">{{ $t('createOrder') }}</span>
        </van-button>
      </div>
    </div>
  </div>
  <AppTabbar />
</template>
<script setup name="Bag">
import throttle from 'lodash/throttle'
import NavBar from '@/components/CustomNavBar/index.vue'
import CartItem from '@/components/CartItem/index.vue'
import toast from '@/utils/toast.js'
import { getCartList, delCarts, setCartNum } from '@/api/cart.js'
import { createOrder } from '@/api/order.js'
import { plus } from '@/utils/math.js'
const { proxy } = getCurrentInstance()
const changeNum = throttle((data) => {
  setCartNum(data)
    .then((res1) => {
      return getCartList()
    })
    .then((res2) => {
      carts.value = res2.data.carts.map((item, index) => ({
        ...item,
        checked: carts.value[index].checked
      }))
    })
    .catch((err) => err)
}, 1200)
const removeItem = (id) => {
  toast.loading({})
  delCarts(id).then(() => {
    toast.success({ msg: proxy.t('deleteSuccess') })
    handlerQuery()
  })
}
const router = useRouter()
const handlerCreateOrder = () => {
  const checkedCarts = carts.value.filter((item) => item.checked)
  if (!checkedCarts.length) {
    toast.show({ msg: proxy.t('pleaseSelectItemToCheckOut') })
    return
  }
  toast.loading()
  const cart_ids = checkedCarts.map((item) => item.cart_id).join(',')
  createOrder({
    cart_id: cart_ids
  })
    .then((res) => {
      toast.success({ msg: proxy.t('createOrderSuccess') })
      router.push({ name: 'Order', query: { orderData: JSON.stringify(res.data) } })
    })
    .catch((err) => {
      console.log(err)
    })
    .finally(() => {
      toast.close()
    })
}
const carts = ref([])
const total = computed(() => {
  let total = 0
  carts.value.forEach((item) => {
    if (item.checked) {
      total = plus(total, item.total_price)
    }
  })
  return total
})
const handlerQuery = () => {
  toast.loading()
  getCartList()
    .then((res) => {
      carts.value = res.data.carts.map((item) => ({
        ...item,
        checked: true
      }))
    })
    .finally(() => {
      toast.close()
    })
}
router.afterEach((to, from, next) => {
  if (to.name === 'Bag') {
    handlerQuery()
  }
})
onMounted(handlerQuery)
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;
  .content {
    display: flex;
    flex-direction: column;
    height: calc(100dvh - (50px + 70px + 50px));
    overflow-y: auto;
    // position: relative;
    .products {
      .cart-item-container {
        margin-bottom: 0.5rem;
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
  .bottom {
    // width: 100%;
    // bottom: 50px;
    // position: fixed;
    background: #ffffff;
    height: 70px;
    padding: 1rem 0.9rem;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    .checkout-left {
      display: flex;
      flex: 3;
      flex-direction: row;
      align-items: center;
      font-size: 1rem;
      font-weight: 600;
      color: #191919;
      line-height: 1.1rem;
      .total {
        padding-left: 0.5rem;
        font-size: 24px;
        font-weight: 600;
        color: #fe4857;
        line-height: 28px;
      }
    }
    .checkout-right {
      flex: 2;
      .button-words {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 400;
        letter-spacing: 2px;
      }
    }
  }
}
</style>
