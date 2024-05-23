import {reactive, ref} from 'vue'
import {clientDeleteApi} from '@/api/client.js'
import {ElMessage} from 'element-plus'
import {useClientWithoutHeadStore} from '@/store/clientWithoutHead.js'
import {logCreateApi} from '@/api/log.js'

export default function () {
    const clientDeleteVisible = ref(false)
    const id = ref()
    const clientDelName = ref()
    const accountId = localStorage.getItem('token')
    const clientWithoutHeadStore = useClientWithoutHeadStore()
    // 写日志
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })

    function showClientDelete(row) {
        id.value = row.id
        clientDelName.value = row.clientname
        clientDeleteVisible.value = !clientDeleteVisible.value
    }

    async function sureClientDelete() {
        // 发送删除请求
        const res = await clientDeleteApi(id.value)
        if (res.code === 204) {
            logForm.content = '删除了客户' + clientDelName.value
            await logCreateApi(logForm)
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            await clientWithoutHeadStore.initTable(accountId)
            clientDeleteVisible.value = !clientDeleteVisible.value

        }
    }


    return {clientDeleteVisible, showClientDelete, sureClientDelete, clientDelName}
}