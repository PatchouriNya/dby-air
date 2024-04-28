import {ref} from 'vue'
import eventBus from '@/listen/event-bus.js'
import {getGroupListByClientApi} from '@/api/group.js'

export default function () {
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
        client_id.value = val.id
        title.value = val.clientname
        await getGroupListByClient(client_id.value)
    })
    return {tableData, currentPage, total, pageSize, title, name, handleSizeChange, handleCurrentChange, reset, search}
}