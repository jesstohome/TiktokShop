<template>
  <div class="horiz-product-container">
    <van-image width="100%" height="100px" :src="product?.mer_avatar" />
    <div class="product-info">
      <div class="product-prices">
        {{ product?.mer_name }}
        <!-- <div class="old-price">{{ product?.market_price }}</div> -->
      </div>
      <!-- {{ $t('selled') }} -->
      &nbsp; {{ product?.follow_count }}&nbsp;{{ $t('fans') }}
      <div class="product-name">
       
		<div>{{ $t('favorableRate') }}:&nbsp;&nbsp;{{ product.good_rate }}%</div>
		<!-- <div>{{ $t('totalSales') }}:&nbsp;&nbsp;{{ product.total_price }}</div> -->
      </div>
    </div>
  </div>
</template>
<script setup>
import { formatNumberWithCommas } from '@/utils/filter.js'
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})
const sales = computed(() => {
  return formatNumberWithCommas(props.product.sales)
})
</script>
<style lang="scss" scoped>
.horiz-product-container {
  background: #ffffff;
  padding: 0.6rem 2rem 0.6rem 0.8rem;
  border-radius: 0.6rem;
  display: flex;
  flex-direction: row;
  align-items: stretch;
  .product-info {
    flex: 3;
    padding: 0 1.5rem 0.5rem 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    align-items: flex-start;
    font-size: 1rem;
    font-weight: 400;
    color: #888888;
    line-height: 12px;
    .product-prices {
      display: flex;
      flex-direction: row;
      font-weight: 600;
      font-size: 1.2rem;
      color: #191919;
      line-height: 1.4rem;
      .old-price {
        padding-left: 0.5rem;
        font-size: 0.9rem;
        font-weight: 400;
        color: #888888;
        line-height: 1.4rem;
        font-style: normal;
        text-decoration-line: line-through;
        text-transform: none;
      }
      .old-price::before {
        content: '$';
      }
    }
    .product-name {
      width: 12rem;
      font-size: 1rem;
      font-weight: 600;
      color: #191919;
      line-height: 1.2rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
  }
}
</style>
