import {buyTrainBill} from "@/api/index.js";
import i18n from '@/lang/index.js'
//多语言



/**
 * 路由模块参数说明:
 * 在该配置文件中，每个路由模块的对象包含以下参数：
 * - path: 路由路径
 * - name: 路由名称
 * - component: 路由组件，使用懒加载方式导入
 * - meta: 路由元信息，包含以下属性：
 *   - title: 页面标题
 *   - requireAuth: 是否需要登录权限，true表示需要登录，false表示不需要登录
 *   - keepAlive: 是否需要缓存，true表示需要缓存，false表示不需要缓存
 */
export default [
    // 首页
    {
        path: '/home',
        name: 'Home',
        component: () => import('@/views/home/index.vue'),
        meta: {
            title: i18n.global.t("router.home"),
            requireAuth: false,
            keepAlive: true,
        },
    },
    //产品
    {
        path: '/product',
        name: 'product',
        component: () => import('@/views/product/index.vue'),
        meta: {
            title: i18n.global.t("router.product"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //订单
    {
        path: '/order',
        name: 'order',
        component: () => import('@/views/order/index.vue'),
        meta: {
            title: i18n.global.t("router.order"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //铺货中心
    {
        path: '/distribution',
        name: 'distribution',
        component: () => import('@/views/distribution/index.vue'),
        meta: {
            title: i18n.global.t("router.distribution"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //商品管理
    {
        path: '/goods',
        name: 'goods',
        component: () => import('@/views/goods/index.vue'),
        meta: {
            title:i18n.global.t("router.goods"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //我的
    {
        path: '/my',
        name: 'my',
        component: () => import('@/views/my/index.vue'),
        meta: {
            title: i18n.global.t("router.my"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //设置
    {
        path: '/setting',
        name: 'setting',
        component: () => import('@/views/setting/index.vue'),
        meta: {
            title:i18n.global.t("router.setting"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //个人信息
    {
        path: '/personalInfo',
        name: 'personalInfo',
        component: () => import('@/views/personalInfo/index.vue'),
        meta: {
            title: i18n.global.t("router.personalInfo"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //退款申请
    {
        path: '/refundRequest',
        name: 'refundRequest',
        component: () => import('@/views/refundRequest/index.vue'),
        meta: {
            title: i18n.global.t("router.refundRequest"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //资金记录
    {
        path: '/fundsRecords',
        name: 'fundsRecords',
        component: () => import('@/views/fundsRecords/index.vue'),
        meta: {
            title: i18n.global.t("router.fundsRecords"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //设置
    {
        path: '/settingup',
        name: 'settingup',
        component: () => import('@/views/settingup/index.vue'),
        meta: {
            title: i18n.global.t("router.settingup"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //财务报告
    {
        path: '/finance',
        name: 'finance',
        component: () => import('@/views/finance/index.vue'),
        meta: {
            title: i18n.global.t("router.finance"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //订单详情
    {
        path: '/orderDetail',
        name: 'orderDetail',
        component: () => import('@/views/orderDetail/index.vue'),
        meta: {
            title: i18n.global.t("router.orderDetail"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //商品详情
    {
        path: '/goodsDetail',
        name: 'goodsDetail',
        component: () => import('@/views/goodsDetail/index.vue'),
        meta: {
            title: i18n.global.t("router.goodsDetail"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //提现
    {
        path: '/withdraw',
        name: 'withdraw',
        component: () => import('@/views/withdraw/index.vue'),
        meta: {
            title: i18n.global.t("router.withdraw"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //提现记录
    {
        path: '/withdrawRecord',
        name: 'withdrawRecord',
        component: () => import('@/views/withdrawRecord/index.vue'),
        meta: {
            title: i18n.global.t("router.withdrawRecord"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //充值
    {
        path: '/recharge',
        name: 'recharge',
        component: () => import('@/views/recharge/index.vue'),
        meta: {
            title:i18n.global.t("router.recharge"),
            requireAuth: false,
            keepAlive: true
        }
    },

    //钱包充值
    {
        path: '/walletRecharge',
        name: 'walletRecharge',
        component: () => import("@/views/walletRecharge/index.vue"),
        meta: {
            title: i18n.global.t("router.walletRecharge"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //银行卡充值
    {
        path: '/bankCardRecharge',
        name: 'bankCardRecharge',
        component: () => import('@/views/bankCardRecharge/index.vue'),
        meta: {
            title:i18n.global.t("router.bankCardRecharge"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //修改登录密码
    {
        path: '/changeLoginPassword',
        name: 'changeLoginPassword',
        component: () => import('@/views/changeLoginPassword/index.vue'),
        meta: {
            title: i18n.global.t("router.changeLoginPassword"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //忘记登录密码
    {
        path: '/forgetPwd',
        name: 'forgetPwd',
        component: () => import('@/views/forgetPwd/index.vue'),
        meta: {
            title: i18n.global.t("router.forgetPwd"),
            requireAuth: false,
            keepAlive: true
        }
    },
    // //支付密码
    // {
    //     path: '/payPassword',
    //     name: 'payPassword',
    //     component: () => import('@/views/payPassword/index.vue'),
    //     meta: {
    //         title: '修改支付密码',
    //         requireAuth: false,
    //         keepAlive: true
    //     }
    // },
    //修改支付密码
    {
        path: '/changePayPassword',
        name: 'changePayPassword',
        component: () => import('@/views/changePayPassword/index.vue'),
        meta: {
            title: i18n.global.t("router.changePayPassword"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //设置支付密码
    {
        path: '/setPaypwd',
        name: 'setPaypwd',
        component: () => import('@/views/setPaypwd/index.vue'),
        meta: {
            title:i18n.global.t("router.setPaypwd"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //区块链充值
    {
        path: '/blockchainRecharge',
        name: 'blockchainRecharge',
        component: () => import('@/views/blockchainRecharge/index.vue'),
        meta: {
            title: i18n.global.t("router.blockchainRecharge"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //充值记录
    {
        path: '/rechargeRecord',
        name: 'rechargeRecord',
        component: () => import('@/views/rechargeRecord/index.vue'),
        meta: {
            title: i18n.global.t("router.rechargeRecord"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //消息
    {
        path: '/message',
        name: 'message',
        component: () => import('@/views/message/index.vue'),
        meta: {
            title: i18n.global.t("router.message"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //消息详情
    {
        path: '/messageDetail',
        name: 'messageDetail',
        component: () => import('@/views/messageDetail/index.vue'),
        meta: {
            title: i18n.global.t("router.messageDetail"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //登录
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/login/index.vue'),
        meta: {
            data: '',
            code: '',
            mer: '',
            path:'',
            title: i18n.global.t("router.login"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //注册
    {
        path: '/register',
        name: 'register',
        component: () => import('@/views/register/index.vue'),
        meta: {
            title:i18n.global.t("router.register"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //店铺直通车
    {
        path: '/storeExpress',
        name: 'storeExpressster',
        component: () => import('@/views/storeExpress/index.vue'),
        meta: {
            title: i18n.global.t("router.storeExpressster"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //直通车记录
    {
        path: '/buyTrainBill',
        name: 'buyTrainBill',
        component: () => import('@/views/buyTrainBill/index.vue'),
        meta: {
            title: i18n.global.t("router.buyTrainBill"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //卖家等级
    {
        path: '/level',
        name: 'level',
        component: () => import('@/views/level/index.vue'),
        meta: {
            title: i18n.global.t("router.level"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //基础信息
    {
        path: '/baseinfo',
        name: 'baseinfo',
        component: () => import('@/views/baseinfo/index.vue'),
        meta: {
            title: i18n.global.t("router.baseinfo"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //社交媒体
    {
        path: '/socialmedia',
        name: 'socialmedia',
        component: () => import('@/views/socialmedia/index.vue'),
        meta: {
            title:i18n.global.t("router.socialmedia"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //创业联盟
    {
        path: '/alliance',
        name: 'alliance',
        component: () => import('@/views/alliance/index.vue'),
        meta: {
            title: i18n.global.t("router.alliance"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //上传横幅
    {
        path: '/uploadBanner',
        name: 'uploadBanner',
        component: () => import('@/views/uploadBanner/index.vue'),
        meta: {
            title: i18n.global.t("router.uploadBanner"),
            requireAuth: false,
            keepAlive: true
        }
    },
    //人工客服
    {
        path: '/service',
        name: 'service',
        component: () => import('@/views/service/index.vue'),
        meta: {
            title: i18n.global.t("router.service"),
            requireAuth: false,
            keepAlive: true
        }
    },
	//客服工作台
	{
	    path: '/services',
	    name: 'services',
	    component: () => import('@/views/service/indexs.vue'),
	    meta: {
	        title: i18n.global.t("router.service"),
	        requireAuth: false,
	        keepAlive: true
	    }
	},
    //认证邀请码
    {
        path: '/code',
        name: 'code',
        component: () => import('@/views/code/index.vue'),
        meta: {
            title: i18n.global.t("router.code"),
            requireAuth: false,
            keepAlive: true
        }
    },
];
