import {onMounted, ref} from 'vue'
import {account} from '@/api/account.js'

export default function () {
    // 控制下主管
    let isSystem = localStorage.getItem('token_')

    const mainFlag = ref()

    async function getAccountData() {
        let res = await account()
        mainFlag.value = res.data.main
    }

    onMounted(async () => {
        await getAccountData()
    })

    return {isSystem, mainFlag}
}