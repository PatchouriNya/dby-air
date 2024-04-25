import {reactive, ref} from 'vue'
import {accountCreateApi} from '@/api/accountCreateApi.js'
import eventBus from '@/listen/event-bus.js'
import {ElMessage} from 'element-plus'
import {useClientStore} from '@/store/client.js'
import {clientDetailApi} from '@/api/client.js'
import {logCreateApi} from '@/api/log.js'

export default function () {
    const accountCreateVisible = ref(false)
    const accountCreateForm = ref({
        account: '',
        password: '',
        email: '',
        mobile: '',
        nickname: '',
        client_id: ''
    })
    const clientStore = useClientStore()

    // 操作日志
    const client = ref()
    const logForm = reactive({
        id: localStorage.getItem("token"),
        type: 1,
        content: ''
    })

    const accountCreate = async () => {
        const res = await accountCreateApi(accountCreateForm.value);
        if (res.code === 201) {
            logForm.content = '在' + client.value + '下新增了账号' + accountCreateForm.value.account
            await logCreateApi(logForm)
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            await clientStore.accountList(clientStore.clientId)
            showAccountCreate()
        }

    }

    const showAccountCreate = async () => {
        const res = await clientDetailApi(accountCreateForm.value.client_id)
        client.value = res.data.clientname
        accountCreateVisible.value = !accountCreateVisible.value
    }

    const sureAccountCreate = () => {
        accountCreate()
    }
    eventBus.on('node-clicked', (val) => {
        accountCreateForm.value.client_id = val.id
    })
    return {accountCreateForm, accountCreateVisible, showAccountCreate, sureAccountCreate}
}