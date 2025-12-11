<script setup>
import {recharge} from '@/api/index.js'
const router = useRouter();

const onClickLeft = () => {
  router.back();
};
//上传参数
const price1=ref()
const changePrice=(e)=>{
  price1.value=Number(e.target.value);
}
const rechargeQuery=ref({
  price:price1,
  recharge_type:'',
  currency_type:'',
})
//提交充值请求
const onSubmit=async()=>{
  const res=await recharge(rechargeQuery.value)
  console.log(res)
}
//校验函数
const validator = (val) =>  /^[1-9]\d*$/.test(val)
</script>

<template>
  <header>
    <van-nav-bar
        title="充值"
        left-text="返回"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main class="mt-3 mx-3">
    <van-form @submit="onSubmit" class="bg-white">
      <div>
        <van-cell title="充值类型*" center style="font-size: 20px"/>
        <van-field placeholder="请输入充值方式" v-model="rechargeQuery.recharge_type" :rules="[{ required: true, message: '请填写充值方式' }]"/>
      </div>
      <div>
        <van-cell title="选择货币*" center style="font-size: 20px"/>
        <van-field placeholder="请输入货币类型" v-model="rechargeQuery.currency_type" :rules="[{ required: true, message: '请填写货币类型' }]"/>
      </div>
      <div>
        <van-cell title="区块链网络" center style="font-size: 20px"/>
        <van-field placeholder="请输入区块链网络"/>
      </div>
      <div>
        <van-cell title="地址" center style="font-size: 20px"/>
        <van-field placeholder="请输入地址"/>
      </div>
      <div>
        <van-cell title="金额*" center style="font-size: 20px"/>
        <van-field placeholder="请输入金额" v-model="price1" @input="changePrice" :rules="[{ required: true, message: '请填写金额' },{  validator ,message: '不能为0'}]" >
          <template #button>
            <span class="text-blue-500">全部</span>
          </template>
        </van-field>
      </div>
      <div class="text-sm mt-3 text-green-500 mx-3">当前余额:
        <span>956,702.91</span>
        USDT ≈
        <span>956,702.91</span>
        USDC
      </div>
      <div class="flex text-blue-500 justify-between mt-3 mx-3 text-sm">
        <span>
          实际到账金额:0.00USDC
        </span>
        <span>
          手续费:3.00%
        </span>
      </div>
      <div class="bg-black mx-3 mt-3 flex justify-center items-center rounded-md ">
        <van-button native-type="submit"> 提交</van-button>
      </div>
      <div class="h-5"></div>
    </van-form>
  </main>
</template>

<style scoped lang="scss">
.van-button--default {
  color: white;
  background-color: black;
  border: 0 solid white;
}
</style>