<script setup>
// const tologin=()=>{
//   if($route.meta.data){
//
//   }
// }
// onBeforeMount(()=>{
//   tologin()
// })
// console.log(window.location)
import {third} from "@/api/index.js";
import {useRoute, useRouter} from "vue-router";
import {useUserStore} from '@/store/modules/user.js';

//获取当前窗口参数
const params = new URLSearchParams(window.location.search);
const paramValue = params.get('data');
const code = params.get('code');
const mer = params.get('mer');
const path = params.get('path');
// console.log(paramValue, code, mer, path);
const query = {
  data: paramValue,
  code,
  mer
};
const router = useRouter();
const userStore = useUserStore();

const loading = showLoadingToast({
  message: '加载中...',
  forbidClick: true,
  loadingType: 'spinner',
  duration: 0
});

if (paramValue) {
  third(query).then(res => {
    loading.close();
    // console.log(res);
    if (res.data.status == 0) {
      // userStore.data = paramValue;
      // console.log(userStore.data);
      localStorage.setItem('data', paramValue);
      // router.push('/code');
      return;
    }
    userStore.token = res.data.merinfo.token;
    if (userStore.token) {
      if (path) {
        router.push(path);
      } else {
        router.push('/home');
      }

    }

  });
} else {
  loading.close();
}

onMounted(() => {

});

//我需要判断当前是ios还是安卓
const getisIos = () => {
  const u = navigator.userAgent;
  return !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/);
};

const isIos = getisIos();


</script>

<template>
  <!--<div v-if="isIos" class="h-20 bg-black">-->

  <!--</div>-->
  <router-view/>
  <!--  <router-view/>-->
</template>

<style scoped>
/* 	header{
		 z-index: 999;padding-top: 44px;background: #fff;
	} */
</style>
