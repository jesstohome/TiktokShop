<template>
  <div class="hot-shop-container">
    <div class="hot-shop-info">
      <div class="shop-info-left">
        <van-image round width="3.6rem" height="3.6rem" :src="shop.mer_avatar" />
        <div class="show-info-name">
          {{ shop.mer_name }}
          <div class="show-info-fans">{{ shop.follow_count }}&nbsp;{{ $t('fans') }}</div>
        </div>
      </div>
      <div class="visit-shop" @click.stop="handlerVisitShop">{{ $t('visit') }}</div>
    </div>
    <div class="hot-shop-popular">
      <div>{{ $t('favorableRate') }}:&nbsp;&nbsp;{{ shop.good_rate }}%</div>
      <div>{{ $t('totalSales') }}:&nbsp;&nbsp;{{ shop.total_price }}</div>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  shop: {
    type: Object,
    required: true
  }
})
const router = useRouter()
const handlerVisitShop = () => {
  router.push({ name: 'Shop', params: { id: props.shop.mer_id } })
}
</script>
<style lang="scss" scoped>
.hot-shop-container {
  height: 120px;
  background: #ffffff;
  padding: 0.9rem 0.6rem;
  border-radius: 0.6rem;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  .hot-shop-info {
    padding-bottom: 0.8rem;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f3f3f3;
    .shop-info-left {
      display: flex;
      flex-direction: row;
      align-items: stretch;
      .show-info-name {
        padding: 0.5rem 0 0.5rem 1rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        font-size: 1rem;
        font-weight: 600;
        .show-info-fans {
          font-weight: 400;
          font-size: 0.8rem;
          color: #666666;
        }
      }
    }
    .visit-shop {
      font-weight: 600;
      font-size: 1rem;
      color: #ffffff;
      line-height: 1.2rem;
      letter-spacing: 2px;
      padding: 0.4rem 1rem;
      border-radius: 1rem;
      background: linear-gradient(129deg, #6b6b6b 0%, #1c1b1b 100%);
    }
  }
  .hot-shop-popular {
    padding-top: 0.6rem;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-size: 1rem;
    font-weight: 600;
  }
}
</style>
