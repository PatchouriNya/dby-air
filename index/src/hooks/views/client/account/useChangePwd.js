import {reactive, ref} from "vue"
import {ElMessage} from "element-plus";
import {changePasswordAdmin} from '@/api/changepasswordadmin.js'
import {logCreateApi} from '@/api/log.js'
import {clientDetailApi} from '@/api/client.js'

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
    const client = ref()
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })

    async function showCpd(row) {
        // 拿当前账号对应的客户名称,用来写日志
        const res = await clientDetailApi(row.row.client_id)
        client.value = res.data.clientname
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
            logForm.content = '修改了' + client.value + '下的' + account.value + '的密码'
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