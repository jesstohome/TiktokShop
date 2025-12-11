<script setup>
import {changeLoginPwd} from "@/api/index.js";
import {useUserStore} from '@/store/modules/user.js';
import {useRouter} from "vue-router";
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
//仓库中获取商户信息请求函数
const userStore = useUserStore();
const router = useRouter();
const onClickLeft = () => history.back();
//请求参数
const forgetLoginQuery = ref({
      newpassword: '',
      mer_phone: '',
      oldpassword: '',
      renewpassword: ''
    }
)
//请求函数
const onSubmit = async (data) => {
  const res = await changeLoginPwd(data)
  if (res.code === 1) {
    showSuccessToast(res.msg);
    router.push('/login');
  } else {
    showFailToast(res.msg);
  }
}

//校验密码
const validator = (val) => {
  if (val !== forgetLoginQuery.value.newpassword) {
    return false;
  }
}
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('forgetPwd.changeLoginPassword')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <van-form @submit="onSubmit(forgetLoginQuery)" class="bg-white">
      <div>
        <van-cell :title="$t('forgetPwd.tel')" center style="font-size: 20px"/>
        <van-field :placeholder="$t('forgetPwd.inputtel')"
                   v-model="forgetLoginQuery.mer_phone" :rules="[{ required: true, message: $t('forgetPwd.telmessage') }]"
        />
      </div>
      <div>
        <van-cell :title="$t('forgetPwd.pwd')"  center style="font-size: 20px"/>
        <van-field :placeholder="$t('forgetPwd.inputpwd')" type="password" v-model="forgetLoginQuery.newpassword" required/>
      </div>
      <div>
        <van-cell :title="$t('forgetPwd.againpwd')"  center style="font-size: 20px"/>
        <van-field :placeholder="$t('forgetPwd.againinput')" type="password" v-model="forgetLoginQuery.renewpassword" required
                   :rules="[{ validator ,message:  $t('forgetPwd.message')}]"/>
      </div>
      <div class="bg-black mx-3 mt-3 flex justify-center items-center rounded-md ">
        <van-button native-type="submit"> {{ $t("forgetPwd.submit") }}</van-button>
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