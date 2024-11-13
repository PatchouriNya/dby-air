import {reactive, ref, watch} from 'vue'
import eventBus from '@/listen/event-bus.js'
import {
    addGroupApi,
    deleteGroupApi,
    editGroupApi,
    getGroupListByClientApi, getGroupMemberApi,
    groupControlApi,
    setStrategyApi
} from '@/api/group.js'
import {ElLoading, ElMessage} from 'element-plus'
import {getStrategyListApi} from '@/api/strategy.js'
import {clientDetailApi} from '@/api/client.js'
import {logCreateApi} from '@/api/log.js'

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
// 写日志
const logForm = reactive({
    id: localStorage.getItem('token'),
    type: 1,
    content: ''
})
const clientname = ref('')
const groupname = ref('')

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
            // 拿当前的客户名称,用来写日志
            const client = await clientDetailApi(client_id.value)
            clientname.value = client.data.clientname
            logForm.content = '为' + clientname.value + '新增了组' + addForm.value.name
            await logCreateApi(logForm)
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
            // 拿当前的客户名称,用来写日志
            const client = await clientDetailApi(client_id.value)
            clientname.value = client.data.clientname
            logForm.content = '编辑了' + clientname.value + '下的组' + editForm.value.name
            await logCreateApi(logForm)
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
        groupname.value = row.name
        deleteVisible.value = true
        id.value = row.id
        deleteName.value = row.name
    }
    const sureDeleteGroup = async () => {
        let res = await deleteGroupApi(id.value)
        if (res.code === 204) {
            // 拿当前的客户名称,用来写日志
            const client = await clientDetailApi(client_id.value)
            clientname.value = client.data.clientname
            logForm.content = '删除了' + clientname.value + '下的组' + groupname.value
            await logCreateApi(logForm)
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
        groupname.value = row.name
        strategy_id.value = row.strategy_id ? row.strategy_id : []
        const res = await getStrategyListApi(true, 1, 1, 1, client_id.value)
        if (res.code === 200)
            strategyList.value = res.data
    }
    const sureSetStrategy = async () => {
        const res = await setStrategyApi(group_id.value, strategy_id.value)
        if (res.code === 201) {
            // 拿当前的客户名称,用来写日志
            const client = await clientDetailApi(client_id.value)
            clientname.value = client.data.clientname
            logForm.content = '为' + clientname.value + '下的组' + groupname.value + '设置了策略'
            await logCreateApi(logForm)
            setStrategyVisible.value = false
            await getGroupListByClient()
            ElMessage.success(res.msg)
        }
    }
    const stopSetStrategy = async () => {
        strategy_id.value = []
        const res = await setStrategyApi(group_id.value, strategy_id.value)
        if (res.code === 201) {
            // 拿当前的客户名称,用来写日志
            const client = await clientDetailApi(client_id.value)
            clientname.value = client.data.clientname
            logForm.content = '停用了' + clientname.value + '下的组' + groupname.value + '的策略'
            await logCreateApi(logForm)
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
        groupname.value = row.name
        controlForm.value = {}
        group_id.value = row.id
        groupControlVisible.value = true
    }
    const sureGroupControl = async () => {
        const loading = ElLoading.service({
            lock: true,
            text: '正在发送指令...',
            background: 'rgba(0, 0, 0, 0.7)'
        })
        const res = await groupControlApi(group_id.value, controlForm.value)
        if (res.data.code === 200) {
            // 拿当前的客户名称,用来写日志
            const client = await clientDetailApi(client_id.value)
            clientname.value = client.data.clientname
            logForm.type = 2
            logForm.content = '操控了' + clientname.value + '下的组' + groupname.value + ' ' + controlForm.value.power_state + ' ' + controlForm.value.set_temperature + ' ' + controlForm.value.operation_mode + ' ' + controlForm.value.wind_speed + ' ' + controlForm.value.wind_mode
            await logCreateApi(logForm)
            groupControlVisible.value = false
            ElMessage.success(res.data.msg)
            loading.close()
        } else {
            ElMessage.error(res.data.msg)
            loading.close()
        }
    }
    return {groupControlVisible, controlForm, showGroupControl, sureGroupControl}
}

export function useGroupMember() {
    const group_id = ref()
    const groupMemberVisible = ref(false)
    const groupMemberData = ref([])
    const memberCurrentPage = ref(1)
    const memberPageSize = ref(10)
    const memberTotal = ref(0)
    const showGroupMember = async (row) => {
        group_id.value = row.id
        groupMemberVisible.value = true
        memberCurrentPage.value = 1
        const res = await getGroupMemberApi(group_id.value, memberCurrentPage.value, memberPageSize.value)
        if (res.code === 200) {
            groupMemberData.value = res.data.data
            memberTotal.value = res.data.total
        }
    }
    const handleMemberSizeChange = async (val) => {
        const res = await getGroupMemberApi(group_id.value, memberCurrentPage.value, val)
        groupMemberData.value = res.data.data
        memberTotal.value = res.data.total
    }

    const handleMemberCurrentChange = async (val) => {
        const res = await getGroupMemberApi(group_id.value, val, memberPageSize.value)
        groupMemberData.value = res.data.data
        memberTotal.value = res.data.total
    }


    return {
        groupMemberVisible,
        groupMemberData,
        memberCurrentPage,
        memberPageSize,
        memberTotal,
        showGroupMember,
        handleMemberSizeChange,
        handleMemberCurrentChange
    }
}


