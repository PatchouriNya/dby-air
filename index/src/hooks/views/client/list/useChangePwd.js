import {ref} from "vue"
import {ElMessage} from "element-plus";
import {changePasswordAdmin} from '@/api/changepasswordadmin.js'

export default function () {
    const showCpdVisible = ref(false)

    const id = ref()
    const cpwForm = ref({
        password: ''
    })

    function showCpd(row) {

        id.value = row.row.account_id
        showCpdVisible.value = !showCpdVisible.value
    }

    function cancelCpd() {
        showCpdVisible.value = !showCpdVisible.value
    }

    async function sureChange() {

        let res = await changePasswordAdmin(cpwForm.value, id.value)
        if (res.code === 201) {
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            showCpdVisible.value = !showCpdVisible.value
        }
    }

    return {showCpdVisible, cpwForm, showCpd, sureChange, cancelCpd}
}