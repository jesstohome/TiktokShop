<template>
    <div class="container">
        <nav-bar :title="$t('comment')" />
        <van-form @submit="handlerSubmit">
            <div class="content">
                <!-- <custom-input 
                    :label="$t('product')"
                    :value="data.good?.title"
                    readonly
                /> -->
                <div>{{ $t('product') }}</div>
                <div>{{ data.good?.title }}</div>
                <van-image style="margin-top:1rem;" :src="data.good?.image" width="8rem" height="8rem" />
                <div class="rate-row">
                    <div>{{ $t('productScore') }}</div>
                    <van-rate v-model="form.product_score" allow-half />
                </div>
                <div class="rate-row">
                    <div>{{ $t('serviceScore') }}</div>
                    <van-rate v-model="form.service_score" allow-half />
                </div>
                <div class="rate-row">
                    <div>{{ $t('postageScore') }}</div>
                    <van-rate v-model="form.postage_score" allow-half />
                </div>
                <custom-input 
                    :label="$t('comment')"
                    :value="form.comment"
                    @blur="val=>form.comment=val"
                    type="textarea"
                    :required="true"
                    :rules="[
                        { required: true, message: $t('commentNotNull'), trigger: 'onSubmit' }
                    ]"
                />
                <div class="pics-title">{{ $t('pics') }}</div>
                <van-uploader
                    ref="uploadRef"
                    v-model="fileList"
                    upload-icon="plus"
                    :after-read="afterRead"
                >
                    <template #preview-cover="{ file }">
                        <div class="preview-cover van-ellipsis">{{ file.name }}</div>
                    </template>
                </van-uploader>
            </div>
            <div class="bottom">
                <van-button color="#191919" round block native-type="submit">{{ $t('submit') }}</van-button>
            </div>
        </van-form>
    </div>
</template>
<script setup>
import toast from '@/utils/toast.js'
import CustomInput from '@/components/Input/index.vue'
import { uploadFile } from '@/api/common.js'
import { comment } from '@/api/order.js'
import { useRouter } from 'vue-router';
const { proxy }=getCurrentInstance();
const route=useRoute();
const router=useRouter();
const data=ref({})
const form=ref({
    product_score:5,
    service_score:5,
    postage_score:5,
    comment:undefined,
    pics:[]
})
const uploadRef = ref(null)
const fileList = ref([])
const afterRead = (file) => {
  toast.loading({ msg: `${proxy.t('uploading')}...` })
  const formData = new FormData()
  formData.append('file', file.file)
  uploadFile(formData)
    .then((res) => {
      toast.success({ msg: `${proxy.t('uploadComplete')}...` })
      form.value.pics.push(res.data.fullurl)
    })
    .catch((err) => {})
    .finally(() => {
      toast.close()
    })
}
const handlerSubmit=()=>{
    toast.loading()
    form.value.order_id=data.value.order_id
    form.value.product_id=data.value.good.product_id
    form.value.pics=form.value.pics.join(',')
    comment(form.value).then((res)=>{
        console.log(res)
        toast.success({ msg: res.msg })
        router.back()
    }).catch(err=>err)
}
onMounted(()=>{
    const _data=JSON.parse(route.query.data)
    data.value=_data
})
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container{
    padding: 0;
    overflow-y: hidden;
    .content{
        height: calc(100dvh - 120px);
        overflow-y: auto;
        .rate-row{
            padding-top:1rem;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
        .pics-title{
            padding: 0.5rem 0;
        }
    }
}
::v-deep(.van-uploader__upload) {
  background: #ffffff;
  width: 6rem;
  height: 6rem;
  border-radius: 1rem;
}
::v-deep(.van-image .van-uploader__preview-cover) {
  width: 6rem;
  height: 5rem;
  border-radius: 1rem;
}
::v-deep(.van-cell) {
    border-radius: 0 !important;
}
</style>