<template>
  <van-floating-panel style="height: 700px; transform: translateY(calc(100vdh- 120px))" v-if="show">
    <div class="floating-panel-container">
      <div class="title-row">
        <div />
        {{ title }}
        <icon-park name="close" size="1.5rem" @click="() => (show = false)" />
      </div>
      <div class="money-row">${{ money }}</div>
      <div class="order-info">
        <div>订单详情</div>
        <div>商品交易</div>
      </div>
      <div class="card-container" v-for="(card, index) in cards" :key="'card-' + index">
        <div class="card-item">
          <icon-park :name="card.icon" size="1.5rem" />
          <span class="card-value">{{ card.value }}</span>
        </div>
      </div>
      <div class="card-container card-container-center">
        <van-icon name="arrow-down" size="1.2rem" color="#191919" />
      </div>
      <div class="pwd-container">
        <van-password-input :value="payPwd" :gutter="10" />
        <van-number-keyboard :show="true" :maxlength="6" v-model="payPwd" @input="onInput" />
      </div>
    </div>
  </van-floating-panel>
  <van-overlay :show="show" @click="handlerClickOverlay" />
</template>
<script setup>
defineProps({
  title: {
    type: String,
    required: false,
    default: '请输入支付密码'
  },
  money: {
    type: Number,
    required: false,
    default: 990.0
  }
})
const show = ref(false)
const handlerClickOverlay = () => {
  show.value = false
}
defineExpose({ show })
const cards = ref([
  {
    value: '钱包余额 ($2.345,00)',
    icon: 'wallet-two'
  },
  {
    value: '万事达银行卡（暂不支持）',
    icon: 'bank-card-cp41pae1'
  }
])
const payPwd = ref('')
const onInput = (val) => {
  console.log(val)
}
const onDelete = () => {}
</script>
<style lang="scss" scoped>
.floating-panel-container {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  .title-row {
    padding-top: 1rem;
    font-weight: 500;
    font-size: 1.2rem;
    color: #191919;
    line-height: 1rem;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  .money-row {
    text-align: center;
    padding-top: 1.5rem;
    font-weight: bold;
    font-size: 2.6rem;
    color: #191919;
    line-height: 3rem;
  }
  .order-info {
    padding: 2.5rem 0 0.5rem 0;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-weight: 400;
    font-size: 1rem;
    color: #757575;
    line-height: 1.2rem;
  }
  .card-container {
    background: #f5f5f5;
    box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.05);
    padding: 1.2rem;
    border-bottom: 1px solid #eeeeee;
    .card-item {
      display: flex;
      flex-direction: row;
      align-items: center;
      padding: 0 1rem;
      .card-value {
        padding-left: 1.5rem;
        font-weight: 400;
        font-size: 1rem;
        color: #191919;
        line-height: 1.2rem;
      }
    }
  }
  .card-container-center {
    border-width: 0;
    text-align: center;
  }
  .pwd-container {
    padding-top: 2rem;
    ::v-deep(.van-password-input__item) {
      background: #f5f5f5 !important;
    }
    ::v-deep(.van-number-keyboard__body) {
      background: #fff;
    }
    ::v-deep(.van-number-keyboard) {
      padding-bottom: 0;
    }
  }
}
</style>
