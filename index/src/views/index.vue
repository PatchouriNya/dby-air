<template>
  <el-row>
    <el-col :span="6">
      <div style="width: 270px">

        <ClientTree ref="tree" style="width: 100%; height: 100%"/>
      </div>

    </el-col>
    <el-col :span="18">
      <div class="right-content">
        <el-row>
          <el-col :span="6" :pull="3" style="margin-top: 40px">
            <el-row>
              <el-col :pull="2">
                <div id="main" :style="{ width: '400px', height: '200px' }" ref="chartRef"></div>
              </el-col>
            </el-row>
            <el-row>
              <el-col :push="4">
                <div id="temp" :style="{ width: '250px', height: '150px' }" style="margin-top: 30px"
                     ref="tempRef"></div>
              </el-col>
            </el-row>
          </el-col>
          <el-col :span="14" :pull="3">
            <h2 style="text-align: center;margin-bottom: 10px;margin-left: 340px">{{
                selectData && selectData.clientname
              }}</h2>
            <img src="@/assets/map.png" alt="">
          </el-col>
        </el-row>
      </div>
    </el-col>
  </el-row>
</template>

<script setup>
import ClientTree from '@/components/ClientTree.vue'
import {ref, onMounted, watch} from 'vue'
import * as echarts from 'echarts/core'
import {TooltipComponent, LegendComponent} from 'echarts/components'
import {PieChart} from 'echarts/charts'
import {LabelLayout} from 'echarts/features'
import {CanvasRenderer} from 'echarts/renderers'
import eventBus from '@/listen/event-bus.js'
import {GaugeChart} from 'echarts/charts'

echarts.use([GaugeChart])

const selectData = ref()
const airBootQuantity = ref(0)
const airQuantity = ref(0)
const tempValue = ref(0)
eventBus.on('node-clicked', (val) => {
  selectData.value = val
  airBootQuantity.value = val.with_overview ? val.with_overview.air_boot_quantity : 0
  if (airBootQuantity.value === null) airBootQuantity.value = 0
  airQuantity.value = val.with_overview ? val.with_overview.air_quantity : 0
  if (airQuantity.value === null) airQuantity.value = 0
  tempValue.value = val.with_overview ? val.with_overview.air_startup_temperature : 0
  if (tempValue.value === null) tempValue.value = 0
})

echarts.use([TooltipComponent, LegendComponent, PieChart, CanvasRenderer, LabelLayout])

const chartRef = ref(null)
const chart = ref(null)
// 第二幅图
const tempRef = ref(null)
const temp = ref(null)

onMounted(() => {
  eventBus.on('defaultNode', (val) => {
    eventBus.emit('node-clicked', val)
  })
  chart.value = echarts.init(chartRef.value)
  temp.value = echarts.init(tempRef.value)
  const option = {
    tooltip: {
      trigger: 'item'
    },
    legend: {
      top: '1%',
      left: 'center'
    },
    series: [
      {
        name: '开机状态',
        type: 'pie',
        radius: ['40%', '70%'],
        avoidLabelOverlap: false,
        label: {
          show: false,
          position: 'center'
        },
        emphasis: {
          label: {
            show: true,
            fontSize: 20,
            fontWeight: 'bold'
          }
        },
        labelLine: {
          show: false
        },
        data: [
          {
            value: airBootQuantity.value,
            name: '开机数量',
            itemStyle: {color: '#5C7BD9'}
          },
          {
            value: airQuantity.value - airBootQuantity.value,
            name: '关机数量',
            itemStyle: {color: '#F06767'}
          }
        ]
      }
    ]
  }
  const option2 = {
    series: [
      {
        type: 'gauge',
        center: ['50%', '60%'],
        startAngle: 200,
        endAngle: -20,
        min: 0,
        max: 60,
        splitNumber: 12,
        itemStyle: {
          color: '#FFAB91'
        },
        progress: {
          show: true,
          width: 20
        },
        pointer: {
          show: false
        },
        axisLine: {
          lineStyle: {
            width: 31
          }
        },
        axisTick: {
          distance: -45,
          splitNumber: 5,
          lineStyle: {
            width: 1,
            color: '#999'
          }
        },
        splitLine: {
          distance: -41,
          length: 14,
          lineStyle: {
            width: 2,
            color: '#999'
          }
        },
        axisLabel: {
          distance: -7,
          color: '#999',
          fontSize: 15
        },
        anchor: {
          show: false
        },
        title: {
          show: false
        },
        detail: {
          valueAnimation: true,
          width: '50%',
          lineHeight: 40,
          borderRadius: 8,
          offsetCenter: [0, '-15%'],
          fontSize: 16,
          fontWeight: 'bolder',
          formatter: '{value} °C',
          color: 'inherit'
        },
        data: [
          {
            value: tempValue.value
          }
        ]
      },
      {
        type: 'gauge',
        center: ['50%', '60%'],
        startAngle: 200,
        endAngle: -20,
        min: 0,
        max: 60,
        itemStyle: {
          color: '#FD7347'
        },
        progress: {
          show: true,
          width: 8
        },
        pointer: {
          show: false
        },
        axisLine: {
          show: false
        },
        axisTick: {
          show: false
        },
        splitLine: {
          show: false
        },
        axisLabel: {
          show: false
        },
        detail: {
          show: false
        },
        data: [
          {
            value: tempValue.value
          }
        ]
      }
    ]
  }
  chart.value.setOption(option)
  temp.value.setOption(option2)
})

watch([airBootQuantity, airQuantity, tempValue], ([newValue1, newValue2, newValue3]) => {
  const option = {
    series: [
      {
        data: [
          {
            value: newValue1,
            name: '开机数量',
            itemStyle: {color: '#5C7BD9'}
          },
          {
            value: newValue2 - newValue1,
            name: '关机数量',
            itemStyle: {color: '#F06767'}
          }
        ]
      }
    ]
  }
  const option2 = {
    series: [
      {
        data: [
          {
            value: newValue3
          }
        ]
      }
    ]
  }
  chart.value.setOption(option)
  temp.value.setOption(option2)
})
</script>

<style scoped>

</style>