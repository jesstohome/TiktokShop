<script setup>
import {useRoute, useRouter} from "vue-router";
import {messageDetail} from "@/api/index.js";

//路由
const router = useRouter();
const route = useRoute()
//返回
const onClickLeft = () => {
  router.back();
};
//请求参数
const query=ref({
  ids:''
})
query.value.ids = route.query.ids
const content=ref()
const getmessageDetail = async (data) => {
  const res = await messageDetail(data)
  content.value=res.data.content
}
onMounted(()=>{
  getmessageDetail(query.value)
})
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('messagedetail.content1')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft">
    </van-nav-bar>
    <main>
      <div v-if="!content">
        <van-empty/>
      </div>
      <div class="bg-white mx-3 mt-3 rounded-md">
      <div class="px-3 pt-3 text-xl">{{ $t("messagedetail.content2") }}</div>
      <div class="p-3 flex flex-col justify-center items-center" v-html="content"></div>
      </div>
    </main>
  </header>
</template>

<style scoped lang="scss">

</style>