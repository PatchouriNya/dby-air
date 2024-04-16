import {ref} from 'vue'
import {accountEdit} from '@/api/accountEdit.js'
import {ElMessage} from 'element-plus'
import {useClientStore} from "@/store/client.js";

export default function () {
    const innerEditVisible = ref(false)
    const id = ref()
    const innerEditForm = ref({
        nickname: '',
        mobile: '',
        email: ''
    })
    const clientStore = useClientStore()

    function showInnerEdit(row) {
        innerEditVisible.value = !innerEditVisible.value
        id.value = row.row.account_id
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