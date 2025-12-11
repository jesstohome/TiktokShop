<script setup>
import {message} from "@/api/index.js";
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
//路由
const router = useRouter();
//返回
const onClickLeft = () => {
  router.back();
};
//tab标签
const tabs = ref([{
  is_see: "all",
  title: t("message.all")
},
  {
    is_see: "1",
    title: t("message.haveread")
  },
  {
    is_see: "0",
    title: t("message.noread")
  }])
//获取信息列表
const List = ref([])
const listQuery = ref({
  is_see: 'all',
  page: '1',
  limit: '10'
})
//触底加载
const loading = ref(false);
const finished = ref(false);
const onload = (id) => {
  loading.value = true;
  if (id) {
    List.value = []
    listQuery.value.page = '1'
    listQuery.value.is_see = id
    finished.value = false
    // loading.value = true
  }
  // loading.value = true;
  message(listQuery.value).then(res => {

    console.log(res)
    let number = parseInt(listQuery.value.page);

    // console.log(res)
    number++;
    listQuery.value.page = number.toString();
    if (res.data.list.length <= 0) {
      finished.value = true;
    }
    List.value.push(...res.data.list)
    loading.value = false
  })

}
onMounted(() => {
  // loading.value = true
  onload(listQuery.is_see)

})
//传递标签参数
const onClickTab = ({name}) => onload(name);

</script>

<template>
  <header class="sticky top-0">
    <van-nav-bar
        :title="$t('message.message2')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft">
    </van-nav-bar>
    <div class="rounded-md w-full">
      <van-tabs v-model:active="active" @click-tab="onClickTab">
        <van-tab v-for="item in tabs" :key="item.switch" :name="item.is_see" :title="item.title">
        </van-tab>
      </van-tabs>
    </div>
  </header>
  <main>
    <div v-if="List.length<=0">
      <van-empty/>
    </div>
    <div>
      <van-list

          v-model:loading="loading"
          :finished="finished"
          :finished-text="$t('message.nomore')"
          @load="onload()"
          :immediate-check="false"
      >
        <div class="bg-white mx-3 mt-3 rounded-md" v-for="item in List " :key="item.id"
             @click="router.push({ path: '/messageDetail', query: { ids: item.id,info:item.content } })">
          <div class="flex justify-between mx-3 py-1.5">
            <div class="text-gray-500">{{ $t("message.message2") }}</div>
            <div class="flex items-center">
              <span>{{ item.title }}</span>
            </div>
          </div>
<!--          <div class="flex justify-between mx-3 py-1.5">-->
<!--            <div class="text-gray-500">内容</div>-->
<!--            <div class="flex items-center">-->
<!--              <span>{{ item.content }}</span>-->
<!--            </div>-->
<!--          </div>-->
          <div class="flex justify-between mx-3 py-1.5">
            <div class="text-gray-500">{{ $t("message.createtime") }}</div>
            <div class="flex items-center">
              <span>{{ item.createtime }}</span>
            </div>
          </div>
          <div class="flex justify-between mx-3 py-1.5">
            <div class="text-gray-500">{{ $t("message.type") }}</div>
            <div class="flex items-center">
              <span class="text-green-500">{{ item.type === 1 ? $t("message.message1") : $t("message.notice") }}</span>
            </div>
          </div>
          <div class="flex justify-between mx-3 py-1.5">
            <div class="text-gray-500">{{ $t("message.people") }}</div>
            <div class="flex items-center">
              <span>{{ item.user }}</span>
            </div>
          </div>
        </div>
      </van-list>
    </div>
  </main>
</template>

<style scoped lang="scss">

</style>