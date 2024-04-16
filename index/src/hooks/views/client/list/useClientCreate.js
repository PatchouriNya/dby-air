import {onMounted, reactive, ref} from 'vue'
import {getMainClientApi, clientCreateApi} from '@/api/client.js'
import {ElMessage} from 'element-plus'
import {useClientWithoutHeadStore} from '@/store/clientWithoutHead.js'


export default function () {
    const clientCreateVisible = ref()
    const clientWithoutHeadStore = useClientWithoutHeadStore()
    const accountId = localStorage.getItem('token')

    let clientCreateForm = reactive({
        clientname: '',
        province: '',
        city: '',
        district: '',
        pid: null,
        type: null,
        info: ''
    })

    const resetForm = () => {
        clientCreateForm.clientname = ''
        clientCreateForm.province = ''
        clientCreateForm.city = ''
        clientCreateForm.district = ''
        clientCreateForm.type = null
        clientCreateForm.info = ''
    }

    const showClientCreate = (row) => {
        resetForm()
        if (row) {
            clientCreateForm.pid = row.row.id
        }
        clientCreateVisible.value = !clientCreateVisible.value

    }

    const cancelClientCreate = () => {
        clientCreateVisible.value = !clientCreateVisible.value
    }

    const sureClientCreate = async () => {
        const res = await clientCreateApi(clientCreateForm)
        if (res.code === 201) {
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            await clientWithoutHeadStore.initTable(accountId)
            clientCreateVisible.value = !clientCreateVisible.value
        }
    }
    onMounted(async () => {
        const res = await getMainClientApi()
        clientCreateForm.pid = res.data.id
    })

    return {clientCreateVisible, showClientCreate, sureClientCreate, cancelClientCreate, clientCreateForm}

}