<script setup>
import {useRoute, useRouter} from "vue-router";
import {orderDetail} from "@/api/index.js";
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
//路由
const router = useRouter();
const route = useRoute()
//返回
const onClickLeft = () => {
  router.back();
};
//请求参数
const id = ref({
  id: "",
})
id.value.id = route.query.id
// console.log(id.value)

//订单信息
const getdata = ref({})
//商品信息
const product = ref([])
//物流信息
const delivery = ref([])
const delivery_no = ref('')
//获取请求数据
const getorderDetail = async () => {
  const res = await orderDetail(id.value)
  getdata.value = res.data
  // console.log(getdata.value)
  product.value = getdata.value.product
  // console.log(product.value)
  delivery.value = res.data.delivery
  // console.log(delivery.value)
  delivery_no.value = delivery.value[0]?.delivery_no
  // console.log(delivery_no.value)
}

//复制函数
const copy = (textToCopy) => {
  // 创建一个临时的textarea元素
  const textarea = document.createElement('textarea');

  // 将要复制的文本设置为textarea的值
  textarea.value = textToCopy;

  // 将textarea隐藏
  textarea.style.position = 'fixed';
  textarea.style.opacity = 0;

  // 将textarea添加到DOM中
  document.body.appendChild(textarea);

  // 选择textarea中的文本
  textarea.select();

  // 执行复制命令
  document.execCommand('copy');

  // 删除textarea元素
  document.body.removeChild(textarea);

  showToast('复制成功');
};

onMounted(() => {
  getorderDetail()
})
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('orderDetail.orderDetail')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <div class="mx-3 mt-3 rounded-md">
      <!--      商品详情-->
      <div class="bg-white p-3 rounded-md back_4" >
<!--        <div class="flex items-center pt-1.5">-->
<!--          <van-icon size="24" class="mx-3" name="ellipsis"/>-->
<!--        </div>-->
        <div class="flex px-3 my-2" v-for="item in product">
          <div>
            <van-image
                width="100"
                height="100"
                radius="10px"
                lazy-load
                :src="item.image"
            >
              <template v-slot:loading>
                <van-loading type="spinner" size="20"/>
              </template>
            </van-image>
          </div>
          <div class="ml-3 flex-1">
            <div>
            <span class="font-semibold line-clamp_2">
              {{ item.title }}
            </span>
            </div>
            <div class="text-left mr-3 py-1 mt-5 ">
              <div class="flex justify-end">
                <span class="text-neutral-500 text-sm">{{ $t("orderDetail.number") }}</span>
                <span class="text-neutral-500 text-sm ml-3">x{{ item.product_num }}</span>
              </div>
              <div class="flex justify-end">
                <span class="text-neutral-500 text-sm">{{ $t("orderDetail.price") }}</span>
                <span class="text-neutral-500 text-sm ml-3">{{ item.total_price }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--      订单详情-->
      <div class="bg-white p-3 rounded-md back_4 my-3">
        <div>
          <div class="flex items-center py-1">
            <span>{{ $t("orderDetail.orderNumber") }}</span>
            <span class="font-semibold">{{ getdata.order_sn }}</span>
            <span class="bg-amber-800 px-3 py-0.5 rounded-md ml-3 text-white text-sm"
                  @click="copy(getdata.order_sn)">{{ $t("orderDetail.copy") }}</span>
          </div>
          <div class="py-1">
            <span>{{ $t("orderDetail.shippingAddress") }}</span>
            <span>{{ getdata.user_address }}</span>
          </div>
          <div class="py-1">
            <span>{{ $t("orderDetail.phoneNumber") }}</span>
            <span>{{ getdata.user_phone }}</span>
          </div>
          <div class="py-1">
            <span>{{ $t("orderDetail.orderTime") }}</span>
            <span>{{ getdata.createtime }}</span>
          </div>
          <!--        <div class="flex justify-between py-1">-->
          <!--          <span>商品价格:</span>-->
          <!--          <span>{{ product.product_price }}</span>-->
          <!--        </div>-->
          <div class="flex justify-between py-1">
            <span>{{ $t("orderDetail.totalEarnings") }}</span>
            <span>{{ getdata.total_profit }}</span>
          </div>
          <div class="flex justify-between">
            <div></div>
            <div><span>{{ $t("orderDetail.totalPayment") }}</span><span class="ml-3 text-red-500">{{ getdata.total_price }}</span></div>
          </div>
        </div>
        <div class="mx-3 font-semibold text-lg mt-3">
          <h3>{{ $t("orderDetail.shippingProgress") }}</h3>
          <p>{{ $t("orderDetail.trackingNumber") }}{{ delivery_no }}</p>
        </div>
        <van-steps direction="vertical" :active="0">
          <van-step v-for="item in delivery" :key="item.id">
            <div>
              <p>{{ $t("orderDetail.shippingStatus") }}{{ item.mark }}</p>
              <p>{{ $t("orderDetail.executionTime") }}{{ item.updatetime }}</p>
            </div>
          </van-step>
        </van-steps>
      </div>
    </div>
  </main>
</template>

<style scoped lang="scss">

</style>