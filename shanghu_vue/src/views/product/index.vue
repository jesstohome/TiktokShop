<script setup>
import {useTheme} from "@/utils/useTheme.js";
import tabbers from '@/components/tabbar/index.vue';
import {hotProduct} from '@/api/index.js'
import {useUserStore} from '@/store/modules/user.js';
import {useRouter} from 'vue-router'
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
const {theme} = useTheme();
const router = useRouter();
//仓库中获取商户信息请求函数
const userStore = useUserStore();

//商户信息
const merInfo = ref({})
merInfo.value = userStore.MerInfo

//切换主题
const switchThemes = () => {
  theme.value = theme.value === 'light' ? 'dark' : 'light';
};

//商品列表
const product = ref([])
const gethotProduct = async () => {
  const res = await hotProduct();
  console.log(res.data)
  product.value = res.data;
}
onMounted(() => {
  gethotProduct()
})
</script>

<template>
  <div class="container_mix">
    <header class="mx-3 bg-black text-white rounded-md mt-3 back_4">
      <div class="flex py-3">
        <div class="flex items-center  mr-auto ml-3">
          <van-image
              width="56px"
              height="56px"
              round
              :src="merInfo.mer_avatar"
          />
          <div class="flex flex-col ml-3">
            <div>
              <span class="font-semibold text-xl">{{ merInfo.mer_name }}</span>
              <span class="text-xs text-white px-3 py-0.5 rounded-lg bg-amber-500 ml-1.5">{{ $t("product.ProductManagement") }}</span>
            </div>
            <div><span class="text-sm">{{ $t("product.crossEcommerce") }}</span></div>
          </div>
        </div>
        <div class="flex items-center" @click="router.push('/setting')">
          <van-icon size="24" class="ml-auto mr-3" name="setting-o"/>
        </div>
      </div>
    </header>
    <main class=" mx-3 mt-3">
      <!--    商品分类-->
      <div class="back_3 back_4">
        <h5 class="antialiased font-semibold text mx-3 pt-3">
          {{ $t("product.ProductCategory") }}
        </h5>
        <div class="mx-3 flex pt-3 my-2" style="" @click="router.push('/distribution')">
          <div class="flex items-center">
            <van-icon size="24" name="cart-circle-o"/>
            <span class="ml-3"> {{ $t("distribution.distributionCenter") }}</span>
          </div>
          <div class="ml-auto mr-3 my-1">
            <van-icon name="arrow"/>
          </div>
        </div>
        <div class="mx-3 flex py-5  " @click="router.push('/goods')">
          <div class="flex items-center">
            <van-icon size="24" name="cart-o"/>
            <span class="ml-3">{{ $t("product.ProductManagement") }}</span>
          </div>
          <div class="ml-auto mr-3">
            <van-icon name="arrow"/>
          </div>
        </div>
        <div class="mx-3 flex py-3 " style="display:none" @click="router.push('/goods')">
          <div class="flex items-center">
            <van-icon size="24" name="cart-o"/>
            <span class="ml-3">{{ $t("product.ProductManagement") }}</span>
          </div>
          <div class="ml-auto mr-3">
            <van-icon name="arrow"/>
          </div>
        </div>
      </div>
      <!--    热销商品-->
      <div class="mt-3 back_3 back_4">
        <h5 class="antialiased font-semibold text mx-3 pt-3 ">
          {{ $t("product.BestSellingProducts") }}TOP10
        </h5>
        <div v-if="product.length<=0">
          <van-empty :description= "$t('product.NoBestSellingProductsYet')"/>
        </div>
        <div class="bg-slate-50 p-3 mx-3 rounded-md mt-3 flex back_2" v-for="item in product"  @click="router.push({ path: '/goodsDetail', query: { product_id: item.product_id } })">
          <div class="w-auto">
            <van-image
                width="78"
                height="78"
                radius="10px"
                lazy-load
                :src="item.goods.image"
            >
              <template v-slot:loading>
                <van-loading type="spinner" size="20"/>
              </template>
            </van-image>
          </div>
          <div class="flex flex-col justify-center  flex-1  flex-nowrap pl-3">
            <div class="flex-initial my-1.5 leading-5"><span class="text-ellipsis line-clamp_2">{{ item.goods.title }}</span>
            </div>
            <div class="flex-auto my-1.5">
              <span class="text-neutral-500 text-sm">{{ $t("product.Click") }} {{ item.click }}</span>

              <span class="text-neutral-500 text-sm pl-6">{{ $t("product.Selling") }}{{ item.sales }}</span>
              <span class="text-neutral-500 text-sm pl-6">{{ $t("product.shoujia") }} {{ item.goods.sales_price }}</span>
            </div>
            <div class="antialiased text-lg text-blue-500">{{ item.sales_price }}</div>
          </div>
        </div>
        <div class="h-4"></div>
      </div>
	  <div style="height: 5rem;"></div>
    </main>
  </div>
  <tabbers></tabbers>

</template>

<style scoped lang="scss">
.bottom-button {
  width: 160px;
  height: 40px;
  background-color: black;
  border: 0 solid white;
}
</style>
