<template>
  <div class="hot-product-container">
    <div class="product-img">
      <van-image :src="product.url" />
    </div>
    <div class="product-info">
      <div class="product-prices">
        {{ product.price }}
        <div class="old-price">{{ product.oldPrice }}</div>
      </div>
      <div class="product-name">
        {{ product.name }}
      </div>
      <div class="product-popular">
        <div>销量&nbsp;&nbsp;{{ product.sales }}</div>
        <div>{{ views }}万看过</div>
      </div>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})
const views = computed(() => {
  if (props.product.viewed) {
    return (props.product.viewed / 10000).toFixed(1)
  }
  return 0
})
</script>
<style lang="scss" scoped>
.hot-product-container {
  background: #fff;
  padding: 1.3rem 0.6rem;
  border-radius: 0.6rem;
  display: flex;
  flex-direction: column;
  .product-img {
    padding: 0 1rem;
  }
  .product-info {
    padding-top: 0.7rem;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    .product-prices {
      display: flex;
      flex-direction: row;
      font-size: 1rem;
      font-weight: 600;
      line-height: 1rem;
      .old-price {
        padding-left: 0.5rem;
        font-size: 0.9rem;
        font-weight: 400;
        color: #888888;
        line-height: 1rem;
        font-style: normal;
        text-decoration-line: line-through;
        text-transform: none;
      }
      .old-price::before {
        content: '$';
      }
    }
    .product-name {
      font-size: 1rem;
      font-weight: 600;
      line-height: 2.2rem;
    }
    .product-popular {
      padding-top: 0.2rem;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      color: #888888;
      font-size: 1rem;
      font-weight: 400;
    }
  }
}
</style>
