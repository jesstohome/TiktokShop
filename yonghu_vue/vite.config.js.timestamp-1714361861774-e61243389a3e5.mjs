// vite.config.js
import vue from "file:///D:/moxiu/moxiu-mall/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import AutoImport from "file:///D:/moxiu/moxiu-mall/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///D:/moxiu/moxiu-mall/node_modules/unplugin-vue-components/dist/vite.js";
import { VantResolver } from "file:///D:/moxiu/moxiu-mall/node_modules/@vant/auto-import-resolver/dist/index.esm.mjs";
import { defineConfig } from "file:///D:/moxiu/moxiu-mall/node_modules/vite/dist/node/index.js";
import { resolve } from "path";
import VueSetupExtend from "file:///D:/moxiu/moxiu-mall/node_modules/vite-plugin-vue-setup-extend/dist/index.mjs";
import { AntDesignVueResolver } from "file:///D:/moxiu/moxiu-mall/node_modules/unplugin-vue-components/dist/resolvers.js";
var __vite_injected_original_dirname = "D:\\moxiu\\moxiu-mall";
var vite_config_default = defineConfig({
  // 使用 defineConfig 函数
  plugins: [
    vue(),
    AutoImport({
      imports: ["vue", "vue-router", "pinia"],
      resolvers: [
        VantResolver()
      ]
    }),
    Components({
      resolvers: [
        VantResolver(),
        AntDesignVueResolver({
          importStyle: false
          // css in js
        })
      ]
    }),
    VueSetupExtend()
  ],
  resolve: {
    alias: {
      "@": resolve(__vite_injected_original_dirname, "src")
    }
  },
  server: {
    // 端口号
    port: 5173,
    // 监听所有地址
    host: "0.0.0.0",
    // 服务启动时是否自动打开浏览器
    open: false,
    // 允许跨域
    cors: true,
    // 自定义代理规则
    proxy: {
      "/api": {
        target: "http://192.168.2.15",
        // 设置代理目标地址
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, "")
        // 去掉请求路径中的 '/api' 前缀
      }
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJEOlxcXFxtb3hpdVxcXFxtb3hpdS1tYWxsXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJEOlxcXFxtb3hpdVxcXFxtb3hpdS1tYWxsXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9EOi9tb3hpdS9tb3hpdS1tYWxsL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnO1xyXG5pbXBvcnQgQXV0b0ltcG9ydCBmcm9tICd1bnBsdWdpbi1hdXRvLWltcG9ydC92aXRlJztcclxuaW1wb3J0IENvbXBvbmVudHMgZnJvbSAndW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZSc7XHJcbmltcG9ydCB7IFZhbnRSZXNvbHZlciB9IGZyb20gJ0B2YW50L2F1dG8taW1wb3J0LXJlc29sdmVyJztcclxuaW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7IC8vIFx1NUYxNVx1NTE2NSBkZWZpbmVDb25maWcgXHU1MUZEXHU2NTcwXHJcbmltcG9ydCB7IHJlc29sdmUgfSBmcm9tICdwYXRoJztcclxuaW1wb3J0IFZ1ZVNldHVwRXh0ZW5kIGZyb20gJ3ZpdGUtcGx1Z2luLXZ1ZS1zZXR1cC1leHRlbmQnXHJcbmltcG9ydCB7IEFudERlc2lnblZ1ZVJlc29sdmVyIH0gZnJvbSAndW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvcmVzb2x2ZXJzJztcclxuXHJcblxyXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoeyAvLyBcdTRGN0ZcdTc1MjggZGVmaW5lQ29uZmlnIFx1NTFGRFx1NjU3MFxyXG4gICAgcGx1Z2luczogW1xyXG4gICAgICAgIHZ1ZSgpLFxyXG4gICAgICAgIEF1dG9JbXBvcnQoe1xyXG4gICAgICAgICAgICBpbXBvcnRzOiBbJ3Z1ZScsICd2dWUtcm91dGVyJywgJ3BpbmlhJ10sXHJcbiAgICAgICAgICAgIHJlc29sdmVyczogW1xyXG4gICAgICAgICAgICAgICAgVmFudFJlc29sdmVyKClcclxuICAgICAgICAgICAgXSxcclxuICAgICAgICB9KSxcclxuICAgICAgICBDb21wb25lbnRzKHtcclxuICAgICAgICAgICAgcmVzb2x2ZXJzOiBbXHJcbiAgICAgICAgICAgICAgICBWYW50UmVzb2x2ZXIoKSxcclxuICAgICAgICAgICAgICAgIEFudERlc2lnblZ1ZVJlc29sdmVyKHtcclxuICAgICAgICAgICAgICAgICAgICBpbXBvcnRTdHlsZTogZmFsc2UsIC8vIGNzcyBpbiBqc1xyXG4gICAgICAgICAgICAgICAgfSksXHJcbiAgICAgICAgICAgIF0gLFxyXG4gICAgICAgIH0pLFxyXG4gICAgICAgIFZ1ZVNldHVwRXh0ZW5kKClcclxuICAgIF0sXHJcbiAgICByZXNvbHZlOiB7XHJcbiAgICAgICAgYWxpYXM6IHtcclxuICAgICAgICAgICAgJ0AnOiByZXNvbHZlKF9fZGlybmFtZSwgJ3NyYycpLFxyXG4gICAgICAgIH0sXHJcbiAgICB9LFxyXG4gICAgc2VydmVyOiB7XHJcbiAgICAgICAgLy8gXHU3QUVGXHU1M0UzXHU1M0Y3XHJcbiAgICAgICAgcG9ydDogNTE3MyxcclxuICAgICAgICAvLyBcdTc2RDFcdTU0MkNcdTYyNDBcdTY3MDlcdTU3MzBcdTU3NDBcclxuICAgICAgICBob3N0OiAnMC4wLjAuMCcsXHJcbiAgICAgICAgLy8gXHU2NzBEXHU1MkExXHU1NDJGXHU1MkE4XHU2NUY2XHU2NjJGXHU1NDI2XHU4MUVBXHU1MkE4XHU2MjUzXHU1RjAwXHU2RDRGXHU4OUM4XHU1NjY4XHJcbiAgICAgICAgb3BlbjogZmFsc2UsXHJcbiAgICAgICAgLy8gXHU1MTQxXHU4QkI4XHU4REU4XHU1N0RGXHJcbiAgICAgICAgY29yczogdHJ1ZSxcclxuICAgICAgICAvLyBcdTgxRUFcdTVCOUFcdTRFNDlcdTRFRTNcdTc0MDZcdTg5QzRcdTUyMTlcclxuICAgICAgICBwcm94eToge1xyXG4gICAgICAgICAgICAnL2FwaSc6IHtcclxuICAgICAgICAgICAgICAgIHRhcmdldDogJ2h0dHA6Ly8xOTIuMTY4LjIuMTUnLCAvLyBcdThCQkVcdTdGNkVcdTRFRTNcdTc0MDZcdTc2RUVcdTY4MDdcdTU3MzBcdTU3NDBcclxuICAgICAgICAgICAgICAgIGNoYW5nZU9yaWdpbjogdHJ1ZSxcclxuICAgICAgICAgICAgICAgIHJld3JpdGU6IChwYXRoKSA9PiBwYXRoLnJlcGxhY2UoL15cXC9hcGkvLCAnJyksIC8vIFx1NTNCQlx1NjM4OVx1OEJGN1x1NkM0Mlx1OERFRlx1NUY4NFx1NEUyRFx1NzY4NCAnL2FwaScgXHU1MjREXHU3RjAwXHJcbiAgICAgICAgICAgIH0sXHJcbiAgICAgICAgfSxcclxuICAgIH0sXHJcbn0pO1xyXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQWlQLE9BQU8sU0FBUztBQUNqUSxPQUFPLGdCQUFnQjtBQUN2QixPQUFPLGdCQUFnQjtBQUN2QixTQUFTLG9CQUFvQjtBQUM3QixTQUFTLG9CQUFvQjtBQUM3QixTQUFTLGVBQWU7QUFDeEIsT0FBTyxvQkFBb0I7QUFDM0IsU0FBUyw0QkFBNEI7QUFQckMsSUFBTSxtQ0FBbUM7QUFVekMsSUFBTyxzQkFBUSxhQUFhO0FBQUE7QUFBQSxFQUN4QixTQUFTO0FBQUEsSUFDTCxJQUFJO0FBQUEsSUFDSixXQUFXO0FBQUEsTUFDUCxTQUFTLENBQUMsT0FBTyxjQUFjLE9BQU87QUFBQSxNQUN0QyxXQUFXO0FBQUEsUUFDUCxhQUFhO0FBQUEsTUFDakI7QUFBQSxJQUNKLENBQUM7QUFBQSxJQUNELFdBQVc7QUFBQSxNQUNQLFdBQVc7QUFBQSxRQUNQLGFBQWE7QUFBQSxRQUNiLHFCQUFxQjtBQUFBLFVBQ2pCLGFBQWE7QUFBQTtBQUFBLFFBQ2pCLENBQUM7QUFBQSxNQUNMO0FBQUEsSUFDSixDQUFDO0FBQUEsSUFDRCxlQUFlO0FBQUEsRUFDbkI7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLE9BQU87QUFBQSxNQUNILEtBQUssUUFBUSxrQ0FBVyxLQUFLO0FBQUEsSUFDakM7QUFBQSxFQUNKO0FBQUEsRUFDQSxRQUFRO0FBQUE7QUFBQSxJQUVKLE1BQU07QUFBQTtBQUFBLElBRU4sTUFBTTtBQUFBO0FBQUEsSUFFTixNQUFNO0FBQUE7QUFBQSxJQUVOLE1BQU07QUFBQTtBQUFBLElBRU4sT0FBTztBQUFBLE1BQ0gsUUFBUTtBQUFBLFFBQ0osUUFBUTtBQUFBO0FBQUEsUUFDUixjQUFjO0FBQUEsUUFDZCxTQUFTLENBQUMsU0FBUyxLQUFLLFFBQVEsVUFBVSxFQUFFO0FBQUE7QUFBQSxNQUNoRDtBQUFBLElBQ0o7QUFBQSxFQUNKO0FBQ0osQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
