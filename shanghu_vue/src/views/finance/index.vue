<script setup>
import {finance, fundsRecords, productManagement} from "@/api/index.js";

const router = useRouter();

const onClickLeft = () => history.back();
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();

//弹出选择层
const show = ref(false);
//选择层选项
const actions = [
  {name:  t("finance.all")},
  {name:  t("finance.yesterday")},
  {name:  t("finance.today")},
  {name:  t("finance.week")},
  {name:  t("finance.month")}
];
//定义选项和发送请求数据
const selects = ref("");
const financeQuery = ref({
  range: 'all',
  page: '1',
  limit: '10'
});

//定义加载中提示
const show1 = showLoadingToast({
  message: t("finance.loading"),
  forbidClick: true,
  duration: 0
});

//点击选项函数
const onSelect = (item) => {
  // 默认情况下点击选项时不会自动收起
  // 可以通过 close-on-click-action 属性开启自动收起
  // console.log(item)
  selects.value = item.name;
  // console.log(selects.value);
  show.value = false;
  // showToast(item.name);
  if (selects.value ===t("finance.today")) {
    financeQuery.value.range = "today"
  } else if (selects.value === t("finance.yesterday")) {
    financeQuery.value.range = "yesterday"
  } else if (selects.value === t("finance.week")) {
    financeQuery.value.range = "week"
  } else if (selects.value === t("finance.month")) {
    financeQuery.value.range = "month"
  } else {
    financeQuery.value.range = "all"
  }
  show1.open();
  orderList.value = [];
  financeQuery.value.page = '1';
  finished.value = false;
  loading.value = true;
  onload()
}
//列表下拉刷新
const loading = ref(false);
const finished = ref(false);
const price = ref('')
const allprofit =ref('')
const orderList = ref([]);
const onload = () => {
  finance(financeQuery.value).then(res => {
    show1.close();
    let number = parseInt(financeQuery.value.page);
    number++;
    financeQuery.value.page = number.toString();
    console.log(orderList.value);
    price.value = res.data.total_unreceived
    allprofit.value=res.data.all_profit
    if (res.data.report.length <= 0) {
      // console.log('结束');
      finished.value = true;}
    else {
      orderList.value.push(...res.data.report);
      loading.value = false;
    }

  });
};

//打开选择层
const openshow = () => {
  console.log(111);
  show.value = true;
};
//首屏加载全部
onMounted(() => {
  onload(financeQuery.value)
})
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('finance.finance')"
        left-text=""
        left-arrow
        @click-left="onClickLeft">
      <template #right>
        <van-icon color="#FFFFFF" @click="openshow" size="22px" name="todo-list"/>
      </template>
    </van-nav-bar>
  </header>
  <main>
    <!--收益数据-->
    <div class="grid grid-flow-row grid-cols-2 gap-3 mx-3 mt-3">
      <div
          class=" back_4 h-20 rounded-md flex flex-col justify-between p-3 bg-gradient-to-r from-[#6B6B6B]  to-[#1C1B1B]">
        <div>
          <span class="font-semibold text-white">{{ $t("finance.awaitmomey") }}</span>
        </div>
        <div class="text-right">
          <span class="font-semibold text-white">{{ price }}</span>
        </div>
      </div>
      <div
          class=" back_4 h-20 rounded-md flex flex-col justify-between p-3 bg-gradient-to-r from-[#6B6B6B]  to-[#1C1B1B]">
        <div>
          <span class="font-semibold text-white ">{{ $t("finance.totalprofit") }}</span>
        </div>
        <div class="text-right">
          <span class="font-semibold text-white">{{ allprofit}}</span>
        </div>
      </div>
    </div>
    <div v-if="orderList.length<=0">
      <van-empty />
    </div>
    <van-list
        v-model:loading="loading"
        :finished="finished"
        :finished-text="$t('finance.nomore')"
        @load="onload()"
        :immediate-check="false"
    >
      <div class="bg-white mx-3 mt-3 rounded-md back_4" v-for="item in orderList">
        <!--日期-->
        <div class="flex items-center py-1.5">
        <span class="ml-3">
<!--          <van-icon size="24px" name="notes-o"/>-->
          <icon-park name="calendar-thirty-two-cp5g4c6j" size="1.6rem"/>
        </span>
          <span class="ml-3">{{ $t("finance.data") }}</span>
          <span>{{ item.date }}</span>
          <span class="ml-auto mr-3 ">{{ $t("finance.profit") }} {{ item.total_profit }}</span>
        </div>
        <!--总订单-->
        <div class="flex items-center py-1.5">
        <span class="ml-3">
          <icon-park name="order-coogl6ob" size="1.6rem"/>
        </span>
          <span class="ml-3">{{ $t("finance.totalorder") }}</span>
          <span>{{ item.total_order }}</span>
        </div>
        <!--取消订单-->
        <div class="flex items-center py-1.5">
        <span class="ml-3">
          <icon-park name="close-one" size="1.6rem"/>
        </span>
          <span class="ml-3">{{ $t("finance.cancel_order") }}</span>
          <span>{{ item.cancel_order }}</span>
        </div>
        <!--退款订单-->
        <div class="flex items-center py-1.5">
        <span class="ml-3">
         <icon-park name="bill" size="1.6rem"/>
        </span>
          <span class="ml-3">{{ $t("finance.refund_order") }}</span>
          <span>{{ item.refund_order }}</span>
        </div>
      </div>
    </van-list>
  </main>
  <!--弹出层-->
  <van-action-sheet v-model:show="show" :actions="actions" @select="onSelect"/>
</template>

<style scoped lang="scss">

</style>