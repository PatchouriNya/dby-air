import {onMounted, ref, watch} from 'vue'
import {addStrategyApi, deleteStrategyApi, editStrategyApi, getStrategyListApi} from '@/api/strategy.js'
import {ElMessage} from 'element-plus'
import {deleteGroupApi} from '@/api/group.js'
import eventBus from '@/listen/event-bus.js'

const tableData = ref()
const client_id = ref()
const title = ref('')
const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)
const name = ref('')
// 下拉框选项1-15
const options1 = ref([])
const options2 = ref([{label: '星期一', value: 1}, {label: '星期二', value: 2}, {
    label: '星期三',
    value: 3
}, {label: '星期四', value: 4}, {label: '星期五', value: 5}, {label: '星期六', value: 6}, {label: '星期日', value: 0}])
for (let i = 1; i <= 15; i++) {
    options1.value.push({label: `${i}分钟`, value: i});
}

const getStrategyList = async () => {
    // 获取分组列表
    const res = await getStrategyListApi(false, pageSize.value, currentPage.value, name.value, client_id.value)
    tableData.value = res.data.data
    total.value = res.data.total
}
const controlForm = ref({
    name: '',
    info: '',
    power_state: '关机',
    operation_mode: '',
    wind_mode: '',
    wind_speed: '',
    set_temperature: '',
    start_date: '',
    end_date: '',
    start_time: '',
    end_time: '',
    week_days: [],
    interval_time: 5
})
// 判断是新增还是编辑
const opFlag = ref(1)
const formVisible = ref(false)

export const useStrategyList = () => {
    const handleSizeChange = async (val) => {
        await getStrategyList()
    }
    const handleCurrentChange = async (val) => {
        await getStrategyList()
    }

    const reset = async () => {
        name.value = ''
        await getStrategyList()
    }
    const search = async () => {
        await getStrategyList()
    }
    eventBus.on('node-clicked', async (val) => {
        if (val.type === 1) {
            client_id.value = val.id
            title.value = val.clientname
            await getStrategyList()
        }
    })
    return {
        tableData,
        client_id,
        currentPage,
        total,
        title,
        pageSize,
        name,
        options1,
        options2,
        handleSizeChange,
        handleCurrentChange,
        getStrategyList,
        reset,
        search
    }
}
export const useStrategyAdd = () => {

    const showAdd = () => {
        formVisible.value = true
        opFlag.value = 1
        controlForm.value = {power_state: '关机'}
    }
    const sureAdd = async () => {
        controlForm.value.client_id = client_id.value
        const res = await addStrategyApi(controlForm.value)
        if (res.code === 201) {
            formVisible.value = false
            controlForm.value = {}
            await getStrategyList()
            ElMessage.success(res.msg)
        }
    }
    watch(formVisible, val => {
        if (val === false) {
            controlForm.value = {}
        }
    })
    return {formVisible, controlForm, opFlag, showAdd, sureAdd}
}
export const useStrategyEdit = () => {
    const id = ref()

    const showEdit = (row) => {
        formVisible.value = true
        opFlag.value = 2
        id.value = row.id
        controlForm.value = {
            name: row.name,
            info: row.info,
            power_state: row.power_state,
            operation_mode: row.operation_mode,
            wind_mode: row.wind_mode,
            wind_speed: row.wind_speed,
            set_temperature: row.set_temperature,
            delta: row.delta,
            start_date: row.start_date,
            end_date: row.end_date,
            start_time: row.start_time,
            end_time: row.end_time,
            interval_time: row.interval_time,
            week_days: row.week_days
        }
    }
    const sureEdit = async () => {
        const res = await editStrategyApi(id.value, controlForm.value)
        if (res.code === 201) {
            formVisible.value = false
            await getStrategyList()
            ElMessage.success(res.msg)
        }
    }
    return {controlForm, showEdit, sureEdit}
}

export function useStrategyDelete() {
    // 需要删除的组的id
    const id = ref()
    const deleteName = ref('')
    const deleteVisible = ref(false)
    const showDelete = async (row) => {
        deleteVisible.value = true
        id.value = row.id
        deleteName.value = row.name
    }
    const sureDelete = async () => {
        let res = await deleteStrategyApi(id.value)
        if (res.code === 204) {
            deleteVisible.value = false
            await getStrategyList()
            ElMessage.success(res.msg)
        }
    }
    return {deleteVisible, deleteName, showDelete, sureDelete}
}

watch(() => controlForm.value, (val) => {
    if (val.start_date && val.end_date && val.start_date > val.end_date) {
        controlForm.value.start_date = ''
        controlForm.value.end_date = ''
        ElMessage.error('开始日期不能大于结束日期')
    }
}, {deep: true})

