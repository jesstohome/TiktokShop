// 从Vue Router中导入必要的函数
import {createRouter, createWebHashHistory} from 'vue-router';
//import {createRouter, createWebHistory} from 'vue-router';
import {useUserStore} from "@/store/modules/user.js";
import {useI18n} from 'vue-i18n';

import i18n from '@/lang/index.js';

// 使用import.meta.glob动态导入'./modules/'目录中的所有JavaScript文件
const files = import.meta.glob('./modules/*.js', {
    eager: true,
});

// 用于路由模块的临时存储
const routeModuleList = [];

// 遍历导入的文件
Object.keys(files).forEach((key) => {
    // 从每个模块中提取默认导出，如果不存在则使用空对象
    const module = files[key].default || {};

    // 如果模块尚未是数组，则转换为数组
    const moduleList = Array.isArray(module) ? [...module] : [module];

    // 将模块(们)添加到临时路由模块列表
    routeModuleList.push(...moduleList);
});

// 将动态路由存储在单独的列表中
const asyncRouterList = [...routeModuleList];

// 将固定路由存储在单独的列表中
const defaultRouterList = [];

// 将固定路由和动态路由组合成最终的路由数组
const routes = [...defaultRouterList, ...asyncRouterList];

// 使用Vue Router的createRouter函数创建路由实例
const router = createRouter({
    // 使用web history模式并设置基本URL
    // history: createWebHistory(import.meta.env.BASE_URL),
	history: createWebHashHistory(import.meta.env.BASE_URL), // 改成 hash
    routes, // 将路由数组传递给路由实例
    scrollBehavior() {
        // 定义路由的滚动行为
        return {
            el: '#app', // 指定要滚动的元素
            top: 20, // 将滚动位置设置为顶部
            behavior: 'smooth', // 使用平滑滚动行为
        };
    },
});

// 设置默认页面为'/home'
router.addRoute({
    path: '/',
    redirect: '/home',
});

//全局前置守卫
router.beforeEach((to, from, next) => {
    const store = useUserStore();
    const token = store.token;
    // console.log(token);
    // if (!token && to.path !== '/login' && to.path !== '/forgetPwd') {
    //     showDialog({
    //         message: i18n.global.t("loginfirst"),
    //     }).then(() => {
    //
    //     });
    //     console.log('2222');
    //     next({path: "/login"});
    //
    // } else {
    //     next();
    // }
    next();
});
// 全局解析守卫
router.beforeResolve((to, from, next) => {
    // 设置页面标题
    // 判断元信息是否存在
    if (to.meta && to.meta.title) {
        // 设置标题
        window.document.title = to.meta.title;
    }
    // 继续导航
    // 如果不调用next()，则导航将被中断
    next();
});

// 将路由实例作为此模块的默认导出进行导出
export default router;
