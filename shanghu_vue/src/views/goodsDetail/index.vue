<script setup>
import {useRoute, useRouter} from "vue-router";
import {orderDetail, productDetail} from "@/api/index.js";
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
const route = useRoute()

const onClickLeft = () => history.back();

//请求参数
const product_id = ref({
  product_id: "",
})
//详情数据
const detailData=ref({})
//传递路由参数
product_id.value.product_id = route.query.product_id
// console.log(product_id.value)
//获取请求数据
const getproductDetail= async()=>{
  const res = await productDetail(product_id.value)
  detailData.value=res.data
  console.log(detailData.value)
}

onBeforeMount(()=>{
  getproductDetail()
})
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('goodsDetail.Detail')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <div class="mx-3 mt-3">
      <div class="bg-white p-3 rounded-md flex">
        <div class="w-auto">
          <van-image
              class=""
              width="78"
              height="78"
              radius="10px"
              lazy-load
              :src="detailData.image"
          />
        </div>
        <div class="flex flex-col justify-center   flex-nowrap pl-3">
          <div class="flex-initial leading-5"><span class="text-ellipsis line-clamp_2">{{detailData.title}}</span>
          </div>
          <div class="flex-auto">
            <span class="text-neutral-500 text-sm">{{ $t("goodsDetail.look") }}{{ detailData.look }}</span>
            <span class="text-neutral-500 text-sm pl-6">{{ $t("goodsDetail.sales") }} {{detailData.sales_price }}</span>
          </div>
          <div class="antialiased text-base pb-3 text-red-600">{{ $t("goodsDetail.profit1") }}${{detailData.profit}}</div>
        </div>
      </div>
    </div>
    <div class="mx-3 mt-0.5 bg-white rounded-md">
      <div class="flex justify-between items-center px-3 py-3">
        <span class="text-gray-500">{{ $t("goodsDetail.looks") }}</span>
        <span>{{ detailData.look }}</span>
      </div>
      <div class="flex justify-between items-center px-3 py-3">
        <span class="text-gray-500">{{ $t("goodsDetail.sales") }}</span>
        <span>{{ detailData.sales_price }}</span>
      </div>
<!--      <div class="flex justify-between items-center px-3 py-3">-->
<!--        <span class="text-gray-500">成本价</span>-->
<!--        <span>{{ detailData.cost_price }}</span>-->
<!--      </div>-->
      <div class="flex justify-between items-center px-3 py-3">
        <span class="text-red-600">{{ $t("goodsDetail.profit2") }}</span>
        <span class="text-red-600">{{ detailData.profit }}</span>
      </div>
      <div class="flex justify-between items-center px-3 py-3">
        <span class="text-gray-500">{{ $t("goodsDetail.stock") }}</span>
        <span>{{detailData.stock}}</span>
      </div>
      <div class="flex justify-between items-center px-3 py-3">
        <span class="text-gray-500">{{ $t("goodsDetail.title") }}</span>
        <span>100</span>
      </div>
    </div>

  </main>
</template>

<style scoped lang="scss">

</style>