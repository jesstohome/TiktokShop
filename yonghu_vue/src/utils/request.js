import axios, { AxiosError } from 'axios'
// import { getToken } from '@/utils/auth.js'
import { tansParams } from '@/utils/tool.js'
import cache from '@/utils/cache.js'
import useUserStore from '@/stores/modules/user.js'
import toast from '@/utils/toast.js'
import { getLocalLang } from '@/utils/auth.js'
import { default_lang } from '@/utils/constants.js'

// 是否显示重新登录
export const isRelogin = { show: false }

// axios.defaults.headers['Content-Type'] = 'application/json;charset=utf-8'
// 创建axios实例
const service = axios.create({
  // axios中请求配置有baseURL选项，表示请求URL公共部分
  // baseURL: '/api',
  baseURL: import.meta.env.VITE_APP_BASE_API || '/api',
  // baseURL: import.meta.env.VITE_APP_BASE_URL || 'http://localhost:3000',
  // 超时
  timeout: 10000
})

// request拦截器
service.interceptors.request.use(
  (config) => {
    // 是否需要设置 token
    const isToken = (config.headers || {}).isToken === false
    // 是否需要防止数据重复提交
    const isRepeatSubmit = (config.headers || {}).repeatSubmit === false
    const token = useUserStore().token
    if (token && !isToken) {
      // config.headers['Authorization'] = 'Bearer ' + token // 让每个请求携带自定义token 请根据实际情况自行修改
      config.headers['token'] = token // 让每个请求携带自定义token 请根据实际情况自行修改
    }
    // get请求映射params参数
    if (config.method === 'get' && config.params) {
      let url = config.url + '?' + tansParams(config.params)
      url = url.slice(0, -1)
      config.params = {}
      config.url = url
    }
    if (!isRepeatSubmit && (config.method === 'post' || config.method === 'put')) {
      const requestObj = {
        url: config.url,
        data: typeof config.data === 'object' ? JSON.stringify(config.data) : config.data,
        time: new Date().getTime()
      }
      const sessionObj = cache.session.getJSON('sessionObj')
      if (sessionObj === undefined || sessionObj === null || sessionObj === '') {
        cache.session.setJSON('sessionObj', requestObj)
      } else {
        const s_url = sessionObj.url // 请求地址
        const s_data = sessionObj.data // 请求数据
        const s_time = sessionObj.time // 请求时间
        const interval = 1000 // 间隔时间(ms)，小于此时间视为重复提交
        if (
          s_data === requestObj.data &&
          requestObj.time - s_time < interval &&
          s_url === requestObj.url
        ) {
          const message = '数据正在处理，请勿重复提交'
          console.warn(`[${s_url}]: ` + message)
          return Promise.reject(new Error(message))
        } else {
          cache.session.setJSON('sessionObj', requestObj)
        }
      }
    }
	const lang = getLocalLang() || default_lang
	
	if (lang) {
	    // if (lang !== 'zh-CN' && lang !== 'zh-cn') {
	    //     config.headers.lang = 'en';
	    // } else {
	        config.headers.lang = lang.file_name;
	    // }
	}
    return config
  },
  (error) => {
    console.log(error)
    Promise.reject(error)
  }
)

// 响应拦截器
service.interceptors.response.use(
  (res) => {
    console.log(res)
    const data = res.data
    if (data.code === 1) {
      //  成功
      console.log('请求成功')
      return Promise.resolve(data)
    } else {
      //  错误
      console.log('请求错误')
      toast.fail({ msg: data.msg })
      return Promise.reject(data)
    }
  },
  async (error) => {
    console.log(error)
    // 未登录或token过期
    if (error.response.data.code === 401) {
      toast.fail({ msg: error.response.data.msg })
      const isLogin = await useUserStore().isLogin()
      //  如果已经登录了,除了提示还要返回登录页面
      if (isLogin) {
        await useUserStore().invalidToken()
        location.href = '/#/login'
      }
      return Promise.reject(error)
    } else {
      let { message } = error
      if (message === 'Network Error') {
        message = '后端接口连接异常'
      } else if (message.includes('timeout')) {
        console.log('超时')
        message = '系统接口请求超时'
      } else if (message.includes('Request failed with status code')) {
        message = '系统接口' + message.substr(message.length - 3) + '异常'
      }
      toast.fail({ msg: message })
      return Promise.reject(error)
    }
  }
)

// 通用下载方法
// export function download(url, params, filename, config) {
//   if (config && !config.hideLoading) {
//     downloadLoadingInstance = ElLoading.service({ text: '正在下载数据，请稍候', background: 'rgba(0, 0, 0, 0.7)' })
//   }
//   return service.post(url, params, {
//     transformRequest: [(params) => { return tansParams(params) }],
//     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
//     responseType: 'blob',
//     ...config
//   }).then(async(data) => {
//     const isLogin = await blobValidate(data)
//     if (isLogin) {
//       const blob = new Blob([data])
//       saveAs(blob, filename)
//     } else {
//       const resText = await data.text()
//       const rspObj = JSON.parse(resText)
//       const errMsg = errorCode[rspObj.code] || rspObj.msg || errorCode['default']
//       ElMessage.error(errMsg)
//     }
//     downloadLoadingInstance && downloadLoadingInstance.close()
//   }).catch((r) => {
//     console.error(r)
//     ElMessage.error('下载文件出现错误，请联系管理员！')
//     downloadLoadingInstance && downloadLoadingInstance.close()
//   })
// }

export default service
