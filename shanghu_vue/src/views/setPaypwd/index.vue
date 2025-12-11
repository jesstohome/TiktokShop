<script setup>
import {changePayPwd} from "@/api/index.js";
import {useI18n} from 'vue-i18n';
//多语言
const {t} = useI18n();
const onClickLeft = () => history.back();

//请求参数
const setPayQuery = ref({
      old: '',
      new: '',
      is_verify: '0'
    }
);
const repwd = ref('');
//请求函数
const onSubmit = async (data) => {
  const res = await changePayPwd(data);
  if (res.code === 1) {
    showSuccessToast(res.msg);
  } else {
    showFailToast(res.msg);
    router.go(-1);
  }
};
//校验密码
const validator = (val) => {
  if (val !== setPayQuery.value.new) {
    return false;
  }
};
</script>

<template>
  <header>
    <van-nav-bar
        :left-text="$t('goback')"
        :title="$t('setPaypwd.setPaymentPassword')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <van-form class="bg-white" @submit="onSubmit(setPayQuery)">
      <div>
        <van-cell :title="$t('setPaypwd.fundsPassword')" center style="font-size: 20px"/>
        <van-field v-model="setPayQuery.new" :placeholder="$t('setPaypwd.enterSixDigits')" maxlength="6" required
                   type="password"/>
      </div>
      <div>
        <van-cell :title="$t('setPaypwd.reenterFundsPassword')" center style="font-size: 20px"/>
        <van-field v-model="repwd" :placeholder="$t('setPaypwd.reenterPassword')" :rules="[{ validator ,message: $t('setPaypwd.passwordsDoNotMatch')}]" maxlength="6"
                   required type="password"/>
      </div>
      <div class="bg-black mx-3 mt-3 flex justify-center items-center rounded-md ">
        <van-button native-type="submit">{{ $t("setPaypwd.submit") }}</van-button>
      </div>
      <div class="h-5"></div>
    </van-form>
  </main>
</template>

<style lang="scss" scoped>
.van-button--default {
  color: white;
  background-color: black;
  border: 0 solid white;
}
</style>
