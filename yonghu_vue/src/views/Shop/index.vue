<template>
  <div class="container">
    <div class="top">
      <div class="top-item">
        <div class="left-icon" @click="router.back()">
          <van-icon name="arrow-left" color="#191919" size="2rem" />
        </div>
        <van-image round width="3.4rem" :src="shopData.mer_avatar" />
        <div class="shop-info">
          <div class="shop-info-name">{{ shopData.mer_name }}</div>
          {{ shopData.follow_count }}&nbsp;&nbsp;{{ $t('fans') }}
        </div>
      </div>
      <div class="top-item" style="padding-right: 0.5rem" @click="handlerFollowShop">
        <div class="follow-shop">
          {{ shopData.is_follow ? $t('followed') : $t('followShop') }}
        </div>
      </div>
    </div>
    <van-swipe :autoplay="3000" :show-indicators="false" lazy-render>
      <van-swipe-item v-for="(item, index) in banners" :key="index + 'banner'">
        <van-image height="8.5rem" width="100%" :src="item" />
      </van-swipe-item>
    </van-swipe>
    <div class="content">
      <div class="classes-container">
        <van-space size="1.5rem" fill>
          <div
            class="class-item"
            :class="queryParams.category_id === item.category_id ? 'item-selected' : ''"
            v-for="item in categories"
            :key="item.category_id"
            @click.stop="handlerClassClick(item)"
          >
            {{ $t(item['name_en'])  || item['name_en']}}
          </div>
        </van-space>
      </div>
      <div v-if="shopData.product_list?.list.length" class="products">
        <product-card
          class="hot-product-item"
          v-for="item in shopData.product_list?.list"
          :key="item.product_id"
          :product="item"
          @click="goDetail(item)"
        />
      </div>
      <van-empty v-else :description="$t('noRecord')" />
    </div>
  </div>
</template>
<script setup>
import Board from '@/assets/image/home-board.png'
import { shopDetail, follow } from '@/api/merchant.js'
import toast from '@/utils/toast.js'
import useBasicData from '@/stores/modules/basicData.js'
import ProductCard from '@/components/ProductCard/index.vue'
const { proxy } = getCurrentInstance()
const basicData = useBasicData()
const queryParams = ref({
  mer_id: undefined,
  page: 1,
  limit: 10,
  category_id: undefined
})
const handlerFollowShop = () => {
  toast.loading({})
  follow({
    mer_id: queryParams.value.mer_id
  })
    .then((res) => {
      toast.success({
        msg: shopData.value.is_follow ? proxy.t('unFollow') : proxy.t('followSucces')
      })
      shopData.value.is_follow = 1
      shopDetail(queryParams.value)
        .then((res) => {
          shopData.value = res.data
        })
        .catch((err) => err)
    })
    .catch((err) => err)
}
const categories = ref([])
const handlerClassClick = (item) => {
  toast.loading({ msg: '加载中...' })
  queryParams.value.category_id = item.category_id
  shopDetail(queryParams.value)
    .then((res) => {
      shopData.value = res.data
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
const goDetail = (product) => {
  router.push({
    path: `/detail/${product.product_id}/${product.mer_id}`
  })
}
const shopData = ref({})
const banners = computed(() => {
  if (shopData.value.banner) {
    return shopData.value.banner.split(',')
  }
  //  商户没有轮播图的话,就是放两张默认的
  return [Board, Board]
})
const route = useRoute()
const router = useRouter()
onMounted(async () => {
  toast.loading()
  queryParams.value.mer_id = route.params.id
  const _categories = await basicData.getCategories()
  categories.value = _categories
  queryParams.value.category_id = categories.value[0].category_id
  shopDetail(queryParams.value)
    .then((res) => {
      shopData.value = res.data
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
})
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  overflow-y: hidden;
  .top {
    height: 70px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    .top-item {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      .left-icon {
        padding: 0 1rem 0 0;
      }
      .shop-info {
        padding-left: 0.6rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        font-weight: 400;
        font-size: 0.72rem;
        color: #191919;
        line-height: 0.85rem;
        .shop-info-name {
          font-weight: 500;
          font-size: 1rem;
          color: #191919;
          line-height: 1.2rem;
          padding-bottom: 0.3rem;
        }
      }
      .follow-shop {
        padding: 0.8rem 1.2rem;
        background: linear-gradient(92deg, #191919 0%, #7f7f7f 100%);
        border-radius: 1.2rem;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.82rem;
        color: #ffffff;
        line-height: 1rem;
      }
    }
  }
  .content {
    padding: 0;
    overflow-y: auto;
    height: calc(100dvh - 190px);
    .classes-container {
      padding-top: 0.8rem;
      min-height: 3.5rem;
      overflow-x: auto;
      .class-item {
        white-space: nowrap;
        font-size: 1rem;
        font-weight: 400;
        padding-bottom: 0.5rem;
      }
      .item-selected {
        font-weight: 600;
        border-bottom: 1px solid #191919;
      }
    }
    .products {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      flex-wrap: wrap;
      .hot-product-item {
        flex: 0 0 calc(50% - 0.36rem);
        margin-bottom: 0.72rem;
      }
    }
  }
}
</style>
