<template>
  <div class="container">
    <nav-bar :title="$t('addressManage')" />
    <van-form @submit="handlerPut">
      <div class="content">
        <custom-input
          :required="true"
          :label="$t('addressee')"
          :placeholder="$t('placeholderAddressee')"
          :value="form.name"
          @blur="(val) => (form.name = val)"
          :rules="[{ required: true, message: $t('placeholderAddressee'), trigger: 'onSubmit' }]"
        />
        <custom-input
          :required="true"
          :label="$t('mobilePhone')"
          :placeholder="$t('placeholderMobilePhone')"
          @blur="(val) => (form.mobile = val)"
          :value="form.mobile"
          :rules="[{ validator: mobileValidator, trigger: 'onSubmit' }]"
        >
        </custom-input>
        <custom-input
          :required="true"
          :label="$t('area')"
          :placeholder="$t('pleaseFillArea')"
          :rules="[{ required: true, message: $t('pleaseFillArea'), trigger: 'onSubmit' }]"
          :value="form.country"
          @blur="(val) => (form.country = val)"
        >
        </custom-input>
        <custom-input
          :required="true"
          :label="$t('detailedAddress')"
          :placeholder="$t('pleaseFillDetailedAddress')"
          type="textarea"
          :value="form.detail"
          @blur="(val) => (form.detail = val)"
          :rules="[
            { required: true, message: $t('pleaseFillDetailedAddress'), trigger: 'onSubmit' }
          ]"
        />
        <custom-input
          :required="true"
          :label="$t('tag')"
          :placeholder="$t('pleaseFillTag')"
          :value="form.tag"
          @blur="(val) => (form.tag = val)"
          :rules="[{ required: true, message: $t('pleaseFillTag'), trigger: 'onSubmit' }]"
        />
      </div>
      <div class="bottom">
        <van-button block round color="#191919" native-type="submit"> {{ $t('save') }} </van-button>
      </div>
    </van-form>
  </div>
</template>
<script setup>
import CustomInput from '@/components/Input/index.vue'
import { addAddress, getAddressInfo } from '@/api/user.js'
import toast from '@/utils/toast.js'
// import { regMobile } from '@/utils/regExp.js'
const { proxy } = getCurrentInstance()
const form = ref({
  name: undefined,
  mobile: undefined,
  province_id: undefined,
  city_id: undefined,
  area_id: undefined,
  detail: undefined,
  tag: undefined,
  is_default: 0,
  country: undefined
})
const mobileValidator = (val) => {
  if (!val) {
    return proxy.t('placeholderMobilePhone')
  } else {
    // if (!regMobile(val)) {
    //   return proxy.t('pleasFillCorrectMobilePhoneNumber')
    // }
    return true
  }
}
const router = useRouter()
const handlerPut = () => {
  toast.loading()
  addAddress(form.value)
    .then((res) => {
      toast.success({ msg: proxy.t('saveSuccess') })
      router.back()
    })
    .catch((err) => err)
}
const route = useRoute()
onMounted(() => {
  if (route.params?.id) {
    toast.loading()
    getAddressInfo({ address_id: route.params.id })
      .then((res) => {
        form.value = res.data
      })
      .catch((err) => err)
      .finally(() => {
        toast.close()
      })
  }
})
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;
  .content {
    overflow-y: auto;
    height: calc(100dvh - 120px);
    ::v-deep(.van-cell) {
      border-radius: 0 !important;
    }
    .bottom {
      position: sticky;
      bottom: 0;
    }
  }
}
</style>
