import {ref} from "vue"
import {changePassword} from '@/api/changepassword'
import {ElMessage} from "element-plus";


export default function (){
    const showCpdVisible = ref(false)
    const cpwForm = ref({
        ori_password: '',
        password: '',
        password_confirmation: ''
    })

    function showCpd() {
        showCpdVisible.value = !showCpdVisible.value
    }

    async function sureChange() {

        let res = await changePassword(cpwForm.value)
        if (res.code === 201)
        {
            ElMessage({
                message: res.msg,
                type: 'success',
            })
            showCpdVisible.value = !showCpdVisible.value
        }
    }
    return {showCpdVisible,cpwForm,showCpd,sureChange}
}