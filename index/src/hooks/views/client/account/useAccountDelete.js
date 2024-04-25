import {reactive, ref} from "vue"
import {accountDelete} from "@/api/accountDeleteApi.js";
import {ElMessage} from "element-plus";
import {useClientStore} from "@/store/client.js";
import {logCreateApi} from '@/api/log.js'

export default function () {
    const accountDeleteVisible = ref(false)
    const id = ref()
    const accountDelName = ref()
    const clientStore = useClientStore()

    // 操作日志
    const account = ref()
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })

    const showAccountDelete = (row) => {
        accountDeleteVisible.value = !accountDeleteVisible.value
        id.value = row.row.account_id
        account.value = row.row.account.account
        accountDelName.value = row.row.account.account
    }
    const sureAccountDelete = async () => {
        const res = await accountDelete(id.value)
        if (res.code === 200) {
            logForm.content = '删除了账号' + account.value
            await logCreateApi(logForm)
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            accountDeleteVisible.value = !accountDeleteVisible.value
            await clientStore.accountList(clientStore.clientId)
        }
    }
    return {accountDeleteVisible, showAccountDelete, sureAccountDelete, accountDelName}
}