import {reactive, ref} from 'vue'
import {clientDetailApi, clientEditApi} from '@/api/client.js'
import {ElMessage} from 'element-plus'
import {useAccountStore} from '@/store/account.js'
import {storeToRefs} from 'pinia'
import {logCreateApi} from '@/api/log.js'


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
    // 写日志
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })
    const clientname = ref('')


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
        if (res.code === 201) {
            // 拿当前的客户名称,用来写日志
            const client = await clientDetailApi(logForm.id)
            clientname.value = client.data.clientname
            logForm.content = '修改了' + clientname.value + '下的信息'
            await logCreateApi(logForm)
            
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