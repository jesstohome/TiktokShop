<script setup>
import {useTheme} from "@/utils/useTheme.js";
import { useI18n } from 'vue-i18n';
import {showFailToast} from "vant";
import {useUserStore} from "@/store/modules/user.js";
//多语言
const { t } = useI18n();
const {theme} = useTheme();
const userStore = useUserStore();

// import {useLang} from "@/utils/useLang.js";

// const {Lang} = useLang();

const router = useRouter();

const switchThemes = () => {
  theme.value = theme.value === 'light' ? 'dark' : 'light';
};

const switchLanguage = () => {
  // Lang.value = Lang.value === 'zh' ? 'en' : 'zh';
};

const onClickLeft = () => {
  router.back();
};
</script>

<template>
  <header>
    <van-nav-bar
        :title= "$t('setting.settings')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <main class="flex flex-col mx-3 mt-5">
      <div class="flex bg-white items-center py-4 mb-4 px-2 back_4" @click="userStore.MerInfo.status===1?router.push('/baseinfo'):showFailToast($t('over'))">
        <span class="ml-3 pt-0.5">
          <icon-park name="waterfalls-v" size="1.6rem" />
        </span>
        <span class="ml-3 pt-1 flex-1">{{ $t("setting.basicInformation") }}</span>
      </div>
      <div class="flex  bg-white items-center mb-4 py-4 px-2 back_4 " @click="userStore.MerInfo.status===1?router.push('/uploadBanner'):showFailToast($t('over'))">
        <span class="ml-3 pt-0.5">
          <icon-park name="pic" size="1.6rem" />
        </span>
        <span class="ml-3 pt-1 flex-1">{{ $t("setting.homeBanner") }}</span>
      </div>
<!--      <div class="flex bg-white items-center mb-4 py-4  px-2 back_4" @click="router.push('/socialmedia')">-->
<!--        <span class="ml-3 pt-0.5">-->
<!--          <icon-park name="movie-board" size="1.6rem" />-->
<!--        </span>-->
<!--        <span class="ml-3 pt-1 flex-1">{{ $t("setting.socialMedia") }}</span>-->
<!--      </div>-->
    </main>
  </main>
</template>

<style scoped lang="scss">

</style>
