<template>
  <div class="pay-panel-container">
    <div class="title-row">
      <div/>
      <span class="pl-6 text-2xl">{{ title }}</span>
      <icon-park
          name="close"
          size="1.5rem"
          @click="handlerClose"
      />
    </div>
    <div class="flex justify-center ">
      <span class="text-2xl pt-4">${{ price }}</span>
    </div>
    <div class="pwd-container">
      <van-password-input :gutter="10" :value="payPwd"/>
      <van-number-keyboard
          v-model="payPwd"
          :close-button-text="$t('confirm')"
          :maxlength="6"
          :show="true"
          theme="custom"
          @close="confirm()"
      />
    </div>
  </div>
</template>
<script setup>
import {useUserStore} from '@/store/modules/user.js';
import {useI18n} from 'vue-i18n';
import i18n from '@/lang/index.js';
//多语言
const {t} = useI18n();
const userStore = useUserStore();

defineProps({
  title: {
    type: String,
    required: false,
    default: i18n.global.t("inputpaypwd")
  },
  price: {
    type: String,
    required: false,
    default: 0
  }
});
const payPwd = ref('');

const emit = defineEmits(['close', 'send-data']);
const handlerClose = () => {
  emit('close');
};
const confirm = () => {
  // console.log(payPwd.value)
  // userStore.pwd=payPwd.value
  // console.log(userStore.pwd)
  emit('send-data', payPwd);
  payPwd.value = '';
};

</script>
<style lang="scss" scoped>
.pay-panel-container {
  padding: 1rem;
  display: flex;
  flex: 1;
  flex-direction: column;
  align-items: stretch;
  height: 60vh;

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
    padding: 1rem 0 0.5rem 0;
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

::v-deep(.van-number-keyboard) {
  margin-bottom: 60px;
}
</style>
