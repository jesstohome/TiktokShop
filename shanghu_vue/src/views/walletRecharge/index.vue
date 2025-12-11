<script setup>
// import NavBar from '@/components/CustomNavBar/index.vue'
import QRCode from 'vue-qrcode';
import {getBlockchain, recharge, upload} from "@/api/index.js";
import CustomFloatingPanel from '@/components/CustomFloatingPanel/index.vue'
import {showSuccessToast} from "vant";
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
const router = useRouter();

//返回
const onClickLeft = () => {
  router.back();
};

//  floatingPanel
const floatingPanel = ref(null)
const handleChooseCoin = () => {
  floatingPanel.value.show = true
}
//充值货币类型
const network1 = ref('')
const change = (item,data) => {
  console.log(item)
  floatingPanel.value.show = false
  network1.value = item
  address.value = data;
}
//充值方式请求
const address = ref('');
const blockchaindata = ref([])
const getBlockchaindata = async () => {
  const res = await getBlockchain()
  blockchaindata.value = res.data
  console.log(blockchaindata.value)
  address.value = blockchaindata.value[0]?.blockchain;
}

//复制充值方式
const copy = (textToCopy) => {
  // 创建一个临时的textarea元素
  const textarea = document.createElement('textarea');

  // 将要复制的文本设置为textarea的值
  textarea.value = textToCopy;

  // 将textarea隐藏
  textarea.style.position = 'fixed';
  textarea.style.opacity = 0;

  // 将textarea添加到DOM中
  document.body.appendChild(textarea);

  // 选择textarea中的文本
  textarea.select();

  // 执行复制命令
  document.execCommand('copy');

  // 删除textarea元素
  document.body.removeChild(textarea);

  // showToast('复制成功');
  showToast(t('coposs'));
};

//充值请求
const price1 = ref(0)

const torechargedata = ref({
  image: '',
  price: 0,
  recharge_type: '1',
  currency_type: 'USD',
  network: '',
  blockchain: '',
  real_name: '',
  bank_card: '',
  bank_name: ''
})

//充值金额双向绑定
const changePrice = (e) => {
  price1.value = Number(e.target.value);
  torechargedata.value.price = price1.value
}
//提交
const onSubmit = () => {
  torechargedata.value.network = network1.value
  torechargedata.value.blockchain = address.value
  if(torechargedata.value.image){
  recharge(torechargedata.value).then(res => {
    if (res.code === 1) {
      router.back();
      showSuccessToast(res.msg);
    } else {
      showFailToast(res.msg);
    }
  })
  }else{
    showFailToast(t("walletrecharge.uprechargevoucher"));
  }
}
const validator = (val) => /^[1-9]\d*$/.test(val)

//上传图片
const uploadRef = ref(null)
const fileList = ref([])
const showUpload = computed(() => {
  return !fileList.value.length
})
const afterRead = file => {
  // toast.loading({msg:'上传中...'})
  const formData = new FormData();
  formData.append('file', file.file);
  upload(formData).then(res => {
    if(res.code===1){
      torechargedata.value.image = res.data.fullurl
      showSuccessToast(res.msg)
    }else{
      showFailToast(res.msg)
    }
  })
}

