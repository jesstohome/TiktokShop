<template>
  <div class="container">
    <div class="login-container">
      <div class="to-home-row">
        <div @click="goHome" class="to-home-left">
          <icon-park name="left" size="1.6rem" />
          &nbsp; {{ $t('backToHome') }}
        </div>
        <van-space size="0.25rem">
          {{ language }}
          <icon-park @click="handlerChangeLanguage" name="earth" size="1.6rem" />
        </van-space>
      </div>
      <div class="welcome">{{ $t('welcomeToShop') }}</div>
      <div class="login-type">
        <van-space size="1rem">
          {{ $t('accountLogin') }}
        </van-space>
      </div>
      <div class="login-forms">
        <div class="form-input">
          <custom-input
            :value="form.account"
            @blur="(val) => (form.account = val)"
            :placeholder="$t('placeholderAccount')"
          >
            <template #left>
              <div class="form-label">{{ $t('account') }}</div>
            </template>
          </custom-input>
        </div>
        <div class="form-input pwd-input">
          <custom-input
            :value="form.password"
            @blur="(val) => (form.password = val)"
            :type="showPwd ? 'text' : 'password'"
            :placeholder="$t('placeholderPassword')"
          >
            <template #left>
              <div class="form-label">{{ $t('password') }}</div>
            </template>
            <template #right>
              <icon-park
                :name="showPwd ? 'preview-close-one' : 'preview-open'"
                size="1.5rem"
                @click.stop="showPwd = !showPwd"
              />
            </template>
          </custom-input>
        </div>
        <div class="forget-pwd" @click="handlerForgetPwd">{{ $t('forgetPassword') }}</div>
      </div>
      <div class="login-button">
        <van-button @click.stop="handlerLogin" color="#191919" block round>
          {{ $t('login') }}
        </van-button>
      </div>
      <div @click="handlerRegister" class="go-register">
        {{ $t('register') }}
        <van-icon style="padding-left: 0.5rem" name="arrow" size="1.2rem" />
      </div>
    </div>
    <!-- <div class="social-container">
      <van-divider>社交账号登录</van-divider>
      <div class="social-items">
        <van-image @click="handlerSocialLogin" width="4rem" height="auto" :src="Twitter" />
        <van-image @click="handlerSocialLogin" width="4rem" height="auto" :src="FaceBook" />
        <van-image @click="handlerSocialLogin" width="4rem" height="auto" :src="Google" />
      </div>
    </div> -->
    <choose-language ref="chooseLanguage" />
  </div>
</template>
<script setup name="Login">
// import FaceBook from '@/assets/image/facebook.png'
// import Google from '@/assets/image/google.png'
// import Twitter from '@/assets/image/twitter.png'
import toast from '@/utils/toast.js'
import CustomInput from '@/components/Input/index.vue'
import useUserStore from '@/stores/modules/user.js'
import ChooseLanguage from '@/components/ChooseLanguage/index.vue'
// import { register } from '@/api/user.js'
const { proxy } = getCurrentInstance()
const userStore = useUserStore()
const language = computed(() => {
  return userStore.getLanguageName()
})
const router = useRouter()
const form = ref({
  account: undefined,
  password: undefined
})
const showPwd = ref(false)
const goHome = () => {
  router.replace({ name: 'Home' })
}
const handlerLogin = async () => {
  if (!form.value.account || !form.value.password) {
    toast.show({ msg: proxy.t('pleaseInputAccountAndPassword'), duration: 2000 })
    return
  }
  toast.loading()
  userStore
    .login(form.value)
    .then(() => {
      toast.success({ msg: proxy.t('loginSuccess') })
      router.push({ name: 'Home' })
    })
    .catch((err) => {})
}
const handlerRegister = () => {
  router.push({ name: 'Register' })
}
const handlerForgetPwd = () => {
  router.push({ name: 'ForgotPassword' })
}
const handlerSocialLogin = () => {
  toast.show({ msg: proxy.t('notYetOpen'), duration: 2000 })
}
//  language
const chooseLanguage = ref(null)
const handlerChangeLanguage = () => {
  chooseLanguage.value.show = true
}
//  language
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 2rem 1rem 1rem 1rem;
  height: 100dvh;
  background: #ffffff;
  .login-container {
    flex: 5;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    .to-home-row {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      font-weight: 500;
      font-size: 1rem;
      color: #191919;
      line-height: 1.6rem;
      .to-home-left {
        display: flex;
        flex-direction: row;
      }
    }
    .welcome {
      padding-top: 3rem;
      font-weight: 600;
      font-size: 2rem;
      color: #191919;
      line-height: 2.2rem;
    }
    .login-type {
      padding-top: 3rem;
      font-weight: 400;
      font-size: 1.2rem;
      color: #191919;
      line-height: 1.4rem;
      display: flex;
      flex-direction: row;
      .type-selected {
        font-weight: 600;
        font-size: 1.2rem;
        color: #191919;
        line-height: 1.4rem;
      }
    }
    .login-forms {
      padding-top: 3rem;
      .form-input {
        .form-label {
          font-weight: 400;
          font-size: 1rem;
          color: #191919;
          line-height: 2rem;
          background: #f7f7f7;
          padding-left: 1rem;
        }
        ::v-deep(.van-cell) {
          padding: 0;
          border-radius: 24px;
        }
        ::v-deep(.van-field__control) {
          padding: 10px;
          background: #f7f7f7;
        }
        ::v-deep(.input-field) {
          background: #f7f7f7;
        }
      }
      .pwd-input {
        padding-top: 2rem;
      }
    }
    .forget-pwd {
      padding: 2rem 1rem 0 0;
      font-weight: 400;
      font-size: 0.72rem;
      color: #000000;
      line-height: 0.9rem;
      text-align: right;
    }
    .login-button {
      padding-top: 4rem;
    }
    .go-register {
      padding-top: 3rem;
      font-weight: 500;
      font-size: 1rem;
      color: #191919;
      line-height: 1.6rem;
      display: flex;
      flex-direction: row;
      justify-content: flex-end;
      align-items: center;
    }
  }
  .social-container {
    flex: 2;
    .social-items {
      padding-top: 2rem;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
    }
  }
}
</style>
