<template>
  <div class="container">
    <nav-bar>
      <slot>
        <div class="nav-bar-items">
          <div
            class="item"
            v-for="item in items"
            :key="item.type"
            :class="selected === item.type ? item.class + ' selected' : item.class"
            @click="handlerItemClick(item.type)"
          >
            {{ item.name }}
          </div>
        </div>
      </slot>
    </nav-bar>
    <div class="content" ref="contentRef">
      <div class="product" ref="productRef">
        <van-swipe class="product-img" :autoplay="-1" indicator-color="#4c4c4c">
          <van-swipe-item v-for="item in imgs" :key="item" style="text-align: center">
            <van-image width="96%" radius="0.5rem" height="220" fit="container" :src="item" />
          </van-swipe-item>
        </van-swipe>
        <div class="product-info">
          <div class="product-info-item">
            <div class="product-info-item-content product-name-row">
              <div class="product-name">{{ detail.goods?.title }}</div>
              <van-icon
                :color="isLike ? '#fe4857' : '#191919'"
                :name="isLike ? 'like' : 'like-o'"
                size="1.4rem"
                @click.stop="toggleLike"
              />
            </div>
          </div>
          <div class="product-info-item">
            <div class="product-info-item-content">
              <div>
                {{ $t('unitPrice') }}
                <span class="product-price">${{ detail.goods?.sales_price }}</span>
              </div>
              <div>{{ $t('stock') }} {{ detail.goods?.stock }}</div>
              <div>{{ $t('sales') }} {{ detail.sales }}</div>
            </div>
          </div>
          <!-- <div class="product-info-item">
            <div class="product-info-item-content">
              <div>运费</div>
              <div>免运费</div>
            </div>
          </div>
          <div class="product-info-item">
            <div class="product-info-item-content">
              <div>税费</div>
              <div>$0.00</div>
            </div>
          </div> -->
          <div v-if="detail.goods?.specList.length">
            <div class="product-info-item">
              <div class="product-info-item-content">
                <van-space size="1rem">
                  <div
                    v-for="(item, index) in detail.goods?.specList"
                    :key="index + 'spec type'"
                    class="product-info-item-spec"
                  >
                    <div
                      class="spec-name"
                      :class="specType === index ? 'spec-selected' : ''"
                      @click.stop="specType = index"
                    >
                      {{ item.name }}
                    </div>
                  </div>
                </van-space>
              </div>
            </div>
            <div class="product-info-item">
              <div class="product-info-item-content">
                <van-space size="1rem">
                  <div
                    v-for="(item, index) in detail.goods?.specList[specType].child"
                    :key="index + 'spec name'"
                    class="product-info-item-spec"
                  >
                    <div
                      class="spec-name"
                      :class="spec[specType] === item ? 'spec-selected' : ''"
                      @click.stop="spec[specType] = item"
                    >
                      {{ item }}
                    </div>
                  </div>
                </van-space>
              </div>
            </div>
          </div>
          <div class="product-info-item">
            <div class="product-info-item-content">
              <div>{{ $t('selectedQuantity') }}</div>
              <van-stepper v-model="detail.number" step="1" />
            </div>
          </div>
        </div>
      </div>
      <div class="comment" ref="commentRef">
        <div class="comment-title">
          {{ $t('userEvlauate') }}&nbsp;（{{ detail.reply_list?.length }}）
        </div>
        <van-space fill v-if="detail.reply_list?.length" direction="vertical" size="0.5rem">
          <div class="comment-card" v-for="item in detail.reply_list">
            <div class="comment-user">
              <van-image width="2rem" :src="item.avatar" round />
              <span style="padding-left: 0.5rem">{{ item.nickname }}</span>
            </div>
            <div class="comment-satisfaction">
              <van-rate
                v-model="item.product_score"
                :size="20"
                color="#191919"
                void-icon="star"
                void-color="#eee"
              />
              <span style="padding-left: 0.5rem">{{ $t('orderCompleted') }}</span>
            </div>
            <div class="words-rows">
              {{ item.comment }}
            </div>
            <div class="comment-pics">
              <van-space size="1rem">
                <div
                  class="comment-pic-card"
                  v-for="(item1, index) in item.pics"
                  :key="index + 'comment-pic'"
                >
                  <van-image width="6rem" height="6rem" :src="item1" />
                </div>
              </van-space>
            </div>
            <div class="comment-time">2024-04-10</div>
          </div>
          <!-- <div
              class="comment-card"
              v-for="(item, index) in detail.replay_list"
              :key="index + 'replay'"
            >
              <div class="comment-user">
                <van-image width="2rem" :src="item.avatar" round />
                <span style="padding-left: 0.5rem">{{ item.nickname }}</span>
              </div>
              <div class="comment-satisfaction">
                <van-rate
                  v-model="item.rate"
                  :size="20"
                  color="#191919"
                  void-icon="star"
                  void-color="#eee"
                />
                <span style="padding-left: 0.5rem">订单已完成</span>
              </div>
              <div class="words-rows">
                {{ item.comment }}
              </div>
              <div class="comment-pics">
                <van-space size="1rem">
                  <div
                    class="comment-pic-card"
                    v-for="(item1, index) in item.pics"
                    :key="index + 'comment-pic'"
                  >
                    <van-image width="100%" height="auto" :src="item1" />
                  </div>
                </van-space>
              </div>
              <div class="comment-time">{{ item.createtime }}</div>
          </div> -->
        </van-space>
        <van-empty v-else style="padding: 0" image-size="6rem" :description="$t('noRecord')" />
      </div>
      <div class="detail" ref="detailRef">
        <div class="detail-title">{{ $t('shopInfo') }}</div>
        <hot-shop :shop="detail.merchant || {}" />
        <div class="detail-title">{{ $t('productDescription') }}</div>
        <div class="product-desc">
          <!-- <div class="words-rows">
            55% 棉，45% 涤纶导入 拉链开合 机洗这款日常经典连帽衫既时尚又舒适最后采用超柔
            软酵素洗液，带来极致舒适感。
          </div> -->
          <!-- <div v-html="detail.goods?.content" /> -->
          <!-- {{ detail.goods?.content }} -->
          <div v-html="desc" />
        </div>
        <div class="detail-title">{{ $t('productRecommend') }}&nbsp;({{ recommends.length }})</div>
        <div v-if="recommends.length" class="recommend-products">
          <product-card
            class="hot-product-item"
            v-for="item in recommends"
            :key="item.name"
            :product="item"
            @click.stop="handlerRecomendDetail(item)"
          />
        </div>
        <van-empty v-else style="padding: 0" image-size="6rem" :description="$t('noRecord')" />
      </div>
    </div>
    <div class="bottom">
      <div class="total-price">
       <van-button @click="goService" style="flex: 8" block round color="#191919">{{
         $t('customerService')
       }}</van-button>
      </div>
	   <div style="width: 1rem" />
      <div class="button">
        <van-button @click="handlerAddCart" style="flex: 8" block round>{{
          $t('addToShopping')
        }}</van-button>
        <div style="width: 1rem" />
        <van-button @click="handlerCreateOrder" style="flex: 6" block round color="#191919">{{
          $t('createOrder')
        }}</van-button>
      </div>
    </div>
    <van-action-sheet :overlay="false" :round="false" v-model:show="showActionSheet">
      <div class="pay-panel-container">
        <div class="title-row">
          <div />
          {{ payInfo.title }}
          <icon-park name="close" size="1.5rem" @click="() => (showActionSheet = false)" />
        </div>
        <div class="money-row">${{ payInfo.money }}</div>
        <div class="order-info">
          <div>订单详情</div>
          <div>商品交易</div>
        </div>
        <div class="card-container" v-for="(card, index) in payInfo.cards" :key="'card-' + index">
          <div class="card-item">
            <icon-park :name="card.icon" size="1.5rem" />
            <span class="card-value">{{ card.value }}</span>
          </div>
        </div>
        <div class="card-container card-container-center">
          <van-icon name="arrow-down" size="1.2rem" color="#191919" />
        </div>
        <div class="pwd-container">
          <van-password-input :value="payPwd" :gutter="10" />
          <van-number-keyboard :show="true" :maxlength="6" v-model="payPwd" @input="onInput" />
        </div>
      </div>
    </van-action-sheet>
  </div>
