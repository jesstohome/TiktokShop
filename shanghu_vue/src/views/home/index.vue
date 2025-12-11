<script setup>
import tabbers from '@/components/tabbar/index.vue';
import firstecharts from '@/components/firstecharts.vue';
import {test, home} from '@/api/index';
import {useRouter} from 'vue-router'
import {useUserStore} from '@/store/modules/user.js';
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
const router = useRouter();
//仓库中获取商户信息请求函数
const userStore = useUserStore();
//测试接口
test().then(res => {
  console.log(res);
});
//今日售出
const imgs2 = ref([{
  title: t("home.todayOrderSold"),
  icon: new URL('@/assets/image/home2/1.png', import.meta.url).href,
  data: ''
},
  {
    title: t("home.todayTotalsales"),
    icon: new URL('@/assets/image/home2/2.png', import.meta.url).href,
    data: ''
  },
  {
    title: t("home.todaysEstimatedProfit"),
    icon: new URL('@/assets/image/home2/3.png', import.meta.url).href,
    data:''
  },
])
//请求首页数据
const homeData = ref({})
const getHomeData = async () => {
  const res = await home()
  // console.log(res)
  homeData.value = res.data
  imgs2.value[0].data=homeData.value.today_order
  imgs2.value[1].data=homeData.value.today_sales
  imgs2.value[2].data=homeData.value.today_profit
  console.log(imgs2.value)
}

/*=====数据折叠====*/
const foldHeight = ref('400px');
const fold = () => {
  foldHeight.value = foldHeight.value === '400px' ? '80px' : '300px';
};

//header图标
const img = ref({
  icon1: new URL('@/assets/image/home1/1.png', import.meta.url).href,
  icon2: new URL('@/assets/image/home1/2.png', import.meta.url).href,
})

//店铺数据--店铺展示卡
const list = [{
  name:  t("home.shopFollowers"),
  data: userStore.MerInfo.follow_count,
  icon: new URL('@/assets/image/home3/店铺关注.png', import.meta.url).href
}, {
  name: t("home.shopRating"),
  data: userStore.MerInfo.grade,
  icon: new URL('@/assets/image/home3/店铺评分.png', import.meta.url).href
},
  {
    name: t("home.shopCreditScore"),
    data: userStore.MerInfo.credit,
    icon: new URL('@/assets/image/home3/店铺信用分.png', import.meta.url).href
  }];
//页面导航选项卡
const imgs = ref([{
  title: t("home.shopSettings"),
  icon: new URL('@/assets/image/home1/店铺设置.png', import.meta.url).href,
  path: '/setting'
},
  {
    title: t("home.refundOrders"),
    icon: new URL('@/assets/image/home1/退款订单.png', import.meta.url).href,
    path: '/refundRequest'
  },
  {
    title:t("home.shopExpressLane"),
    icon: new URL('@/assets/image/home1/店铺直通车.png', import.meta.url).href,
    path: '/storeExpress'
  },
  {
    title:  t("home.humanCustomerService"),
    icon: new URL('@/assets/image/home1/创业联盟.png', import.meta.url).href,
    path: '/service'
  },
  {
    title: t("home.withdrawal"),
    icon: new URL('@/assets/image/home1/提现.png', import.meta.url).href,
    path: '/withdraw'
  },
  {
    title:t("home.distributionCenter"),
    icon: new URL('@/assets/image/home1/卖家等级.png', import.meta.url).href,
    path: '/distribution'
  }
])

onBeforeMount(() => {
  getHomeData()
  userStore.toGetMerInfo()
})
onMounted(() => {
  userStore.toGetMerInfo();
});
</script>

