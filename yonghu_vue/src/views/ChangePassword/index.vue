<template>
  <div class="container">
    <App-Header :title="$t('loginPassword')" />
    <van-form @submit="handlerChangePwd">
      <div class="content">
        <div class="top-tip">{{ $t('newPasswordMustDifferentFromOld') }}</div>
        <div class="forms">
          <Custom-Input
            :label="$t('yourMobile')"
            :value="form.mobile"
            @blur="(val) => (form.mobile = val)"
            :rules="[{ validator: mobileValidator, trigger: 'onSubmit' }]"
          >
          </Custom-Input>
          <Custom-Input
            :label="$t('oldPassword')"
            :value="form.oldPassword"
            @blur="(val) => (form.oldPassword = val)"
            :type="showPwd ? 'text' : 'password'"
            :rules="[{ validator: oldPwdValidator, trigger: 'onSubmit' }]"
          >
            <template #right>
              <icon-park
                :name="showPwd ? 'preview-close-one' : 'preview-open'"
                size="1.5rem"
                @click.stop="showPwd = !showPwd"
              />
            </template>
          </Custom-Input>
          <div class="tips">
            <div>
              <van-icon name="success" />
              &nbsp; {{ $t('passwordLengthAtLeast6OnlySupportNumber') }}
            </div>
            <!-- <div style="padding-top: 0.5em">
              <van-icon name="success" />
              &nbsp; There must be a unique code like @!#
            </div> -->
          </div>
          <Custom-Input
            :label="$t('newPassword')"
            :value="form.newPassword"
            @blur="(val) => (form.newPassword = val)"
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
          </Custom-Input>
          <Custom-Input
            :label="$t('confirmPassword')"
            :value="form.repeatPwd"
            @blur="(val) => (form.repeatPwd = val)"
            :type="showRepNewPwd ? 'text' : 'password'"
            :rules="[{ validator: repeatNewPwdValidator, trigger: 'onSubmit' }]"
          >
            <template #right>
              <icon-park
                :name="showRepNewPwd ? 'preview-close-one' : 'preview-open'"
                size="1.5rem"
                @click.stop="showRepNewPwd = !showRepNewPwd"
              />
            </template>
          </Custom-Input>
        </div>
      </div>
      <div class="bottom">
        <van-button round block color="#000000" native-type="submit">
          {{ $t('submit') }}
        </van-button>
      </div>
    </van-form>
  </div>
</template>
<script setup>
import AppHeader from '@/components/CustomNavBar/index.vue'
import CustomInput from '@/components/Input/index.vue'
import toast from '@/utils/toast.js'
import { resetpwd } from '@/api/user.js'
// import { regMobile } from '@/utils/regExp.js'
import useUserStore from '@/stores/modules/user.js'
const { proxy } = getCurrentInstance()
const userStore = useUserStore()
const form = ref({
  mobile: undefined,
  oldPassword: undefined,
  newPassword: undefined,
  repeatPwd: undefined
})
const showPwd = ref(false)
const showNewPwd = ref(false)
const showRepNewPwd = ref(false)
const mobileValidator = (val) => {
  if (!val) {
    return proxy.t('')
  } else {
    // if (!regMobile(val)) {
    //   return proxy.t('pleasFillCorrectMobilePhoneNumber')
    // }
    return true
  }
}
const oldPwdValidator = (val) => {
  if (!val) {
    return proxy.t('placeholderOldPassword')
  } else {
  }
}
const checkPwd = (val) => {
  if (val.indexOf('@') !== -1 || val.indexOf('!') !== -1 || val.indexOf('#') !== -1) {
    return true
  }
  return false
}
const newPwdValidator = (val) => {
  if (!val) {
    return proxy.t('placeholderNewPassword')
  } else {
    if (val.length < 6) {
      return proxy.t('pleaseFillNewPasswordAsRequired')
    }
    return true
  }
}
const repeatNewPwdValidator = (val) => {
  if (!val) {
    return proxy.t('pleaseConfirmPassword')
  } else {
    if (val !== form.value.newPassword) {
      return proxy.t('twoPasswordsNotMatch')
    }
    return true
  }
}
const router = useRouter()
const handlerChangePwd = () => {
  toast.loading()
  resetpwd({
    mobile: form.value.mobile,
    newpassword: form.value.newPassword,
    oldpassword: form.value.oldPassword
  })
    .then((res) => {
      toast.success(proxy.t('changeSuccess'))
      userStore.afterRePwd().then(() => {
        router.push({ name: 'Login' })
      })
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
    padding-top: 1rem;
    height: calc(100dvh - (50px + 70px));
    .top-tip {
      font-size: 1.5em;
      font-weight: 500;
      color: rgba(17, 17, 17, 1);
    }
    .forms {
      .tips {
        padding: 1.2em 0 0.5em 0;
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
}
</style>
