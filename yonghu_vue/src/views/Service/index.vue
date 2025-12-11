<template>
  <div class="container">
   <nav-bar :title="$t('customerService')" />
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
  </div>
</template>
<script setup>
import { platformInfo,get_shops_kfs } from '@/api/home.js'
import toast from '@/utils/toast.js'

const router = useRouter()
console.log(getParameterByName('pid'));

const serviceSrc = ref('');
const loading = ref(true);
const onIframeLoad = () => {
	loading.value = false;
};
function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

const shopskfs = () => {
  get_shops_kfs({
    pid: getParameterByName('pid'),
  })
    .then((res) => {
      console.log(res.data);
	   // return;
	 
	  // window.open(res.data);
	  //window.location.href=res.data;
	  serviceSrc.value=res.data;
	  
    })
    .catch((err) => err)
    .finally(() => {
      toast.close()
    })
}

shopskfs();
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  .iframe {
    width: 100%;
    height: calc(100vh - 46px);
    height: calc(100dvh - 46px);
  }
  .content {
    padding: 0;
    height: calc(100dvh - 50px);
  }
}
</style>
