import {onMounted, ref, watch} from 'vue'
import {addStrategyApi, deleteStrategyApi, editStrategyApi, getStrategyListApi} from '@/api/strategy.js'
import {ElMessage} from 'element-plus'
import {deleteGroupApi} from '@/api/group.js'

const tableData = ref()
const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)
const name = ref('')
const getStrategyList = async () => {
    // 获取分组列表
    const res = await getStrategyListApi(pageSize.value, currentPage.value, name.value)
    tableData.value = res.data.data
    total.value = res.data.total
}
getStrategyList()
const controlForm = ref({
    name: '',
    info: '',
    power_state: '',
    operation_mode: '',
    wind_mode: '',
    wind_speed: '',
    set_temperature: ''
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
        handleSizeChange,
        handleCurrentChange,
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
            power_state: '',
            operation_mode: '',
            wind_mode: '',
            wind_speed: '',
            set_temperature: ''
        }
    }
    const sureAdd = async () => {
        const res = await addStrategyApi(controlForm.value)
        if (res.code === 201) {
            formVisible.value = false
            controlForm.value = {
                name: '',
                info: '',
                power_state: '',
                operation_mode: '',
                wind_mode: '',
                wind_speed: '',
                set_temperature: ''
            }
            ElMessage.success(res.msg)
        }
    }
    watch(formVisible, val => {
        if (val === false) {
            controlForm.value = {
                name: '',
                info: '',
                power_state: '',
                operation_mode: '',
                wind_mode: '',
                wind_speed: '',
                set_temperature: ''
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
            set_temperature: row.set_temperature
        }
    }
    const sureEdit = async () => {
        const res = await editStrategyApi(id.value, controlForm.value)
        if (res.code === 201) {
            formVisible.value = false
            ElMessage.success(res.msg)
            await getStrategyList()
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


