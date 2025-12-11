import service from '@/utils/request.js'

//  首页轮播图
export function banner() {
  return service.request({
    url: '/api/index/banner',
    method: 'get'
  })
}

export function category() {
  return service.request({
    url: '/api/index/category',
    method: 'get'
  })
}

export function product(data) {
  return service.request({
    url: `/api/index/product`,
    method: 'get',
    params: data
  })
}

export function hotProduct(data) {
  return service.request({
    url: `/api/index/hot_product`,
    method: 'get',
    params: data
  })
}

export function hotShop() {
  return service.request({
    url: `/api/index/hot_merchant?page=1&limit=8`,
    method: 'get'
  })
}

export function platformInfo() {
  return service.request({
    url: `/api/index/index`,
    method: 'get'
  })
}

export function get_shops_kfs(data) {
  return service.request({
    url: `/api/user/get_shops_kf`,
    method: 'get',
	 params: data
  })
}
