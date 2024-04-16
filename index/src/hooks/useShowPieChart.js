import {ref, onMounted, watch} from 'vue'
import * as echarts from 'echarts/core'
import {TooltipComponent, LegendComponent} from 'echarts/components'
import {PieChart} from 'echarts/charts'
import {LabelLayout} from 'echarts/features'
import {CanvasRenderer} from 'echarts/renderers'
import eventBus from '@/listen/event-bus.js'

export default function () {
    const selectData = ref()
    const airBootQuantity = ref(0)
    const airQuantity = ref(0)

    eventBus.on('node-clicked', (val) => {
        selectData.value = val
        airBootQuantity.value = val.with_overview ? val.with_overview.air_boot_quantity : 0
        if (airBootQuantity.value === null) airBootQuantity.value = 0
        airQuantity.value = val.with_overview ? val.with_overview.air_quantity : 0
        if (airQuantity.value === null) airQuantity.value = 0
        console.log(airBootQuantity.value)
    })

    echarts.use([TooltipComponent, LegendComponent, PieChart, CanvasRenderer, LabelLayout])

    const chartRef = ref(null)
    const chart = ref(null)

    onMounted(() => {
        eventBus.on('defaultNode', (val) => {
            eventBus.emit('node-clicked', val)
        })
        chart.value = echarts.init(chartRef.value)
        const option = {
            tooltip: {
                trigger: 'item'
            },
            legend: {
                top: '5%',
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
                            fontSize: 40,
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

        chart.value.setOption(option)
    })

    watch([airBootQuantity, airQuantity], ([newValue1, newValue2]) => {
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

        chart.value.setOption(option)
    })

    return {selectData}
}