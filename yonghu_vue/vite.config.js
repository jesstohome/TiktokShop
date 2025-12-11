import vue from '@vitejs/plugin-vue';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';
import { VantResolver } from '@vant/auto-import-resolver';
import { defineConfig } from 'vite'; // 引入 defineConfig 函数
import { resolve } from 'path';
import VueSetupExtend from 'vite-plugin-vue-setup-extend'
import { AntDesignVueResolver } from 'unplugin-vue-components/resolvers';


export default defineConfig({ // 使用 defineConfig 函数
    plugins: [
        vue(),
        AutoImport({
            imports: ['vue', 'vue-router', 'pinia'],
            resolvers: [
                VantResolver()
            ],
        }),
        Components({
            resolvers: [
                VantResolver(),
                AntDesignVueResolver({
                    importStyle: false, // css in js
                }),
            ] ,
        }),
        VueSetupExtend()
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'src'),
        },
    },
    server: {
        // 端口号
        port: 5173,
        // 监听所有地址
        host: '0.0.0.0',
        // 服务启动时是否自动打开浏览器
        open: false,
        // 允许跨域
        cors: true,
        // 自定义代理规则
        proxy: {
            '/api': {
                target: 'http://192.168.2.15', // 设置代理目标地址
                changeOrigin: true,
                rewrite: (path) => path.replace(/^\/api/, ''), // 去掉请求路径中的 '/api' 前缀
            },
        },
    },
});
