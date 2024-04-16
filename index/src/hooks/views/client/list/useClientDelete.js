import {ref} from 'vue'
import {clientDeleteApi} from '@/api/client.js'
import {ElMessage} from 'element-plus'
import {useClientWithoutHeadStore} from '@/store/clientWithoutHead.js'

export default function () {
    const clientDeleteVisible = ref(false)
    const id = ref()
    const clientDelName = ref()
    const accountId = localStorage.getItem('token')
    const clientWithoutHeadStore = useClientWithoutHeadStore()


    function showClientDelete(row) {
        id.value = row.id
        clientDelName.value = row.clientname
        clientDeleteVisible.value = !clientDeleteVisible.value
    }

    async function sureClientDelete() {
        // 发送删除请求
        const res = await clientDeleteApi(id.value)
        if (res.code === 200) {
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