import service from '@/utils/request.js'

export function login(data) {
  return service({
    url: '/api/user/login',
    method: 'post',
    data
  })
}

export function getInfo() {
  return service({
    url: '/api/user/getUserInfo',
    method: 'get'
  })
}

export function logout() {
  return service({
    url: '/api/user/logout?lang=zh-cn',
    method: 'post'
  })
}

export function register(data) {
  return service({
    url: '/api/user/register',
    method: 'post',
    data
  })
}

//  我的订单列表
export function orderList(data) {
  return service({
    url: '/api/user/order_list',
    method: 'get',
    params: data
  })
}

export function profile(data) {
  return service({
    url: '/api/user/profile',
    method: 'post',
    data: data
  })
}

export function resetpwd(data) {
  return service({
    url: '/api/user/resetpwd',
    method: 'post',
    data: data
  })
}

export function resetPaypwd(data) {
  return service({
    url: '/api/user/resetPay',
    method: 'post',
    data: data
  })
}

export function followList(data) {
  return service({
    url: '/api/user/follow_list',
    method: 'get',
    params: data
  })
}

export function likeList(data) {
  return service({
    url: '/api/user/like_list',
    method: 'get',
    params: data
  })
}

export function blockChain() {
  return service({
    url: '/api/user/getBlockchain',
    method: 'get'
  })
}

export function recharge(data) {
  return service({
    url: '/api/user/recharge',
    method: 'post',
    data: data
  })
}

export function deposit(data) {
  return service({
    url: '/api/user/extract',
    method: 'post',
    data: data
  })
}

export function depositInfo() {
  return service({
    url: '/api/user/extract',
    method: 'get'
  })
}

export function addressList(data) {
  return service({
    url: '/api/user/address_list',
    method: 'get',
    params: data
  })
}

export function addAddress(data) {
  return service({
    url: '/api/user/address_add',
    method: 'post',
    data: data
  })
}

export function getAddressInfo(data) {
  return service({
    url: '/api/user/address',
    method: 'get',
    params: data
  })
}

export function setDefaultAddress(data) {
  return service({
    url: '/api/user/address_set_default',
    method: 'post',
    data: data
  })
}

export function delAddress(data) {
  return service({
    url: '/api/user/address_del',
    method: 'get',
    params: data
  })
}

export function languageList(data) {
  return service({
    url: '/api/user/language_list',
    method: 'get',
    params: data
  })
}

export function setDefaultLanguage(data) {
  return service({
    url: '/api/user/set_language',
    method: 'get',
    params: data
  })
}

export function billList(data) {
  return service({
    url: '/api/user/bill',
    method: 'get',
    params: data
  })
}

export function resetPwd(data) {
  return service({
    url: '/api/user/resetPay',
    method: 'post',
    data: data
  })
}

export function area(data) {
  return service({
    url: '/api/user/area',
    method: 'get',
    params: data
  })
}

export function helpSupport() {
  return service({
    url: '/api/user/help_support',
    method: 'get'
  })
}

export function law() {
  return service({
    url: '/api/user/law',
    method: 'get'
  })
}

export function verifyPay(data) {
  return service({
    url: '/api/user/verify_pay',
    method: 'post',
    data: data
  })
}

/**
 * 查询用户充值记录
 *
 * @returns 返回一个Promise对象，表示请求的结果
 */
export function rechargeRecord(data) {
  return service({
    url: '/api/user/recharge_log',
    method: 'get',
    params: data
  })
}

/**
 * 获取提现记录
 *
 * @returns 返回一个Promise对象，resolve时返回提现记录数据
 */
export function depositRecord(data) {
  return service({
    url: '/api/user/extract_log',
    method: 'get',
    params: data
  })
}
