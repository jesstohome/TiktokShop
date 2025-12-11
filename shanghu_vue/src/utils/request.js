import axios from 'axios'; // 引入axios

import {useUserStore} from '@/store/modules/user.js';
import {showFailToast} from "vant";
import {useRouter} from "vue-router";
// import {useRouter} from "vue-router";


const service = axios.create({
    baseURL: import.meta.env.VITE_APP_BASE_URL || 'http://localhost:3000',
    timeout: 99999
});
const router = useRouter();

// http request 拦截器
service.interceptors.request.use(
    config => {

        const userStore = useUserStore();
        config.headers.token = userStore.token

        const lang = localStorage.getItem('lang')
        // console.log(lang)
        if (lang) {
            // if (lang !== 'zh-CN' && lang !== 'zh-cn') {
            //     config.headers.lang = 'en';
            // } else {
                config.headers.lang = lang;
            // }
        }
        // console.log(config)
        return config;
    },
    error => {

        return error;
    }
);

// http response 拦截器
service.interceptors.response.use(
    response => {
        // console.log(response.data)
        // const router = useRouter();
        console.log(111)
        return response.data;

    },
    error => {
        console.log(error)
        if (error.response.data.code === 401) {
            const userStore = useUserStore();
            userStore.nologin()
            // useRouter().push('/login');
            //location.href = '/login'
			router.push('/login');
            // console.log(11)
            showFailToast(error.response.data.msg)
        }
    }
);
export default service;
