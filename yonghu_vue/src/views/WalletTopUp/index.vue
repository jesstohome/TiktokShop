<template>
  <NavBar :title="$t('rechargeOnChain')"> </NavBar>
  <div class="container">
    <div class="content">
      <main class="mx-3">
        <div class="qrcode-row">
          <canvas style="width: 8rem; height: 8rem" ref="qrcodeRef" />
        </div>
        <!-- <div class="flex justify-center items-center my-2">
          <div class="w-40 h-40 bg-white rounded-md">
            <canvas ref="qrcodeRef" />
          </div>
          <canvas ref="qrcodeRef" />
        </div> -->
        <div class="pt-5 flex items-center">
          <h5 class="text-base font-semibold pr-1">{{ $t('rechargeAddress') }}</h5>
          <van-icon name="arrow" size="1.1rem" />
        </div>
        <div class="bg-white rounded-md mt-3 flex items-center py-3">
          <div class="flex justify-between w-full mx-3">
            <span class="font-normal">{{ form.blockchain }} </span>
            <icon-park name="copy" size="1.4rem" @click.stop="handlerCopy" />
          </div>
        </div>
        <div class="flex mt-3">
          <div>
            <icon-park name="info" size="1.4rem" />
          </div>
          <div class="leading-">
            <span class="opacity-80">{{ $t('notSupportDexBscHeco') }}</span>
          </div>
        </div>
        <h5 class="mt-3 text-base font-semibold">{{ $t('quantity') }}</h5>
        <!-- <input 
          class="bg-white w-full rounded-md mt-3 flex items-center py-3 px-3" 
          placeholder="请输入金额" 
        /> -->
        <custom-input
          style="padding-top: 1rem"
          :value="form.price"
          type="digit"
          @blur="(val) => (form.price = val)"
        />
        <div class="flex items-center mt-3">
          <h5 class="text-base font-semibold pr-1">{{ $t('rechargeNetwork') }}</h5>
          <van-icon name="info" color="" size="1.1rem" />
        </div>
        <div class="bg-white rounded-md mt-3 flex items-center py-3">
          <div class="flex justify-between w-full mx-3 items-center" @click.stop="handleChooseCoin">
            <span class="font-normal">{{ form.network }} </span>
            <van-icon name="arrow-down" />
          </div>
        </div>
        <div class="pt-3">
          <h5 class="mt-3 pb-3 text-base font-semibold">{{ $t('paymentevidence') }}</h5>
          <van-uploader
            ref="uploadRef"
            v-model="fileList"
            :show-upload="showUpload"
            :max-count="1"
            upload-icon="plus"
            :after-read="afterRead"
          >
            <template #preview-cover="{ file }">
              <div class="preview-cover van-ellipsis">{{ file.name }}</div>
            </template>
          </van-uploader>
        </div>
      </main>
    </div>
    <div class="bottom">
      <van-button round block color="#191919" @click="handlerSubmit">{{ $t('submit') }}</van-button>
    </div>
  </div>
  <custom-floating-panel
    ref="floatingPanel"
    height="800px"
    :title="$t('pleaseChooseChargeNetwork')"
    :tip="$t('pleaseChooseSameNetworkCoin')"
  >
    <van-space direction="vertical" size="1rem">
      <div
        class="coin-card"
        v-for="(item, index) in nets"
        :key="index"
        @click.stop="handlerChooseNetwork(item)"
      >
        <div class="name-row">{{ item.network }}</div>
        <div class="other-row">{{ item.blockchain }} USDT</div>
      </div>
    </van-space>
  </custom-floating-panel>
