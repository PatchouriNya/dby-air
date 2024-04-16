import {useRouter} from "vue-router"

export default function () {
    const router = useRouter()

    function logout() {
        localStorage.removeItem('token')
        router.push({path: '/login'})
    }

    return {logout}
}