<template>
  <!--头部-->
  <div class="container_mix">
    <header>
      <div class="grid grid-cols-2 grid-flow-col gap-4 py-6   text-white ">
        <div class="flex items-center  mr-auto ml-3">
          <van-image
              width="60px"
              height="60px"
              round
              :src="userStore.MerInfo.mer_avatar"
          />
          <div class="flex flex-col ml-3" style="width: 10rem">
            <div class="mt-1 flex justify-left ">
              <span class="text-lg text-[#191919] ml-1.5 font-semibold ">{{ userStore.MerInfo.mer_name }}</span>
            </div>
           <div class="ml-1 mt-2" @click="router.push('/level')">
			   <span class="text-lg text-[#191919] font-semibold ">{{ $t("level.merchantLevel") }}：</span>
			<span class="text-sm  px-3 py-2 rounded-xl bg-gradient-to-r from-[#9f953e]  to-[#867e00]">{{ userStore.MerInfo.level_name }}</span>
             <!-- <span class="text-sm  px-3 py-2 rounded-xl bg-gradient-to-r from-[#6B6B6B]  to-[#1C1B1B]">未认证</span> -->
           </div>
          </div>
        </div>
        <div class="flex items-center ml-auto">
          <div class="flex items-center ml-auto">
            <van-badge  class="mr-3" :content="userStore.MerInfo.unread_notice" @click="router.push('/message')">
              <img class="h-8 w-8" :src="img.icon1" alt="">
            </van-badge>
          </div>
        </div>
        <div class="h-10">
        </div>
      </div>
    </header>

    <main>
      <!--数据展示-->
      <div class="bg-white mx-3 py-3 rounded-md back_4">
        <div class="z-10 rounded-md overflow-hidden" :style="{'max-height':foldHeight}"
             style="transition: max-height 1s ease-in-out;">
          <div class="flex justify-between mr-3">
            <div class=" antialiased font-semibold mx-3.5 text-lg">{{ $t("home.shopData") }}</div>
            <div class="flex justify-between align-middle "  @click="fold">
              <div class="flex justify-end items-center text-gray-500 w-60 mr-1">{{ $t("home.expandDetails") }}</div>
              <div class="flex justify-center items-center ">
                <img :src="img.icon2" alt="">
              </div>
            </div>
          </div>
          <div class="bg-white  z-10 mx-3 rounded-md ">
            <div class="grid grid-cols-2 grid-flow-col gap-4 py-2 rounded-b-lg">
              <div class="flex flex-col justify-center items-start">
                <div class="antialiased  font-semibold text-2xl">${{ homeData.total_sales }}</div>
                <div>
                  <van-icon name="cart-circle-o"/>
                  <span class="text-sm text-neutral-500">{{ $t("home.totalsales") }}</span>
                </div>
              </div>
              <div class="flex flex-col justify-center items-start">
                <div ><span class="antialiased font-semibold text-2xl">${{ homeData.total_profit }}</span></div>
                <div>
                  <van-icon name="gold-coin-o"/>
                  <span class="text-sm text-neutral-500">{{ $t("home.totalprofit") }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="">
            <div class="grid grid-cols-3 grid-flow-row gap-4 mx-3 rounded-b-lg mt-3 ">
              <div class="bg-gradient-to-r from-[#6B6B6B]  to-[#1C1B1B] rounded-md  flex-none flex justify-center"
                   v-for="item in imgs2">
                <div class="flex flex-col justify-center items-center">
                  <div class="antialiased text-base  pt-2 px-2 text-white text-center ">
                    {{ item.title }}
                  </div>
                  <div>
                    <span class="text-lg antialiased font-semibold text-white ">{{ item.data }}</span>
                  </div>
                </div>
                <!--              <div class="w-3/12 mt-4 mr-4">-->
                <!--                <img :src="item.icon" alt="">-->
                <!--              </div>-->
              </div>
            </div>
          </div>
          <div class="grid grid-cols-3 grid-flow-row gap-4 mx-3 rounded-b-lg mt-3">
            <!--数据详情-->
            <div class="flex justify-center items-center back_2 rounded-md" v-for="(item,index) in list" :key="index">
              <div class="flex flex-col justify-center items-center p-1">
                <div class="antialiased font-semibold  text-lg  text-black">{{ item.data }}</div>
                <div class="antialiased text-neutral-500 text-sm">{{ item.name }}</div>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-3 grid-flow-row gap-4 mx-3 rounded-b-lg mt-3">
            <!--数据详情-->
            <div class="flex justify-center items-center back_2 rounded-md">
              <div class="flex flex-col justify-center items-center p-1">
                <div class="antialiased font-semibold  text-lg  text-black">{{ homeData.today_visit }}</div>
                <div class="antialiased text-neutral-500 text-sm">{{ $t("home.visitorsToday") }}</div>
              </div>
            </div>
            <div class="flex justify-center items-center back_2 rounded-md">
              <div class="flex flex-col justify-center items-center p-1">
                <div class="antialiased font-semibold  text-lg  text-black">{{ homeData.seven_visit }}</div>
                <div class="antialiased text-neutral-500 text-sm">{{ $t("home.visitorsLast7Days") }}</div>
              </div>
            </div>
            <div class="flex justify-center items-center back_2 rounded-md">
              <div class="flex flex-col justify-center items-center p-1">
                <div class="antialiased font-semibold  text-lg  text-black">{{ homeData.thirty_visit }}</div>
                <div class="antialiased text-neutral-500 text-sm">{{ $t("home.visitorsLast30Days") }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--路由按钮-->
      <div class="mx-3 bg-white  mt-3 rounded-md back_4">
        <div class="grid grid-cols-3 grid-flow-row gap-x-3 mx-3 rounded-b-lg mt-3">
          <div class="flex flex-col justify-center items-center py-3" v-for="item in imgs"
               @click="router.push(item.path)">
            <div class="flex justify-center container">
              <img :src="item.icon" class="w-7 h-7 my-2" alt="">
            </div>
            <div>
              <span class="text-sm text-neutral-500">{{ item.title }}</span>
            </div>
          </div>
        </div>
      </div>
      <!--图表组件-->
      <div class="mx-3 bg-white mt-3 rounded-md back_4">
        <div class="p-6 text-blue-800 text-base -mb-10">{{ $t("home.salesDataCurve") }}</div>
        <div style="height: 350px">
          <firstecharts/>
        </div>
      </div>
      <!--店铺信息-->
      <!--<div>-->
      <!--  <div class="mx-3 bg-white  mt-3 rounded-md" v-for="item in 2" :key="item">-->
      <!--    <div class="grid grid-cols-2 grid-flow-col gap-4 py-3 pl-3 rounded-b-lg">-->
      <!--      <div class="flex items-center  mr-auto ml-3">-->
      <!--        <img src="@/assets/image/set.png" class="w-10 h-10" alt="">-->
      <!--        <span class="antialiased text-lg pl-3">店铺关注</span>-->
      <!--      </div>-->
      <!--      <div class="flex items-center ml-auto mr-3">-->
      <!--        <span class="antialiased font-semibold text-lg pl-3">350</span>-->
      <!--      </div>-->
      <!--    </div>-->
      <!--  </div>-->
      <!--  <div class="mx-3 bg-white  mt-3 rounded-md">-->
      <!--    <div class="grid grid-cols-2 grid-flow-col gap-4 py-3 pl-3 rounded-b-lg">-->
      <!--      <div class="flex items-center  mr-auto ml-3">-->
      <!--        <img src="@/assets/image/set.png" class="w-10 h-10" alt="">-->
      <!--        <span class="antialiased text-lg pl-3">店铺关注</span>-->
      <!--        <van-icon class="ml-1.5" name="info-o"/>-->
      <!--      </div>-->
      <!--      <div class="flex items-center ml-auto mr-3">-->
      <!--        <span class="antialiased font-semibold text-lg pl-3">350</span>-->
      <!--      </div>-->
      <!--    </div>-->
      <!--  </div>-->
      <!--</div>-->
      <!--订单信息-->
      <!--商品分类-->
      <!--<div>-->
      <!--  <h5 class="antialiased font-semibold text mx-3">-->
      <!--    商品分类-->
      <!--  </h5>-->
      <!--  <div class="scroll-content">-->
      <!--    <div class="item" v-for="item in 5">-->
      <!--      <van-image-->
      <!--          radius="10px"-->
      <!--          width="100%"-->
      <!--          height="100%"-->
      <!--          lazy-load-->
      <!--          src="https://fastly.jsdelivr.net/npm/@vant/assets/cat.jpeg"-->
      <!--      />-->
      <!--      <div class="info">-->
      <!--        <p>奢侈品</p>-->
      <!--        <p>(18)</p>-->
      <!--      </div>-->
      <!--    </div>-->
      <!--  </div>-->
      <!--</div>-->
      <!--热销商品TOP10-->
      <!--<div>-->
      <!--  <h5 class="antialiased font-semibold text mx-3">-->
      <!--    热销商品TOP10-->
      <!--  </h5>-->
      <!--  <div class="bg-white p-3 mx-3 rounded-md mt-3 flex" v-for="item in 6" @click="router.push('/goodsDetail')">-->
      <!--    <div class="w-auto">-->
      <!--      <van-image-->
      <!--          class=""-->
      <!--          width="78"-->
      <!--          height="78"-->
      <!--          lazy-load-->
      <!--          src="https://fastly.jsdelivr.net/npm/@vant/assets/cat.jpeg"-->
      <!--      />-->
      <!--    </div>-->
      <!--    <div class="flex flex-col justify-center   flex-nowrap pl-3">-->
      <!--      <div class="flex-initial leading-5"><span class="text-ellipsis line-clamp_2">Shy Velvet Balaclava 防风冬季面罩，男女抓绒滑雪面罩，保暖面罩帽子围巾</span>-->
      <!--      </div>-->
      <!--      <div class="flex-auto">-->
      <!--        <span class="text-neutral-500 text-sm">浏览: 3,029</span>-->
      <!--        <span class="text-neutral-500 text-sm pl-6">销量: 201</span>-->
      <!--      </div>-->
      <!--      <div class="antialiased text-lg text-blue-500">$14.69</div>-->
      <!--    </div>-->
      <!--  </div>-->
      <!--</div>-->
    </main>
  </div>
  <tabbers></tabbers>
</template>


<style>

</style>

<style scoped lang="scss">
body {
  background-color: #F7F7F7;
}


.scroll-content {
  width: 100%;
  height: 140px;
  margin-top: 13px;
  overflow-x: auto;
  overflow-y: hidden;
  display: flex;


  .item {
    width: 120px;
    height: 140px;
    display: block;
    margin-left: 15px;
    position: relative;
    flex: 0 0 auto;


    .info {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      color: #EFF2F6;
      background-color: rgba(0, 0, 0, .4);
      border-radius: 10px;
    }
  }
}


</style>