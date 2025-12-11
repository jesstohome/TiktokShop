import service from '@/utils/request.js'

export function uploadFile(data) {
  return service({
    url: '/merchant/common/upload',
    method: 'post',
    data
  })
}
