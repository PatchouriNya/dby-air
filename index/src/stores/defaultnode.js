import {defineStore} from 'pinia'
import {ref} from 'vue'

export const useDefaultnodeStore = defineStore('defaultnode', () => {
    const currentKey = ref(1)
    const expandedKeys = ref([1])

    const changeDefault = (clicked, expanded) => {
        currentKey.value = clicked
        expandedKeys.value = expanded
    }

    return {
        currentKey, expandedKeys, changeDefault
    }
})