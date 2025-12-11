<template>
  <div class="container">
    <nav-bar :can-back="false" :title="$t('personCenter')">
      <!--<template #right>-->
      <!--  <icon-park name="more" size="1.8rem"/>-->
      <!--</template>-->
    </nav-bar>
    <div class="content">
      <div class="user-info">
        <div class="user-avatar">
          <van-image 
            :src="userInfo.avatar || Avatar"
             round width="4rem" height="4rem"
             @click="handlerChooseAvatar"
          />
          <van-uploader 
            v-show="uploadShow"
            ref="uploaderRef"
            v-model="fileList"
            :after-read="afterRead"
          />
          <div class="user-tips">
            {{ userInfo.username }}
            <div class="welcome">
              {{ user_contacts }}
            </div>
          </div>
        </div>
        <!-- <icon-park name="mail" size="1.8rem" @click="goNotification"/> -->
      </div>
      <div class="user-card">
        <div class="row1">
          <div class="row1">
            {{ $t('myBalance') }}&nbsp;({{ $t('dollar') }})
            <icon-park
              color="#ffffff"
              :name="showBalance ? 'preview-close-one' : 'preview-open'"
              size="1.8rem"
              style="padding-left: 0.6rem"
              @click.stop="handlerSwitchShowBalance"
            />
          </div>
          <!--{{ balance }}-->
        </div>
        <div class="text-3xl font-semibold">
          {{ balance }}
        </div>
        <div class="row3">
          <div class="draw" @click.stop="handlerDraw">{{ $t('draw') }}</div>
          <div style="flex: 1" />
          <div class="deposit" @click.stop="handlerTopUp">{{ $t('recharge') }}</div>
        </div>
        <!-- <van-divider style="color: #ffffff" dashed /> -->
        <div class="grid grid-cols-2 mt-5">
          <div class="row4-column" @click.stop="goMyOrders('1')">
            <div class="rows-amount" style="padding-right: 2.5rem">
              {{ userInfo.order_unreceived }}
            </div>
            <span>{{ $t('goodsNotReceived') }}</span>
          </div>
          <div class="row4-column" @click.stop="goMyOrders('0')">
            <div class="rows-amount">{{ userInfo.order_unpaid }}</div>
            <span>{{ $t('goodsNotPaid') }}</span>
          </div>
        </div>
      </div>
      <!-- <div class="user-board">
        <van-image :src="PersonCenterBoard" widht="100%" />
      </div> -->
      <div class="user-menus">
        <list-menus :menus="menus1" @click="handlerMenuClick" />
        <div style="height: 0.5rem" />
        <list-menus :menus="menus2" @click="handlerMenuClick" />
      </div>
    </div>
  </div>
  <choose-language ref="chooseLanguage" />
  <AppTabbar />
</template>
<script name="Filter" setup>
import NavBar from '@/components/CustomNavBar/index.vue'
import Avatar from '@/assets/image/avatar.png'
import { formatNumberWithCommas } from '@/utils/filter.js'
import ListMenus from '@/components/ListMenus/index.vue'
import ChooseLanguage from '@/components/ChooseLanguage/index.vue'
import useUserStore from '@/stores/modules/user.js'
import toast from '@/utils/toast.js'
import to from 'await-to-js'
import { uploadFile } from '@/api/common.js'
import { profile } from '@/api/user.js'
const { proxy } = getCurrentInstance()
const userStore = useUserStore()
const fileList=ref([])
const uploadShow=ref(false)
const uploaderRef=ref(null)
const handlerChooseAvatar=()=>{
  uploaderRef.value.chooseFile()
}
const afterRead=async(file)=>{
  toast.loading()
  const formData = new FormData()
  formData.append('file', file.file)
  const [err1, res1] = await to(uploadFile(formData))
  if(err1){
    toast.fail({ msg: err1.msg })
    return
  }
  const [err2,res2]=await to(profile({ avatar: res1.data.fullurl }))
  if(err2){
    toast.fail({ msg: err2.msg })
    return
  }
  toast.success({msg:res2.msg})
  userStore.userInfo.avatar=res1.data.fullurl
  // location.reload()
}
const userInfo = computed(() => {
  return userStore.userInfo || {}
})
const user_contacts = computed(() => {
  return userStore.userInfo?.email
})
const language = computed(() => {
  return userStore.getLanguageName()
})
const router = useRouter()
//  提现
const handlerDraw = () => {
  router.push({ name: 'WithDraw' })
}
//  提现
//  充值
const handlerTopUp = () => {
  router.push({ name: 'TopUp' })
}
//  跳到订单列表
const goMyOrders = (status) => {
  router.push({ name: 'MyPurchases', params: { status: status } })
}
//  充值
const balance = computed(() => {
  if (showBalance.value) {
    return formatNumberWithCommas(userInfo.value.money)
  }
  return '******'
})
const menus1 = ref([
  {
    // name: proxy.t('myOrders'),
    name:'myOrders',
    iconName: 'order',
    routeName: 'MyPurchases'
  },
  {
    // name: proxy.t('fundRecord'),
    name:'fundRecord',
    iconName: 'funds',
    routeName: 'CapitalRecord'
  },
  // {
  //   name: '我的喜欢',
  //   iconName: 'like-d06ib93c',
  //   routeName: 'MyFavourite'
  // },
  {
    // name: proxy.t('merchantIn'),
    name:'merchantIn',
    iconName: 'chart-graph',
    routeName: 'Platform'
  },
  {
    // name: proxy.t('helpAndSupport'),
    name:'helpAndSupport',
    iconName: 'help',
    routeName: 'HelpAndSupport'
  },
  {
    // name: proxy.t('lawAndPolicy'),
    name:'lawAndPolicy',
    iconName: 'shield',
    routeName: 'Law'
  }
])
const menus2 = ref([
  {
    type: 'language',
    // name: proxy.t('language'),
    name:'language',
    iconName: 'earth',
    // routeName: 'Language',
    rightName: language
  },
  {
    type: 'language',
    // name: proxy.t('shippingAddress'),
    name:'shippingAddress',
    iconName: 'local',
    routeName: 'Address'
  },
  {
    // name: proxy.t('setting'),
    name:'setting',
    iconName: 'setting-two',
    routeName: 'Setting'
  }
])
const goNotification = () => {
  router.push({ name: 'Notification' })
}
const showBalance = ref(false)
//每次打开页面执行
onActivated(() => {
  useUserStore()
    .getInfo()
})

