import {ref} from 'vue'
import {accountCreateApi} from '@/api/accountCreateApi.js'
import eventBus from '@/listen/event-bus.js'
import {ElMessage} from 'element-plus'
import {useClientStore} from '@/store/client.js'

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
    const accountCreate = async () => {
        const res = await accountCreateApi(accountCreateForm.value);
        if (res.code === 201) {
            ElMessage({
                message: res.msg,
                type: 'success'
            })
            await clientStore.accountList(clientStore.clientId)
            showAccountCreate()
        }

    }

    const showAccountCreate = () => {
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