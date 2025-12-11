<script setup>
import {baseinfo, upload} from "@/api/index.js";
import {useUserStore} from '@/store/modules/user.js';
import {showSuccessToast} from "vant";

const router = useRouter();
const onClickLeft = () => {
  router.back();
};
//引入仓库信息
const userStore = useUserStore();

const formData1 = ref({
  mer_avatar: '',
  mer_name: userStore.MerInfo.mer_name,
  mer_address: userStore.MerInfo.mer_address,
  mer_phone: userStore.MerInfo.mer_phone,
  mer_info: userStore.MerInfo.mer_info,
  mark: userStore.MerInfo.mark,
});
//上传信息接口
const tobaseinfo = async () => {
  const res = await baseinfo(formData1.value);
  await userStore.toGetMerInfo();
  if (res.code === 1) {
    showSuccessToast(res.msg);
  } else {
    showFailToast(res.msg);
  }
};
//上传图片
const uploadRef = ref(null);
const fileList = ref([]);
const showUpload = computed(() => {
  return !fileList.value.length;
});
const afterRead = file => {
  // toast.loading({msg:'上传中...'})
  const formData = new FormData();
  formData.append('file', file.file);
  upload(formData).then(res => {
    if (res.code === 1) {
      formData1.value.mer_avatar = res.data.fullurl;
      showSuccessToast(res.msg);
    } else {
      showFailToast(res.msg);
    }
  });
};

onMounted(() => {
  console.log(userStore.MerInfo);
  fileList.value = [{url: userStore.MerInfo.mer_avatar}];
});
</script>

<template>
  <header>
    <van-nav-bar
        :left-text="$t('goback')"
        :title="$t('baseinfo.baseinfo')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <div class="mt-4">
      <van-cell-group inset>
        <!-- 输入任意文本 -->
        <div class="flex items-center ml-5 mt-4">
          <div class="mr-10">{{ $t("baseinfo.logo") }}</div>
          <van-uploader
              ref="uploadRef"
              v-model="fileList"
              :after-read="afterRead"
              :max-count="1"
              :show-upload="showUpload"
              upload-icon="plus"
          >
          </van-uploader>
        </div>
        <van-field v-model="formData1.mer_name" :label="$t('baseinfo.name')"
                   :placeholder="userStore.MerInfo.mer_name ? userStore.MerInfo.mer_name:$t('baseinfo.inputname')"
                   type="text"/>
        <van-field v-model="formData1.mer_phone" :label="$t('baseinfo.tel')"
                   :placeholder="userStore.MerInfo.mer_phone ?userStore.MerInfo.mer_phone :$t('baseinfo.inputtel')"
                   type="tel"/>
        <van-field v-model="formData1.mer_address" :label="$t('baseinfo.address')"
                   :placeholder="userStore.MerInfo.mer_address ? userStore.MerInfo.mer_address :$t('baseinfo.inputaddress')"
                   type="text"/>
      </van-cell-group>
      <div class="h-5"></div>
      <van-cell-group class="mt-10" inset>
        <van-field
            v-model="formData1.mer_info"
            :label="$t('baseinfo.introduction')"
            :placeholder="userStore.MerInfo.mer_info ? userStore.MerInfo.mer_info:$t('baseinfo.inputintroduction')"
            autosize
            label-align="top"
            rows="1"
            type="textarea"
        />
      </van-cell-group>
      <div class="h-5"></div>
      <van-cell-group inset>
        <van-field v-model="formData1.mark" :label="$t('baseinfo.mark')"
                   :placeholder="userStore.MerInfo.mark ? userStore.MerInfo.mark :$t('baseinfo.inputmark')"
                   type="text"/>
      </van-cell-group>
    </div>
    <div class="px-5 pt-20">
      <van-button block color="#191919" round @click="tobaseinfo()">{{ $t("baseinfo.submit") }}</van-button>
    </div>
  </main>
</template>

<style lang="scss" scoped>
:deep(.van-uploader__preview-image) {
  border-radius: 50% !important;
}
</style>
