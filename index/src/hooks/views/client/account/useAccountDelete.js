import {ref} from "vue"
import {accountDelete} from "@/api/accountDeleteApi.js";
import {ElMessage} from "element-plus";
import {useClientStore} from "@/store/client.js";

export default function () {
    const accountDeleteVisible = ref(false)
    const id = ref()
    const accountDelName = ref()
    const clientStore = useClientStore()
    const showAccountDelete = (row) => {
        console.log(row)
        accountDeleteVisible.value = !accountDeleteVisible.value
        id.value = row.row.account_id
        accountDelName.value = row.row.account.account
    }
    const sureAccountDelete = async () => {
        const res = await accountDelete(id.value)
        if (res.code === 200) {
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