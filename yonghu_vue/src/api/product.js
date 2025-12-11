import service from '@/utils/request.js'

export function topTenKey() {
  return service({
    url: `/api/product/index`,
    method: 'get'
  })
}

export function recommend(data) {
  return service({
    url: `/api/product/recommend`,
    method: 'get',
    params: data
  })
}

export function getProductDetail(data) {
  return service({
    url: `/api/product/detail`,
    method: 'get',
    params: data
  })
}

export function search(data) {
  return service({
    url: `/api/product/search`,
    method: 'get',
    params: data
  })
}

export function searchs(data) {
  return service({
    url: `/api/product/searchs`,
    method: 'get',
    params: data
  })
}

export function like(data) {
  return service({
    url: `/api/product/like`,
    method: 'post',
    data: data
  })
}
