<script setup>
import {fundsRecords, productManagement} from "@/api/index.js";

const router = useRouter();
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
//返回
const onClickLeft = () => {
  router.back();
};
//弹出选择层
const show = ref(false);
const actions = [
  {name: t("fundsRecords.all")},
  {name: t("fundsRecords.redcord1")},
  {name: t("fundsRecords.redcord2")},
  {name:  t("fundsRecords.redcord3")}
];
//打开选择层
const openshow = () => {
  show.value = true;
};

//点击选项函数
const selects = ref("");
const onSelect = (item) => {
  // 默认情况下点击选项时不会自动收起
  // 可以通过 close-on-click-action 属性开启自动收起
  console.log(item)
  selects.value = item.name;
  console.log(selects.value);
  show.value = false;
  showToast(item.name);
  if (selects.value === t("fundsRecords.recharge")) {
    fundsQuery.value.type = "recharge"
  } else if (selects.value === t("fundsRecords.extract")) {
    fundsQuery.value.type = "extract"
  } else if (selects.value ===t("fundsRecords.order")) {
    fundsQuery.value.type = "order"
  } else {
    fundsQuery.value.type = "all"
  }
  orderList.value = [];
  fundsQuery.value.page = '1';
  finished.value = false;
  loading.value = true;
  onload()
};

//定义选项和发送请求数据
const fundsQuery = ref({
  type: 'all',
  page: '1',
  limit: '10'
});
//发送请求获取列表
const orderList = ref([]);

//加载列表
const loading = ref(false);
const finished = ref(false);
const onload = () => {
  fundsRecords(fundsQuery.value).then(res => {
    loading.value = false
    let number = parseInt(fundsQuery.value.page);
    console.log(res.data)
    number++;
    fundsQuery.value.page = number.toString();
    if (res.data.list.length <= 0) {
      finished.value = true;
    }else{
      orderList.value.push(...res.data.list)
    }
  })
}

//加载提示
// showLoadingToast({
//   message: '加载中...',
//   forbidClick: true,
// });
// onBeforeMount(()=>{
//   showLoadingToast()
// })
onMounted(() => {
  onload()
})


</script>

<template>
  <header class="sticky top-0 z-10 w-dvw back_4">
    <van-nav-bar
        :title="$t('fundsRecords.fundsRecords')"
        left-text=""
        left-arrow
        @click-left="onClickLeft">
      <template #right>
        <van-icon color="#FFFFFF" @click="openshow" size="22px" name="todo-list"/>
      </template>
    </van-nav-bar>
  </header>
  <main class="mx-3 mt-2">
    <div v-if="orderList.length<=0">
      <van-empty :description="$t('fundsRecords.norecord')"/>
    </div>
    <van-list
        v-model:loading="loading"
        :finished="finished"
        :finished-text="$t('fundsRecords.nomore')"
        @load="onload()"
        :immediate-check="false"
    >
    <div class="bg-white h-20 rounded-md flex mb-3" style="box-shadow: 0 1px 3px 0 rgba(0,0,0,0.08);"
         v-for="item in orderList" :key="item.id">
      <div class="flex justify-center items-center mx-3">
        <icon-park name="finance" size="2.5rem"/>
      </div>
      <div class="flex-auto flex flex-col justify-center">
        <span class="text-base" style="font-weight: 400;">
          {{ item.title }}
        </span>
        <span class="text-sm opacity-80">
          {{ item.createtime }}
        </span>
      </div>
      <div class="p-4 mr-2 flex flex-col justify-center items-end ">
        <div class="flex justify-end items-center ml-3">
          <icon-park color="#FE4857" name="plus" size="1.6rem" v-if="item.pm===1"/>
          <van-icon name="minus" v-if="item.pm===0" color="green"/>
          <span class="text-base font-medium" :style="{ color: item.pm === 1 ? '#FE4857' : 'green'}">{{
              item.money
            }}</span>
        </div>
        <div><span class="text-xs">{{ $t("fundsRecords.money") }}{{ item.after }}</span></div>
      </div>
    </div>
    </van-list>
  </main>
  <van-action-sheet v-model:show="show" :actions="actions" @select="onSelect"/>
</template>
<style lang="scss" scoped>
</style>