import {onMounted, ref, watch} from 'vue'
import {addStrategyApi, deleteStrategyApi, editStrategyApi, getStrategyListApi} from '@/api/strategy.js'
import {ElMessage} from 'element-plus'
import {deleteGroupApi} from '@/api/group.js'

const tableData = ref()
const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)
const name = ref('')
// 下拉框选项1-15
const options = ref([{label: '1分钟', value: 1}, {label: '5分钟', value: 5}, {
    label: '10分钟',
    value: 10
}, {label: '15分钟', value: 15}, {label: '20分钟', value: 20}, {label: '25分钟', value: 25}, {
    label: '30分钟',
    value: 30
}])
const getStrategyList = async () => {
    // 获取分组列表
    const res = await getStrategyListApi(false, pageSize.value, currentPage.value, name.value)
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
    start_time: '',
    end_time: '',
    interval_time: 5
})
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
    return {
        tableData,
        currentPage,
        total,
        pageSize,
        name,
        options,
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
        controlForm.value = {
            name: '',
            info: '',
            power_state: '关机',
            operation_mode: '',
            wind_mode: '',
            wind_speed: '',
            set_temperature: '',
            start_time: '',
            end_time: '',
            interval_time: 5
        }
    }
    const sureAdd = async () => {
        const res = await addStrategyApi(controlForm.value)
        if (res.code === 201) {
            formVisible.value = false
            controlForm.value = {
                name: '',
                info: '',
                power_state: '关机',
                operation_mode: '',
                wind_mode: '',
                wind_speed: '',
                set_temperature: '',
                start_time: '',
                end_time: '',
                interval_time: 5
            }
            await getStrategyList()
            ElMessage.success(res.msg)
        }
    }
    watch(formVisible, val => {
        if (val === false) {
            controlForm.value = {
                name: '',
                info: '',
                power_state: '关机',
                operation_mode: '',
                wind_mode: '',
                wind_speed: '',
                set_temperature: '',
                start_time: '',
                end_time: '',
                interval_time: 5
            }
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
            start_time: row.start_time,
            end_time: row.end_time,
            interval_time: row.interval_time
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


