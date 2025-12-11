<template>
  <div class="container safari_only">
    <App-Header title="Notifications" />
    <div class="container-scroll">
      <div class="security-items">
        <div class="items-title">Messages Notifications</div>
        <div class="security-item" v-for="item in items" :key="item.name">
          {{ item.name }}
          <van-switch v-model="item.value" active-color="#31452e" />
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import AppHeader from '@/components/CustomNavBar/index.vue'
const items = ref([
  {
    name: 'Payment',
    value: true
  },
  {
    name: 'New Furniture',
    value: true
  },
  {
    name: 'Message',
    value: true
  },
  {
    name: 'Call',
    value: true
  }
])
</script>
<style lang="scss" scoped>
.container {
  .container-scroll {
    height: calc(100vh - 50px);
    padding: 2em;
    .security-items {
      padding: 1.2em 1.2em 2.4em 1.2em;
      border-radius: 1em;
      border: 1px solid rgba(227, 231, 236, 1);
      .items-title {
        font-size: 1em;
        font-weight: 500;
        letter-spacing: 0px;
        line-height: 0.8em;
        color: rgba(102, 112, 122, 1);
      }
      .security-item {
        padding: 1em 0;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(227, 231, 236, 1);
        font-size: 1.5em;
        font-weight: 500;
        letter-spacing: 0.08px;
        line-height: 1em;
        color: rgba(17, 17, 17, 1);
      }
    }
  }
}
</style>
