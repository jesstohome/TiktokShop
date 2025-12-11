<script setup>
import {useUserStore} from '@/store/modules/user.js';
const router = useRouter();
const onClickLeft = () => {
  router.back();
};
const userStore = useUserStore();
const serviceSrc=ref('')
serviceSrc.value=userStore.MerInfo.custom_service
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
    <iframe  style="min-height: 100%; width: 100%; overflow: hidden;" frameborder="0" id="feee_c2c_widget"
            :src="serviceSrc"></iframe>
  </div>
</template>

<style scoped lang="scss">
.iframe {
  width: 100%;
  height: calc(100vh - 46px);
  height: calc(100dvh - 46px);
}
</style>