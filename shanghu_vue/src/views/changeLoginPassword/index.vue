<script setup>
import {changeLoginPwd} from "@/api/index.js";
import {useUserStore} from '@/store/modules/user.js';
//仓库中获取商户信息请求函数
const userStore = useUserStore();
const onClickLeft = () => history.back();
//请求参数
const changeLoginQuery=ref({
      newpassword:'',
      mer_phone:'',
      renewpassword:'',
      oldpassword:''
}
)
//请求函数
const onSubmit=async(data)=>{
  const res=await changeLoginPwd(data)
  if(res.code===1){
    showSuccessToast(res.msg);
    userStore.nologin()
  }else{
    showFailToast(res.msg);
  }
}

//校验密码
const validator = (val) => {
  if(val!==changeLoginQuery.value.newpassword){
    return false;
  }
}
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('changeLoginPassword.changeLoginPassword')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <van-form @submit="onSubmit(changeLoginQuery)" class="bg-white">
      <div>
        <van-cell :title="$t('changeLoginPassword.oldpwd')" center style="font-size: 20px"/>
        <van-field :placeholder="$t('changeLoginPassword.inputpwd')" type="password" v-model="changeLoginQuery.oldpassword" required />
      </div>
      <div>
        <van-cell :title="$t('changeLoginPassword.newpwd')" center style="font-size: 20px"/>
        <van-field :placeholder="$t('changeLoginPassword.inputpwd')" type="password" v-model="changeLoginQuery.newpassword" required />
      </div>
      <div>
        <van-cell :title="$t('changeLoginPassword.againpwd')" center style="font-size: 20px"/>
        <van-field :placeholder="$t('changeLoginPassword.againinput')" type="password"  v-model="changeLoginQuery.renewpassword" required :rules="[{ validator ,message: '两次密码输入不一致'}]"/>
      </div>
      <div class="bg-black mx-3 mt-3 flex justify-center items-center rounded-md ">
        <van-button native-type="submit"> {{ $t("changeLoginPassword.submit") }}</van-button>
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