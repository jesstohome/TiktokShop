<template>
  <div
      class="echart"
      ref="mychart"
      id="mychart"
      :style="{ float: 'left', width: '100%', height: '350px' }"
  ></div>
</template>

<script>
import * as echarts from "echarts";
import {echart} from "@/api/index"

export default {
  data() {
    return {
      list: {},
      range: {
        range: "seven"
      }
    };
  },
  mounted() {
    this.getList();
  },
  methods: {
    getList() {
      echart(this.range).then(res=>{
        this.list=res.data;
        console.log(this.list)
        const option = {
          xAxis: {
            type: 'category',
            data: this.list.x,
          },
          yAxis: {
            type: 'value'
          },
          series: [
            {
              data: this.list.y,
              type: 'line'
            }
          ]
        };
        const myChart = echarts.init(this.$refs.mychart);// 图标初始化
        myChart.setOption(option);// 渲染页面
        //随着屏幕大小调节图表
        window.addEventListener("resize", () => {
          myChart.resize();
        });
      })

    }
  }
};
</script>