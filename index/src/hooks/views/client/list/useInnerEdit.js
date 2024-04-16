import {ref} from 'vue'
import {accountEdit} from '@/api/accountEdit.js'
import {ElMessage} from 'element-plus'
import useAccountManage from '@/hooks/views/client/list/useAccountManage.js'

export default function () {
    const innerEditVisible = ref(false)
    const id = ref()
    const innerEditForm = ref({
        nickname: ''
    })

    function showInnerEdit(row) {
        innerEditVisible.value = !innerEditVisible.value
        id.value = row.row.account_id
        innerEditForm.value.nickname = row.row.account.nickname
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
        }
    }


    return {showInnerEdit, innerEditVisible, innerEditForm, sureInnerEdit, cancelInnerEdit}
}