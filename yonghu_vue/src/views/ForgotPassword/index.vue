<template>
  <div class="container">
    <nav-bar :title="$t('loginPassword')" />
    <van-form @submit="handlerChangePwd">
      <div class="content">
        <div class="forms">
          <div class="tips">
            <div>
              <van-icon name="success" />
              &nbsp; {{ $t('passwordLengthAtLeast6') }}
            </div>
          </div>
          <Custom-Input
            :label="$t('mobilePhone')"
            :value="form.mobile"
            @blur="(val) => (form.mobile = val)"
            type="digit"
            :placeholder="$t('placeholderMobilePhone')"
            :rules="[{ validator: mobileValidator, trigger: 'onSubmit' }]"
          >
          </Custom-Input>
          <Custom-Input
            :label="$t('newPassword')"
            :value="form.newPassword"
            @blur="(val) => (form.newPassword = val)"
            :type="showNewPwd ? 'text' : 'password'"
            :placeholder="$t('placeholderNewPassword')"
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
            :placeholder="$t('pleaseConfirmPassword')"
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
        <van-button round block color="#000000" native-type="submit"> Submit </van-button>
      </div>
    </van-form>
  </div>
</template>
<script setup>
import NavBar from '@/components/CustomNavBar/index.vue'
import CustomInput from '@/components/Input/index.vue'
import toast from '@/utils/toast.js'
import { resetpwd } from '@/api/user.js'
import useUserStore from '@/stores/modules/user.js'
const { proxy } = getCurrentInstance()
const userStore = useUserStore()
const form = ref({
  mobile: undefined,
  newPassword: undefined,
  repeatPwd: undefined
})
const showPwd = ref(false)
const showNewPwd = ref(false)
const showRepNewPwd = ref(false)
const mobileValidator = (val) => {
  if (!val) {
    return proxy.$t('placeholderMobilePhone')
  }
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
    newpassword: form.value.newPassword,
    mobile: form.value.mobile
  })
    .then((res) => {
      toast.success()
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
