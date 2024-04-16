// 当前账号信息
import {defineStore} from 'pinia'
import {onMounted, reactive, ref} from 'vue'
import {account} from '@/api/account.js'
import {getMainClientApi, getSystemClientApi} from '@/api/client.js'

export const useAccountStore = defineStore('account', () => {
    const accountData = ref()
    const mainClient = ref()
    const systemClient = ref()

    async function accountDetail() {
        const res = await account()
        accountData.value = res.data

    }

    async function getMainClient() {
        const res = await getMainClientApi()
        mainClient.value = res.data
    }

    async function getSystemClient() {
        const res = await getSystemClientApi()
        systemClient.value = res.data
    }


    onMounted(async () => {
        await accountDetail()
        await getMainClient()
        await getSystemClient()
    })

    return {accountData, mainClient, accountDetail, systemClient, getMainClient}
})