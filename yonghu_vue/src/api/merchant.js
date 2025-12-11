import service from '@/utils/request.js'

export function verifyPay(data) {
  return service({
    url: '/merchant/merchant/verify_pay',
    method: 'post',
    data
  })
}

export function shopDetail(data) {
  return service({
    url: '/api/merchant/detail',
    method: 'get',
    params: data
  })
}

export function follow(data) {
  return service({
    url: '/api/merchant/follow',
    method: 'post',
    data
  })
}
