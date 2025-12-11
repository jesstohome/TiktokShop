<script setup>
import {buyTrain, train,payPwdconfirm} from "@/api/index.js";
import {useUserStore} from "@/store/modules/user.js";
import {showFailToast} from "vant";
import {useI18n} from "vue-i18n";
const userStore = useUserStore();
const { t } = useI18n();
const router = useRouter();
const onClickLeft = () => {
  router.back();
};


//获取信息请求
const List=ref([])
const getTrain=async()=>{
  const res=await train()
  List.value=res.data
  console.log(res)
}
//购买直通车
const query=ref({
  id:''
})
const tobuy=async(id)=>{
  if(userStore.MerInfo.status===1) {
    query.value.id = id;
    showActionSheet.value = true
  }else{
    showFailToast(t("over"))
  }
}
/*
  支付密码模块
*/
//展现支付密码
const showActionSheet = ref(false)
//定义支付密码
const payPwd = ref({
  password_pay: ''
})

//支付密码确认接口
const order_id = ref({
  order_id: ''
})
const confirm = async () => {
  const res = await payPwdconfirm(payPwd.value)
  // code.value = res.code
  // console.log(code.value)
  if (res.code == 1) {
    buyTrain(query.value).then(res => {
      if (res.code == 1) {
        showActionSheet.value = false
        showSuccessToast(res.msg)
      }
    })
  } else {
    showFailToast(res.msg);
  }
}
//输入完成之后触发
const confirmdata = (data) => {
  payPwd.value.password_pay = data
  console.log(data)
  confirm()
}

onBeforeMount(()=>{
  getTrain()
})
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('storeExpress.storeExpress')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft">
      <template #right>
        <span  class="text-white" @click="router.push('/buyTrainBill')">{{ $t("storeExpress.record") }}</span>
      </template>
    </van-nav-bar>
  </header>
  <main class="mt-4">
    <div class="bg-white  rounded-md mt-3 flex items-center py-3 mx-3 back_4" v-for="item in List">
      <div class="mx-5 flex items-center"><img class="w-[120px]" :src=item.image alt=""></div>
      <div class="mr-3">
        <div class="mb-1.5"><span class="font-semibold text-base">{{ item.name }}</span></div>
        <div><span class="text-sm opacity-80">{{ $t("storeExpress.title1") }}</span></div>
        <div><span class="text-sm opacity-80 mr-3">{{ $t("storeExpress.title2") }}</span><span class="font-semibold">{{ item.order_num }}</span></div>
        <div class="flex justify-between -mt-1.5 text-base items-center"><span class="font-semibold">{{ item.price }}</span><span class="bg-black p-3 px-5 text-white rounded-3xl" @click="tobuy(item.id)">{{ $t("storeExpress.buy") }}</span></div>
      </div>
    </div>
  </main>
  <van-action-sheet :overlay="false" :round="false" v-model:show="showActionSheet">
    <payPassword :price="price1" @close="()=>showActionSheet=false" @send-data="confirmdata"/>
  </van-action-sheet>
  <van-overlay :show="showActionSheet" @click="showActionSheet = false"/>
</template>

<style scoped lang="scss">

</style>