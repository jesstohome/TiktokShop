<template>
  <div class="h-10"></div>
  <van-tabbar v-model="active" inactive-color="#888888"	 active-color="#191919">
    <van-tabbar-item v-for="(item, index) in icon" :key="index" :to="item.route" replace>
      {{ $t(item.name) }}
      <template #icon="{ active }">
        <!--<img :src="active ? item.icon_1 : item.icon_2" alt=""/>-->
        <icon-park 
          :name="active ? item.icon_1 : item.icon_2" size="1.8rem" 
          :color="active ? '#191919' : '#888888'"
        />
      </template>
    </van-tabbar-item>
  </van-tabbar>
</template>

<script setup>
import { onActivated } from 'vue'

const active = ref(0)

const route = useRoute()

const icon = [
  {
    route: '/home',
    name:'home',
    icon_1: '1',
    icon_2: 'home'
  },
  {
    route: '/explore',
    name:'explore',
    icon_1: '2',
    icon_2: 'commodity'
  },
  {
    route: '/bag',
    name:'cart',
    icon_1: '3',
    icon_2: 'buy'
  },
  {
    route: '/filter',
    name:'me',
    icon_1: '4',
    icon_2: 'user'
  }
]

onActivated(() => {
  icon.forEach((item, index) => {
    if (item.route === route.path) {
      active.value = index
    }
  })
})
</script>
<style scoped></style>
