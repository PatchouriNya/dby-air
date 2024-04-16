import {useRouter} from "vue-router"
import {ref} from 'vue'
import {loginCreateApi} from '@/api/log.js'

export default function () {
    const router = useRouter()

    const logoutLog = ref({
        id: '',
        type: 1,
        content: '登出系统'
    })


    function logout() {
        // 登出
        logoutLog.value.id = localStorage.getItem('token')
        localStorage.removeItem('token')
        loginCreateApi(logoutLog.value)
        router.push({path: '/login'})
    }

    return {logout}
}