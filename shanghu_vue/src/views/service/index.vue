<script setup>
import {useUserStore} from '@/store/modules/user.js';
import {getKfUrls} from "@/api/index.js";
const router = useRouter();
const onClickLeft = () => {
  router.push('/home')
};
//获取
 const serviceSrc = ref('');
const loading = ref(true);
const getKfUrlss = async () => {
  const res = await getKfUrls();
  // console.log(res);return;
	// window.open(res.data);
	serviceSrc.value = res.data;
};
const onIframeLoad = () => {
	loading.value = false;
};
onMounted(() => {
 getKfUrlss();
});
</script>

<template>
  <header>
    <van-nav-bar
        :title="$t('service.service')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
<div class="iframe">
	  
	  <van-loading v-if="loading" style="text-align: center;margin-top: 30%;" size="44px" type="circular" color="#1989fa" />
	   <iframe 
		   :src="serviceSrc" 
		   @load="onIframeLoad"
		   ref="iframe"
		   title="External website"
		   class="iframe"
	   ></iframe>
  </div>
</template>

<style scoped lang="scss">
.iframe {
  width: 100%;
  height: calc(100vh - 46px);
  height: calc(100dvh - 46px);
}
</style>