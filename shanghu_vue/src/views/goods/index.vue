<script setup>

import {handleProduct, productList, productManagement, removeProduct} from "@/api/index.js";
import {showFailToast, showSuccessToast} from "vant";
import {useI18n} from 'vue-i18n';
import {useUserStore} from "@/store/modules/user.js";
//多语言
const {t} = useI18n();
const userStore = useUserStore();
//返回
const router = useRouter();
const onClickLeft = () => {
  router.go(-1);
};

//去详情页
const toDetail = (data) => {
  console.log(data);
  router.push({path: '/goodsDetail', query: {product_id: data}});
};

//tab标签
const tabs = ref([{
  switch: "all",
  title: t("goods.all"),
},
  {
    switch: "1",
    title: t("goods.selling"),
  },
  {
    switch: "0",
    title: t("goods.store"),
  }]);
// 获取商品列表
const List = ref([]);
const listQuery = ref({
  switch: 'all',
  page: '1',
  limit: '10',
  title: ''
});
const loading = ref(false);
const finished = ref(false);
const onload = (id) => {
  const show = showLoadingToast({
    message: t("goods.loading"),
    forbidClick: true,
    duration: 0
  });
  show.close();
  if (id !== undefined) {
    show.open();
    List.value = [];
    listQuery.value.page = '1';
    listQuery.value.switch = id;
    finished.value = false;
  }
  // console.log(listQuery.value);
  productManagement(listQuery.value).then(res => {
    show.close();
    listQuery.value.page++;
    // console.log(List.value);
    if (res.data.list.length <= 0) {
      // console.log('结束');
      finished.value = true;
    } else {
      List.value.push(...res.data.list);
      console.log(List.value);
    }
    loading.value = false;
  });
};

//点击传递标签参数
const onClickTab = ({name}) => {
  loading.value = true;
  searchValue.value = '';
  listQuery.value.title = '';
  onload(name);
};

//搜索
const searchValue = ref('');
const onSearch = async () => {
  List.value = [];
  finished.value = false;
  listQuery.value.page = '1';
  listQuery.value.title = searchValue.value;
  loading.value = true;
  const res = await productManagement(listQuery.value);
  console.log(res);
  loading.value = false;
  let number = parseInt(listQuery.value.page);

  // console.log(res)
  number++;
  listQuery.value.page = number.toString();

  if (res.data.list.length <= 0) {
    finished.value = true;
  }
  List.value.push(...res.data.list);
};

//右滑删除商品
const removeQuery = ref({
  ids: ''
});
const toRemoveProduct = async (id) => {
  removeQuery.value.ids = id;
  const res = await removeProduct(removeQuery.value);
  if (res.code === 1) {
    List.value = List.value.filter(item => item.product_id !== id);
    showSuccessToast(res.msg);
  } else {
    showFailToast(res.msg);
  }
};
//商品上下架
const query = ref({
  id: '',
  switch: ''
});
const productMove = async (id, product_id, data) => {
  if (userStore.MerInfo.status === 1) {
    // console.log(data)
    query.value.id = id;
    if (data === 1) {
      query.value.switch = "0";
    } else {
      query.value.switch = "1";
    }
    // console.log(query.value)
    //处理商品上下架接口
    const res = await handleProduct(query.value);
    if (res.code === 1) {
      showSuccessToast(res.msg);
    } else {
      showFailToast(res.msg);
    }
    //商品点击上下架之后全部页面 把swith标签改相反,其他页面上下架直接删除
    if (listQuery.value.switch == 'all') {
      List.value = List.value.map(item => {
        if (item.product_id === product_id) {
          return {...item, switch: item.switch == 1 ? 0 : 1};
        } else {
          return item;
        }
      });
    } else {
      List.value = List.value.filter(item => item.product_id !== product_id);
    }
  } else {
    showFailToast(t("over"));
  }
};
onBeforeMount(() => {
  loading.value = true;
  onload();
  userStore.toGetMerInfo();
});
</script>

<template>
  <header class="sticky top-0 z-10 pb-1.5" style="background-color: #F7F7F7">
    <van-nav-bar
        :left-text="$t('goback')"
        :title="$t('goods.ProductManagement')"
        left-arrow
        @click-left="onClickLeft"
    />
    <div class="mx-3 mt-3 rounded-md   back_4">
      <van-tabs @click-tab="onClickTab">
        <van-tab v-for="item in tabs" :key="item.switch" :name="item.switch" :title="item.title">
        </van-tab>
      </van-tabs>
      <div>
        <van-search v-model="searchValue" :placeholder="$t('goods.searchinput')" clearable @search="onSearch"/>
      </div>
    </div>
  </header>
  <main>
    <div v-if="List.length<=0">
      <van-empty/>
    </div>
    <div>
      <van-list
          v-model:loading="loading"
          :finished="finished"
          :finished-text="$t('goods.nomore')"
          :immediate-check="false"
          @load="onload()"
      >

        <div v-for="item in List " class="bg-white mx-3 mt-3 back_4">
          <van-swipe-cell>
            <div class="flex p-3" @click="toDetail(item.product_id)">
              <div class="p-3 ">
                <van-image
                    :src="item.goods.image"
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
              <div class="ml-1 flex-1 mb-6 mt-2 flex flex-col justify-between ">
                <div class="my-3">
            <span class="line-clamp_2">
              {{ item.goods.title }}
            </span>
                </div>
                <div class="flex justify-between ">
                  <div class=" mr-3 py-1 ">
                    <span class="text-neutral-500 text-sm">{{ $t("goods.price") }}</span>
                    <span class="text-lg font-semibold pl-2 ">{{ item.goods.sales_price }}</span>
                  </div>
                  <div class=" mr-3 py-1 ">
                    <span class="text-neutral-500 text-sm">{{ $t("goods.profit") }}</span>
                    <span class="text-lg font-semibold pl-2 ">{{ item.profit }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div>
              <div class="flex items-center  px-5  pb-3 -mt-5">
                <div class="text-gray-400 flex items-center">
                  <span v-if="item.switch===0" style="padding-top: 5px">{{ $t("goods.out") }}</span>
                  <span v-if="item.switch===1" style="padding-top: 5px">{{ $t("goods.get") }}</span>
                </div>
                <!--<div class="ml-auto px-4 py-2 mb-1 mr-1 bg-black rounded text-white">-->
                <!--  <span>调整</span>-->
                <!--</div>-->
                <div class="ml-auto px-4 py-2 mb-1 bg-black rounded text-white"
                     @click="productMove(item.id,item.product_id, item.switch)">
                  <span v-if="item.switch===0">{{ $t("goods.OnShelf") }}</span>
                  <span v-else>{{ $t("goods.OffShelf") }}</span>
                </div>
              </div>
            </div>
            <template #right>
              <van-button :text="$t('goods.remove')" class="delete-button" square type="danger"
                          @click="toRemoveProduct(item.product_id)"/>
            </template>
          </van-swipe-cell>
        </div>


      </van-list>
    </div>
  </main>
</template>

<style lang="scss" scoped>
.delete-button {
  height: 100%;
}
</style>
