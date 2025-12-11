<script setup>

import {checkInvite, languageList, changelanguage} from "@/api/index";
import {useI18n} from 'vue-i18n';
import IconPark from "@/components/IconPark/index.vue";
//多语言
const {locale, t} = useI18n();
const router = useRouter();
//定义邀请码
const code = ref('');
//定义'申请商户'展示状态
const show = ref(false);


//切换语言
//点击展示选项卡
const showAction = ref(false);
const Goto = () => {
  showAction.value = true;
}
//定义选项卡内容
const actions = ref([])
//从后端拿取默认语言,并且渲染语言列表
const langList = ref([])
const actionList = ref([])
//语言选项卡接口
const defautlanguage = () => {
  languageList({page: 1, limit: 10}).then(res => {
    langList.value = res.data.list;
    //提取里面属性组成新的数组渲染选项卡
    actionList.value = langList.value.map(item => ({name: item.language_name, file_name: item.file_name}));
    // console.log(actionList.value)
    actions.value = actionList.value
    console.log(actions.value)
    //存入一个数据判断是否执行过此功能,如未执行,就获取默认语言,如果获取过,页面更新就不再执行存入默认语言
    const hasExecutedFunction = localStorage.getItem('hasExecutedFunction');
    if (!hasExecutedFunction) {
      console.log(langList.value)
      langList.value.forEach((item) => {
        if (item.is_default == 1) {
          localStorage.setItem('lang', item.file_name);
          location.reload()
        }
      })
      localStorage.setItem('hasExecutedFunction', 'true')
    }
  })
}
//请求参数
const lang1 = ref({
  lang: ''
});
//打开选择层
const onSelect = (item) => {
  showAction.value = false;
  lang1.value.lang = item.file_name;
  locale.value = item.file_name;
  localStorage.setItem('lang', item.file_name);
  changelanguage(lang1.value).then(res => {
    if (res.code === 1) {
      console.log(1)
      showSuccessToast(res.msg)
    } else {
      showFailToast(res.msg)
    }
  })
  location.reload()
};
const getcheckInvite = () => {
  checkInvite({code: code.value}).then(res => {
    console.log(res);
    if (res.code === 1) {
      localStorage.setItem('code', code.value)
      router.push('/register');
    } else {
      showToast(res.msg);
    }
  });
};
onMounted(() => {
  defautlanguage()
});
</script>

<template>
  <header>
    <div class="mx-3  flex justify-end items-center mt-3">
      <!--         <span class="ml-3 pt-0.5">-->
      <!--          <icon-park name="left" @click="onClickLeft" size="1.6rem"/>-->
      <!--        </span>-->
      <span class="ml-3 pt-0.5">
          <icon-park name="earth" size="1.6rem" @click="Goto()"/>
        </span>
    </div>
  </header>
  <div class="">
    <div class="flex justify-center items-center flex-col mt-60">
      <span>
        <icon-park name="financing-one" size="100"/>
      </span>
      <span class="text-3xl">
        SHOP
      </span>
      <span class="text-xl mt-3">{{ $t("code.title") }}</span>
      <span class="opacity-80">Currently supports internal invitations only</span>
      <div class="px-3 mt-20">
        <van-button block color="#191919" round style="height:45px;width: 50vw" @click="show=true">
          {{ $t("code.Apply") }}
        </van-button>
      </div>
    </div>
  </div>

  <van-popup
      v-model:show="show"
      :style="{ height: '50%' }"
      class="rounded-t-2xl"
      position="bottom bg-white"
  >
    <div class="mx-6">
      <div class="grid grid-cols-3 mt-6">
        <div></div>
        <div class="text-center ">
          <span class="font-semibold text-xl"> {{ $t("code.Open") }}</span>
        </div>
        <div class="ml-auto" @click="show=false">
          <icon-park name="close" size="20"/>
        </div>
      </div>
      <div class="mt-6">
        <h5 class="font-semibold text-xl">{{ $t("code.Authentication") }}</h5>
        <input v-model="code" class="border border-black rounded-3xl w-full h-10 bg-gray-100 mt-3 p-6"
               :placeholder="$t('code.enter')"
               type="text"/>
      </div>
      <div class="bg-black h-14 rounded-3xl text-white flex justify-center items-center mt-40" @click="getcheckInvite">
        <span>{{ $t("code.Open") }}</span>
      </div>
    </div>
  </van-popup>
  <van-action-sheet v-model:show="showAction" :actions="actions" @select="onSelect"/>
</template>

<style lang="scss" scoped>

</style>
