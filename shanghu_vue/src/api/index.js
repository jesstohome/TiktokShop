import service from '@/utils/request';

//测试接口
export const test = (data) => {
    return service({
        url: '/merchant/index/index',
        method: 'GET',
        data
    });
};
//上传图片
export const upload = (data) => {
    return service({
        url: '/merchant/common/upload',
        method: 'post',
        data
    });
};
//注册
export const register = (data) => {
    return service({
        url: '/merchant/merchant/register',
        method: 'Post',
        data
    });
};
//登录
export const login = (data) => {
    return service({
        url: '/merchant/merchant/login',
        method: 'Post',
        data
    });
};
//修改设置登录密码
export const changeLoginPwd = (data) => {
    return service({
        url: '/merchant/merchant/resetpwd',
        method: 'Post',
        data
    });
};
//修改支付密码
export const changePayPwd = (data) => {
    return service({
        url: '/merchant/merchant/resetPay',
        method: 'Post',
        data
    });
};
//支付密码确认
export const payPwdconfirm = (data) => {
    return service({
        url: '/merchant/merchant/verify_pay',
        method: 'Post',
        data
    });
};
//首页数据
export const home = () => {
    return service({
        url: '/merchant/index/index',
        method: 'GET',
    });
};
//商铺信息
export const getMerInfo = () => {
    return service({
        url: '/merchant/merchant/getMerInfo',
        method: 'GET',
    });
};
//首页消息列表
export const message = (data) => {
    return service({
        url: '/merchant/index/notice',
        method: 'GET',
        params: data
    });
};
//首页消息详情
export const messageDetail = (data) => {
    return service({
        url: '/merchant/index/see_notice',
        method: 'post',
        data
    });
};
//首页图表数据
export const echart = (data) => {
    return service({
        url: '/merchant/index/echart',
        method: 'get',
        params: data
    });
};
//产品-热销商品
export const hotProduct = () => {
    return service({
        url: '/merchant/product/hot',
        method: 'GET',
    });
};
//铺货中心--商品分类
export const productCategory = () => {
    return service({
        url: '/merchant/product/category',
        method: 'GET',
    });
};
//铺货中心--商品列表
export const productList = (data) => {
    return service({
        url: '/merchant/product/product',
        method: 'get',
        params: data
    });
};
//铺货
export const productAdd = (data) => {
    return service({
        url: '/merchant/product/add',
        method: 'post',
        data
    });
};
//一件铺货
export const addAll = (data) => {
    return service({
        url: '/merchant/product/addAll',
        method: 'post',
        data
    });
};
//商品管理
export const productManagement = (data) => {
    return service({
        url: '/merchant/product/manage',
        method: 'get',
        params: data
    });
};
//商品详情
export const productDetail = (data) => {
    return service({
        url: '/merchant/product/detail',
        method: 'get',
        params: data
    });
};
//商品上下架
export const handleProduct = (data) => {
    return service({
        url: '/merchant/product/setSwitch',
        method: 'post',
        data
    });
};
//删除商品
export const removeProduct = (data) => {
    return service({
        url: '/merchant/product/del',
        method: 'post',
        data
    });
};
//充值方式
export const getBlockchain = (data) => {
    return service({
        url: '/merchant/merchant/getBlockchain',
        method: 'get',
    });
};
//银行卡充值
export const recharge = (data) => {
    return service({
        url: '/merchant/merchant/recharge',
        method: 'post',
        data
    });
};
//充值记录
export const rechargeRecord = (data) => {
    return service({
        url: '/merchant/merchant/recharge_log',
        method: 'get',
        params: data
    });
};
//提现
export const withdraw = (data) => {
    return service({
        url: '/merchant/merchant/extract',
        method: 'post',
        data
    });
};
//提现记录
export const withdrawRecord = (data) => {
    return service({
        url: '/merchant/merchant/extract_log',
        method: 'get',
        params: data
    });
};
//提现信息
export const getwithdrawInfo = (data) => {
    return service({
        url: '/merchant/merchant/extract',
        method: 'get',
    });
};
//资金记录
export const fundsRecords = (data) => {
    return service({
        url: '/merchant/merchant/bill',
        method: 'get',
        params: data
    });
};
//财务报表
export const finance = (data) => {
    return service({
        url: '/merchant/merchant/financial_report',
        method: 'get',
        params: data
    });
};
//订单管理
export const orderManagement = (data) => {
    return service({
        url: '/merchant/order/list',
        method: 'get',
        params: data
    });
};
//订单详情
export const orderDetail = (data) => {
    return service({
        url: '/merchant/order/detail',
        method: 'get',
        params: data
    });
};
//提货
export const pick = (data) => {
    return service({
        url: '/merchant/order/pick',
        method: 'post',
        data
    });
};
//一键提货
export const pickAll = (data) => {
    return service({
        url: '/merchant/order/pick_batch',
        method: 'post',
        data
    });
};
//公告
export const notice = (data) => {
    return service({
        url: '/merchant/merchant/affiche',
        method: 'get',
        params: data
    });
};
//退款订单
export const refundorder = (data) => {
    return service({
        url: '/merchant/merchant/refund_order',
        method: 'get',
        params: data
    });
};
//商户等级
export const level = () => {
    return service({
        url: '/merchant/index/level',
        method: 'get',
    });
};
//店铺直通车
export const train = () => {
    return service({
        url: '/merchant/index/train',
        method: 'get',
    });
};
//购买直通车
export const buyTrain = (data) => {
    return service({
        url: '/merchant/index/buy_train',
        method: 'post',
        data
    });
};
//直通车记录
export const buyTrainBill = (data) => {
    return service({
        url: '/merchant/index/buy_train_bill',
        method: 'get',
        params:data
    });
};
//修改店铺基本信息
export const baseinfo = (data) => {
    return service({
        url: '/merchant/merchant/store',
        method: 'post',
        data
    });
};
//首页横幅
export const banner = (data) => {
    return service({
        url: '/merchant/merchant/store_banner',
        method: 'post',
        data
    });
};
//第三方注册登录
export const third = (data) => {
    return service({
        url: '/merchant/merchant/third',
        method: 'post',
        data
    });
};
//创业联盟
export const alliance = () => {
    return service({
        url: '/merchant/index/coalition',
        method: 'get',
    });
};

//验证邀请码

export const checkInvite = (data) => {
    return service({
        url: '/merchant/merchant/verify_code',
        method: 'post',
        data
    });
}


//三方注册
export const thirdRegister = (data) => {
    return service({
        url: '/merchant/merchant/tkregister',
        method: 'post',
        data
    });
}
//切换中英文
export const changelanguage = (data) => {
    return service({
        url: '/merchant/merchant/set_language',
        method: 'get',
        params:data
    });
}
//语言列表
export const languageList = (data) => {
    return service({
        url: '/merchant/merchant/language_list',
        method: 'get',
        params:data
    });
}
//热销商品一键铺货/merchant/product/addAllHot
export const addAllHot = (data) => {
    return service({
        url: '/merchant/product/addAllHot',
        method: 'post',
        data
    });
}
//获取客服工作台链接
export const getKfUrl = () => {
    return service({
        url: '/merchant/newapi/get_kf_url',
        method: 'get'
    });
};
//获取人工客服链接
export const getKfUrls = () => {
    return service({
        url: '/merchant/newapi/get_kf_urls',
        method: 'get'
    });
};
//获取客服工作台消息数量
export const getKfnum = () => {
    return service({
        url: '/merchant/newapi/get_kf_num',
        method: 'get'
    });
};
