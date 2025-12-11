import service from '@/utils/request.js'

export function getCartList() {
  return service({
    url: '/api/cart/index',
    method: 'get'
  })
}

export function delCarts(ids) {
  return service({
    url: '/api/cart/del',
    method: 'post',
    data: {
      cart_ids: ids
    }
  })
}

export function setCartNum(data) {
  return service({
    url: '/api/cart/set_num',
    method: 'post',
    data: data
  })
}

export function addCart(data) {
  return service({
    url: '/api/cart/add',
    method: 'post',
    data: data
  })
}
