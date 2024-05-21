import {ref, watch} from 'vue'
import eventBus from '@/listen/event-bus.js'
import {
    addGroupApi,
    deleteGroupApi,
    editGroupApi,
    getGroupListByClientApi,
    groupControlApi,
    setStrategyApi
} from '@/api/group.js'
import {ElMessage} from 'element-plus'
import {getStrategyListApi} from '@/api/strategy.js'

const tableData = ref()
const client_id = ref()
const currentPage = ref(1)
const pageSize = ref(10)
const total = ref(0)
const name = ref('')
const title = ref('')
const getGroupListByClient = async () => {
    // 获取分组列表
    const res = await getGroupListByClientApi(client_id.value, pageSize.value, currentPage.value, name.value)
    tableData.value = res.data.data
    total.value = res.data.total
}

export function useGroupList() {
    const handleSizeChange = async (val) => {
        await getGroupListByClient()
    }
    const handleCurrentChange = async (val) => {
        await getGroupListByClient()
    }

    const reset = async () => {
        name.value = ''
        await getGroupListByClient()
    }
    const search = async () => {
        await getGroupListByClient()
    }
    eventBus.on('node-clicked', async (val) => {
        if (val.type === 1) {
            client_id.value = val.id
            title.value = val.clientname
            await getGroupListByClient(client_id.value)
        }
    })
    return {
        tableData,
        currentPage,
        total,
        pageSize,
        title,
        name,
        client_id,
        handleSizeChange,
        handleCurrentChange,
        reset,
        search
    }
}

export function useGroupAdd() {
    const addVisible = ref(false)
    const addForm = ref({
        name: '',
        info: '',
        client_id: ''
    })
    const sureAddGroup = async () => {
        addForm.value.client_id = client_id.value
        let res = await addGroupApi(addForm.value)
        if (res.code === 201) {
            addVisible.value = false
            await getGroupListByClient()
            ElMessage.success(res.msg)
        }
    }
    watch(addVisible, async (val) => {
        if (val === false) {
            addForm.value.name = ''
            addForm.value.info = ''
        }
    })
    return {addForm, addVisible, sureAddGroup}
}

export function useGroupEdit() {
    // 需要修改的组的id
    const id = ref()
    const editVisible = ref(false)
    const editForm = ref({
        name: '',
        info: ''
    })
    const showEditGroup = async (row) => {
        editVisible.value = true
        id.value = row.id
        editForm.value.name = row.name
        editForm.value.info = row.info
    }
    const sureEditGroup = async () => {
        editForm.value.client_id = client_id.value
        let res = await editGroupApi(id.value, editForm.value)
        if (res.code === 201) {
            editVisible.value = false
            await getGroupListByClient()
            ElMessage.success(res.msg)
        }
    }
    return {editForm, editVisible, showEditGroup, sureEditGroup}
}

export function useGroupDelete() {
    // 需要删除的组的id
    const id = ref()
    const deleteName = ref('')
    const deleteVisible = ref(false)
    const showDeleteGroup = async (row) => {
        deleteVisible.value = true
        id.value = row.id
        deleteName.value = row.name
    }
    const sureDeleteGroup = async () => {
        let res = await deleteGroupApi(id.value)
        if (res.code === 204) {
            deleteVisible.value = false
            await getGroupListByClient()
            ElMessage.success(res.msg)
        }
    }
    return {deleteVisible, deleteName, showDeleteGroup, sureDeleteGroup}
}

export function useSetStrategy() {
    // 策略设置
    const setStrategyVisible = ref(false)
    const group_id = ref()
    const strategyList = ref([])
    const strategy_id = ref([])
    const showSetStrategy = async (row) => {
        setStrategyVisible.value = true
        group_id.value = row.id
        strategy_id.value = row.strategy_id ? row.strategy_id : []
        const res = await getStrategyListApi(true, 1, 1, 1, client_id.value)
        if (res.code === 200)
            strategyList.value = res.data
    }
    const sureSetStrategy = async () => {
        const res = await setStrategyApi(group_id.value, strategy_id.value)
        if (res.code === 201) {
            setStrategyVisible.value = false
            await getGroupListByClient()
            ElMessage.success(res.msg)
        }
    }
    const stopSetStrategy = async () => {
        strategy_id.value = []
        const res = await setStrategyApi(group_id.value, strategy_id.value)
        if (res.code === 201) {
            setStrategyVisible.value = false
            await getGroupListByClient()
            ElMessage.success('策略停用成功')
        }
    }
    return {setStrategyVisible, strategy_id, strategyList, showSetStrategy, sureSetStrategy, stopSetStrategy}
}

export function userGroupControl() {
    // 组控制
    const groupControlVisible = ref(false)
    const group_id = ref()
    const controlForm = ref({})
    const showGroupControl = async (row) => {
        controlForm.value = {}
        group_id.value = row.id
        groupControlVisible.value = true
    }
    const sureGroupControl = async () => {
        const res = await groupControlApi(group_id.value, controlForm.value)
        if (res.code === 201) {
            groupControlVisible.value = false
            ElMessage.success(res.msg)
        }
    }
    return {groupControlVisible, controlForm, showGroupControl, sureGroupControl}
}


