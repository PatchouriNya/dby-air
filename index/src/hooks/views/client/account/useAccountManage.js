import {getAccountListByClient} from '@/api/getAccountListByClient.js'
import {useClientStore} from "@/store/client.js";

export default function () {
    const clientStore = useClientStore()

    // 发请求
    async function accountList(id) {
        const res = await getAccountListByClient(id)
        clientStore.tableManageData = res.data
    }


    return {accountList}
}