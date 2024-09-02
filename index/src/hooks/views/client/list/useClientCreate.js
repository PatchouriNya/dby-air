import {onMounted, reactive, ref} from 'vue'
import {getMainClientApi, clientCreateApi, clientDetailApi} from '@/api/client.js'
import {ElMessage} from 'element-plus'
import {useClientWithoutHeadStore} from '@/store/clientWithoutHead.js'
import {logCreateApi} from '@/api/log.js'


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
        info: '',
        total_air: null
    })
    // 写日志
    const logForm = reactive({
        id: localStorage.getItem('token'),
        type: 1,
        content: ''
    })
    const clientname = ref('')

    const resetForm = () => {
        clientCreateForm.clientname = ''
        clientCreateForm.province = ''
        clientCreateForm.city = ''
        clientCreateForm.district = ''
        clientCreateForm.type = 0
        clientCreateForm.info = ''
        clientCreateForm.total_air = null
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
            // 如果有pid,拿pid的名字来写日志没有的话就是在自己下面加
            if (clientCreateForm.pid) {
                const client = await clientDetailApi(clientCreateForm.pid)
                clientname.value = client.data.clientname
            } else {
                const client = await clientDetailApi(logForm.id)
                clientname.value = client.data.clientname
            }

            logForm.content = '在' + clientname.value + '下创建了客户' + clientCreateForm.clientname
            await logCreateApi(logForm)
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