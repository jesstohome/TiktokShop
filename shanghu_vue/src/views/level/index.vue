<script setup>
import {level} from "@/api/index.js";

const router = useRouter();
//返回上一级
const onClickLeft = () => {
  router.back();
};
//获取信息请求
const List=ref([])
const getlevel=async()=>{
  const res=await level()
  List.value=res.data
  List.value[0].showDesc = true
}
onBeforeMount(()=>{
  getlevel()
})
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('level.merchantLevel')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft">
    </van-nav-bar>
  </header>
  <van-space direction="vertical" size="0.5rem" style="width: 100%;padding: 1rem;">
    <div
      v-for="item in List"
      :key="item.level_id"
      class="item-container bg-white rounded-md shadow"
	  
    >
      <div class="p-3">
        <div class="item-row" @click="() => (item.showDesc = !item.showDesc)">
          <div class="item-title">{{ item.name }}</div>
          <van-icon
            :name="item.showDesc ? 'arrow-up' : 'arrow-down'"
            color="#111111"
            size="20"
          />
        </div>
        <div v-show="item.showDesc" class="item-desc">
          <div v-html="item.text" />
        </div>
      </div>
    </div>
  </van-space>
</template>

<style scoped lang="scss">
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
</style>