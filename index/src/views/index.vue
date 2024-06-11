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
            <!--            <img src="@/assets/map.png" alt="">-->
            <div id="map" :style="{ width: '1053px', height: '775px' }" ref="mapRef"></div>

          </el-col>
        </el-row>
      </div>
    </el-col>
  </el-row>
</template>

<script setup>
import axios from 'axios'
import ClientTree from '@/components/ClientTree.vue'
import {ref, onMounted, watch} from 'vue'
import * as echarts from 'echarts'
import {MapChart} from 'echarts/charts.js'
import {TooltipComponent, LegendComponent} from 'echarts/components'
import {PieChart} from 'echarts/charts'
import {LabelLayout} from 'echarts/features'
import {CanvasRenderer} from 'echarts/renderers'
import eventBus from '@/listen/event-bus.js'
import {GaugeChart} from 'echarts/charts'
import {cityMap} from '@/assets/citymap.js'
import {mapDataApi} from '@/api/client.js'

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
// 第一幅图
const chartRef = ref(null)
const chart = ref(null)
// 第二幅图
const tempRef = ref(null)
const temp = ref(null)
// 第三幅图
const mapRef = ref(null)
const mapChart = ref(null)
//34个省、市、自治区的名字拼音映射数组
let provinces = {
  //23个省
  "台湾": "taiwan",
  "河北": "hebei",
  "山西": "shanxi",
  "辽宁": "liaoning",
  "吉林": "jilin",
  "黑龙江": "heilongjiang",
  "江苏": "jiangsu",
  "浙江": "zhejiang",
  "安徽": "anhui",
  "福建": "fujian",
  "江西": "jiangxi",
  "山东": "shandong",
  "河南": "henan",
  "湖北": "hubei",
  "湖南": "hunan",
  "广东": "guangdong",
  "海南": "hainan",
  "四川": "sichuan",
  "贵州": "guizhou",
  "云南": "yunnan",
  "陕西": "shanxi1",
  "甘肃": "gansu",
  "青海": "qinghai",
  //5个自治区
  "新疆": "xinjiang",
  "广西": "guangxi",
  "内蒙古": "neimenggu",
  "宁夏": "ningxia",
  "西藏": "xizang",
  //4个直辖市
  "北京": "beijing",
  "天津": "tianjin",
  "上海": "shanghai",
  "重庆": "chongqing",
  //2个特别行政区
  "香港": "xianggang",
  "澳门": "aomen"
}
//直辖市和特别行政区-只有二级地图，没有三级地图
let special = ["北京", "天津", "上海", "重庆", "香港", "澳门"];
let mapdata = [];
let highlighted = []; // 要高亮的省份
const getMapHighlightData = async () => {
  let {data} = await mapDataApi(localStorage.getItem('token'), true)
  highlighted = data
  console.log(highlighted)
}
const getMapData = async () => {
  let {data} = await mapDataApi(localStorage.getItem('token'), false)
  console.log(data)
}
const renderMap = (map, data) => {
  const mapData = data.map(item => {
    return {
      name: item.name,
      itemStyle: highlighted.includes(item.name) ? {areaColor: '#FDDCAB'} : {}
    };
  });
  optionMap.title.subtext = map;
  optionMap.series = [
    {
      name: map,
      type: 'map',
      map: map, // 使用 map 而不是 mapType
      roam: false,
      nameMap: {
        'china': '中国'
      },
      label: {
        show: true, // 确保标签显示
        position: 'inside', // 保证标签位于区域内部
        color: '#0F1F40',
        fontSize: 13
      },
      itemStyle: {
        areaColor: '#fff',
        borderColor: 'dodgerblue'
      },
      emphasis: {
        label: {
          show: true,
          // position: 'inside',
          color: '#0F1F40',
          fontSize: 13
        },
        itemStyle: {
          areaColor: '#FEDDAC'
        }
      },
      select: {
        itemStyle: {
          areaColor: '#FDDCAB'
        }
      },
      data: mapData
    }
  ];
  //渲染地图
  mapChart.value.setOption(optionMap);
}
const loadChina = async () => {
  let {data} = await axios.get('/map/china.json')
  let d = [];
  for (let i = 0; i < data.features.length; i++) {
    d.push({
      name: data.features[i].properties.name
    })
  }
  mapdata = d;
  //注册地图
  echarts.registerMap('china', data);

  //绘制地图
  renderMap('china', d);
}
//初始化绘制全国地图配置
let optionMap = {
  backgroundColor: '#0F1F40',
  title: {
    text: '',
    subtext: '三级下钻',
    left: 'center',
    textStyle: {
      color: '#fff',
      fontSize: 16,
      fontWeight: 'normal',
      fontFamily: "Microsoft YaHei"
    },
    subtextStyle: {
      color: '#ccc',
      fontSize: 13,
      fontWeight: 'normal',
      fontFamily: "Microsoft YaHei"
    }
  },
  tooltip: {
    trigger: 'item',
    formatter: '{b}'
  },
  toolbox: {
    show: true,
    orient: 'vertical',
    left: 'right',
    top: 'center',
    feature: {
      myCustomButton: {
        show: true,
        title: '返回上一级',
        icon: 'path://M512 0C229.230933 0 0 229.230933 0 512s229.230933 512 512 512 512-229.230933 512-512S794.769067 0 512 0zM354.304 510.432l178.944 178.944a42.666667 42.666667 0 1 1-60.501333 60.586667L245.717333 542.72a42.666667 42.666667 0 0 1 0-60.586667l226.773334-226.773333a42.666667 42.666667 0 0 1 60.501333 60.586666L354.304 510.432z',
        onclick: async () => {
          if (parentArea === 'china') {
            renderMap('china', mapdata);
            parentArea = '';
          } else if (parentArea === '') {
          } else {
            let {data} = await axios.get('/map/province/' + provinces[parentArea] + '.json')
            echarts.registerMap(parentArea, data);
            let d = [];
            for (let i = 0; i < data.features.length; i++) {
              d.push({
                name: data.features[i].properties.name
              })
            }
            renderMap(parentArea, d)
            parentArea = 'china'
          }
        }
      },
      dataView: {readOnly: false},
      restore: {},
      saveAsImage: {}
    },
    iconStyle: {
      color: '#fff'
    }
  },
  animationDuration: 1000,
  animationEasing: 'cubicOut',
  animationDurationUpdate: 1000
}
let parentArea = '';

