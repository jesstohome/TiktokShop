<script setup>
import {refundorder} from "@/api/index.js";
import {useI18n} from 'vue-i18n';
//多语言
const {t} = useI18n();
const router = useRouter();

const onClickLeft = () => {
  router.back();
};
//退款订单请求
const refundQuery = ref({
  page: '1',
  limit: '5'
})
const List = ref([])
const loading = ref(false);
const finished = ref(false);
const onLoad = async () => {
  const res = await refundorder(refundQuery.value)
  loading.value = false
  let number = parseInt(refundQuery.value.page);
  // console.log(res)
  number++;
  refundQuery.value.page = number.toString();
  List.value.push(...res.data);
  if (res.data.length < 5) {
    finished.value = true;
  }
}
onBeforeMount(() => {
  onLoad()
})
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('refundRequest.refundRequest')"
        left-text=""
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <section>
    <div v-if="List.length<=0">
      <van-empty :description="$t('refundRequest.empty')"/>
    </div>
    <van-list
        v-model:loading="loading"
        :finished="finished"
        finished-text="没有更多了"
        @load="onLoad"
        :immediate-check="false"
    >
      <div class="bg-white mx-3 rounded-md mt-3" v-for="item in List">
        <div class="p-3">
          <div class="flex justify-between py-1">
            <div>{{ $t("refundRequest.applyTime") }}</div>
            <div>{{ item.createtime }}</div>
          </div>
          <div class="flex justify-between py-1">
            <div>{{ $t("refundRequest.refundId") }}</div>
            <div>{{ item.refund_sn }}</div>
          </div>
          <div class="flex justify-between py-1">
            <div>{{ $t("refundRequest.refundAmount") }}</div>
            <div>{{ item.amount }}</div>
          </div>
          <div class="flex justify-between py-1">
            <div>{{ $t("refundRequest.refundReason") }}</div>
            <div>{{ item.refund_explain }}</div>
          </div>
          <div class="flex justify-between py-1">
            <div>{{ $t("refundRequest.goodsStatus") }}</div>
            <div>{{ item.receiving_status = 0 ? $t("refundRequest.noarrived") : $t("refundRequest.arrived") }}</div>
          </div>
          <div class="flex justify-between py-1">
            <div>{{ $t("refundRequest.refundStatus") }}</div>
            <div v-if="item.status===0">{{ $t("refundRequest.pendingApproval") }}</div>
            <div v-if="item.status===1">{{ $t("refundRequest.approved") }}</div>
            <div v-if="item.status===-1">{{ $t("refundRequest.rejected") }}</div>
          </div>
        </div>
      </div>
    </van-list>
  </section>
</template>

<style scoped lang="scss">

</style>