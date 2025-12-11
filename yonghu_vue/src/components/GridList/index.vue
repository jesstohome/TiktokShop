<!-- 公共商品列表 -->
<template>
  <div class="App-Pro-List">
    <!-- <van-cell v-for="item in goodsList" :key="item.proid" :title="item.proname" /> -->
    <div class="half mx-3">
      <div
        class="List"
        v-for="item in goodsList"
        :key="item.proid"
        @click="ToPageDetails(item.proid)"
      >
        <img :src="item.img" alt="" />
        <div class="productInfo mx-3 flex flex-col justify-center">
          <div class="top mt-3">
            <div>{{ item.office }}</div>
            <div class="right">
              <div class="flex justify-center items-center">
                <img :src="item.star" alt="" />
              </div>
              <div class="pl-1.5">
                <span>{{ item.score }}</span>
              </div>
            </div>
          </div>
          <div class="center">
            {{ item.title }}
          </div>
          <div class="bottom mb-3">
            <span>{{ item.currentPrice }}</span>
            <span>{{ item.originalPrice }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
// 渲染列表
const goodsList = reactive([
  {
    proid: 1,
    img: '/public/imgs/product1.png',
    discount: '50% Off',
    office: 'Office',
    star: '/public/imgs/star.png',
    score: '4.4',
    title: 'Dulax Estra Chair',
    currentPrice: '$48',
    originalPrice: '$99',
    love: ''
  },
  {
    proid: 2,
    img: '/public/imgs/product2.png',
    discount: '50% Off',
    office: 'Office',
    star: '/public/imgs/star.png',
    score: '4.4',
    title: 'Dulax Estra Chair',
    currentPrice: '$48',
    originalPrice: '$99',
    love: ''
  },
  {
    proid: 2,
    img: '/public/imgs/product2.png',
    discount: '50% Off',
    office: 'Office',
    star: '/public/imgs/star.png',
    score: '4.4',
    title: 'Dulax Estra Chair',
    currentPrice: '$48',
    originalPrice: '$99',
    love: ''
  }
])
let loading = ref(false)
let finished = ref(false)

// 去商品详情页
const router = useRouter()
const { column } = defineProps({
  column: {
    type: Number,
    default: '1'
  }
})
const ToPageDetails = (proid) => {
  router.push({ name: 'Detail', params: { proid } })
}
</script>

<style scoped lang="scss">
.Pro-List {
  padding: 1.2rem;

  .List {
    width: 100%;
    display: flex;
    margin: 1rem 0rem;

    img {
      margin-right: 1rem;
    }

    .productInfo {
      margin-top: 2rem;

      .top {
        height: 1.25rem;
        display: flex;
        justify-content: space-between;

        .right {
          display: flex;

          img {
            width: 0.875rem;
            height: 0.875rem;
            margin-right: 0.3125rem;
          }

          div {
            font-size: 0.8rem;
            color: #ffcd1a;
          }
        }
      }

      .center {
        margin: 0.625rem 0;
      }

      .bottom {
        span {
          margin-right: 0.625rem;

          &:last-child {
            color: red;
          }
        }
      }
    }
  }
}

.half {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  column-gap: 0.5rem;

  .List {
    border-radius: 0.25rem;
    margin-bottom: 0.5rem;

    img {
      width: 100%;
      background-color: #f6f8fe;
      border-radius: 0.75rem;
    }

    .productInfo {
      .top {
        display: flex;
        justify-content: space-between;

        .right {
          display: flex;
          justify-content: center;
          align-items: center;

          img {
            width: 0.875rem;
            height: 0.875rem;
          }

          div {
            span {
              font-size: 0.8rem;
              color: #ffcd1a;
            }
          }
        }
      }

      .center {
        margin: 0.25rem 0;
      }

      .bottom {
        span {
          margin-right: 0.625rem;

          &:last-child {
            color: red;
          }
        }
      }
    }
  }
}
</style>