onMounted(async () => {
  await getMapHighlightData()
  eventBus.on('defaultNode', (val) => {
    eventBus.emit('node-clicked', val)
  })
  chart.value = echarts.init(chartRef.value)
  temp.value = echarts.init(tempRef.value)
  mapChart.value = echarts.init(mapRef.value)
  // 注册点击时间
  mapChart.value.on('click', async (params) => {
    if (params.name in provinces) {
      parentArea = params.seriesName

      //如果点击的是34个省、市、自治区，绘制选中地区的二级地图
      let {data} = await axios.get('/map/province/' + provinces[params.name] + '.json')
      echarts.registerMap(params.name, data);
      let d = [];
      for (let i = 0; i < data.features.length; i++) {
        d.push({
          name: data.features[i].properties.name
        })
      }
      renderMap(params.name, d);
    } else if (params.seriesName in provinces) {
      parentArea = params.seriesName
      //如果是【直辖市/特别行政区】只有二级下钻
      if (special.indexOf(params.seriesName) >= 0) {
        // 可以做事情
        renderMap('china', mapdata);
      } else {
        parentArea = params.seriesName
        //显示县级地图
        let {data} = await axios.get('/map/city/' + cityMap[params.name] + '.json')
        echarts.registerMap(params.name, data);
        let d = [];
        for (let i = 0; i < data.features.length; i++) {
          d.push({
            name: data.features[i].properties.name
          })
        }
        renderMap(params.name, d);
      }
    } else {
      //到底了可以做事了
      await getMapData()

      // renderMap('china', mapdata);
    }
  })
  await loadChina()

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