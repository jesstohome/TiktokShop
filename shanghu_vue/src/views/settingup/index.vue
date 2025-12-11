<script setup>
import {useRouter} from "vue-router";
import { useI18n } from 'vue-i18n';
//多语言
const { t } = useI18n();
const router = useRouter();
</script>

<template>
  <header>
    <span class="s1"   @click="router.push('/my')"><van-icon name="arrow-left" size="18px" /></span>
    <span class="s2">设置</span>
    <span class="s3"></span>
  </header>
  <section>
    <div class="box1">
      <div>清楚缓存</div>
      <div>0MB</div>
    </div>
    <div class="box1">
      <div>检查更新</div>
      <div>V1.0.2<van-icon name="arrow" /></div>
    </div>
    <button class="btn">退出</button>
  </section>
</template>

<style scoped lang="scss">
header{
  width: 100%;
  height: 46px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #fff;
  padding: 0 16px;
  .s2{
    font-size: 16px;
  }
  .s3{
    width: 20px;
  }
}
section{
  flex: 1;
  overflow-y: auto;
  .box1{
    width: 100%;
    height: 45px;
    padding: 0 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
  }
  .btn{
    width: 90vw;
    height: 45px;
    background: red;
    color: #fff;
    border-radius: 5px;
    margin: 20px ;

  }
}
section::-webkit-scrollbar{
  display: none;
}
</style>