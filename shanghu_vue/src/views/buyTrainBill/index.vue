<script setup>
import {buyTrainBill} from "@/api/index.js";
const router = useRouter();
const onClickLeft = () => {
  router.back();
};
//请求数据
const query = ref({
  page: '1',
  limit: '10'
})
//充值记录列表
const List = ref([])
//请求函数

//滚动加载
const loading = ref(false);
const finished = ref(false);
const onload=()=>{
  buyTrainBill(query.value).then(res=>{
    loading.value = false
    let number = parseInt(query.value.page);
    number++;
    query.value.page = number.toString();
    if (res.data.length <= 0) {
      finished.value = true;
    }
    List.value.push(...res.data.list)
    console.log(List.value)
  })
}

onMounted(() => {
  onload()
})
</script>

<template>
  <header >
    <van-nav-bar
        :title="$t('buyTrainBill.record')"
        :left-text="$t('goback')"
        left-arrow
        fixed
        @click-left="onClickLeft"
    />
  </header>
  <main class="mt-16">
    <van-list

        v-model:loading="loading"
        :finished="finished"
        finished-text="$t('buyTrainBill.nomore')"
        @load="onload()"
        :immediate-check="false"
    >
      <div class="bg-white mx-3 mt-3 rounded-md back_4" v-for="item in List " :key="item.recharge_id">
        <div class="flex justify-between mx-3 py-1.5">
          <div class="text-gray-500">{{ $t("buyTrainBill.type") }}</div>
          <div class="flex items-center">
            <span>{{item.train.name}}</span>
            <van-icon class="pl-1.5" size="24px" name="birthday-cake-o"/>
          </div>
        </div>
        <div class="flex justify-between mx-3 py-1.5">
          <div class="text-gray-500">{{ $t("buyTrainBill.time") }}</div>
          <div class="flex items-center">
            <span>{{ item.createtime }}</span>
          </div>
        </div>
        <div class="flex justify-between mx-3 py-1.5">
          <div class="text-gray-500">{{ $t("buyTrainBill.money") }}</div>
          <div class="flex items-center">
            <span>{{item.money}}</span>
          </div>
        </div>
<!--        <div class="flex justify-between mx-3 py-1.5">-->
<!--          <div class="text-gray-500">支付状态</div>-->
<!--          <div class="flex items-center">-->
<!--            <span class="text-green-500">{{item.paid===1?'已支付':'待支付'}}</span>-->
<!--          </div>-->
<!--        </div>-->
      </div>
    </van-list>
  </main>
</template>

<style scoped lang="scss">

</style>