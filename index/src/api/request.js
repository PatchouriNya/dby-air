import axios from 'axios'
import {API_URL} from "../../config/config";
import {ElMessage} from "element-plus";

const service = axios.create({
    baseURL: API_URL,
    timeout: 5000
})

service.interceptors.request.use(
    (config) => {
        /* if (localStorage.getItem('token')) {
             if (diffTokenTime()) {
                 store.dispatch('app/logout')
                 return Promise.reject(new Error('token 失效了'))
             }
         }*/
        config.headers.Authorization = localStorage.getItem('token')
        return config
    },
    (error) => {
        return Promise.reject(new Error(error))
    })

service.interceptors.response.use((response) => {
    const {data} = response
    if (data.code === 200 || data.code === 201) {
        return data
    } else {
        ElMessage.error(data.msg)
        return Promise.reject(new Error(data.msg))
    }
}, error => {
    error.response && ElMessage.error(error.response.data)
    return Promise.reject(new Error(error.response.data))
})
export default service