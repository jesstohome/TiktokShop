<script setup>
import tabbers from '@/components/tabbar/index.vue';
import {changelanguage, finance, notice, languageList} from "@/api/index.js";
import {useUserStore} from '@/store/modules/user.js';
import {useRouter} from 'vue-router';
import {useI18n} from 'vue-i18n';
const {locale, t} = useI18n();
import {showFailToast, showSuccessToast} from "vant";


const router = useRouter();
//仓库中获取商户信息请求函数
const userStore = useUserStore();
//切换语言
//定义选项卡内容
const actions = ref([]);
//从后端拿取默认语言,并且渲染语言列表
const langList = ref([]);
const actionList = ref([]);
const defautlanguage = () => {
  languageList({page: 1, limit: 10}).then(res => {
    langList.value = res.data.list;
    //提取里面属性组成新的数组渲染选项卡
    actionList.value = langList.value.map(item => ({name: item.language_name, file_name: item.file_name}));
    // console.log(actionList.value)
    actions.value = actionList.value;
    console.log(actions.value);
    //存入一个数据判断是否执行过此功能,如未执行,就获取默认语言,如果获取过,页面更新就不再执行存入默认语言
    const hasExecutedFunction = localStorage.getItem('hasExecutedFunction');
    if (!hasExecutedFunction) {
      console.log(langList.value);
      langList.value.forEach((item) => {
        if (item.is_default == 1) {
          localStorage.setItem('lang', item.file_name);
          location.reload();
        }
      });
      localStorage.setItem('hasExecutedFunction', 'true');
    }
  });
};


//请求参数
const lang1 = ref({
  lang: ''
});
//打开选择层
const show = ref(false);
const onSelect = (item) => {
  show.value = false;
  lang1.value.lang = item.file_name;
  locale.value = item.file_name;
  localStorage.setItem('lang', item.file_name);
  // changelanguage(lang1.value).then(res => {
  //   if (res.code === 1) {
  //     console.log(1)
  //     showSuccessToast(res.msg)
  //   } else {
  //     showFailToast(res.msg)
  //   }
  // })
  location.reload();
};
const contentList = ref([]);
const getNotice = async () => {
  const res = await notice(lang1.value);
  contentList.value = res.data;
  // console.log(contentList.value);
};
//列表图片
const imgs = ref({
  icon1: new URL('@/assets/image/my/个人信息.png', import.meta.url).href,
  icon2: new URL('@/assets/image/my/语言.png', import.meta.url).href,
  icon3: new URL('@/assets/image/my/申请退款.png', import.meta.url).href,
  icon4: new URL('@/assets/image/my/资金记录.png', import.meta.url).href,
  icon5: new URL('@/assets/image/my/财务报表.png', import.meta.url).href,
  icon6: new URL('@/assets/image/my/创业联盟.png', import.meta.url).href,
});
//列表
const list = ref(
    [
      {
        name: t('my.info'),
        path: '/personalInfo',
        icon: new URL('@/assets/image/my/个人信息.png', import.meta.url).href
      },
      {name: t("my.lang"), path: '/lang', icon: new URL('@/assets/image/my/语言.png', import.meta.url).href},
      {
        name: t('my.applyForRefund'),
        path: '/refundRequest',
        icon: new URL('@/assets/image/my/申请退款.png', import.meta.url).href
      },
      {
        name: t("my.financialRecords"),
        path: '/fundsRecords',
        icon: new URL('@/assets/image/my/资金记录.png', import.meta.url).href
      },
      {
        name: t("my.financialReports"),
        path: '/finance',
        icon: new URL('@/assets/image/my/财务报表.png', import.meta.url).href
      },
      {
        name: t("my.entrepreneurAlliance"),
        path: '/alliance',
        icon: new URL('@/assets/image/my/创业联盟.png', import.meta.url).href
      },
    ]
);
//去对应页面
const Goto = ((path) => {
  if (path === '/lang') {
    show.value = true;
    return;
  }
  router.push(path);
});
//退出登录
const loginOut = () => {
  showConfirmDialog({
    title: t("my.logout"),
	confirmButtonText: t("confirm"),
	cancelButtonText: t("quxiao"),
    message:
        t("my.confirmLogout"),
  })
      .then(() => {
        userStore.nologin();
      })
      .catch(() => {
        // on cancel
      });
};
//
// //悬浮窗
// const offset = ref({y: 500});
//财务报告中待到账金额获取
const totalunreceived = ref('');
const getfinance = () => {
  finance({
    range: 'all',
    page: '1',
    limit: '10'
  }).then(res => {
    console.log(res.data.total_unreceived);
    totalunreceived.value = res.data.total_unreceived;
  });
};

