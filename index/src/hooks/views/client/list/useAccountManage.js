import {ref} from 'vue'
import {getAccountListByClient} from '@/api/getAccountListByClient.js'
export default function () {
    const client_id = ref()
    const manageVisible = ref(false)
    const tableManageData = ref()

    // 发请求
    async function accountList(id) {
        const res = await getAccountListByClient(id)
        tableManageData.value = res.data
    }

    function showManage(row) {
        manageVisible.value = !manageVisible.value
        client_id.value = row.row.id
        accountList(client_id.value)
    }

    function closeManage() {
        manageVisible.value = !manageVisible.value
    }

    return {showManage, manageVisible, tableManageData, client_id}
}