onBeforeMount(() => {
  getBlockchaindata()
})
</script>
<template>
  <!--  <NavBar title="充值">-->
  <!--    <template #right>-->
  <!--      <icon-park style="padding-right:0.5rem;" class="pr-1" name="log" size="1.75rem" />-->
  <!--    </template>-->
  <!--  </NavBar>-->
  <header>
    <van-nav-bar
        :title= "$t('walletrecharge.recharge')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft">
      <template #right>
        <span @click="router.push('/rechargeRecord')" class="text-white">{{ $t("walletrecharge.rechargerecord") }}</span>
      </template>
    </van-nav-bar>
  </header>
  <main class="mx-3">
    <div class="flex justify-center items-center my-5">
      <div class="w-40 h-40 bg-white rounded-md">
        <!--        <vue-qrcode :size="qrCodeSize" :value="address"></vue-qrcode>-->
        <QRCode :value="address" :size="150"></QRCode>
      </div>
    </div>
    <div class="flex items-center">
      <h5 class="text-base font-semibold pr-1">{{ $t("walletrecharge.rechargemethod") }}</h5>
      <van-icon name="arrow" size="1.1rem"/>
    </div>
    <div class="bg-white rounded-md mt-3 flex items-center py-3">
      <div class="flex justify-between w-full mx-3">
        <span class="font-normal">{{ address }} </span>
        <icon-park name="copy" size="1.4rem" @click="copy(address)"/>
      </div>
    </div>
    <div class="flex mt-3">
      <div>
        <icon-park name="info" size="1.4rem"/>
      </div>
      <div class="leading-">
        <span class="opacity-80">{{ $t("walletrecharge.title1") }}</span>
      </div>
    </div>
    <van-form @submit="onSubmit()">
      <h5 class="mt-3 text-base font-semibold">{{ $t("walletrecharge.number") }}</h5>
      <!--        <input class="bg-white w-full rounded-md mt-3 flex items-center py-3 px-3" @input="changePrice"-->
      <!--               placeholder="请输入金额" required />-->
      <van-field class="bg-white w-full rounded-md mt-3 flex items-center py-3 px-3" @input="changePrice"
                 v-model="price1"
                 :placeholder="$t('walletrecharge.inputmoney')"
                 :rules="[{ required: true, message: $t('walletrecharge.inputmoney') },{  validator ,message: $t('walletrecharge.nozero')}]"/>
      <div class="flex items-center mt-3">
        <h5 class="text-base font-semibold pr-1">{{ $t("walletrecharge.rechargemethod") }}</h5>
        <van-icon name="info" color="" size="1.1rem"/>
      </div>
      <div class="bg-white rounded-md mt-3 flex items-center py-3">
        <div class="flex justify-between w-full mx-3 items-center" @click.stop="handleChooseCoin">
          <span class="font-normal">{{ network1 ? network1 : "USDT-TRC20" }} </span>
          <van-icon name="arrow-down"/>
        </div>
      </div>
      <div class="flex flex-col mt-3">
        <h5 class="text-base font-semibold pr-1">{{ $t("walletrecharge.rechargevoucher") }}</h5>
        <!--        <van-icon name="info" color="" size="1.1rem"/>-->
        <van-uploader
            ref="uploadRef"
            v-model="fileList"
            :show-upload="showUpload"
            :max-count="1"
            upload-icon="plus"
            :after-read="afterRead"
        >
        </van-uploader>
      </div>
      <div class="pt-10">
        <van-button native-type="submit" round block color="#191919">{{ $t("walletrecharge.submit") }}</van-button>
      </div>
    </van-form>
  </main>
  <custom-floating-panel
      ref="floatingPanel"
      height="800px"
  >
    <div class="coins-container">
      <div class="title-row">
        {{ $t("walletrecharge.title2") }}
      </div>
      <div class="tip-row">
        {{ $t("walletrecharge.title3") }}
      </div>
      <van-space direction="vertical" size="1rem">
        <div
            class="coin-card"
            v-for="(item,index) in blockchaindata"
            :key="index"
            @click="change(item.network,item.blockchain)"
        >
          <div class="name-row">{{ item.network }}</div>
          <div class="other-row">{{ $t("walletrecharge.title4") }} 0.00000001 USDT</div>
          <div class="other-row">{{ $t("walletrecharge.title5") }}</div>
        </div>
      </van-space>
    </div>
  </custom-floating-panel>
</template>
<style lang="scss" scoped>
.coins-container {
  padding: 0 1rem 1rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: stretch;

  .title-row {
    padding-top: 1rem;
    text-align: center;
    font-weight: 500;
    font-size: 1.2rem;
    color: #191919;
    line-height: 1rem;
  }

  .tip-row {
    padding: 1rem 0;
    display: flex;
    flex-direction: row;
    align-items: center;
    font-weight: 400;
    font-size: 0.8rem;
    color: #191919;
    line-height: 1rem;
  }

  .coin-card {
    background: #ffffff;
    box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.08);
    border-radius: 4px 4px 4px 4px;
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    align-items: flex-start;

    .name-row {
      font-weight: 500;
      font-size: 1rem;
      color: #191919;
      line-height: 1.2rem;
      padding-bottom: 0.3rem;
    }

    .other-row {
      font-weight: 400;
      font-size: 0.8rem;
      color: #191919;
      line-height: 1rem;
    }
  }
}
</style>