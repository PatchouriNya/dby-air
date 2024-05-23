import {reactive, ref} from "vue"
import {changePassword} from '@/api/changepassword.js'
import {ElMessage} from "element-plus";
import {clientDetailApi} from '@/api/client.js'
import {logCreateApi} from '@/api/log.js'


export default function () {
    const showCpdVisible = ref(false)
    const id = localStorage.getItem("token")
    const cpwForm = ref({
        ori_password: '',
        password: '',
        password_confirmation: ''
    })
    // 写日志
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })
    const clientname = ref('')

    function showCpd() {
        showCpdVisible.value = !showCpdVisible.value
        cpwForm.value.ori_password = ''
        cpwForm.value.password = ''
        cpwForm.value.password_confirmation = ''
    }

    async function sureChange() {

        let res = await changePassword(cpwForm.value, id)
        if (res.code === 201) {
            // 拿当前的客户名称,用来写日志
            const client = await clientDetailApi(logForm.id)
            clientname.value = client.data.clientname
            logForm.content = '修改了' + clientname.value + '下的密码'
            await logCreateApi(logForm)
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            showCpdVisible.value = !showCpdVisible.value
        }
    }

    return {showCpdVisible, cpwForm, showCpd, sureChange}
}