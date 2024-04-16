import {onMounted, ref, watch} from 'vue'
import {menuRoute} from '@/api/menuRoute.js'
import {useRoute} from 'vue-router'

export default function () {
    const path = ref('主页')
    const route = useRoute()

    async function getMenuRoute() {
        const res = await menuRoute(route.path)
        if (res.data)
            path.value = res.data
        else
            path.value = '主页'
    }

    onMounted(() => {
        getMenuRoute()
    })

    watch(() => route.path, () => {
        getMenuRoute()
    })

    return {path}
}