</template>
<script setup>
import QRCode from 'qrcode'
import NavBar from '@/components/CustomNavBar/index.vue'
import CustomInput from '@/components/Input/index.vue'
import CustomFloatingPanel from '@/components/CustomFloatingPanel/index.vue'
import toast from '@/utils/toast.js'
import { blockChain, recharge } from '@/api/user.js'
import { uploadFile } from '@/api/common.js'
import { useRouter } from 'vue-router'
const { proxy } = getCurrentInstance()
const router = useRouter()
const goRecord = () => {
  router.push({ name: 'CapitalRecord' })
}
//  floatingPanel
const floatingPanel = ref(null)
const handleChooseCoin = () => {
  floatingPanel.value.show = true
}
//  floatingPanel
const uploadRef = ref(null)
const fileList = ref([])
const showUpload = computed(() => {
  return !fileList.value.length
})
const afterRead = (file) => {
  toast.loading({ msg: `${proxy.t('uploading')}...` })
  const formData = new FormData()
  formData.append('file', file.file)
  uploadFile(formData)
    .then((res) => {
      toast.success({ msg: `${proxy.t('uploadComplete')}...` })
      form.value.image = res.data.fullurl
    })
    .catch((err) => {})
    .finally(() => {
      toast.close()
    })
}
const form = ref({
  recharge_type: 1,
  blockchain: undefined,
  network: undefined,
  price: 0,
  image: undefined
})
const nets = ref([])
const handlerCopy = async () => {
  if (!form.value.blockchain) {
    toast.show({ msg: proxy.t('pleaseChooseChargeNetwork') })
    return
  }
  try {
    await navigator.clipboard.writeText(form.value.blockchain)
    toast.success({ msg: proxy.t('copied') })
  } catch (error) {
    console.log(error)
    toast.show({ msg: proxy.t('copyFail')+','+proxy.t('notAllowCopy') })
  }
}
const getBlockchain = () => {
  toast.loading()
  blockChain()
    .then((res) => {
      nets.value = res.data
      form.value.blockchain = res.data[0].blockchain
      form.value.network = res.data[0].network
      QRCode.toCanvas(qrcodeRef.value, form.value.blockchain, {}, (err) => {
        console.log(err)
      })
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
const qrcodeRef = ref(null)
const handlerSubmit = () => {
  if (!form.value.blockchain || !form.value.network) {
    toast.show({ msg: proxy.t('pleaseChooseChargeNetwork') })
    return
  }
  if (form.value.price <= 0) {
    toast.show({ msg: proxy.t('pleaseFillCorrectAmount') })
    return
  }
  if (!form.value.image) {
    toast.show({ msg: proxy.t('pleaseUploadPaymentEvidence') })
    return
  }
  toast.loading()
  recharge(form.value)
    .then((res) => {
      router.back()
      toast.success({ msg: res.msg })
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
const handlerChooseNetwork = (net) => {
  form.value.blockchain = net.blockchain
  form.value.network = net.network
  floatingPanel.value.show = false
  QRCode.toCanvas(qrcodeRef.value, form.value.blockchain, {}, (err) => {
    console.log(err)
  })
}
getBlockchain()
</script>
<style lang="scss" scoped>
.container {
  padding: 0 1rem 1rem 1rem;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  overflow-y: hidden;
  .content {
    overflow-y: auto;
    height: calc(100dvh - (50px + 80px));
    display: flex;
    flex-direction: column;
    align-items: stretch;
    .qrcode-row {
      display: flex;
      flex-direction: row;
      justify-content: center;
    }
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

    .preview-cover {
      position: absolute;
      bottom: 0;
      box-sizing: border-box;
      width: 100%;
      padding: 4px;
      color: #fff;
      font-size: 12px;
      text-align: center;
      background: rgba(0, 0, 0, 0.3);
    }
  }
  .bottom {
    height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: stretch;
  }
}
::v-deep(.van-uploader__upload) {
  background: #ffffff;
  width: 8rem;
  height: 8rem;
  border-radius: 1rem;
}
::v-deep(.van-image .van-uploader__preview-cover) {
  width: 8rem;
  height: 8rem;
  border-radius: 1rem;
}
</style>
