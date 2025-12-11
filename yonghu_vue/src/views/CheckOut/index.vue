<template>
  <div class="container safari_only">
    <App-Header title="Checkout" />
    <div class="container-scroll">
      <div class="content-address">
        <div class="item-title">收货地址</div>
        <div class="address-info">
          <div class="location">
            <van-icon name="location-o" color="#000000" size="20" />
          </div>
          <div class="words">
            <div class="words-title">Jane Cooper</div>
            <div class="words-desc" style="padding-top: 12px">4517 Washington Ave. Manchester,</div>
            <div class="words-desc">Kentucky 39495</div>
            <div class="words-desc"></div>
            <div class="words-phone">(406)555-0120</div>
          </div>
          <div class="location">
            <van-icon name="edit" color="#000000" size="20" @click="editAddress" />
          </div>
        </div>
      </div>
      <div class="content-products">
        <div class="item-title">商品清单</div>
        <div class="product-list">
          <van-space direction="vertical" fill :size="10">
            <cart-item v-for="item in products" :key="item.productId" :product="item" />
          </van-space>
        </div>
      </div>
      <div class="products-add">
        <div class="add-tip">Enter Your Promo Code</div>
        <van-button round color="#000000"> Add </van-button>
      </div>
      <div class="products-total">
        <van-space direction="vertical" fill :size="15">
          <div class="total-item">
            <div>Sub Total</div>
            <div class="total-item-val">$ {{ total }}</div>
          </div>
          <div class="total-item">
            <div>Shipping</div>
            <div class="total-item-val">FREE</div>
          </div>
          <van-divider dashed />
          <div class="total-item">
            <div style="font-size: 20px">Total</div>
            <div class="total-item-val">$ {{ total }}</div>
          </div>
        </van-space>
      </div>
    </div>
    <div class="container-bottom">
      <div class="left-discount">
        <div class="bottom-top">Price</div>
        <div class="bottom-bottom">
          <div>${{ total }}</div>
          <div class="bottom-discount">{{ discount }}</div>
        </div>
      </div>
      <van-button round color="#000000"> Pay Now </van-button>
    </div>
    <van-dialog v-model:show="showDialog" :showConfirmButton="false" closeOnClickOverlay>
      <div class="edit-address">
        <div class="edit-address-title">My Address</div>
        <div class="edit-address-forms">
          <div class="form-label">Your Name</div>
          <van-field v-model.trim="form.name" />
          <div class="form-label">Your Address</div>
          <van-field v-model.trim="form.address" />
          <div class="form-label">Your Tel</div>
          <van-field v-model.trim="form.tel" />
        </div>
        <div class="edit-address-buttons">
          <van-button round block @click="() => (showDialog = false)"> Cancel </van-button>
          <div style="width: 3em" />
          <van-button color="#000000" round block @click="() => (showDialog = false)">
            Confirm
          </van-button>
        </div>
      </div>
    </van-dialog>
  </div>
</template>
<script setup>
// import Cart from '@/components/Cart/index.vue'
import CartItem from '@/components/CartItem/index.vue'
import AppHeader from '@/components/CustomNavBar/index.vue'
import { computed } from 'vue'
const products = ref([
  {
    productId: 1,
    num: 0,
    price: 88,
    total: 0,
    title: 'Direka Chair',
    desc: 'Brown',
    url: new URL('@/assets/image/product1.png', import.meta.url).href
  },
  {
    productId: 2,
    num: 0,
    price: 72,
    total: 0,
    title: 'Esacapea Chair',
    desc: 'Brown',
    url: new URL('@/assets/image/product2.png', import.meta.url).href
  }
])
const total = computed(() => {
  let _total = 0
  products.value.forEach((item) => {
    _total += item.total
  })
  return _total.toFixed(2)
})
const discount = computed(() => {
  if (total.value !== '0.00') {
    return (parseFloat(total.value) + 43.98).toFixed(2)
  }
  return 0
})
const showDialog = ref(false)
const form = ref({
  name: undefined,
  address: undefined,
  tel: undefined
})
const editAddress = () => {
  showDialog.value = true
}
const route = useRoute()
onMounted(() => {
  const _products = JSON.parse(route.query.products)
  products.value = _products
})
</script>
<style lang="scss" scoped>
@import url('@/assets/style/gj.scss');
.container {
  display: flex;
  flex-direction: column;
  .container-scroll {
    padding-top: 24px;
    height: calc(100vh - 134px);
    .item-title {
      font-size: 16px;
      font-weight: 600;
      letter-spacing: 0.08px;
    }
    .content-address {
      display: flex;
      flex-direction: column;
      .address-info {
        padding-top: 16px;
        display: flex;
        flex-direction: row;
        align-items: top;
        .words {
          padding-left: 12px;
          flex: 1;
          display: flex;
          flex-direction: column;
          .words-desc {
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.12px;
            line-height: 18px;
            color: rgba(156, 164, 171, 1);
          }
          .words-phone {
            padding-top: 12px;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.12px;
            line-height: 16.41px;
            color: rgba(17, 17, 17, 1);
          }
        }
        .location {
          margin-top: 10px;
          width: 37px;
          height: 37px;
          border-radius: 18.5px;
          background: #f6f6f6;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
        }
      }
    }
    .content-products {
      padding-top: 24px;
      .product-list {
        padding-top: 16px;
      }
    }
    .products-add {
      padding-top: 24px;
      padding-left: 18px;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      .add-tip {
        font-size: 14px;
        font-weight: 400;
        letter-spacing: 0px;
        line-height: 16.41px;
        color: rgba(153, 153, 153, 1);
      }
    }
    .products-total {
      padding-top: 37px;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      .total-item {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
        font-weight: 500;
        letter-spacing: -0.64px;
        line-height: 18.75px;
        color: rgba(17, 17, 17, 1);
        .total-item-val {
          font-size: 14px;
          font-weight: 500;
        }
      }
      ::v-deep(.van-divider) {
        margin: 0;
      }
    }
  }
  .container-bottom {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    .left-discount {
      display: flex;
      flex-direction: column;
      justify-content: space-around;
      .bottom-top {
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 0.07px;
        line-height: 22px;
        color: rgba(102, 112, 122, 1);
      }
      .bottom-bottom {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 0.1px;
        line-height: 28px;
        color: rgba(17, 17, 17, 1);
        display: flex;
        flex-direction: row;
        align-items: center;
        .bottom-discount {
          padding-left: 20px;
          font-size: 12px;
          font-weight: 700;
          letter-spacing: 0.06px;
          line-height: 28px;
          text-decoration-line: line-through;
          color: rgba(229, 57, 53, 1);
        }
      }
    }
  }
  .edit-address {
    padding: 1em;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    .edit-address-title {
      text-align: center;
      font-size: 1em;
      font-weight: 600;
      letter-spacing: 0.08px;
    }
    .edit-address-forms {
      .form-label {
        padding-top: 16px;
        padding-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.07px;
        color: rgba(120, 130, 138, 1);
      }
      ::v-deep(.van-cell) {
        padding: 0;
      }
      ::v-deep(.van-field__control) {
        padding: 10px;
        border-radius: 15px;
        border: 1px solid #31452e;
      }
    }
    .edit-address-buttons {
      padding-top: 1em;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
    }
  }
}
</style>
