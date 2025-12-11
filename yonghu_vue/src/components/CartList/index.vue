<script setup>
const cartList = ref([
  {
    cartid: 1,
    num: 2,
    price: 2.0,
    desc: '描述信息',
    title: '商品标题',
    src: '/public/imgs/product1.png'
  }
])
</script>
<template>
  <div :class="Pro - List">
    <div class="List flex" v-for="item in cartList" :key="item.cartid">
      <img :src="item.src" alt="" />
      <div class="productInfo">
        <div class="top">
          <span>{{ item.title }}</span>
        </div>
        <div class="center">
          <span>{{ item.desc }}</span>
        </div>
        <div class="bottom">
          <span>{{ item.price }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped lang="scss"></style>
