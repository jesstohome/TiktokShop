<template>
  <div class="container safari_only">
    <div class="home-header">
      <van-image :src="tiktok" class="tiktok-img" />
      <div class="header-imgs" @click.stop="handlerChooseLanguage">
        <van-space size="0.4rem">
          {{ language }}
          <icon-park name="earth" size="1.8rem" />
        </van-space>
      </div>
    </div>
    <div class="home-content mx-3">
      <Custom-Input :readonly="true" @click="goSearch">
        <template #left>
          <icon-park name="search" size="1.8rem" />
        </template>
        <template #right>
          <icon-park name="find" size="2rem" />
        </template>
      </Custom-Input>
      <div class="swipe-board">
        <van-swipe :autoplay="3000" :show-indicators="false" lazy-render>
          <van-swipe-item v-for="(img, index) in swipeImgs" :key="index + 'board'">
            <van-image :src="img" height="11.7rem" width="100%" />
          </van-swipe-item>
        </van-swipe>
      </div>
      <div class="classes-container">
        <van-space fill size="1.5rem">
          <div
            v-for="item in categories"
            :key="item.category_id"
            :class="selectedClass.name === item.name ? 'item-selected' : ''"
            class="class-item"
            @click.stop="handlerClassClick(item)"
          >
            {{ $t(item['name_en']) || item['name_en'] }}
          </div>
        </van-space>
      </div>
      <div class="hot-product">
        <div class="hot-product-title">
          <div class="title-icon">HOT</div>
          <div class="title-words">{{ $t('hotProduct') }}</div>
        </div>
        <van-swipe v-if="hotProducts.length" :show-indicators="false" lazy-render>
          <van-swipe-item v-for="(item, index) in hotProducts" :key="index + 'product'">
            <div class="hot-product-items">
              <product-card
                v-for="item1 in item"
                :key="item1.product_id"
                :product="item1"
                class="hot-product-item"
                @click="goDetail(item1)"
              />
            </div>
          </van-swipe-item>
        </van-swipe>
        <van-empty v-else :description="$t('noProducts')"> </van-empty>
      </div>
      <!-- <div class="hot-product hot-shop">
        <div class="hot-product-title">
          <div class="title-icon">HOT</div>
          <div class="title-words">{{ $t('hotShop') }}</div>
        </div>
        <van-swipe
          :autoplay="3000"
          :show-indicators="false"
          lazy-render
          style="height: 390px"
          vertical
        >
          <van-swipe-item v-for="(item, index) in hotShops" :key="index + 'shop'">
            <div class="hot-shop-items">
              <van-space direction="vertical" size="10px">
                <hot-shop v-for="item1 in item" :key="item1.mer_id" :shop="item1" />
              </van-space>
            </div>
          </van-swipe-item>
        </van-swipe>
      </div> -->
      <div class="hot-product hot-shop">
        <div class="hot-product-title">
          <div class="title-icon">RECOMEND</div>
          <div class="title-words">{{ $t('recommend') }}</div>
        </div>
        <van-space style="margin-top: 0.5rem;" direction="vertical" size="0.5rem">
          <product-horiz-card 
            v-for="(item, index) in recommends"
            :key="index+'recommend'"
            :product="item"
            @click="goDetail(item)"
          />
        </van-space>
      </div>
    </div>
    <van-back-top right="5vw" bottom="7vh" />
  </div>
  <choose-language ref="chooseLanguage" />
  <AppTabbar />
