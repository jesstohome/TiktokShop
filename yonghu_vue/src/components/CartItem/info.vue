<template>
  <div class="cart-item-container">
    <div class="item-img">
      <van-image width="6.5rem" height="auto" :src="product.image" />
    </div>
    <div class="item-info">
      <span class="name">
        {{ product.title }}
      </span>
      <div class="price">
        {{ product.product_price }}
        <div class="spec">{{ product.spec || $t('noSpec') }}</div>
      </div>
      <div class="number">数量: &nbsp;{{ product.product_num }}</div>
      <div class="last-row">
        <div>总计: &nbsp;${{ product.total_price }}</div>
        <van-button 
          v-if="comment"
          size="small" color="#191919" round
          @click.stop="handlerAddComment"
        >
          {{ $t(product.is_reply?'commented':'comment') }}
        </van-button>
      </div>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  product: {
    type: Object,
    required: true
  },
  comment:{
    type:Boolean,
    required:false,
    default:false
  }
})
const emit=defineEmits(['addComment'])
const handlerAddComment=()=>{
  if(props.product.is_reply){
    return
  }
  emit('addComment',props.product)
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
    .number {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
    }
    .last-row{
      padding-top: 0.8rem;
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
    }
  }
}
</style>
