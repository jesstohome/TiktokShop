<template>
  <!--头部-->
  <div class="header">
    <AppNavbar title="Popular Furniture">
      <template #left>
        <img src="@/assets/image/ArrowBack.png" class="h-10" alt="" />
      </template>
      <template #right>
        <img src="/public/imgs/icon2.png" class="h-10" alt="" />
      </template>
    </AppNavbar>
  </div>
  <!--  列表-->
  <div class="list">
    <List :column="2" />
    <List :column="2" />
    <List :column="2" />
  </div>
</template>

<script setup>
import AppNavbar from '@/components/AppNavbar/index.vue'
import List from '@/components/List/index.vue'
</script>

<style scoped>
.header {
  .right {
    background-color: #e3e9ed;
    border-radius: 50%;
    width: 2.85rem;
    height: 2.85rem;
    text-align: center;
    position: relative;
    img {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }
  }
}
.list {
  padding-top: 3rem;
}
</style>
