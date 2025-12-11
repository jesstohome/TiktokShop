import serivce from '@/utils/request.js'

export function createOrder(data) {
  return serivce.request({
    url: '/api/order/merCreate',
    method: 'post',
    data: data
  })
}

export function submitOrder(data) {
  return serivce.request({
    url: '/api/order/merSubmit',
    method: 'post',
    data: data
  })
}

export function cancelOrder(data) {
  return serivce.request({
    url: '/api/order/cancel',
    method: 'post',
    data: data
  })
}

export function received(data) {
  return serivce.request({
    url: '/api/order/received',
    method: 'post',
    data: data
  })
}

export function detail(data) {
  return serivce.request({
    url: '/api/order/detail',
    method: 'get',
    params: data
  })
}

export function merPay(data) {
  return serivce.request({
    url: '/api/order/merPay',
    method: 'post',
    data: data
  })
}

export function pay(data) {
  return serivce.request({
    url: '/api/user/pay',
    method: 'post',
    data: data
  })
}

export function deleteOrder(data) {
  return serivce.request({
    url: '/api/order/delete',
    method: 'post',
    data: data
  })
}

/**
 * 退款订单
 *
 * @param data 退款数据
 * @returns 返回退款请求结果
 */
export function refundOrder(data) {
  return serivce.request({
    url: '/api/order/refund',
    method: 'post',
    data: data
  })
}


export function comment(data) {
  return serivce.request({
    url: '/api/order/comment',
    method: 'post',
    data: data
  })
}