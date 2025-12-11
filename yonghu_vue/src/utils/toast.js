import { showToast, showSuccessToast, showFailToast, showLoadingToast, closeToast } from 'vant'
const toast = {
  show: (data) => {
    showToast({
      type: data?.type,
      message: data?.msg,
      position: data?.position,
      forbidClick: data?.forbidClick || true,
      duration: data?.duration || 1200,
      className: 'toast-class'
    })
  },
  success: (data) => {
    showSuccessToast({
      message: data?.msg,
      position: data?.position,
      forbidClick: data?.forbidClick || true,
      duration: data?.duration || 1200,
      className: 'toast-class'
    })
  },
  fail: (data) => {
    showFailToast({
      message: data?.msg,
      position: data?.position,
      forbidClick: data?.forbidClick || true,
      duration: data?.duration || 1200,
      className: 'toast-class'
    })
  },
  loading: (data) => {
    showLoadingToast({
      message: data?.msg,
      position: data?.position,
      forbidClick: data?.forbidClick || true,
      duration: data?.duration || 0,
      loadingType: data?.loadingType || 'spinner',
      className: 'toast-class'
    })
  },
  close: () => {
    closeToast()
  }
}
export default toast