const handlerSwitchShowBalance = () => {
	
  showBalance.value = !showBalance.value
}
// language
const chooseLanguage = ref(null)
// language
const handlerMenuClick = (menu) => {
  if (menu.routeName) {
    router.push({ name: menu.routeName })
    return
  }
  switch (menu.type) {
    case 'language':
      chooseLanguage.value.show = true
      break
    default:
      break
  }
}
</script>
<style lang="scss" scoped>
@import url('@/assets/style/main.scss');
.container {
  padding: 0;
  overflow-y: hidden;

  .content {
    padding-top: 0;
    height: calc(100dvh - 100px);
    overflow-y: auto;

    .user-info {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;

      .user-avatar {
        display: flex;
        flex-direction: row;
        align-items: center;

        .user-tips {
          padding-left: 0.8rem;
          display: flex;
          flex-direction: column;
          justify-content: space-around;
          font-size: 1rem;
          font-weight: 600;
          line-height: 1.2rem;

          .welcome {
            padding-top: 3px;
            font-weight: 400;
            font-size: 0.8rem;
            line-height: 1rem;
            color: #888888;
          }
        }
      }
    }

    .user-card {
      margin-top: 0.5rem;
      padding: 0.5rem 0.5rem 0.8rem 0.5rem;
      background: linear-gradient(317deg, #6b6b6b 0%, #1c1b1b 100%);
      border-radius: 0.6rem;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      color: #ffffff;

      .row1 {
        display: flex;
        flex-direction: row;
        align-items: center;
        font-weight: 500;
        font-size: 1rem;
        line-height: 1.2rem;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
      }

      // .row2 {
      //   font-weight: bold;
      //   font-size: 1.4rem;
      //   line-height: 1.6rem;
      // }
      .row3 {
        padding: 0.5rem 0 1rem 0;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        font-weight: 400;
        font-size: 1.2rem;
        line-height: 1.4rem;
        letter-spacing: 0.15rem;
        border-bottom-style: dashed;
        border-bottom-width: 2px; /* 或者你想要的任何值 */
        border-bottom-color: #ffffff;
        .draw {
          flex: 3;
          text-align: center;
          color: #ffffff;
          border: 1px solid #fff;
          border-radius: 0.2rem;
          padding: 0.4rem;
        }
        .deposit {
          flex: 3;
          text-align: center;
          background: #ffffff;
          color: #191919;
          border-radius: 0.2rem;
          padding: 0.4rem;
        }
      }

      .row4 {
        padding-top: 0.6rem;
        display: flex;

        .row4-column {
          display: flex;
          flex-direction: column-reverse;
          font-weight: 400;
          font-size: 1rem;
          color: #ffffff;
          line-height: 1.6rem;

          .rows-amount {
            font-size: 1.2rem;
          }
        }
      }
    }

    .user-board {
      padding-top: 0.5rem;
    }

    .user-menus {
      padding-top: 0.5rem;

      ::v-deep(.menu-container) {
        padding: 0.8rem 0;
      }
    }
  }
}
</style>
