<script setup>
import tabbers from '@/components/tabbar/index.vue';
import {addAll, orderManagement, payPwdconfirm, pick, pickAll, productManagement} from "@/api/index.js";
import {useUserStore} from '@/store/modules/user.js';
import {useI18n} from 'vue-i18n';
import {showFailToast} from "vant";
//多语言
const {t} = useI18n();
const router = useRouter();
const userStore = useUserStore();
/*
  tab标签模块
*/
const tabs = ref([{
  status: "all",
  title: t("order.all")
},
  {
    status: "0",
    title: t("order.waitingForPayment")
  },
  {
    status: "5",
    title: t("order.awaitingPickup")
  },
  {
    status: "1",
    title: t("order.waitingForShipment")
  },
  {
    status: "2",
    title: t("order.shipped")
  },
  {
    status: "3",
    title: t("order.received")
  },
  {
    status: "4",
    title: t("order.completed")
  },
  {
    status: "-1",
    title: t("order.canceled")
  }]);

//传递标签参数
const tabname = ref('');
const onClickTab = ({name}) => {
  tabname.value = name;
  loading.value = true;
  search.value = null;
  onload(name);
  console.log(name);
};

/*
  请求数据模块
*/

const List = ref([]);
const listQuery = ref({
  status: 'all',
  page: '1',
  limit: '4',
});
const loading = ref(false);
const finished = ref(false);
const totalprice = ref('');
const totalprofit = ref('');


const search = ref('');//搜索框
//触底加载
const onload = (id) => {
  //如果传入标签数值 让数组为空...
  if (id || id === 0) {
    List.value = [];
    listQuery.value.page = '1';
    listQuery.value.status = id;
    finished.value = false;
    // loading.value = true
  }
  if (search.value) {
    listQuery.value.search = search.value;
    finished.value = true;
    listQuery.value.page = null;
  } else {
    finished.value = false;
    listQuery.value.search = null;
  }
  //发送获取列表请求
  orderManagement(listQuery.value).then(res => {
    totalprice.value = res.data.total_price;
    totalprofit.value = res.data.total_profit;
    console.log(totalprice.value);
    console.log(totalprofit.value);
    loading.value = false;
    //转换page为number后增加
    let number = parseInt(listQuery.value.page);
    number++;
    listQuery.value.page = number.toString();
    if (res.data.list.length <= 0) {
      finished.value = true;
    }
    List.value.push(...res.data.list);
    //添加待提货中运输状态
    List.value.forEach(item => {
      if (item.delivery_status === 1) {
        item.deliverystatus = t("order.inTransit");
      } else if (item.delivery_status === 2) {
        item.deliverystatus = t("order.reached");
      } else if (item.delivery_status === 3) {
        item.deliverystatus = t("order.pendingSettlement");
      } else {
        item.deliverystatus = t("order.settled");
      }
    });
    console.log(List.value);
  });
};

/*
  提货模块
*/
const totalcost = ref('');
//点击提货
const toPick = async (id, cost) => {
	console.log(userStore.MerInfo.status)
  if (userStore.MerInfo.status === 1) {
    showActionSheet.value = true;
    order_id.value.order_id = id;
    const roundedcost = parseFloat(cost);
    totalcost.value = roundedcost.toFixed(2).toString();
  } else {
    showFailToast(t("over"));
  }
};

//一键提货
const addList = ref({
  ids: []
});
const costList = ref([]);
//点击传递订单id,切换订单checked属性
const changeclick = (id, cost) => {
  console.log(id, cost);
  //遍历数组让checked属性为true
  List.value.forEach(item => {
    if (item.order_id == id) {
      item.checked = !item.checked;
    }
  });
  //遍历数组让传递过来的cost进入新数组
  List.value.forEach(item => {
    if (item.order_id == id) {
      costList.value.push(item.total_cost);
      // console.log(costList.value);
    }
  });
  //把数组中所有项相加
  const newcostList = costList.value.map(item => parseFloat(item));
  const sum = newcostList.reduce((total, currentValue) => total + currentValue, 0);
  console.log(sum);
  const roundedsum = sum.toFixed(2);
  totalcost.value = roundedsum.toString();
};
// 一键提货函数
const add = async () => {
  if (userStore.MerInfo.status === 1) {
    List.value.forEach(item => {
      if (item.checked == true) {
        addList.value.ids.push(item.order_id);
      }
    });
    // console.log(addList.value.ids.length)
    //如果有选中则继续操作,无选中提示
    if (addList.value.ids.length > 0) {
      showActionSheet.value = true;
    } else {
      showToast(t("order.selectProduct"));
    }
  } else {
    showFailToast(t("over"));
  }
};
const confirmAll = async () => {
  const res = await payPwdconfirm(payPwd.value);
  if (res.code == 1) {
    const res = await pickAll(addList.value);
    //如果操作成功那么进行下一步,未成功给提示
    if (res.code == 1) {
      //如果在所有订单中,那么提货成功不可删,改变状态,否则删除list列表对应订单
      if (listQuery.value.status === 'all') {
        List.value.forEach(item => {
          item.is_pick = 1;
        });
      } else {
        List.value = List.value.filter(item =>
            !addList.value.ids.includes(item.order_id));
      }
      addList.value.ids = [];
      costList.value = [];
      showSuccessToast(res.msg);
      showActionSheet.value = false;
    } else {
      showFailToast(res.msg);
    }
  }
};


