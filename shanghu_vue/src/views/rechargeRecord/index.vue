<script setup>
import {rechargeRecord} from "@/api/index.js";
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
const router = useRouter();
const onClickLeft = () => {
  router.back();
};
//弹出选择层
const show = ref(false);
const actions = [
  {name: t("rechargerecord.all")},
  {name: t("rechargerecord.pending")},
  {name: t("rechargerecord.approved")},
  {name: t("rechargerecord.rejected")}
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
  // console.log(item)
  selects.value = item.name;
  // console.log(selects.value);
  show.value = false;
  showToast(item.name);
  if (selects.value === t("rechargerecord.all")) {
    query.value.status = "all"
  } else if (selects.value === t("rechargerecord.pending")) {
    query.value.status = "0"
  } else if (selects.value === t("rechargerecord.approved")) {
    query.value.status = "1"
  } else {
    query.value.status = "-1"
  }
  List.value = [];
  query.value.page = '1';
  finished.value = false;
  loading.value = true;
  onload()
};


//请求数据
const query = ref({
  status: 'all',
  page: '1',
  limit: '10'
})
//充值记录列表
const List = ref([])
//滚动加载
const loading = ref(false);
const finished = ref(false);
const onload = (id) => {
  rechargeRecord(query.value).then(res => {
    console.log(res)
    let number = parseInt(query.value.page);
    number++;
    query.value.page = number.toString();
    if (res.data.list.length <= 0) {
      finished.value = true;
    }else{
      List.value.push(...res.data.list)
      console.log(List.value)
      loading.value = false
    }

  })
}

onMounted(() => {
  loading.value = true;
  onload()
})
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('rechargerecord.depositRecords')"
        :left-text="$t('goback')"
        left-arrow
        fixed
        @click-left="onClickLeft"
		style="top: 44px;"
    >
      <template #right>
        <van-icon color="#FFFFFF" @click="openshow" size="22px" name="todo-list"/>
      </template>
    </van-nav-bar>
  </header>
  <main class="mt-16">
    <div v-if="List.length<=0">
      <van-empty :description="$t('rechargerecord.noRecords')" />
    </div>
    <van-list
              v-model:loading="loading"
              :finished="finished"
              :finished-text="$t('rechargerecord.noMore')"
              @load="onload()"
              :immediate-check="false"
    >
      <div class="bg-white mx-3 mt-3 rounded-md" v-for="item in List " :key="item.recharge_id">
        <div class="flex justify-between mx-3 py-1.5">
          <div class="text-gray-500">{{ $t("rechargerecord.orderNumber") }}</div>
          <div class="flex items-center">
            <span>{{ item.order_id }}</span>
            <van-icon class="pl-1.5" size="24px" name="birthday-cake-o"/>
          </div>
        </div>
        <div class="flex justify-between mx-3 py-1.5">
          <div class="text-gray-500">{{ $t("rechargerecord.time") }}</div>
          <div class="flex items-center">
            <span>{{ item.createtime }}</span>
          </div>
        </div>
        <div class="flex justify-between mx-3 py-1.5">
          <div class="text-gray-500">{{ $t("rechargerecord.amount") }}</div>
          <div class="flex items-center">
            <span>{{ item.price }}</span>
          </div>
        </div>
        <div class="flex justify-between mx-3 py-1.5">
          <div class="text-gray-500">{{ $t("rechargerecord.paymentStatus") }}</div>
          <div class="flex items-center">
            <span class="text-blue-500" v-if="item.status===0">{{ $t("rechargerecord.inReview") }}</span>
            <span class="text-green-500" v-if="item.status===1">{{ $t("rechargerecord.haveapproved") }}</span>
            <span class="text-red-500" v-if="item.status===-1">{{ $t("rechargerecord.nopass") }}({{ item.admin_msg }})</span>
          </div>
        </div>
      </div>
    </van-list>
  </main>
  <van-action-sheet v-model:show="show" :actions="actions" @select="onSelect"/>
</template>

<style scoped lang="scss">

</style>