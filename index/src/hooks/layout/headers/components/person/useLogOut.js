import {useRouter} from "vue-router"
import {ref} from 'vue'
import {logCreateApi} from '@/api/log.js'
import {loginOutApi} from '@/api/login.js'

export default function () {
    const router = useRouter()

    const logoutLog = ref({
        id: '',
        type: 1,
        content: '退出系统'
    })


    async function logout() {
        // 登出
        logoutLog.value.id = localStorage.getItem('token')
        await loginOutApi(logoutLog.value.id)
        localStorage.clear()
        await logCreateApi(logoutLog.value)
        await router.push({path: '/login'})
    }

    return {logout}
}