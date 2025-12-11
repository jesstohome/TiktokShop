<template>
  <!-- 头部 -->
  <div class="title">Explore</div>
  <!--  搜索框-->
  <div class="search">
    <van-search
      v-model="value"
      shape="round"
      placeholder="Search"
      :style="{ height: '3.125rem' }"
      @click="toSearch()"
    >
      <template #right-icon>
        <img
          src="/public/imgs/Filter.png"
          alt="图片描述"
          style="height: 1.125rem; margin: 1.0625rem; line-height: 3.25rem"
        />
      </template>
    </van-search>
  </div>
  <!--  选项卡-->
  <div class="tabs">
    <van-button
      round
      type="success"
      class=""
      style="border-color: #e3e7ec; margin-right: 0.5rem"
      color="#FFFFFF"
      v-for="(item, index) in 4"
      :key="index"
    >
      {{ item }}all
    </van-button>
  </div>
  <!-- Popular-->
  <div class="popular">
    <p>Popular Furniture</p>
    <p @click="router.push({ name: 'Popular' })">See All</p>
  </div>
  <!--  products-->
  <List :column="2" />
  <!--  recommendation-->
  <div class="recommendation">
    <p>Recommendation</p>
    <p>See All</p>
  </div>
  <!--  recomendation productiion-->
  <List :column="1" />
  <!-- 底部导航栏 -->
  <AppTabbar />
</template>

<script setup>
import List from '@/components/List/index.vue'
import AppTabbar from '@/components/AppTabbar/index.vue'
import { useRouter } from 'vue-router'

const router = useRouter()
//进入搜索页面
const toSearch = () => {
  router.push({ name: 'Search' })
}
</script>

<style scoped lang="scss">
.van-search {
  background: #eff2f6;
  :deep(.van-search__content) {
    border: 1px solid #210303;
  }
}

.title {
  text-align: center;
  margin: 1.25rem 0;
}

.search {
  margin: 1rem;

  :deep(.van-field__control) {
    line-height: 2.125rem;
  }
}

.tabs {
  padding: 1.2rem;
  display: flex;
  flex-wrap: nowrap;
  margin-right: 1.25rem;
  overflow-x: auto;
  :deep(.van-button__content) {
    color: #e3e7ec;
  }
}

.popular {
  padding: 1.2rem;
  height: 1.625rem;
  display: flex;
  justify-content: space-between;
  margin: 0 auto;
}

.recommendation {
  padding: 1.2rem;
  height: 1.625rem;
  display: flex;
  justify-content: space-between;
  margin: 0 auto;
}
</style>
