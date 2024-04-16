import {ref} from "vue"
import {changePassword} from '@/api/changepassword.js'
import {ElMessage} from "element-plus";


export default function () {
    const showCpdVisible = ref(false)
    const id = localStorage.getItem("token")
    const cpwForm = ref({
        ori_password: '',
        password: '',
        password_confirmation: ''
    })

    function showCpd() {
        showCpdVisible.value = !showCpdVisible.value
        cpwForm.value.ori_password = ''
        cpwForm.value.password = ''
        cpwForm.value.password_confirmation = ''
    }

    async function sureChange() {

        let res = await changePassword(cpwForm.value, id)
        if (res.code === 201) {
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            showCpdVisible.value = !showCpdVisible.value
        }
    }

    return {showCpdVisible, cpwForm, showCpd, sureChange}
}