// 当前客户信息
import {defineStore} from "pinia"
import {ref} from 'vue'
import {getAccountListByClient} from '@/api/getAccountListByClient.js'

export const useClientStore = defineStore('client', () => {
    const clientId = ref()
    const tableManageData = ref()


    /*    function accountList(id) {
            getAccountListByClient(id).then(res => {
                tableManageData.value = res.data
            })
        }*/
    async function accountList(id) {
        const res = await getAccountListByClient(id)
        tableManageData.value = res.data
    }

    return {clientId, tableManageData, accountList}
})
