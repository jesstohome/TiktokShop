<script setup>
import {useUserStore} from '@/store/modules/user.js';
import {getKfUrl} from "@/api/index.js";
const router = useRouter();
const onClickLeft = () => {
  //router.back();
  router.push('/home')
};

//获取
const serviceSrc = ref('');
const loading = ref(true);
const getKfUrls = async () => {
  const res = await getKfUrl();
  console.log(res);
	// window.open(res.data);
	//window.location.href=res.data;
	serviceSrc.value = res.data;
};
const onIframeLoad = () => {
	loading.value = false;
};

onMounted(() => {
 getKfUrls();
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