import {account} from '@/api/account.js'
import {onMounted, ref} from 'vue'
import {useAccountStore} from '@/store/account.js'
// 获取当前登录账号信息
export default function () {
    const accountStore = useAccountStore()
    const accountData = ref()

    async function currentAcount() {
        const res = await account()
        accountData.value = res.data

    }

    onMounted(() => {
        accountStore.accountDetail()
    })

    return {accountData}
}