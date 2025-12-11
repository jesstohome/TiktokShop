<script setup>
import {addAll, productAdd, productCategory, productList, addAllHot} from "@/api/index.js";
import {useI18n} from 'vue-i18n';
import {useUserStore} from "@/store/modules/user.js";
import {showFailToast} from "vant";
//多语言
const {t} = useI18n();
const router = useRouter();
//仓库
const userStore = useUserStore();
const active = ref(0);

const onClickLeft = () => {
  router.go(-1);
};

//获取分类
const category = ref([]);
const lang = localStorage.getItem('lang');
const getCategory = () => {
  productCategory().then((res) => {
    category.value = res.data;
    category.value.unshift({
      "category_id": 0,
      "name": t("distribution.all"),
      "name_en": t("distribution.all"),
    });
    console.log(category.value);
    // listQuery.value.category_id = res.data[0].category_id;
    onload();
    // console.log(category.value)
    // onload(category.value[0].category_id)
  }).catch((err) => {
    console.log(err);
  });
};
//获取商品列表
const listQuery = ref({
  category_id: '0',
  title: '',
  page: '1',
  limit: '10'
});
const List = ref([]);
// const getProductList = async (data) => {
//   const res = await productList(data);
//   List.value = res.list
// }
//商品列表下拉刷新
const loading = ref(false);
const finished = ref(false);
const onload = (id) => {
  const show = showLoadingToast({
    message: t("distribution.loading"),
    forbidClick: true,
  });
  show.close();
  if (id || id === 0) {
    List.value = [];
    listQuery.value.page = '1';
    listQuery.value.category_id = id;
    finished.value = false;
    show.open();
  }
  // loading.value = true;
  productList(listQuery.value).then(res => {
    show.close();
    loading.value = false;
    let number = parseInt(listQuery.value.page);

    // console.log(res)
    number++;
    listQuery.value.page = number.toString();

    if (res.data.list.length <= 0) {
      finished.value = true;
    }
    List.value.push(...res.data.list);
  });
};
//传递标签参数
const onClickTab = ({name}) => {
  loading.value = true;
  searchValue.value = '';
  listQuery.value.title = '';
  onload(name);
  // console.log(name)
};
//去详情页
const toDetail = (data) => {
  console.log(data);
  router.push({path: '/goodsDetail', query: {product_id: data}});
};
//铺货
const ids = ref({
  ids: ''
});
const push = async (id) => {
  if (userStore.MerInfo.status === 1) {
    ids.value.ids = id;
    const res = await productAdd(ids.value);
    List.value = List.value.filter(item => item.product_id !== id);
    // console.log(List.value);
    if (res.code == 1) {
      showSuccessToast(res.msg);
    } else {
      showFailToast(res.msg);
    }
  } else {
    showFailToast(t("over"));
  }
  // console.log(res);
};
//搜索
const searchValue = ref('');
const onSearch = async () => {
  List.value = [];
  finished.value = false;
  listQuery.value.page = '1';
  listQuery.value.title = searchValue.value;
  loading.value = true;
  const res = await productList(listQuery.value);
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
//一键铺货
const addList = ref({
  ids: []
});
const changeclick = (id) => {
  // console.log(id);
  List.value.forEach(item => {
    if (item.product_id == id) {
      item.checked = !item.checked;
    }
  });
  console.log(List.value);
};

const add = async () => {
  if (userStore.MerInfo.status === 1) {
    List.value.forEach(item => {
      if (item.checked == true) {
        addList.value.ids.push(item.product_id);
        console.log(addList.value.ids);
      }
    });
    console.log(addList.value.ids.length);
    if (addList.value.ids.length > 0) {
      const res = await addAll(addList.value);
      if (res.code == 1) {
        List.value = List.value.filter(item =>
            !addList.value.ids.includes(item.product_id));
        addList.value.ids = [];
        showSuccessToast(res.msg);
      } else {
        showFailToast(res.msg);
      }
    } else {
      showToast(t("distribution.chooseProduct"));
    }
  } else {
    showFailToast(t("over"));
  }
};

const total = computed(() => {
  let num = 0;
  List.value.forEach(item => {
    if (item.checked == true) {
      num++;
    }
  });
  return num;
});

const hot_sales_one_click_distribution = async () => {
  addAllHot().then(res => {
    if (res.code == 1) {
      showSuccessToast(res.msg);
    } else {
      showFailToast(res.msg);
    }
  });
};

onBeforeMount(() => {
  getCategory();
  userStore.toGetMerInfo();
  // console.log(category.value)
  // onload(category.value[0].category_id)
});
</script>

<template>
  <!--导航栏-->
  <header class="sticky top-0 z-10" style="background-color: #F7F7F7">
    <van-nav-bar
        :left-text="$t('goback')"
        :title="$t('distribution.distributionCenter')"
        left-arrow
        @click-left="onClickLeft"
    />
    <div class="mx-3 mt-3 back_4">
      <van-tabs v-model:active="active" @click-tab="onClickTab">
        <van-tab v-for="item in category " :key="item.category_id" :name="item.category_id"
                 :title="lang==='zh-CN'?item.name:item.name_en">
        </van-tab>
      </van-tabs>
      <!--搜索-->
      <div>
        <van-search v-model="searchValue" :placeholder="$t('distribution.inputsearch')" @blur="onSearch"/>
      </div>
    </div>
  </header>
  <main class="mx-3 mt-1">
    <!--标签-->
    <!--商品列表-->
    <div class="mt-3">
      <van-list
          v-model:loading="loading"
          :finished="finished"
          :finished-text="$t('distribution.nomore')"
          :immediate-check="false"
          @load="onload()"
      >
        <div class="grid grid-cols-2 grid-flow-row gap-2">
          <div v-for="item in List" class="bg-white rounded-md back_4 ">
            <div class="flex justify-end pt-3 px-3">
              <van-checkbox v-model="item.clicked" @click="changeclick(item.product_id)"></van-checkbox>
            </div>
            <div class="rounded pl-2 py-2 ">
              <van-image
                  :src="item.image"
                  block
                  fit="fill"
                  height="12rem"
                  lazy-load
                  radius="10px"
                  width="12rem"
                  @click="toDetail(item.product_id)"
              >
                <template v-slot:loading>
                  <van-loading size="20" type="spinner"/>
                </template>
              </van-image>
            </div>
            <div class="px-3">
              <div class="van-ellipsis " @click="toDetail(item.product_id)">
                <span class="font-semibold text-base ">
                    {{ item.title }}
                </span>
              </div>
              <div>
                <span class="text-base">
                    <span class="text-sm text-gray-400 mr-2"> {{ $t("distribution.sells") }}</span>
                    <span class="text-sm text-red-500">{{ item.sales_price }}</span>
                </span>
              </div>
              <div class="flex items-center pt-0.5 py-3">
                <div class="text-sm text-gray-400 van-ellipsis w-8/12" @click="toDetail(item.product_id)">
                  {{ item.title }}
                </div>
                <div class="text-sm text-white ml-auto px-3 bg-black rounded-md py-1" @click="push(item.product_id)">
                  {{ $t("distribution.distribution") }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </van-list>
    </div>
    <div class="fixed bottom-10 left-12 botton_1">
      <div class="flex justify-center">
        <van-badge :content="total">
          <van-button color="rgba(0, 0, 0, 0.8)" round size="small" style="width:150px" @click="add">
            {{ $t("distribution.distributionAll") }}
          </van-button>
        </van-badge>
        <div style="width: 50px;">
        </div>
        <van-button color="rgba(0, 0, 0, 0.8)" round size="small" style="width:150px"
                    @click="hot_sales_one_click_distribution">
          {{ $t('distribution.hotSell') }}
        </van-button>
      </div>
    </div>
  </main>
</template>

<style lang="scss" scoped>
.botton_1 {
  left: 50%;
  transform: translateX(-50%);
}
</style>
