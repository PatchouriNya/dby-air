import {reactive, ref} from "vue"
import {ElMessage} from "element-plus";
import {changePasswordAdmin} from '@/api/changepasswordadmin.js'
import {logCreateApi} from '@/api/log.js'

export default function () {
    const showCpdVisible = ref(false)

    const id = ref()
    //该标识符用于标识是否要修改当前登录账号的密码
    const flag = ref(true)
    const cpwForm = ref({
        password: ''
    })
    // 操作日志
    const account = ref()
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })

    function showCpd(row) {
        id.value = row.row.account_id
        account.value = row.row.account.account
        if (id.value == localStorage.getItem("token"))
            flag.value = false
        showCpdVisible.value = !showCpdVisible.value
    }

    function cancelCpd() {
        showCpdVisible.value = !showCpdVisible.value
    }

    async function sureChange() {

        let res = await changePasswordAdmin(cpwForm.value, id.value)
        if (res.code === 201) {
            logForm.content = '修改了' + account.value + '的密码'
            await logCreateApi(logForm)
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            showCpdVisible.value = !showCpdVisible.value
        }
    }

    return {showCpdVisible, cpwForm, showCpd, sureChange, cancelCpd, flag}
}