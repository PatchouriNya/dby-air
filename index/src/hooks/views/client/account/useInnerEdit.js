import {reactive, ref} from 'vue'
import {accountEdit} from '@/api/accountEdit.js'
import {ElMessage} from 'element-plus'
import {useClientStore} from "@/store/client.js";
import {logCreateApi} from '@/api/log.js'

export default function () {
    const innerEditVisible = ref(false)
    const id = ref()
    const innerEditForm = ref({
        nickname: '',
        mobile: '',
        email: ''
    })
    const clientStore = useClientStore()
    // 操作日志
    const account = ref()
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })

    function showInnerEdit(row) {
        innerEditVisible.value = !innerEditVisible.value
        id.value = row.row.account_id
        account.value = row.row.account.account
        innerEditForm.value.nickname = row.row.account.nickname
        innerEditForm.value.mobile = row.row.account.mobile
        innerEditForm.value.email = row.row.account.email
    }

    function cancelInnerEdit() {
        innerEditVisible.value = !innerEditVisible.value
    }


    // 发请求
    async function sureInnerEdit() {
        let res = await accountEdit(innerEditForm.value, id.value)
        if (res.code === 201) {
            logForm.content = '编辑了' + account.value + '的账号信息'
            await logCreateApi(logForm)
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            innerEditVisible.value = !innerEditVisible.value
            await clientStore.accountList(clientStore.clientId)
        }
    }


    return {showInnerEdit, innerEditVisible, innerEditForm, sureInnerEdit, cancelInnerEdit}
}