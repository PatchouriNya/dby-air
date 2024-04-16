import {ref} from 'vue'
import {defineStore} from 'pinia'

export const useLoginstateStore = defineStore('loginstate', () => {
    const id = ref()
    return {
        id
    }
})