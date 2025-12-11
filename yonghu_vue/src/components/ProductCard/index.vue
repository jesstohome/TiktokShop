<template>
  <div class="hot-product-container">
    <div class="product-img">
      <van-image width="100%" radius="0.5rem" height="10rem" :src="product.goods?.image" />
    </div>
    <div class="product-info">
      <div class="product-prices">
        {{ product.goods?.sales_price }}
        <div class="old-price">{{ product.goods?.market_price }}</div>
      </div>
      <div class="product-name">
        {{ product.goods?.title }}
      </div>
      <div class="product-popular">
        <div>{{ $t('sales') }}&nbsp;&nbsp;{{ product.sales }}</div>
        <div>{{ product.click }} {{ $t('seen') }}</div>
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
</script>
<style lang="scss" scoped>
.hot-product-container {
  background: #fff;
  padding: 1.3rem 0.6rem;
  border-radius: 0.6rem;
  display: flex;
  flex-direction: column;
  .product-img {
    padding: 0;
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
      width: calc((100dvw - 6.5rem) / 2);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .product-popular {
      padding-top: 0.2rem;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      color: #888888;
      display: block;
      font-size: 1rem;
      font-weight: 400;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
  }
}
</style>