</template>
<script setup>
import HotShop from '@/views/Home/components/HotShop/index.vue'
import ProductCard from '@/components/ProductCard/index.vue'
import { useElementBounding } from '@vueuse/core'
import useUserStore from '@/stores/modules/user'
import toast from '@/utils/toast.js'
import { getProductDetail, like } from '@/api/product.js'
import { addCart } from '@/api/cart.js'
import { createOrder } from '@/api/order.js'
import { multiply } from '@/utils/math.js'
const { proxy } = getCurrentInstance()
const userStore = useUserStore()
const router = useRouter()
const total = computed(() => {
  if (detail.value.goods) {
    return multiply(detail.value.goods.sales_price, detail.value.number || 0)
  }
  return 0
})

const detail = ref({})
const isLike = computed(() => {
  return detail.value.is_like ? true : false
})
const spec = ref([])
const specType = ref(0)
const desc = ref('')
const imgs = computed(() => {
  if (detail.value.goods) {
    if (detail.value.goods.images) {
      return detail.value.goods.images.split(',')
    }
    return [detail.value.goods.image]
  }
  return []
})
const contentRef = ref(null)
const productRef = ref(null)
const productView = useElementBounding(productRef)
const commentRef = ref(null)
const commentView = useElementBounding(commentRef)
const detailRef = ref(null)
const detailView = useElementBounding(detailRef)
const selected = computed({
  get() {
    const array = [
      Math.abs(productView.top.value),
      Math.abs(commentView.top.value),
      Math.abs(detailView.top.value)
    ]
    const min = Math.min(...array)
    const index = array.indexOf(min)
    switch (index) {
      case 0:
        return 'product'
      case 1:
        return 'comment'
      case 2:
        return 'detail'
      default:
        return 'product'
    }
  },
  set(val) {}
})
const items = ref([
  { name: proxy.t('product'), type: 'product', class: 'left' },
  { name: proxy.t('evaluate'), type: 'comment' },
  { name: proxy.t('detail'), type: 'detail', class: 'right' }
])
const toggleLike = () => {
  like({
    mer_id: detail.value.mer_id,
    product_id: detail.value.product_id
  })
    .then((res) => {
      detail.value.is_like = detail.value.is_like ? 0 : 1
      toast.success({ msg: res.msg })
    })
    .catch((err) => err)
}
const handlerItemClick = (type) => {
  if (type === selected.value) {
    return
  }
  let _ref
  switch (type) {
    case 'product':
      _ref = productRef
      break
    case 'comment':
      _ref = commentRef
      break
    case 'detail':
      _ref = detailRef
      break
    default:
      break
  }
  selected.value = type
  if (_ref) {
    _ref.value.scrollIntoView({ behavior: 'smooth' })
  }
}

