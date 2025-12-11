<template>
  <div class="cart-container">
    <Product-Img class="good-img" :url="product.src" />
    <div class="good-info">
      <div class="info-title">
        <div>{{ product.title }}</div>
        <van-icon name="delete" color="#e53935" size="20px" @click="handelDelete" />
      </div>
      <div class="info-desc">
        {{ product.desc }}
      </div>
      <div class="info-ctrl">
        <div class="ctrl-price">${{ product.price }}</div>
        <Num-Button :num="product.num" @changeNum="changeNum" />
      </div>
      <div class="info-total">Total:&nbsp;&nbsp;{{ product.total }}</div>
    </div>
  </div>
</template>
<script setup>
import NumButton from '@/components/Cart/numButton.vue'
import ProductImg from '@/components/ProductImg/index.vue'
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})
const changeNum = (val) => {
  props.product.num = val
  props.product.total = val * props.product.price
}
const emit = defineEmits(['delete'])
const handelDelete = () => {
  emit('delete')
}
</script>
<style lang="scss" scoped>
.cart-container {
  display: flex;
  flex-direction: row;
  .good-img {
    width: 11em;
    height: 10em;
    border-radius: 1em;
  }
  .good-info {
    padding-left: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    .info-title {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      font-size: 16px;
      font-weight: 700;
    }
    .info-desc {
      font-size: 12px;
      font-weight: 500;
      color: #616c76;
    }
    .info-ctrl {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      .ctrl-price {
        font-size: 20px;
        font-weight: 600;
      }
    }
    .info-total {
      display: flex;
      flex-direction: row;
      font-size: 20px;
      font-weight: 600;
    }
  }
}
</style>
