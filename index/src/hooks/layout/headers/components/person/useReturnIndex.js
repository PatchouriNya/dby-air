import {useRouter} from "vue-router"

export default function () {
    const router = useRouter()

    function returnIndex() {
        router.push({path: '/main'})
    }

    return {returnIndex}
}