<script setup>
import {useRouter} from "vue-router";
import {banner, upload} from "@/api/index.js";
import {showFailToast, showSuccessToast} from "vant";
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
const router = useRouter()
//返回上级
const onClickLeft = () => {
  router.back();
};
//上传图片接口
const logo1 = ref('')
const logo2 = ref('')
const logo3 = ref('')

const uploadRef1 = ref(null)
const fileList1 = ref([])
const showUpload1 = computed(() => {
  return !fileList1.value.length
})
const uploadRef2 = ref(null)
const fileList2 = ref([])
const showUpload2 = computed(() => {
  return !fileList2.value.length
})
const uploadRef3 = ref(null)
const fileList3 = ref([])
const showUpload3 = computed(() => {
  return !fileList3.value.length
})
const afterRead1 = file => {
  // toast.loading({msg:'上传中...'})
  const formData1 = new FormData();
  formData1.append('file', file.file);
  upload(formData1).then(res => {
    if (res.code === 1) {
      showSuccessToast(res.msg)
      logo1.value = res.data.fullurl
      // console.log(query.value.mer_avatar)
    } else {
      showFailToast(res.msg)
    }

  })
}
const afterRead2 = file => {
  // toast.loading({msg:'上传中...'})
  const formData2 = new FormData();
  formData2.append('file', file.file);
  upload(formData2).then(res => {
    if (res.code === 1) {
      showSuccessToast(res.msg)
      logo2.value = res.data.fullurl
      console.log(logo1.value)
    } else {
      showFailToast(res.msg)
    }
  })
}
const afterRead3 = file => {
  // toast.loading({msg:'上传中...'})
  const formData3 = new FormData();
  formData3.append('file', file.file);
  upload(formData3).then(res => {
    if (res.code === 1) {
      showSuccessToast(res.msg)
      logo3.value = res.data.fullurl
      console.log(logo2.value)
    } else {
      showFailToast(res.msg)
    }
  })
}
const imgs = ref({
  image1: '',
  image2: '',
  image3: ''
})

// 上传横幅接口
const tobanner = async () => {
  imgs.value.image1 = logo1.value
  imgs.value.image2 = logo2.value
  imgs.value.image3 = logo3.value
  const res = await banner(imgs.value)
  if (res.code === 1) {
    showSuccessToast(res.msg);
  } else {
    showFailToast(res.msg);
  }
}
</script>

<template>
  <header>
    <van-nav-bar
        :title= "$t('banner.bannerset')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <div class="flex flex-col ">
      <div class="mx-5 mt-5 mb-2">
        <span>{{ $t("banner.merbanner") }}（1920*300）</span>
      </div>
      <div class=" flex flex-col items-center justify-center pt-3 mx-4 bg-white back_4  rounded">
        <van-uploader
            ref="uploadRef1"
            v-model="fileList1"
            :show-upload="showUpload1"
            :max-count="1"
            upload-icon="plus"
            :preview-size="[320 ,50]"
            :after-read="afterRead1">
          <van-button style="width:100px;height:40px" color="#FFFFFF" type="primary">
            <van-icon class="w-full h-full" name="plus" size="25" color="rgba(0, 0, 0, 0.4)"/>
          </van-button>
          <div class="flex justify-center mt-1 mb-3 "><span class="opacity-40 ">{{ $t("banner.mediabanner") }}</span></div>
        </van-uploader>
      </div>
      <div class="mx-5 mt-5 mb-2">
        <span>{{ $t("banner.merbanner") }}（1920*300）</span>
      </div>
      <div class=" flex flex-col items-center justify-center mx-4 bg-white back_4 pt-3 rounded">
        <van-uploader
            ref="uploadRef2"
            v-model="fileList2"
            :show-upload="showUpload2"
            :max-count="1"
            upload-icon="plus"
            :preview-size="[320 ,50]"
            :after-read="afterRead2"
        >
          <van-button style="width:100px;height:40px" color="#FFFFFF" type="primary">
            <van-icon class="w-full h-full" name="plus" size="25" color="rgba(0, 0, 0, 0.4)"/>
          </van-button>
          <div class="flex justify-center mt-1 mb-3"><span class="opacity-40 ">{{ $t("banner.mediabanner") }}</span></div>
        </van-uploader>
      </div>
      <div class="mx-5 mt-5 mb-2">
        <span>{{ $t("banner.merbanner") }}（1920*300）</span>
      </div>
      <div class=" flex flex-col items-center justify-center mx-4 bg-white back_4 pt-3 rounded">
        <van-uploader
            ref="uploadRef3"
            v-model="fileList3"
            :show-upload="showUpload3"
            :max-count="1"
            upload-icon="plus"
            :preview-size="[320 ,50]"
            :after-read="afterRead3"
        >
          <van-button style="width:100px;height:40px" color="#FFFFFF" type="primary">
            <van-icon class="w-full h-full" name="plus" size="25" color="rgba(0, 0, 0, 0.4)"/>
          </van-button>
          <div class="flex justify-center mt-1 mb-3"><span class="opacity-40 ">{{ $t("banner.mediabanner") }}</span></div>
        </van-uploader>
      </div>
    </div>
    <div class="px-5 pt-10">
      <van-button round block color="#191919" @click="tobanner">{{ $t("banner.submit") }}</van-button>
    </div>
  </main>
</template>

<style scoped lang="scss">
</style>