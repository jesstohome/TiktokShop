<template>
  <div class="container safari_only">
    <App-Header title="Payment Method">
      <template #right>
        <van-icon name="plus" size="28" color="#111111" />
      </template>
    </App-Header>
    <div class="container-scroll">
      <div class="card-container" v-for="(item, index) in cards" :key="item.name">
        <div class="card-img">
          <van-image width="36" fit="fill" :src="item.src" />
        </div>
        <div class="card-info">
          <div class="card-left">
            {{ item.name }}
            <div class="card-no">Card: &nbsp;&nbsp;{{ item.no }}</div>
          </div>
          <van-icon class-prefix="icon" size="22" color="#000000" name="edit-line" />
        </div>
      </div>
    </div>
    <div class="container-bottom">
      <van-button round block color="#000000"> Select Payment </van-button>
    </div>
  </div>
</template>
<script setup>
import AppHeader from '@/components/CustomNavBar/index.vue'
const cards = ref([
  {
    name: 'Visa',
    no: '******2',
    src: new URL('@/assets/image/visa.png', import.meta.url).href
  },
  {
    name: 'Gopay',
    no: '******2',
    src: new URL('@/assets/image/go-pay.png', import.meta.url).href
  },
  {
    name: 'Mastercard',
    no: '******2',
    src: new URL('@/assets/image/master-card.png', import.meta.url).href
  },
  {
    name: 'Shopee Pay',
    no: '******2',
    src: new URL('@/assets/image/shopee-pay.png', import.meta.url).href
  }
])
</script>
<style lang="scss" scoped>
.container {
  .container-scroll {
    padding-top: 2em;
    height: calc(100vh - 134px);
    .card-container {
      padding-bottom: 1.5em;
      display: flex;
      flex-direction: row;
      align-items: flex-start;
      .card-img {
        padding: 0.6em;
        border-radius: 1em;
        border: 1px solid rgba(227, 231, 236, 1);
      }
      .card-info {
        padding-left: 1em;
        padding-bottom: 1.5em;
        display: flex;
        flex: 1;
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-end;
        border-bottom: 1.5px solid rgba(227, 231, 236, 1);
        .card-left {
          display: flex;
          flex-direction: column;
          font-size: 1em;
          font-weight: 600;
          color: rgba(17, 17, 17, 1);
          .card-no {
            padding-top: 0.3em;
            font-size: 0.5;
            font-weight: 400;
            letter-spacing: 0.06px;
            line-height: 20px;
            color: rgba(102, 112, 122, 1);
          }
        }
      }
    }
  }
}
</style>