const recommends = ref([])
//  pay
const payInfo = ref({
  title: '请输入支付密码',
  money: 990.0,
  cards: [
    {
      value: '钱包余额 ($2.345,00)',
      icon: 'wallet-two'
    },
    {
      value: '万事达银行卡（暂不支持）',
      icon: 'bank-card-cp41pae1'
    }
  ]
})
const showActionSheet = ref(false)
const payPwd = ref(undefined)
const handlerRecomendDetail=item=>{
  router.push({
    path: `/detail/${item.product_id}/${item.mer_id}`
  })
}
const product_id = useRoute().params.mer

const goService = () => {
	console.log(product_id)
  router.push({ name: `Service`, query:{pid:`${product_id}`} })
}
const handlerAddCart = () => {
  userStore
    .isLogin()
    .then(() => {
      toast.loading()
      addCart({
        mer_id: detail.value.mer_id,
        product_id: detail.value.product_id,
        num: detail.value.number,
        spec: spec.value.join('|'),
		id:detail.value.id
      })
        .then((res) => {
          toast.success({ msg: res.msg })
        })
        .catch((err) => err)
    })
    .catch((err) => {
      console.log(err)
      toast.show({ msg: proxy.t('loginFirst') })
      router.push('/login')
    })
}
const handlerCreateOrder = () => {
  userStore
    .isLogin()
    .then(() => {
      //  创建订单
      toast.loading()
      createOrder({
        spec: spec.value.join('|'),
        product_id: detail.value.product_id,
        number: detail.value.number,
        mer_id: detail.value.mer_id,
		id:detail.value.id
      })
        .then((res) => {
          toast.success({ msg: proxy.t('orderCreateSuccess') })
          router.push({ name: 'Order', query: { orderData: JSON.stringify(res.data) } })
        })
        .catch((err) => {})
    })
    .catch((err) => {
      console.log(err)
      toast.show({ msg: proxy.t('loginFirst') })
      router.push('/login')
    })
}
const showPay = () => {
  payPwd.value = undefined
  showActionSheet.value = true
}
const route = useRoute()
const query=()=>{
  toast.loading()
  const _params = route.params
  getProductDetail({
    mer_id: _params.mer,
    product_id: _params.product
  })
    .then((res) => {
      detail.value = res.data
      detail.value.number = 1
      recommends.value = res.data.other
      if (res.data.goods?.content) {
        desc.value = res.data.goods.content.replace('/\/g', '')
      }
      if(res.data.reply_list.length){
        detail.value.reply_list=res.data.reply_list.map(item=>{
          item.pics=item.pics.split(',').map(pic=>{
            return pic.replace('/\/g', '')
          })
          return item
        })
      }
      //  有规格的话
      if (detail.value.goods?.specList.length) {
        spec.value = detail.value.goods?.specList.map((item) => {
          return item.child[0]
        })
      }
    })
    .finally(() => {
      if(productRef.value&&selected.value!=='product'){
        productRef.value.scrollIntoView({ behavior: 'smooth' })
      }
      toast.close()
    })
}
watch(
  ()=>route.params.product, 
  query,
  {
    immediate: false
  }
)
onMounted(query)
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;
  position: fixed;
  top: 0;
  .nav-bar-items {
    // pointer-events: none;
    display: flex;
    flex-direction: row;
    align-items: center;
    position: absolute;
    width: 100%;
    .item {
      flex: 5;
      text-align: center;
      font-size: 1.2rem;
      font-weight: 400;
      color: #191919;
      line-height: 1.5rem;
    }
    .selected {
      font-weight: 500;
      font-size: 1.2rem;
      color: #191919;
      color: red;
      line-height: 1.5rem;
    }
    .left {
      flex: 5;
      text-align: right;
    }
    .right {
      flex: 4;
      text-align: left;
    }
  }
  .content {
    padding-bottom: 0;
    overflow-y: auto;
    height: calc(100dvh - (50px + 70px));
    -webkit-overflow-scrolling: touch;
    .words-rows {
      padding-top: 0.5rem;
      line-height: 1.4rem;
      letter-spacing: 0.5px;
    }
    .product {
      padding: 0rem 0 1rem 0;
      ::v-deep(.van-swipe__indicators) {
        bottom: 0;
      }
      .product-img {
        padding: 0.5rem 0;
        border-radius: 0.6rem;
        background: #ffffff;
      }
      .product-info {
        margin-top: 1rem;
        border-radius: 0.6rem;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        .product-info-item {
          border-bottom: 1px solid #f5f5f5;
          .product-info-item-content {
            padding: 1rem;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            font-weight: 400;
            font-size: 1rem;
            color: #191919;
            line-height: 1.2rem;
            .product-price {
              padding-left: 0.5rem;
              font-weight: 500;
              font-size: 1rem;
              color: #fe4857;
              line-height: 1.2rem;
            }
          }
          .product-info-item-spec {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            .spec-name {
              border-radius: 0.5rem;
              padding: 0.5rem 1.2rem;
              background: #eeeeee;
            }
            .spec-selected {
              background: #191919;
              color: #ffffff;
            }
          }
          .product-name-row {
            padding: 0.8rem 1rem;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            .product-name {
              padding-right: 1rem;
              display: block;
              font-weight: 500;
              font-size: 1rem;
              color: #191919;
              line-height: 1.2rem;
              white-space: nowrap;
              overflow: hidden;
              text-overflow: ellipsis;
            }
          }
        }
      }
    }
    .comment {
      // padding-bottom: 0.5rem;
      .comment-title {
        padding-bottom: 0.5rem;
        font-weight: 400;
        font-size: 1rem;
        color: #191919;
        line-height: 1.2rem;
      }
      .comment-card {
        background: #ffffff;
        box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.1);
        border-radius: 0.6rem;
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        font-weight: 400;
        font-size: 1rem;
        color: #191919;
        line-height: 1.2rem;
        .comment-user {
          display: flex;
          flex-direction: row;
          align-items: center;
        }
        .comment-satisfaction {
          padding-top: 0.5rem;
          display: flex;
          flex-direction: row;
          align-items: center;
        }
        .comment-pics {
          padding-top: 0.5rem;
          display: flex;
          flex-direction: row;
          .comment-pic-card {
            flex: 1;
            background: #ffffff;
            box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.2);
            border-radius: 0.6rem;
          }
        }
        .comment-time {
          padding-top: 0.5rem;
          font-weight: 400;
          font-size: 1rem;
          color: #757575;
          line-height: 1.2rem;
        }
      }
    }
    .detail {
      .detail-title {
        padding: 0.5rem 0;
        font-weight: 400;
        font-size: 1rem;
        color: #191919;
        line-height: 1.2rem;
      }
      .product-desc {
        background: #ffffff;
        box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.1);
        border-radius: 0.6rem;
        padding: 0.5rem 1rem;
        white-space: wrap;
      }
      .recommend-products {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        flex-wrap: wrap;
        .hot-product-item {
          flex: 0 0 calc(50% - 5px);
          margin-bottom: 0.8rem;
        }
      }
    }
  }
  .bottom {
    .total-price {
      flex: 1;
      font-weight: 500;
      font-size: 1rem;
      color: #191919;
      line-height: 1.2rem;
      display: flex;
      flex-direction: row;
      align-items: center;
      .total-number {
        padding-left: 0.4rem;
        font-weight: 500;
        font-size: 1.4rem;
        color: #fe4857;
        line-height: 1.6rem;
      }
    }
    .button {
      flex: 2;
      display: flex;
      flex-direction: row;
    }
  }
  // ::v-deep(.van-popup--bottom){
  //     top:0 !important;
  // }
  // ::v-deep(.van-action-sheet__content){
  //     height: 100dvh;
  // }
  .pay-panel-container {
    padding: 1rem;
    display: flex;
    flex: 1;
    flex-direction: column;
    align-items: stretch;
    height: 100dvh;
    .title-row {
      padding-top: 1rem;
      font-weight: 500;
      font-size: 1.2rem;
      color: #191919;
      line-height: 1rem;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
    }
    .money-row {
      text-align: center;
      padding-top: 1.5rem;
      font-weight: bold;
      font-size: 2.6rem;
      color: #191919;
      line-height: 3rem;
    }
    .order-info {
      padding: 1rem 0 0.5rem 0;
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      font-weight: 400;
      font-size: 1rem;
      color: #757575;
      line-height: 1.2rem;
    }
    .card-container {
      background: #f5f5f5;
      box-shadow: 0px 4px 7px 0px rgba(0, 0, 0, 0.05);
      padding: 1.2rem;
      border-bottom: 1px solid #eeeeee;
      .card-item {
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 0 1rem;
        .card-value {
          padding-left: 1.5rem;
          font-weight: 400;
          font-size: 1rem;
          color: #191919;
          line-height: 1.2rem;
        }
      }
    }
    .card-container-center {
      border-width: 0;
      text-align: center;
    }
    .pwd-container {
      padding-top: 2rem;
      ::v-deep(.van-password-input__item) {
        background: #f5f5f5 !important;
      }
      ::v-deep(.van-number-keyboard__body) {
        background: #fff;
      }
      ::v-deep(.van-number-keyboard) {
        padding-bottom: 0;
      }
    }
  }
  ::v-deep(.van-action-sheet) {
    max-height: 100dvh;
  }
}
</style>