/*
  提货显示支付密码模块
*/
//展现支付密码
const showActionSheet = ref(false);
//定义支付密码
const payPwd = ref({
  password_pay: ''
});

//支付密码确认接口
const order_id = ref({
  order_id: ''
});

const confirmOne = async () => {
  const res = await payPwdconfirm(payPwd.value);
  // code.value = res.code
  // console.log(code.value)
  if (res.code == 1) {
    pick(order_id.value).then(res => {
      if (res.code == 1) {
        showActionSheet.value = false;
        showSuccessToast(res.msg);
        List.value = List.value.filter(item => item.order_id !== order_id.value.order_id);
      } else {
        showFailToast(res.msg);
      }
    });
  } else if (userStore.MerInfo.have_pay === 1) {
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
  if (addList.value.ids.length <= 0) {
    confirmOne();
  } else {
    confirmAll();
  }
};


onMounted(() => {
  loading.value = true;
  onload(listQuery.value.status);
  userStore.toGetMerInfo();
  // console.log(userStore.MerInfo)
});

//查找商品请求
const searchList = () => {
  List.value = [];
  loading.value = true;
  listQuery.value.page = '1';
  onload();
};

</script>

<template>
  <header class="sticky top-0 z-10 w-dvw back_4" style="z-index: 999;padding-top: 44px;background: #fff;">
    <van-tabs @click-tab="onClickTab">
      <van-tab v-for="item in tabs" :key="item.status" :name="item.status" :title="item.title" class="relative">
      </van-tab>
    </van-tabs>
    <div>
      <van-search v-model="search" :placeholder="$t('goods.searchinput')" clearable @blur="searchList"/>
    </div>
  </header>
  <div v-if="tabname==='1'" class="grid grid-cols-2 gap-4 justify-items-center back_4 mx-3 mt-2 bg-white">
    <div class="flex flex-col my-2 items-center">
      <span class="text-gray-600">{{ $t("order.totalPayment") }}</span><span
        class="text-lg font-semibold text-[#FE4857]">${{ totalprice }}</span>
    </div>
    <div class="flex flex-col my-2 items-center">
      <span class="text-gray-600">{{ $t("order.earnings") }}</span><span
        class="text-lg font-semibold text-[#FE4857]">${{ totalprofit }}</span>
    </div>
  </div>
  <main class="pb-20">
    <div v-if="List.length<=0">
      <van-empty/>
    </div>
    <van-list

        v-model:loading="loading"
        :finished="finished"
        :finished-text="$t('order.noMore')"
        :immediate-check="false"
        @load="onload()"
    >
      <div v-for="item in List" class="bg-white mx-3 mt-3 rounded-md back_3 back_4">
        <div class="flex items-center pt-1.5">
          <van-icon class="mx-3" name="ellipsis" size="24"/>
          <span v-if="item.status===0" class="ml-auto mt-1 mr-3">{{ $t("order.pendingPayment") }}</span>
          <span v-if="item.is_pick===1 &&item.status===1" class="ml-auto mt-1 mr-3">{{
              $t("order.pendingShipment")
            }}</span>
          <span v-if="item.is_pick===0 && item.status===1" class="ml-auto mt-1 mr-3">{{
              $t("order.awaitingPickup")
            }}</span>
          <span v-if="item.status===2" class="ml-auto mt-1 mr-3">{{ item.deliverystatus }}</span>
          <span v-if="item.status===3" class="ml-auto mt-1 mr-3">{{ $t("order.received") }}</span>
          <span v-if="item.status===4" class="ml-auto mt-1 mr-3">{{ $t("order.completed") }}</span>
          <span v-if="item.status===-1" class="ml-auto mt-1 mr-3">{{ $t("order.canceled") }}</span>
          <div v-if="item.is_pick===0 && item.status===1" class="flex justify-end pt-1  pr-3">
            <van-checkbox v-model="item.clicked" @click="changeclick(item.order_id,item.total_cost)"></van-checkbox>
          </div>
        </div>
        <div class="flex mx-3" @click="router.push({ path: '/orderDetail', query: { id: item.order_id } })">
          <div class="mt-2">
            <van-image
                :src="item.orderProduct[0].goods.image ? item.orderProduct[0].goods.image : '@/assets/images/no img.png'"
                height="100"
                lazy-load
                radius="10px"
                width="100"
            >
              <template v-slot:loading>
                <van-loading size="20" type="spinner"/>
              </template>
            </van-image>
          </div>
          <div class="ml-3 mt-3 flex-1 mr-1 flex  flex-col">
            <div class="-mt-2 font-bold "><span class="line-clamp_2 text-ellipsis">{{
                item.orderProduct[0].goods.title ? item.orderProduct[0].goods.title : $t("notitle")
              }}</span></div>
            <div>
            <span class="text-neutral-500 text-sm">
              {{ $t("order.orderNumber") }}
            </span>
              <span class="font-semibold text-sm">
              {{ item.order_sn }}
            </span>
            </div>
            <div class="text-left mr-3 ">
              <span class="text-neutral-500 text-sm">{{ item.createtime }}</span>
            </div>
            <div class="flex mr-2 justify-between items-center ">
              <div class="text-right flex justify-items-start items-center  ">
                <span class="text-neutral-500 text-sm font-bold mr-2 ">{{ $t("order.quantity") }}</span>
                <span class="font-semibold  ">{{ item.total_num }}</span>
              </div>
              <div class="text-right flex justify-items-start items-center  ">
                <span class="text-neutral-500 text-sm font-bold mr-2">{{ $t("order.totalCost") }}</span>
                <span class="font-semibold  ">{{ item.total_cost }}</span>
              </div>
            </div>
            <div class="flex mr-2 justify-between items-center ">
              <div class="text-right flex justify-items-start items-center">
                <span class="text-neutral-500 text-sm font-bold mr-2 ">{{ $t("order.sellingPrice") }}</span>
                <span class="font-semibold  ">{{ item.total_price }}</span>
              </div>
              <div class="text-right flex justify-items-start items-center">
                <span class="text-neutral-500 text-sm font-bold mr-2">{{ $t("order.earnings") }}</span>
                <span class="font-semibold  text-[#FE4857]">{{ item.total_profit }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex items-center pb-3 pt-1">
          <span class="mx-3" @click="router.push({ path: '/orderDetail', query: { id: item.order_id } })">{{
              $t("order.details")
            }}</span>
          <!--          <span-->
          <!--              class="ml-auto mr-3 border-solid border border-white rounded-md py-1.5 px-3 bg-black text-white hidden"-->
          <!--              v-if="item.status===0">等待付款</span>-->
          <!--          <span-->
          <!--              class="ml-auto mr-3 border-solid border border-white rounded-md py-1.5 px-3 bg-black text-white hidden"-->
          <!--              v-if="item.is_pick===1 &&item.status===1">待发货</span>-->
          <span
              v-if="item.is_pick===0 && item.status===1"
              class="ml-auto mr-3 border-solid border border-white rounded-md py-1.5 px-3 bg-black text-white"
              @click="toPick(item.order_id,item.total_cost)">{{ $t("order.clickToPickup") }}</span>
          <!--          <span-->
          <!--              class="ml-auto mr-3 border-solid border border-white rounded-md py-1.5 px-3 bg-black text-white hidden"-->
          <!--              v-if="item.status===2">已发货</span>-->
          <!--          <span-->
          <!--              class="ml-auto mr-3 border-solid border border-white rounded-md py-1.5 px-3 bg-black text-white hidden"-->
          <!--              v-if="item.status===3">已收货</span>-->
          <!--          <span-->
          <!--              class="ml-auto mr-3 border-solid border border-white rounded-md py-1.5 px-3 bg-black text-white hidden"-->
          <!--              v-if="item.status===4">已完成</span>-->
          <!--          <span-->
          <!--              class="ml-auto mr-3 border-solid border border-white rounded-md py-1.5 px-3 bg-black text-white hidden"-->
          <!--              v-if="item.status===-1">已取消</span>-->
        </div>
      </div>
    </van-list>
    <div v-if="listQuery.status==='all' || listQuery.status==='5'" class="fixed bottom-32 left-12 button_1">
      <van-button color="rgba(0, 0, 0, 0.8)" round size="small" style="width:300px" @click="add">
        {{ $t("order.pickupAll") }}
      </van-button>
    </div>
  </main>
  <van-action-sheet v-model:show="showActionSheet" :overlay="false" :round="false">
    <payPassword :price="totalcost" @close="()=>showActionSheet=false" @send-data="confirmdata"/>
  </van-action-sheet>
  <van-overlay :show="showActionSheet" @click="showActionSheet = false"/>
  <tabbers></tabbers>
</template>

<style lang="scss" scoped>
.button_1 {
  left: 50%;
  transform: translateX(-50%);
}
</style>
