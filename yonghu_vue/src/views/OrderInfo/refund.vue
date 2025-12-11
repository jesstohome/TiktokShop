<template>
  <div class="container">
    <nav-bar :title="$t('appDrawBack')" />
    <van-form @submit="handlerSubmit">
      <div class="content">
        <custom-input :label="$t('orderNo')" readonly :value="form.sn" />
        <custom-input
          :label="$t('refundType')"
          readonly
          :value="form.typeName"
          @click="showChooseType = true"
        >
          <template #right>
            <icon-park name="right" size="1.8rem" />
          </template>
        </custom-input>
        <custom-input
          :label="$t('productStatus')"
          readonly
          :value="form.statusName"
          @click="showChooseStatus = true"
        >
          <template #right>
            <icon-park name="right" size="1.8rem" />
          </template>
        </custom-input>
        <custom-input
          :label="$t('refundAmount')"
          required
          :placeholder="$t('pleaseFillRefundAmount')"
          type="number"
          :value="form.amount"
          @blur="(val) => (form.amount = val)"
          :rules="[{ validator: amountValidator, trigger: 'onSubmit' }]"
        />
        <custom-input
          :label="$t('reason')"
          required
          :placeholder="$t('pleaseFillReason')"
          :value="form.reason_type"
          @blur="(val) => (form.reason_type = val)"
          type="textarea"
          :rules="[{ required: true, message: $t('pleaseFillReason'), trigger: 'onSubmit' }]"
        />
        <custom-input
          :label="$t('explain')"
          required
          :placeholder="$t('pleaseFillExplain')"
          :value="form.refund_explain"
          @blur="(val) => (form.refund_explain = val)"
          type="textarea"
          :rules="[{ required: true, message: $t('pleaseFillExplain'), trigger: 'onSubmit' }]"
        />
        <div class="uploader-label">{{ $t('attachment') }}</div>
        <van-uploader
          ref="uploadRef"
          v-model="fileList"
          upload-icon="plus"
          :after-read="afterRead"
          @delete="handlerDeleteFile"
        >
        </van-uploader>
      </div>
      <div class="bottom">
        <van-button block round color="#191919" native-type="submit">{{ $t('submit') }}</van-button>
      </div>
    </van-form>
    <van-action-sheet v-model:show="showChooseType" :actions="types" @select="handlerChooseType" />
    <van-action-sheet
      v-model:show="showChooseStatus"
      :actions="statuses"
      @select="handlerChooseStatus"
    />
  </div>
</template>
<script setup>
import { refundOrder } from '@/api/order.js'
import { uploadFile } from '@/api/common.js'
import toast from '@/utils/toast.js'
import CustomInput from '@/components/Input/index.vue'
const {proxy}=getCurrentInstance()
const form = ref({
  order_id: undefined,
  sn: undefined,
  amount: undefined,
  service_type: '0',
  typeName: proxy.t('refund(noReturn)'),
  receiving_status: '0',
  statusName: proxy.t('notReceived'),
  reason_type: undefined,
  refund_explain: undefined,
  images: undefined,
  order_product_id: undefined
})
const amountValidator = (val) => {
  if (!val) {
    return proxy.t('pleaseFillRefundAmount')
  } else {
    if (val < 0) {
      return proxy.t('refundAmountCannotBeNagtiative')
    } else {
      return true
    }
  }
}
const showChooseType = ref(false)
const types = ref([
  { name: proxy.t('refund(noReturn)'), value: '0' },
  { name: proxy.t('refundAndReturn'), value: '1' }
])
const handlerChooseType = (item) => {
  showChooseType.value = false
  form.value.service_type = item.value
  form.value.typeName = item.name
}
const showChooseStatus = ref(false)
const statuses = ref([
  { name: proxy.t('notReceived'), value: '0' },
  { name: proxy.t('received'), value: '1' }
])
const handlerChooseStatus = (item) => {
  showChooseStatus.value = false
  form.value.receiving_status = item.value
  form.value.statusName = item.name
}
const fileList = ref([])
const urls = ref([])
const afterRead = (file) => {
  toast.loading()
  const formData = new FormData()
  formData.append('file', file.file)
  uploadFile(formData)
    .then((res) => {
      toast.success({ msg: proxy.t('uploadSuccess') })
      urls.value.push(res.data.fullurl)
    })
    .catch((err) => {})
}
const handlerDeleteFile = (data) => {
  const _url = data.objectUrl.slice(data.objectUrl.indexOf(':') + 1)
  urls.value.splice(urls.value.indexOf(_url), 1)
}
const route = useRoute()
const router = useRouter()
const handlerSubmit = () => {
  if (!urls.value.length) {
    toast.show({ msg: proxy.t('pleaseUploadProof') })
    return
  }
  toast.loading()
  form.value.images = urls.value.join(',')
  refundOrder(form.value)
    .then((res) => {
      toast.success({ msg: proxy.t('applySuccess') })
      router.back()
    })
    .catch((err) => err)
}
onMounted(() => {
  form.value.order_id = route.params.id
  form.value.sn = route.params.sn
  form.value.order_product_id = route.params.product_ids
  form.value.amount = parseFloat(route.params.amount)
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
    .uploader-label {
      padding: 1em 0 0 0;
      font-size: 1em;
      font-weight: 600;
      letter-spacing: 0.07px;
      color: rgba(120, 130, 138, 1);
    }
    ::v-deep(.van-cell) {
      border-radius: 0 !important;
    }
  }
}
::v-deep(.van-uploader__upload) {
  margin-top: 1rem;
  background: #ffffff;
  width: 6rem;
  height: 6rem;
  border-radius: 1rem;
}
::v-deep(.van-image .van-uploader__preview-cover) {
  width: 6rem;
  height: 6rem;
  border-radius: 1rem;
}
::v-deep(.van-uploader__preview) {
  margin: 1rem 1rem 0 0;
}
</style>
