<script setup>
import {useRouter} from 'vue-router';
import {getwithdrawInfo, withdraw, payPwdconfirm} from "@/api/index.js";
import {useI18n} from 'vue-i18n';
import {useUserStore} from "@/store/modules/user.js";
import {showFailToast} from "vant";
//多语言
const {t} = useI18n();
// import {  } from "@/components/payPassword/index.vue"
const router = useRouter();
const onClickLeft = () => {
  router.back();
};
const userStore = useUserStore();
/*
  选项卡模块
*/
//提现方式选项卡
const showtype = ref(false);
const actionstype = [
  {name: t("withdraw.card")},
  {name: t("withdraw.blockchain")},
];
const extracttype = ref('');
const onSelecttype = (item) => {
  showtype.value = false;
  console.log(item);
  extracttype.value = item.name;
};
//选择货币选项卡
const showcurrency = ref(false);
const actionscurrency = [
  // { name: 'RMB' },
  {name: 'USD'},
];
const currencytype = ref('');
const onSelectcurrency = (item) => {
  showcurrency.value = false;
  console.log(item);
  currencytype.value = item.name;
};
//区块链网络和地址选项卡
const shownetwork = ref(false);
const actionsnetwork = [
  {name: 'USDT-TRC20'},
  {name: 'USDT-ERC20'},
];
const networktype = ref('');
const blockchaintype = ref('');
const onSelectnetwork = (item) => {
  shownetwork.value = false;
  console.log(item);
  networktype.value = item.name;
  if (networktype.value === 'USDT-TRC20') {
    blockchaintype.value = '';
  } else {
    blockchaintype.value = '';
  }
};


//提交参数
const price1 = ref(0);
const changePrice = (e) => {
  price1.value = Number(e.target.value);
};

