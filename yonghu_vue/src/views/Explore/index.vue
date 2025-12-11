<template>
  <div class="container">
    <nav-bar :title="$t('explore')" :can-back="false" />
    <div class="content">
      <custom-input :value="hotProductTitle" @blur="blurTitleInput" readonly @click="goSearch">
        <template #left>
          <icon-park name="search" size="1.8rem" />
        </template>
        <template #right>
          <icon-park name="close" size="1.6rem" @click.stop="clearSearch" />
        </template>
      </custom-input>
      <div class="tag-row">
        <van-space size="10px">
          <div
            class="tag"
            v-for="item in categories"
            :key="item.category_id"
            :class="categorey?.category_id === item.category_id ? 'selected-tag' : ''"
            @click.stop="handlerCategoreyClick(item)"
          >
            {{ $t(item['name_en']) || item['name_en'] }}
          </div>
        </van-space>
      </div>
      <div class="popular-row">
        <div class="row-title">
          {{ $t('hotProduct') }}
          <div @click.stop="handlerGoProducts" class="sub-title">{{ $t('viewAll') }}</div>
        </div>
        <div v-if="products.length" class="popular-items">
          <van-space size="0.6rem">
            <product-card
              class="popular-item"
              v-for="item in products"
              :key="item.product_id + 'popular'"
              :product="item"
              @click="goDetail(item)"
            />
          </van-space>
        </div>
        <van-empty v-else :description="$t('noProducts')"> </van-empty>
      </div>
      <div class="recommend-row">
        <div class="row-title">
          {{ $t('recommend') }}
          <div @click.stop="handlerGoProducts" class="sub-title">{{ $t('viewAll') }}</div>
        </div>
        <div v-if="remcommends.length" class="recommend-items">
          <van-space direction="vertical">
            <horiz-product-card
              class="horiz-popular-item"
              v-for="item in remcommends"
              :key="item.product_id + 'recommend'"
              :product="item"
              @click="goDetail(item)"
            />
          </van-space>
        </div>
        <van-empty v-else :description="$t('noProducts')"> </van-empty>
      </div>
    </div>
  </div>
  <AppTabbar />
</template>
<script setup name="Explore">
import throttle from 'lodash/throttle'
import NavBar from '@/components/CustomNavBar/index.vue'
import CustomInput from '@/components/Input/index.vue'
import ProductCard from '@/components/ProductCard/index.vue'
import HorizProductCard from '@/components/ProductCard/horiz.vue'
import useBasicData from '@/stores/modules/basicData.js'
import { recommend } from '@/api/product.js'
import { hotProduct } from '@/api/home.js'
import toast from '@/utils/toast.js'
const { proxy } = getCurrentInstance()
const queryHotProduct = () => {
  toast.loading()
  hotProduct({
    page: 1,
    limit: 10,
    title: hotProductTitle.value
  })
    .then((res) => {
      products.value = res.data.list
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
const hotProductTitle = ref(undefined)
const blurTitleInput = throttle(
  (val) => {
    hotProductTitle.value = val
    queryHotProduct()
  },
  1000,
  { trailing: true }
)
const goSearch = () => {
  router.push({ name: 'Search' })
}
const clearSearch = () => {
  hotProductTitle.value = undefined
  queryHotProduct()
}
const basicData = useBasicData()
const categories = ref([])
const categorey = ref(null)
const products = ref([])
const remcommends = ref([])
const handlerCategoreyClick = (item) => {
  toast.loading()
  categorey.value = item
  recommend({
    category_id: categorey.value?.category_id,
    page: 1,
    limit: 8
  })
    .then((res) => {
      remcommends.value = res.data.list
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
const router = useRouter()
const handlerGoProducts = () => {
  router.push({ name: 'Products' })
}
const goDetail = (product) => {
  router.push({
    path: `/detail/${product.product_id}/${product.mer_id}`
  })
}
const getData = () => {
  toast.loading()
  Promise.all([
    hotProduct({
      page: 1,
      limit: 10,
      title: hotProductTitle.value
    })
      .then((res) => res)
      .catch((err) => err),
    recommend({
      category_id: categorey.value?.category_id,
      page: 1,
      limit: 8
    })
      .then((res) => res)
      .catch((err) => err)
  ])
    .then((responses) => {
      products.value = responses[0].data.list
      remcommends.value = responses[1].data.list
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
onMounted(async () => {
  categories.value = await basicData.getCategories()
  getData()
})
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;
  .content {
    padding: 0 1rem 1.2rem 1rem;
    height: calc(100dvh - (50px + 50px));
    .tag-row {
      min-height: 3.2rem;
      flex-wrap: nowrap;
      align-items: center;
      overflow-x: auto;
      .tag {
        white-space: nowrap;
      }
    }
    .popular-row {
      padding-top: 0.5rem;
      display: flex;
      flex-direction: column;
      .popular-items {
        padding-bottom: 0.8rem;
        overflow-x: auto;
        display: flex;
        flex-direction: row;
        .popular-item {
          width: calc((100vw - 20px) / 2.5);
        }
      }
    }
    .recommend-row {
      padding-top: 0.5rem;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      .recommend-items {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        .horiz-popular-item {
          height: 8.6rem;
        }
      }
    }
  }
}
</style>
