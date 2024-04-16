import {onMounted, ref} from 'vue'
import {getChildrenByAccount} from '@/api/getChildrenByAccount.js'
import {useAccountStore} from '@/store/account.js'
import {useClientWithoutHeadStore} from '@/store/clientWithoutHead.js'

export default function () {
    const accountStore = useAccountStore()
    const clientWithoutHeadStore = useClientWithoutHeadStore()
    const accountId = accountStore.accountData ? accountStore.accountData.id : localStorage.getItem('token')
    const pageSize = ref(15)
    const currentPage = ref(1)
    const keyword = ref('')
    const total = ref(0)


    onMounted(() => {
        clientWithoutHeadStore.initTable(accountId, keyword.value, pageSize.value, currentPage.value)
    })


}
