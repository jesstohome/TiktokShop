<template>
  <div class="container">
    <nav-bar :title="$t('paymentCode')" />
    <van-form @submit="handlerSubmit">
      <div class="content">
        <div class="tips">
          <div>
            <van-icon name="success" />
            &nbsp; {{ $t('passwordLengthAtLeast6OnlySupportNumber') }}
          </div>
          <div style="padding-top: 0.5em">
            <van-icon name="success" />
            &nbsp; {{ $t('ifFirstSettingPaymentCode') }},{{ $t('doNotFillOldPassword') }},{{
              $t('ifChangePaymentCode')
            }},{{ $t('oldPasswordIsRequired') }}
          </div>
        </div>
        <custom-input
          :value="form.oldpassword"
          :label="$t('oldPassword')"
          :placeholder="$t('placeholderOldPassword')"
          @blur="(val) => (form.oldpassword = val)"
          :rules="[{ validator: oldPwdValidator, trigger: 'onSubmit' }]"
          :type="showPwd ? 'text' : 'password'"
        >
          <template #right>
            <icon-park
              :name="showPwd ? 'preview-close-one' : 'preview-open'"
              size="1.5rem"
              @click.stop="showPwd = !showPwd"
            />
          </template>
        </custom-input>
        <custom-input
          style="margin-top: 1.5rem"
          :value="form.newpassword"
          :label="$t('newPassword')"
          required
          :placeholder="$t('placeholderNewPassword')"
          @blur="(val) => (form.newpassword = val)"
          :type="showNewPwd ? 'text' : 'password'"
          :rules="[{ validator: newPwdValidator, trigger: 'onSubmit' }]"
        >
          <template #right>
            <icon-park
              :name="showNewPwd ? 'preview-close-one' : 'preview-open'"
              size="1.5rem"
              @click.stop="showNewPwd = !showNewPwd"
            />
          </template>
        </custom-input>
      </div>
      <div class="bottom">
        <van-button round block color="#191919" native-type="submit"> {{ $t('submit') }} </van-button>
      </div>
    </van-form>
  </div>
</template>
<script setup>
import { resetPaypwd } from '@/api/user.js'
import CustomInput from '@/components/Input/index.vue'
import toast from '@/utils/toast.js'
import { useRoute } from 'vue-router';
import useUserStore from '@/stores/modules/user';
const { proxy } = getCurrentInstance()
const userStore=useUserStore()
const form = ref({
  oldpassword: undefined,
  newpassword: undefined,
  is_verify: '0' //  默认设置支付密码
})
const showPwd = ref(false)
const showNewPwd = ref(false)
const oldPwdValidator = (val) => {
  if (val) {
    if (val.length !== 6) {
      return proxy.t('passwordLengthAtLeast6')
    } else {
      return true
    }
  }
  return true
}
const newPwdValidator = (val) => {
  if (val) {
    if (val.length !== 6) {
      return proxy.t('passwordLengthAtLeast6')
    } else {
      return true
    }
  } else {
    return proxy.t('placeholderNewPassword')
  }
}
const router=useRouter()
const handlerSubmit = () => {
  toast.loading()
  const _data = {
    newpassword: form.value.newpassword,
    oldpassword: form.value.oldpassword,
    is_verify: !!form.value.oldpassword ? '1' : '0'
  }
  resetPaypwd(_data)
    .then((res) => {
      toast.success(proxy.t('changeSuccess'))
      form.value.oldpassword = undefined
      form.value.newpassword = undefined
      form.value.is_verify = '0'
      //  设置用户已设置过支付密码
      userStore.userInfo.have_pay = 1
      router.back()
    })
    .catch((err) => err)
}
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;
  .content {
    overflow-y: auto;
    height: calc(100dvh - 120px);
    .tips {
      padding: 1rem 0 2rem 0;
      display: flex;
      flex-direction: column;
      font-size: 0.8em;
      font-weight: 500;
      color: rgba(0, 197, 102, 1);
    }
    ::v-deep(.van-cell) {
      border-radius: 0 !important;
    }
  }
}
</style>
