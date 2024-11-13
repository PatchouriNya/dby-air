<template>
  <el-container>
    <el-header>
      <el-button @click="qqq">Click me</el-button>
      <el-button @click="www">Click me</el-button>
    </el-header>
    <el-main>
      <el-row :gutter="20">
        <el-col :span="12">
          <h1 :style="{ width: '100%', textAlign: 'center', marginBottom: '10px' }">图表 1</h1>
          <div :style="{ width: '100%', height: '300px' }" ref="chart1Ref"></div>
        </el-col>
        <el-col :span="12">
          <h1 :style="{ width: '100%', textAlign: 'center', marginBottom: '10px' }">单位空调总数</h1>
          <div :style="{ width: '100%', height: '300px' }" ref="chart2Ref"></div>
        </el-col>
      </el-row>
      <el-row :gutter="20" style="margin-top: 20px;">
        <el-col :span="12">
          <h1 :style="{ width: '100%', textAlign: 'center', marginBottom: '10px' }">单位实时设置温度</h1>
          <div :style="{ width: '100%', height: '300px' }" ref="chart3Ref"></div>
        </el-col>
        <el-col :span="12">
          <h1 :style="{ width: '100%', textAlign: 'center', marginBottom: '10px' }">图表 4</h1>
          <div :style="{ width: '100%', height: '300px' }" ref="chart4Ref"></div>
        </el-col>
      </el-row>
    </el-main>
  </el-container>
</template>

<script setup>
import * as echarts from 'echarts'
import {onMounted, ref} from 'vue'
import {chart2DataApi} from '@/api/client.js'


const chart1Ref = ref(null)
let chart1 = null
let option1 = {
  xAxis: {
    type: 'category',
    data: ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期天']
  },
  yAxis: {
    type: 'value'
  },
  series: [
    {
      data: [150, 230, 224, 218, 135, 147, 260],
      type: 'line'
    }
  ]
}

// 图2
const chart2Ref = ref(null)
let chart2 = null
let option2 = {
  title: {
    text: '',
    subtext: '',
    left: 'center'
  },
  tooltip: {
    trigger: 'item'
  },
  legend: {
    orient: 'vertical',
    left: 'left'
  },
  series: [
    {
      name: '内机总数',
      type: 'pie',
      radius: '50%',
      data: [],
      emphasis: {
        itemStyle: {
          shadowBlur: 10,
          shadowOffsetX: 0,
          shadowColor: 'rgba(0, 0, 0, 0.5)'
        }
      }
    }
  ]
}
const data2 = ref()
const getData2 = async () => {
  const res = await chart2DataApi(localStorage.getItem('token'))
  data2.value = res.data
  console.log(data2.value)
  updateChart2()
}
const updateChart2 = () => {
  option2.series[0].data = data2.value.map(item => {
    return {
      value: item.overview.air_quantity,
      name: item.clientname
    }
  })
  option3.xAxis.data = data2.value.map(item => item.clientname)
  option3.series[0].data = data2.value.map(item => item.overview.air_startup_temperature)
  chart3.setOption(option3)
  chart2.setOption(option2)
}

const chart3Ref = ref(null)
let chart3 = null
let option3 = {
  xAxis: {
    type: 'category',
    data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  },
  yAxis: {
    type: 'value',
    axisLabel: {
      formatter: '{value}℃'
    }
  },
  series: [
    {
      data: [120, 200, 150, 80, 70, 110, 130],
      type: 'bar'
    }
  ]
}
const updateChart3 = () => {


}

const chart4Ref = ref(null)
let chart4 = null
let option4 = {
  series: [
    {
      type: 'gauge',
      startAngle: 180,
      endAngle: 0,
      center: ['50%', '75%'],
      radius: '90%',
      min: 0,
      max: 1,
      splitNumber: 8,
      axisLine: {
        lineStyle: {
          width: 6,
          color: [
            [0.25, '#FF6E76'],
            [0.5, '#FDDD60'],
            [0.75, '#58D9F9'],
            [1, '#7CFFB2']
          ]
        }
      },
      pointer: {
        icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
        length: '12%',
        width: 20,
        offsetCenter: [0, '-60%'],
        itemStyle: {
          color: 'auto'
        }
      },
      axisTick: {
        length: 12,
        lineStyle: {
          color: 'auto',
          width: 2
        }
      },
      splitLine: {
        length: 20,
        lineStyle: {
          color: 'auto',
          width: 5
        }
      },
      axisLabel: {
        color: '#464646',
        fontSize: 20,
        distance: -60,
        rotate: 'tangential',
        formatter: function (value) {
          if (value === 0.875) {
            return 'Grade A'
          } else if (value === 0.625) {
            return 'Grade B'
          } else if (value === 0.375) {
            return 'Grade C'
          } else if (value === 0.125) {
            return 'Grade D'
          }
          return ''
        }
      },
      title: {
        offsetCenter: [0, '-10%'],
        fontSize: 20
      },
      detail: {
        fontSize: 30,
        offsetCenter: [0, '-35%'],
        valueAnimation: true,
        formatter: function (value) {
          return Math.round(value * 100) + ''
        },
        color: 'inherit'
      },
      data: [
        {
          value: 0.7,
          name: 'Grade Rating'
        }
      ]
    }
  ]
}


onMounted(() => {
  chart1 = echarts.init(chart1Ref.value)
  chart1.setOption(option1)
  chart2 = echarts.init(chart2Ref.value)
  chart2.setOption(option2)
  chart3 = echarts.init(chart3Ref.value)
  chart3.setOption(option3)
  chart4 = echarts.init(chart4Ref.value)
  chart4.setOption(option4)
  getData2()
  updateChart3()
})

function qqq() {
  option1.series[0].data = [100, 200, 300, 400, 500, 600, 700]
  chart1.setOption(option1)
}

function www() {
  option1.series[0].data = [150, 230, 224, 218, 135, 147, 260]
  chart1.setOption(option1)
}
</script>

<style scoped lang="scss">

</style>