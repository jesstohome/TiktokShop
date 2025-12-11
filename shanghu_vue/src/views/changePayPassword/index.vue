<script setup>
import {changePayPwd} from "@/api/index.js";

const onClickLeft = () => history.back();

//请求参数
const changePayQuery = ref({
      old: '',
      new: '',
  is_verify: '1'
    }
)
const repwd=ref('')
//请求函数
const onSubmit = async (data) => {
  const res = await changePayPwd(data)
  if (res.code === 1) {
    showSuccessToast(res.msg);
  }else{
    showFailToast(res.msg);
  }
}
//校验密码
const validator = (val) => {
  if(val!==changePayQuery.value.new){
    return false;
  }
}
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('changePayPassword.changePayPassword')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <van-form @submit="onSubmit(changePayQuery)" class="bg-white">
      <div>
        <van-cell :title="$t('changePayPassword.oldpwd')" center style="font-size: 20px"/>
        <van-field :placeholder="$t('changePayPassword.inputpwd')" type="password" v-model="changePayQuery.old" required maxlength="6" />
      </div>
      <div>
        <van-cell :title="$t('changePayPassword.newpwd')" center style="font-size: 20px"/>
        <van-field :placeholder="$t('changePayPassword.inputpwd')" type="password" v-model="changePayQuery.new" required maxlength="6" />
      </div>
      <div>
        <van-cell :title="$t('changePayPassword.againpwd')" center style="font-size: 20px"/>
        <van-field :placeholder="$t('changePayPassword.againinput')" type="password" v-model="repwd" required :rules="[{ validator ,message: $t('changePayPassword.message')}]" maxlength="6" />
      </div>
      <div class="bg-black mx-3 mt-3 flex justify-center items-center rounded-md ">
        <van-button native-type="submit">{{ $t("changePayPassword.submit") }}</van-button>
      </div>
      <div class="h-5"></div>
    </van-form>
  </main>
</template>

<style scoped lang="scss">
.van-button--default {
  color: white;
  background-color: black;
  border: 0 solid white;
}
</style>