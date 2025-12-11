<template>
  <div class="container">
    <nav-bar :title="$t('draw')">
      <template #right>
        <div style="font-size: 1.2rem; padding-right: 1rem" @click.stop="goRecord">
          {{ $t('record') }}
        </div>
      </template>
    </nav-bar>
    <van-form @submit="handlerSubmit">
      <div class="content">
        <Custom-Input
          :label="$t('drawType')"
          :value="typeName"
          readonly
          @click="handlerShowChooseType"
        >
          <template #right>
            <icon-park @click.stop="handlerShowChooseType" name="right" size="1.5rem" />
          </template>
        </Custom-Input>
        <custom-input
          :label="priceLabel"
          :placeholder="$t('pleaseInutDrawAmount')"
          required
          :value="form.price"
          type="number"
          @blur="formatNumber"
          :rules="[{ validator: priceValidator, trigger: 'onSubmit' }]"
        />
        <template v-if="form.extract_type === 0">
			<custom-input
			  :label="$t('realname')"
			  :placeholder="$t('pleaserealname')"
			  required
			  defa
			  :value="form.real_name"
			  @blur="(val) => (form.real_name = val)"
			  :rules="[{ required: true, message: $t('pleaserealname'), trigger: 'onSubmit' }]"
			/>
          <custom-input
            :label="$t('bankCardNo')"
            :placeholder="$t('placeholderBankCardNo')"
            required
            defa
            :value="form.bank_card"
            @blur="(val) => (form.bank_card = val)"
            :rules="[{ required: true, message: $t('placeholderBankCardNo'), trigger: 'onSubmit' }]"
          />
          <custom-input
            :label="$t('bankName')"
            :placeholder="$t('placeholderBankName')"
            required
            :value="form.bank_name"
            @blur="(val) => (form.bank_name = val)"
            :rules="[{ required: true, message: $t('placeholderBankName'), trigger: 'onSubmit' }]"
          />
        </template>
        <template v-else>
          <Custom-Input
            :label="$t('rechargeNetwork')"
            :placeholder="$t('pleaseChooseChargeNetwork')"
            required
            :value="form.network"
            readonly
            :rules="[
              { required: true, message: $t('pleaseChooseChargeNetwork'), trigger: 'onSubmit' }
            ]"
            @click="handlerShowNetwork"
          >
            <template #right>
              <icon-park name="right" size="1.5rem" @click="handlerShowNetwork" />
            </template>
          </Custom-Input>
          <Custom-Input
            :label="$t('rechargeAddress')"
            :placeholder="$t('pleaseFillRechargeAddress')"
            :value="form.blockchain"
            @blur="(val) => (form.blockchain = val)"
            :rules="[
              { required: true, message: $t('pleaseFillRechargeAddress'), trigger: 'onSubmit' }
            ]"
          >
          </Custom-Input>
        </template>
        <div class="tips">
          <div class="balance">{{ $t('balance') }}: {{ info.balance }}</div>
          <div @click="form.price = info.balance">{{ $t('drawAll') }}</div>
        </div>
      </div>
      <div class="bottom">
        <van-button round block color="#000000" native-type="submit">{{ $t('submit') }}</van-button>
      </div>
    </van-form>
  </div>
  <van-action-sheet v-model:show="showChooseType" :actions="types" @select="handlerChooseType" />
  <van-action-sheet v-model:show="showChooseCard" :actions="cards" @select="handlerChooseCard" />
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
      </div>
    </van-space>
  </custom-floating-panel>
</template>

<script setup>
import CustomInput from '@/components/Input/index.vue'
import { deposit, depositInfo } from '@/api/user.js'
import toast from '@/utils/toast.js'
import { multiply } from '@/utils/math.js'
const { proxy } = getCurrentInstance()
const router = useRouter()
const goRecord = () => {
  router.push({ name: 'DepositRecord' })
}
const types = ref([
  { name: proxy.t('bankCard'), value: 0 },
  { name: proxy.t('rechargeOnChain'), value: 1 }
])
const priceValidator = (val) => {
  const _val = Number(val)
  if (_val <= 0) {
    return proxy.t('drawAmountCannotLessThan0OrEmpty')
  }
  if (_val > info.value.balance) {
    return proxy.t('drawAmountCannotLargeThanBalance')
  }
  return true
}
const info = ref({})
const form = ref({
  extract_type: 0,
  price: undefined,
  blockchain: undefined,
  network: undefined,
  bank_card: undefined,
  bank_name: undefined,
  real_name:undefined
})
const priceLabel = computed(() => {
  const _val = multiply(info.value.service_charge || 0.03, 100).toFixed(2) + '%'
  return proxy.t('drawAmount') + `(${proxy.t('serviceCharge')} ${_val})`
})
const formatNumber = (val) => {
  form.value.price = Number(val).toFixed(2)
}
const typeName = computed(() => {
  const _type = types.value.find((item) => item.value === form.value.extract_type)
  return _type ? _type.name : ''
})
const showChooseType = ref(false)
const handlerShowChooseType = () => {
  showChooseType.value = true
}
const handlerChooseType = (val) => {
  form.value.extract_type = val.value
  form.value.price = undefined
  form.value.blockchain = undefined
  form.vaue.netwrok = undefined
  form.value.bank_card = undefined
  form.value.bank_name = undefined
  form.value.real_name = undefined
  showChooseType.value = false
}
const cards = computed(() => {
  if (info.value.card_list) {
    return info.value.card_list.map((item) => {
      return {
        name: item.bank_name,
        value: item.bank_card,
		real_name: item.real_name
      }
    })
  }
  return []
})
const showChooseCard = ref(false)
const handlerShowChooseCard = () => {
  showChooseCard.value = true
}
const handlerChooseCard = (val) => {
  form.value.bank_card = val.value
  form.value.bank_name = val.name
  form.value.real_name = val.real_name
  showChooseCard.value = false
}
const nets = ref([{ network: 'USDT-TRC20' }, { network: 'USDT-ERC20' }])
const floatingPanel = ref(null)
const handlerShowNetwork = () => {
  floatingPanel.value.show = true
}
const handlerChooseNetwork = (net) => {
  form.value.network = net.network
  floatingPanel.value.show = false
}
const handlerSubmit = () => {
  toast.loading()
  deposit(form.value)
    .then((res) => {
      router.back()
      toast.success({ msg: proxy.t('drawSuccess') })
    })
    .catch((err) => err)
}
const getData = () => {
  toast.loading()
  depositInfo()
    .then((res) => {
      info.value = res.data
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
getData()
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: auto;
  .content {
    overflow-y: auto;
    .tips {
      padding-top: 1.5rem;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      font-weight: 500;
      font-size: 0.86rem;
      color: #191919;
      line-height: 1rem;
      .balance {
        font-weight: 400;
      }
    }
    height: calc(100dvh - 120px);
    ::v-deep(.van-cell) {
      border-radius: 0 !important;
    }
  }
}
</style>