onMounted(() => {
  const headerData = ref({});
  getNotice();
  userStore.toGetMerInfo();
  getfinance();
  defautlanguage();
});
</script>

<template>
  <header class="bg-black h-40 bg-cover" style="height: 13rem;">
    <div class="mx-3 pt-6 flex items-center">
      <div>
        <van-image
            :src="userStore.MerInfo.mer_avatar"
            height="60px"
            lazy-load
            round
            width="60px"
        />
      </div>
      <div class="flex flex-col ml-3">
        <span class="font-semibold text-lg text-white">{{ userStore.MerInfo.mer_email }}</span>
        <span class="text-sm text-white"> ID:{{ userStore.MerInfo.mer_id }}</span>
      </div>
      <div class="ml-auto mr-3">
        <!--        <van-image-->
        <!--            width="50px"-->
        <!--            height="50px"-->
        <!--            lazy-load-->
        <!--            round-->
        <!--            src="https://shopify-kr82.com/www/png/name-bca59e04.png"-->
        <!--        />-->
      </div>
    </div>
  </header>
  <main class="bottom">
    <div class=" bg-white mx-3 rounded-md -mt-10 back_4">
      <div class="flex items-center pl-3 pt-3">
        <span class="font-semibold text-xl pr-3">{{ $t("my.Amount") }}</span><span
          class="text-xl text-[#FE4857] mr-2">{{ totalunreceived }}</span>
        <van-icon name="replay" size="14px" @click="userStore.toGetMerInfo"/>
      </div>
      <div class="mx-3 mt-3">
        <span class="text-sm">{{ $t("my.Balance") }}</span>
        <span class="font-semibold ml-5">{{ userStore.MerInfo.mer_money }}</span>
      </div>
      <div class="mx-3 py-3">
        <span class="pr-2 text-sm">{{ $t("my.Income") }}</span>
        <span class="font-semibold ml-2">{{ userStore.MerInfo.total_income }}</span>
      </div>
    </div>
    <div class="grid grid-cols-2 mx-3 mt-3 gap-3 ">
      <div class="bg-white h-10 rounded-md border border-black flex justify-center items-center"
           @click="router.push('/recharge')">
        <span>{{ $t("my.deposit") }}</span>
      </div>
      <div class="bg-black h-10 rounded-md border border-white flex justify-center items-center"
           @click="router.push('/withdraw')">
        <span class="text-white">{{ $t("my.Withdraw") }}</span>
      </div>
    </div>
    <!--    系统通知-->
    <div class="mx-3 mt-3 ">
      <van-notice-bar background="#ffffff" class="rounded-md back_4" color="#000000" left-icon="volume-o" scrollable>
        <span v-for="item in contentList" class="mr-0.5">{{ item.content }}</span>
      </van-notice-bar>
    </div>
    <div class="bg-white mx-3 mt-3 back_4">
      <div v-for="item in list.slice(0,3)" :key="item.path"
           class="flex py-3 mx-3 justify-between border-b border-gray-100 "
           @click="Goto(item.path)">
        <div class="flex items-center">
          <img :src="item.icon" alt="" class="w-6">
          <span class="text-base pl-2">{{ item.name }}</span>
        </div>
        <div class="flex items-center">
          <van-icon name="arrow" size="18px"/>
        </div>
      </div>
    </div>
    <div class="bg-white mx-3 mt-6 back_4">
      <div v-for="item in list.slice(3)" class="flex py-3 mx-3 justify-between border-b border-gray-100"
           @click="Goto(item.path)">
        <div class="flex items-center">
          <img :src="item.icon" alt="" class="w-6">
          <span class="text-base pl-2">{{ item.name }}</span>
        </div>
        <div class="flex items-center">
          <van-icon name="arrow" size="18px"/>
        </div>
      </div>
    </div>
    <div class="bg-black mx-3 mt-3 flex justify-center items-center rounded-md py-3" @click="loginOut">
      <span class="text-white">{{ $t("my.logout") }}</span>
    </div>
  </main>
  <tabbers></tabbers>
  <!--  <van-floating-bubble v-model:offset="offset" axis="xy" icon="chat" @click="router.push('/service')"/>-->
  <van-action-sheet v-model:show="show" :actions="actions" @select="onSelect"/>
</template>

<style lang="scss" scoped>
.bottom {
  height: calc(100vh - 40px);
  height: calc(100dvh - 40px);
  padding-bottom: 30px;
}

</style>
