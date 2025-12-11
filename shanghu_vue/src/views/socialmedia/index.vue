<script setup>
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
const router = useRouter();
const onClickLeft = () => {
  router.back();
};
</script>

<template>
  <header>
    <van-nav-bar
        :title= "$t('socialMedia.socialMedia')"
        :left-text="$t('goback')"
        left-arrow
        @click-left="onClickLeft"
    />
  </header>
  <main>
    <div class="mt-4">
      <van-cell-group inset>
        <!-- 输入任意文本 -->
        <van-field v-model="text" label="Facebook" :placeholder="$t('socialMedia.insertLinkHTTPS')"/>
        <!-- 输入手机号，调起手机号键盘 -->
        <van-field v-model="tel" type="tel" :placeholder="$t('socialMedia.insertLinkHTTPS')" label="Instagram"/>
        <!-- 允许输入正整数，调起纯数字键盘 -->
        <van-field v-model="digit" type="digit" :placeholder="$t('socialMedia.insertLinkHTTPS')" label="YouTube"/>
        <!-- 允许输入数字，调起带符号的纯数字键盘 -->
        <van-field v-model="number" type="number" :placeholder="$t('socialMedia.insertLinkHTTPS')" :label="$t('socialMedia.twitter')"/>
        <van-field v-model="number" type="number" :placeholder="$t('socialMedia.insertLinkHTTPS')" :label="$t('socialMedia.google')"/>
      </van-cell-group>
      <div class="px-5 pt-10">
        <van-button round block color="#191919">{{ $t("socialMedia.save") }}</van-button>
      </div>
    </div>
  </main>
</template>

<style scoped lang="scss">

</style>