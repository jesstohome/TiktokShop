<template>
  <div class="tabbar-container">
    <section style="flex: 1">
      <router-view v-slot="{ Component }">
        <keep-alive>
          <component :is="Component" />
        </keep-alive>
      </router-view>
    </section>
  </div>
  <div class="tabbar-bottom">
    <div v-for="item in tabs" :key="item.url" class="tab-item" @click="tabChange(item)">
      <van-icon
        class-prefix="icon"
        :name="item.active === active ? item.activeIcon : item.inActiveIcon"
        :size="item.active === active ? '30' : '28'"
        :color="item.active === active ? '#1989fa' : '#9e9e9e'"
      />
    </div>
  </div>
</template>
<script setup></script>
<style lang="scss" scoped>
.tabbar-container {
  display: flex;
  flex-direction: column;
  align-items: stretch;
  .tabbar-bottom {
    height: 70px;
    background: #ffffff;
    // border-top: 1px solid #e0e0e0;
    display: flex;
    flex-direction: row;
    align-items: center;
    .tab-item {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
  }
}
</style>
