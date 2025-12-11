<template>
  <div class="cart-container">
    <div class="good-img">
      <VanImage width="100" height="100" :src="product.src" />
    </div>
    <div class="good-info">
      <div class="info-title">
        <div>{{ product.title }}</div>
        <van-tag :type="tagType">{{ status }}</van-tag>
      </div>
      <div class="info-desc">
        {{ product.desc }}
        <span class="info-items">{{ product.num }} items</span>
      </div>
      <div class="info-ctrl">
        <div class="ctrl-price">${{ product.total }}</div>
        <van-button round icon="arrow" color="#31452e" @click.stop="handlerArrowClick" />
      </div>
    </div>
  </div>
</template>
<script setup>
import NumButton from '@/components/Cart/numButton.vue'
const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  status: {
    type: String,
    required: false,
    default: 'delivered'
  }
})
const changeNum = (val) => {
  props.product.num = val
  props.product.total = val * props.product.price
}
const tagType = computed(() => {
  switch (props.status) {
    case 'delivered':
      return 'success '
    case 'processing':
      return 'warning'
    case 'cancelled':
      return 'danger'
    default:
      return 'success '
  }
})
const emit = defineEmits(['arrowClick'])
const handlerArrowClick = () => {
  emit('arrowClick')
}
</script>
<style lang="scss" scoped>
.cart-container {
  display: flex;
  flex-direction: row;
  .good-img {
    padding: 0.2rem 0.6rem;
    background: #ffffff;
    border-radius: 0.6rem;
  }
  .good-info {
    padding: 0.5rem 0 0.5rem 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    .info-title {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      font-size: 1.2rem;
      font-weight: 700;
    }
    .info-desc {
      font-size: 1rem;
      font-weight: 500;
      color: #616c76;
      .info-items {
        padding-left: 1rem;
        font-size: 1rem;
        font-weight: 500;
        letter-spacing: 0.06px;
        line-height: 20px;
        color: #191919;
      }
    }
    .info-ctrl {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: flex-end;
      .ctrl-price {
        font-size: 1.3rem;
        font-weight: 600;
        letter-spacing: 0.1px;
        line-height: 2rem;
        color: #191919;
      }
    }
  }
}
</style>
