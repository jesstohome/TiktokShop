<script setup>
import {fundsRecords, productManagement} from "@/api/index.js";

const router = useRouter();

//弹出选择层
const show = ref(false);
const actions = [
  {name: '全部'},
  {name: '充值订单'},
  {name: '提现订单'},
  {name: '推广订单'},
  {name: '商品佣金'},
  {name: '商品退款'},
  {name: '商品采用'}
];

//定义选项和发送请求数据
const selects = ref("");
const fundsQuery = ref({
  type: 'order',
  page: '1',
  limit: '10'
});

//点击选项函数
const onSelect = (item) => {
  // 默认情况下点击选项时不会自动收起
  // 可以通过 close-on-click-action 属性开启自动收起
  console.log(item)
  selects.value = item.name;
  console.log(selects.value);
  show.value = false;
  showToast(item.name);
  if (selects.value === '充值订单') {
    fundsQuery.value.type = "recharge"
  } else if (selects.value === '提现订单') {
    fundsQuery.value.type = "extract"
  } else {
    fundsQuery.value.type = "order"
  }
  onload(item)
};
//打开选择层
const openshow = () => {
  // console.log(111);
  show.value = true;
};

const onClickLeft = () => {
  router.back();
};
//发送请求获取列表
const orderList = ref([]);

//加载列表
const loading = ref(false);
const finished = ref(false);
const onload = (id) => {
  // if (listQuery.value.switch === '') {
  //   // console.log(111)
  //   return
  // }
  // console.log(111)
  if (id) {
    orderList.value = []
    fundsQuery.value.page = '1'
    finished.value = false
    // loading.value = true
  }

  fundsRecords(fundsQuery.value).then(res => {
    loading.value = false
    let number = parseInt(fundsQuery.value.page);
    console.log(res.data)
    number++;
    fundsQuery.value.page = number.toString();
    if (res.data.length <= 0) {
      finished.value = true;
    }
    orderList.value.push(...res.data)
  })}

//加载提示
// showLoadingToast({
//   message: '加载中...',
//   forbidClick: true,
// });
// onBeforeMount(()=>{
//   showLoadingToast()
// })
onMounted(()=>{
  onload(fundsQuery.value)
})
</script>

<template>
  <header>
    <van-nav-bar
        title="资金记录"
        left-text=""
        left-arrow
        @click-left="onClickLeft">
      <template #right>
        <van-icon @click="openshow" size="22px" name="todo-list"/>
      </template>
    </van-nav-bar>
  </header>
  <van-list

      v-model:loading="loading"
      :finished="finished"
      finished-text="没有更多了"
      :immediate-check="false"
      @load="onload()"
  >
    <section class="bg-white h-24" v-for="item in orderList" :key="item.id">
      <div class="flex">
        <div class="p-3">
          <img class="w-10" src="@/assets/image/收入.png" alt="">
        </div>
        <div class="flex flex-col justify-center">
          <span>{{ item.memo }}</span>
          <span class="text-gray-500 text-sm">{{ item.createtime }}</span>
        </div>
        <div class="flex items-center ml-auto mr-3 text-red-500">
          <span>{{ item.money }}</span>
        </div>
      </div>
    </section>
  </van-list>

  <!--弹出层-->
  <van-action-sheet v-model:show="show" :actions="actions" @select="onSelect"/>
</template>

<style scoped lang="scss">

</style>