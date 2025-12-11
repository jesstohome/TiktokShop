<template>
  <div class="container">
    <nav-bar :title="$t('helpAndSupport')" />
    <div class="content">
      <van-space direction="vertical" size="0.5rem">
        <div
          v-for="item in items"
          :key="item.title"
          class="item-container bg-white rounded-md shadow"
        >
          <div class="p-3">
            <div class="item-row" @click="() => (item.showDesc = !item.showDesc)">
              <div class="item-title">{{ item.title }}</div>
              <van-icon
                :name="item.showDesc ? 'arrow-up' : 'arrow-down'"
                color="#111111"
                size="20"
              />
            </div>
            <div v-show="item.showDesc" class="item-desc">
              <div v-html="item.content" />
            </div>
          </div>
        </div>
      </van-space>
    </div>
  </div>
</template>
<script setup>
import throttle from 'lodash/throttle'
import NavBar from '@/components/CustomNavBar/index.vue'
import CustomInput from '@/components/Input/index.vue'
import toast from '@/utils/toast.js'
import { helpSupport } from '@/api/user.js'

const query = ref({
  searchVal: undefined
})
const handlerFilter = throttle(
  (val) => {
    if (!val) {
      items.value = data.value
    }
    items.value = data.value.filter((item) => {
      return item.title.indexOf(val) !== -1
    })
  },
  1000,
  { trailing: true }
)
const cleanSearch = () => {}
const data = ref([])
const items = ref([])
const getData = () => {
  toast.loading()
  helpSupport()
    .then((res) => {
      data.value = res.data
      items.value = res.data
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}
getData()
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');

.container {
  padding: 0;
  overflow-y: hidden;
  .content {
    padding: 2rem 1rem 1rem 1rem;
    overflow-y: auto;
    height: calc(100dvh - 120px);

    .item-container {
      padding: 1rem 0;
      display: flex;
      flex-direction: column;
      border-bottom: 1px solid #e5e5e5;

      .item-row {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;

        .item-title {
          font-size: 1.2rem;
          font-weight: 500;
          letter-spacing: 0.08px;
          line-height: 1.4rem;
        }
      }

      .item-desc {
        padding: 1rem 0 0.8rem 0;
        font-size: 1rem;
        font-weight: 500;
        letter-spacing: 0.07px;
        line-height: 1.6rem;
        color: #a7a7a7;
      }
    }
  }
}
</style>