function formatNumber(num) {
  if (!num) return '0'
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

//提现信息请求
const info = ref({});
const huilv = ref({});
const getWithdrawData = async () => {
  const res = await getwithdrawInfo();
  console.log(res);
  info.value = res.data;
  huilv.value = res.data.huilv;
};
//校验函数
const validator1 = (val) => {
  if (val <= false) {
    return false;
  }
};
const validator2 = (val) => {
  if (val > info.value.balance) {
    return false;
  }
};
//控制余额小数点
const formattedPrice = computed(() => {
  if (price1.value) {
    return (price1.value * (1 - info.value.service_charge)).toFixed(2);
  } else {
    return '0.00';
  }
});
//提交接口
const withdrawQuery = ref({
  extract_price: price1,
  extract_type: "",
  currency_type: "",
  network: "",
  blockchain: "",
  real_name: "",
  bank_card: "",
  bank_name: ""
});
//提交提现请求
const onSubmit = () => {
  if (userStore.MerInfo.status === 1) {
    showActionSheet.value = true;
    if (extracttype.value === t("withdraw.card")) {
      withdrawQuery.value.extract_type = '0';
    } else {
      withdrawQuery.value.extract_type = '1';
    }
    withdrawQuery.value.currency_type = currencytype.value;
    withdrawQuery.value.network = networktype.value;
    withdrawQuery.value.blockchain = blockchaintype.value;
  } else {
    showFailToast(t("over"));
  }
};

/*
  支付密码模块
*/
//展现支付密码
const showActionSheet = ref(false);
//定义支付密码
const payPwd = ref({
  password_pay: ''
});
//支付密码确认接口
const confirm = async () => {
  const res = await payPwdconfirm(payPwd.value);
  if (res.code == 1) {
    showSuccessToast(res.msg);
    withdraw(withdrawQuery.value).then(res => {
      if (res.code === 1) {
        showSuccessToast(res.msg);
        showActionSheet.value = false;
      } else {
        showFailToast(res.msg);
      }
    });
  } else if (res.code == 0) {
    showFailToast(res.msg);
  } else {
    showFailToast(res.msg);
    router.push('/setPaypwd');
  }
};
//输入完成之后触发
const confirmdata = (data) => {
  payPwd.value.password_pay = data;
  console.log(data);
  confirm();
};

onBeforeMount(() => {
  getWithdrawData();
});
</script>

<template>
  <header>
    <van-nav-bar
        :left-text="$t('goback')"
        :title="$t('withdraw.withdrawal')"
        left-arrow
        @click-left="onClickLeft"
    >
      <template #right>
        <span class="text-white" @click="router.push('/withdrawRecord')">{{ $t("withdraw.withdrawalRecords") }}</span>
      </template>
    </van-nav-bar>
  </header>
  <main class="mt-3 mx-3">
    <van-form class="bg-white">
      <div>
        <van-cell :title="$t('withdraw.withdrawalMethod')" center style="font-size: 20px"/>
        <van-field v-model="extracttype" :placeholder="$t('withdraw.enterWithdrawalMethod')"
                   :rules="[{ required: true, message: $t('withdraw.enterWithdrawalMethod') }]"
                   readonly
                   @focus="showtype=true"/>
      </div>
      <div>
        <van-cell :title="$t('withdraw.selectCurrency')" center style="font-size: 20px"/>
        <van-field v-model="currencytype" :placeholder="$t('withdraw.enterCurrencyType')"
                   :rules="[{ required: true, message: $t('withdraw.enterCurrencyType') }]"
                   readonly
                   @focus="showcurrency=true"/>
      </div>
      <div v-if="extracttype===$t('withdraw.blockchain')">
        <van-cell :title="$t('withdraw.blockchainNetwork')" center style="font-size: 20px"/>
        <van-field v-model="networktype" :placeholder="$t('withdraw.enterBlockchainNetwork')"
                   :rules="[{ required: true, message: $t('withdraw.enterBlockchainNetwork') }]"
                   readonly
                   @focus="shownetwork=true"/>
      </div>
      <div v-if="extracttype===$t('withdraw.card')">
        <van-cell :title="$t('withdraw.fullName')" center style="font-size: 20px"/>
        <van-field v-model="withdrawQuery.real_name" :placeholder="$t('withdraw.enterFullName')"
                   :rules="[{ required: true, message: $t('withdraw.enterFullName') }]"/>
      </div>
      <div v-if="extracttype===$t('withdraw.card')">
        <van-cell :title="$t('withdraw.bankName')" center style="font-size: 20px"/>
        <van-field v-model="withdrawQuery.bank_name" :placeholder="$t('withdraw.enterBankName')"
                   :rules="[{ required: true, message: $t('withdraw.enterBankName') }]"/>
      </div>
      <div v-if="extracttype===$t('withdraw.blockchain')">
        <van-cell :title="$t('withdraw.blockchainAddress')" center style="font-size: 20px"/>
        <van-field v-model="blockchaintype" :placeholder="$t('withdraw.enterBlockchainAddress')"
                   :rules="[{ required: true, message: $t('withdraw.enterBlockchainAddress') }]"/>
      </div>
      <div v-if="extracttype===$t('withdraw.card')">
        <van-cell :title="$t('withdraw.bankCardNumber')" center style="font-size: 20px"/>
        <van-field v-model="withdrawQuery.bank_card" :placeholder="$t('withdraw.enterBankCardNumber')"
                   :rules="[{ required: true, message: $t('withdraw.enterBankCardNumber') }]"/>
      </div>
      <div>
        <van-cell :title="$t('withdraw.amount')" center style="font-size: 20px"/>
        <van-field v-model="price1" :placeholder="$t('withdraw.enterAmount')"
                   :rules="[{ required: true, message: $t('withdraw.enterAmount') },{ validator:validator1 ,message:  $t('withdraw.nozero')}]"
                   @input="changePrice">
          <template #button>
            <span class="text-blue-500" @click="price1=info.balance">{{ $t("withdraw.all") }}</span>
          </template>
        </van-field>
      </div>
      <div class="flex text-sm mt-3 text-green-500 justify-between mx-3">
        <span>{{ $t("withdraw.currentBalance") }}{{ info.balance }}USD</span>
        
		<span>
		{{ $t("withdraw.fee") }}{{ info.service_charge }}
		</span>
      </div>
	  <div class="flex text-blue-500 justify-between mt-3 mx-3 text-sm" v-if="extracttype===$t('withdraw.card')">
	    <span>
	     1USD ≈ {{ formatNumber(huilv.value) }} {{ huilv.tip }}
	    </span>
	  </div>
      <div class="flex text-blue-500 mt-3 mx-3 text-sm" v-if="extracttype===$t('withdraw.card')">
        <span>
         {{ $t("withdraw.actualAmountReceived") }} ≈ {{ formatNumber((price1 * huilv.value).toFixed(2)) }} {{ huilv.tip }}
        </span>
        
      </div>
      <div class="bg-black mx-3 mt-3 flex justify-center items-center rounded-md " @click="onSubmit">
        <van-button native-type="submit">{{ $t("withdraw.submit") }}</van-button>
      </div>
      <div class="h-5"></div>
    </van-form>


  </main>
  <!--  支付密码-->
  <van-action-sheet v-model:show="showActionSheet" :overlay="false" :round="false">
    <payPassword :price="price1" @close="()=>showActionSheet=false" @send-data="confirmdata"/>
  </van-action-sheet>
  <van-overlay :show="showActionSheet" @click="showActionSheet = false"/>
  <!--提现方式-->
  <van-action-sheet v-model:show="showtype" :actions="actionstype" :overlay="true" @select="onSelecttype"/>
  <!--  选择货币-->
  <van-action-sheet v-model:show="showcurrency" :actions="actionscurrency" :overlay="true" @select="onSelectcurrency"/>
  <!--  充值网络-->
  <van-action-sheet v-model:show="shownetwork" :actions="actionsnetwork" :overlay="true" @select="onSelectnetwork"/>
</template>


<style lang="scss" scoped>
.van-button--default {
  color: white;
  background-color: black;
  border: 0 solid white;
}
</style>
