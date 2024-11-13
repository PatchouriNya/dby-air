import {ref} from 'vue'

export default function () {
    const setExpireVisible = ref(false)
    const showSetExpire = (row) => {
        setExpireVisible.value = true
    }
    return {setExpireVisible, showSetExpire}
}