</template>
<script name="Home" setup>
import tiktok from '@/assets/image/titok-wholesale.png'
import CustomInput from '@/components/Input/index.vue'
import homeBoard from '@/assets/image/home-board.png'
import ProductCard from '@/components/ProductCard/index.vue'
import ProductHorizCard from '@/components/ProductCard/horiz.vue'
// import HotShop from './components/HotShop/index.vue'
import ChooseLanguage from '@/components/ChooseLanguage/index.vue'
import { banner, product } from '@/api/home.js'
import { search  } from '@/api/product.js'
import { splitArray } from '@/utils/tool.js'
import toast from '@/utils/toast.js'
import useBasicData from '@/stores/modules/basicData.js'
import useUserStore from '@/stores/modules/user.js'
import AppTabbar from '@/components/AppTabbar/index.vue'
const basicData = useBasicData()
const userStore = useUserStore()
// language
const chooseLanguage = ref(null)
const handlerChooseLanguage = () => {
  chooseLanguage.value.show = true
}
//   language
const swipeImgs = ref([homeBoard, homeBoard])
const categories = ref([])
const selectedClass = ref({})
const hotProducts = ref([])
// const hotShops = ref([])
const recommends = ref([])
const router = useRouter()
const goSearch = () => {
  router.push({ name: 'Search' })
}
const handlerClassClick = (item) => {
  if(item===selectedClass.value){
    return
  }
  toast.loading()
  product({
    category_id: item.category_id,
    page: 1,
    limit: 8
  })
    .then((res) => {
      selectedClass.value = item
      hotProducts.value = splitArray(res.data.list, 4)
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
const language = computed(() => {
  return userStore.getLanguageName()
})
onMounted(async () => {
  toast.loading()
  const responses1 = await Promise.all([
    banner()
      .then((res) => res)
      .catch((err) => err),
    basicData
      .getCategories()
      .then((res) => res)
      .catch((err) => err)
  ])
  swipeImgs.value = responses1[0].data
  categories.value = responses1[1]
  selectedClass.value = categories.value[0]
  const responses2 = await Promise.all([
    product({
      category_id: selectedClass.value.category_id,
      page: 1,
      limit: 8
    })
      .then((res) => res)
      .catch((err) => err),
    search({
      page: 1,
      limit: 100,
    }).then(res=>res).catch(err=>err)
    // hotShop()
    //   .then((res) => res)
    //   .catch((err) => err)
  ])
  hotProducts.value = splitArray(responses2[0].data.list, 4)
  // hotShops.value = [responses2[1].data.list, responses2[1].data.list]
  recommends.value = responses2[1].data.list
  toast.close()
})
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');

.container {
  overflow-y: auto;
  height: calc(100vh - 50px);
  padding: 0 !important;

  .home-header {
    background: #ffffff;
    height: 5rem;
    padding: 1.2rem 0.7rem 0.8rem 0.7rem;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;

    .tiktok-img {
      flex: 3;
    }

    .header-imgs {
      flex: 2;
      line-height: 0;
      text-align: right;
    }
  }

  .home-content {
    padding-top: 1rem;
    display: flex;
    flex-direction: column;
    align-items: stretch;

    .swipe-board {
      padding-top: 0.7rem;
    }

    .classes-container {
      padding-top: 1rem;
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

    .hot-product {
      padding-top: 1rem;
      display: flex;
      flex-direction: column;
      align-items: stretch;

      .hot-product-title {
        display: flex;
        flex-direction: row;
        align-items: center;

        .title-icon {
          font-weight: 500;
          font-size: 0.8rem;
          color: #ffffff;
          padding: 1px 0.5rem;
          background: linear-gradient(129deg, #6b6b6b 0%, #1c1b1b 100%);
          border-radius: 0.7rem;
        }

        .title-words {
          padding-left: 0.5rem;
          font-size: 1rem;
          font-weight: 600;
        }
      }

      .hot-product-items {
        padding-top: 0.5rem;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        flex-wrap: wrap;

        .hot-product-item {
          flex: 0 0 calc(50% - 5px);
          margin-bottom: 10px;
        }

        ::v-deep(.van-swipe-item) {
          flex: 0 0 calc(50% - 5px);
          margin-bottom: 10px;
        }
      }
    }

    .hot-shop {
      padding-top: 0.2rem;

      .hot-shop-items {
        padding-top: 10px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
      }
    }
  }
}
</style>
