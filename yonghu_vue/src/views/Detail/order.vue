<template>
  <div class="container">
    <nav-bar title="订单详情" />
    <div class="content">
      <div class="address content-item">
        收货地址
        <list-tile
          :title="address?.name"
          :label="address?.address"
          :arrow="!!address"
          @click.stop="showChooseAddress"
        >
          <template v-if="!address" #left>
            <div class="choose-address">
              <icon-park name="plus" size="1.6rem" />
              <div class="choose-address-word">添加地址</div>
            </div>
          </template>
        </list-tile>
      </div>
      <div class="info content-item">
        收货地址
        <cart-item class="product" :product="product"> </cart-item>
      </div>
      <div class="promotion content-item">
        <custom-input
          :value="promotion"
          @blur="(val) => (promotion = val)"
          placeholder="输入您的促销码"
        />
      </div>
      <div class="content-item">
        <list-tile title="小计" :arrow="false" :value="total + ''" />
      </div>
      <div class="content-item">
        <list-tile title="航运" :arrow="false" value="免费" />
      </div>
      <div class="content-item">
        <list-tile title="总" :arrow="false" :value="total + ''" />
      </div>
    </div>
    <div class="bottom">
      <div class="total-price">
        总计：
        <div class="total-number">${{ total }}</div>
      </div>
      <div class="button">
        <van-button block round color="#191919">立即付款</van-button>
      </div>
    </div>
  </div>
  <custom-floating-panel ref="floatingPanel" title="请选择收货地址">
    <van-space direction="vertical" size="1rem">
      <div
        class="address-card"
        v-for="(item, index) in addresses"
        :key="index"
        @click="chooseAddress(item)"
      >
        <div class="name">{{ item.name }}</div>
        <div class="address">{{ item.address }}</div>
      </div>
    </van-space>
  </custom-floating-panel>
</template>
<script setup>
import ListTile from '@/components/ListTile/index.vue'
import CartItem from '@/components/CartItem/index.vue'
import CustomInput from '@/components/Input/index.vue'
import CustomFloatingPanel from '@/components/CustomFloatingPanel/index.vue'
import Product2 from '@/assets/image/product2.png'
const product = ref({
  name: '奶油风格沙发奶油风格沙发奶油风格沙发',
  amount: 1,
  price: 200,
  total: 200,
  url: Product2
})
const total = computed(() => {
  return product.value.amount * product.value.price
})
const promotion = ref(undefined)
const address = ref(null)
const addresses = ref([
  {
    name: 'Jane Cooper',
    address: '华盛顿大道 4517 号曼彻斯特'
  },
  {
    name: 'Jane Cooper',
    address: '华盛顿大道 4517 号曼彻斯特'
  },
  {
    name: 'Jane Cooper',
    address: '华盛顿大道 4517 号曼彻斯特'
  },
  {
    name: 'Jane Cooper',
    address: '华盛顿大道 4517 号曼彻斯特'
  },
  {
    name: 'Jane Cooper',
    address: '华盛顿大道 4517 号曼彻斯特'
  },
  {
    name: 'Jane Cooper',
    address: '华盛顿大道 4517 号曼彻斯特'
  },
  {
    name: 'Jane Cooper',
    address: '华盛顿大道 4517 号曼彻斯特'
  }
])
const floatingPanel = ref(null)
const showChooseAddress = () => {
  floatingPanel.value.show = true
}
const chooseAddress = (item) => {
  address.value = item
  floatingPanel.value.show = false
}
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  overflow-y: hidden;
  padding: 0;
  .content {
    height: calc(100dvh - (50px + 70px));
    .content-item {
      padding-top: 0.25rem;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      font-size: 1rem;
      font-weight: 500;
      color: #191919;
      line-height: 1.2rem;
    }
    .address {
      padding: 0;
      .choose-address {
        display: flex;
        flex-direction: row;
        align-items: center;
        .choose-address-word {
          padding-left: 0.6rem;
          font-weight: 400;
          font-size: 1rem;
          color: #191919;
          line-height: 2rem;
        }
      }
    }
    .info {
      padding-top: 1rem;
      .product {
        margin-top: 0.75rem;
      }
    }
    .promotion {
      padding-top: 1rem;
    }
  }
  .bottom {
    .total-price {
      flex: 3;
      font-weight: 500;
      font-size: 1rem;
      color: #191919;
      line-height: 1.2rem;
      display: flex;
      flex-direction: row;
      align-items: center;
      .total-number {
        padding-left: 0.25rem;
        font-weight: 500;
        font-size: 2rem;
        color: #fe4857;
        line-height: 2rem;
      }
    }
    .button {
      flex: 2;
    }
  }
}
.address-card {
  background: #ffffff;
  box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.08);
  border-radius: 0.25rem;
  padding: 0.5rem;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  .name {
    font-size: 1rem;
    font-weight: 400;
    color: #191919;
    line-height: 1.2rem;
  }
  .address {
    padding-top: 0.2rem;
    width: 16rem;
    font-weight: 400;
    font-size: 1rem;
    color: #757575;
    line-height: 1.2rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
</style>
