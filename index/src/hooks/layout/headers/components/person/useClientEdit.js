import {reactive, ref} from 'vue'
import {clientEditApi} from '@/api/client.js'
import {ElMessage} from 'element-plus'
import {useAccountStore} from '@/store/account.js'
import {storeToRefs} from 'pinia'


export default function () {
    const clientEditVisible = ref()
    const accountId = localStorage.getItem('token')
    const id = ref()
    const accountStore = useAccountStore()
    const {mainClient} = storeToRefs(accountStore)

    let clientEditForm = reactive({
        clientname: '',
        province: '',
        city: '',
        district: '',
        info: ''
    })

    const showClientEdit = () => {
        clientEditForm.clientname = mainClient.value.clientname
        clientEditForm.province = mainClient.value.province
        clientEditForm.city = mainClient.value.city
        clientEditForm.district = mainClient.value.district
        clientEditForm.info = mainClient.value.info
        clientEditForm.pid = mainClient.value.pid
        clientEditVisible.value = !clientEditVisible.value
    }

    const cancelClientEdit = () => {
        clientEditVisible.value = !clientEditVisible.value
    }

    const sureClientEdit = async () => {
        const res = await clientEditApi(mainClient.value.id, clientEditForm)
        if (res.code === 200) {
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            await accountStore.getMainClient()
            clientEditVisible.value = !clientEditVisible.value
        }
    }

    return {clientEditVisible, showClientEdit, sureClientEdit, cancelClientEdit, clientEditForm}

}