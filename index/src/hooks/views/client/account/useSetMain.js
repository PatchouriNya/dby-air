import {reactive, ref} from 'vue'
import {account, accountSetMainApi} from '@/api/account'
import {ElMessage} from 'element-plus'
import {useClientStore} from '@/store/client.js'
import {logCreateApi} from '@/api/log.js'

export default function () {
    const accountSetMainVisible = ref(false)
    const accountSetMainName = ref('')
    const id = ref()
    const clientStore = useClientStore()
    // 是否为主管
    const mainFlag = ref()

    // 操作日志
    const account = ref()
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })

    // 初始化
    getAccountData()

    async function getAccountData() {
        let res = await account()
        mainFlag.value = res.data.main
    }


    // 显示设置主账号弹窗
    const showAccountSetMain = (row) => {
        accountSetMainVisible.value = !accountSetMainVisible.value
        accountSetMainName.value = row.account.account
        id.value = row.account.id
        account.value = row.account.account
    }

    // 确定设置主账号
    const sureAccountSetMain = async () => {
        const res = await accountSetMainApi(id.value)
        if (res.code === 201) {
            logForm.content = '设置' + account.value + '为主管'
            await logCreateApi(logForm)
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            accountSetMainVisible.value = !accountSetMainVisible.value
            await getAccountData()
            await clientStore.accountList(clientStore.clientId)
        }
    }
    return {accountSetMainVisible, accountSetMainName, showAccountSetMain, sureAccountSetMain, mainFlag}
}