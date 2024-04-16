import {defineStore} from 'pinia'
import {onMounted, ref} from 'vue'
import {getChildrenByAccount} from '@/api/getChildrenByAccount.js'
import {useAccountStore} from '@/store/account.js'

export const useClientWithoutHeadStore = defineStore('clientWithoutHead', () => {

    const tableData = ref()
    const flag = ref(false)

    async function initTable(id, keyword, pageSize, currentPage) {
        const res = await getChildrenByAccount(id, keyword, pageSize, currentPage)
        if (res.code === 200 && res.data.data) {
            tableData.value = res.data.data
            if (Array.isArray(tableData.value) && tableData.value.length > 0)
                flag.value = true
            else
                flag.value = false
        }
    }


    // onMounted(() => {
    //     initTable(accountId, keyword.value, pageSize.value, currentPage.value)
    // })

    return {tableData, initTable, flag}
})