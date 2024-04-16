import {onMounted, ref} from 'vue'
import {getOneAirClient} from '@/api/getAirDetail'

export default function (){
    const clientId = ref()
    // 配置表格
    const airData = ref([])
    const tableData = ref([])
// 配置表格分页查询
    const currentPage = ref(1)
    const total = ref(0)
    const pageSize = ref(10)
    const noDataMeg = ref('暂无数据')
    const options = ref([
        {
            lable: '开机',
            value: '开机'
        }, {
            lable: '关机',
            value: '关机'
        }
    ])
    const filters = ref({
        designation: '',
        online_state: '',
        power_state: '',
        operation_mode: '',
        room_temperature: ''
        // 添加更多筛选条件...
    })

// 配置表格隐藏列
    const columnVisible = ref(false)
    const showColumn = ref({
        designation: true,
        air_brand: true,
        online_state: true,
        electrify_state: false,
        power_state: true,
        operation_mode: true,
        wind_speed: true,
        set_temperature: true,
        room_temperature: true,
        voltage: false,
        electric_current: false,
        power: false,
        electric_quantity: false
    })

    async function initAirList(id, filters, pageSize, currentPage){
        if (clientId.value){
            let res = await getOneAirClient (id, filters, pageSize, currentPage)
            airData.value = res.data
            tableData.value = airData.value.data
            total.value = airData.value.total
        }

    }

    function search() {
        initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
    }

    function reset() {
        filters.value = {
            designation: '',
            online_state: '',
            power_state: '',
            operation_mode: '',
            room_temperature: ''
        }
        initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
        showColumn.value = {
            designation: true,
            air_brand: true,
            online_state: true,
            electrify_state: false,
            power_state: true,
            operation_mode: true,
            wind_speed: true,
            set_temperature: true,
            room_temperature: true,
            voltage: false,
            electric_current: false,
            power: false,
            electric_quantity: false
        }
    }

    function showSelectColumn() {
        columnVisible.value = !columnVisible.value
    }

    const handleCurrentChange = () => {
        initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
    }

    const handleSizeChange = () => {
        initAirList(clientId.value, filters.value, pageSize.value, currentPage.value)
    }

    // onMounted(() => {
    //     initclientList()
    // })

    return {airData,tableData,currentPage,total,pageSize,noDataMeg,options,filters,columnVisible,showColumn,initAirList,search,reset,showSelectColumn,handleCurrentChange,handleSizeChange}
}