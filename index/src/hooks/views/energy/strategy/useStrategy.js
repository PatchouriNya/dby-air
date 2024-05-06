import {ref, watch} from 'vue'
import {addStrategyApi} from '@/api/strategy.js'
import {ElMessage} from 'element-plus'

const controlForm = ref({
    name: '',
    info: '',
    power_state: '',
    operation_mode: '',
    wind_mode: '',
    wind_speed: '',
    temperature: ''
})
export const useStrategyAdd = () => {
    const addVisible = ref(false)

    const sureAdd = async () => {
        const res = await addStrategyApi(controlForm.value)
        if (res.code === 201) {
            addVisible.value = false
            controlForm.value = {
                name: '',
                info: '',
                power_state: '',
                operation_mode: '',
                wind_mode: '',
                wind_speed: '',
                temperature: ''
            }
            ElMessage.success('创建策略成功')
        }
    }
    watch(addVisible, val => {
        if (val === false) {
            controlForm.value = {
                name: '',
                info: '',
                power_state: '',
                operation_mode: '',
                wind_mode: '',
                wind_speed: '',
                temperature: ''
            }
        }
    })
    return {addVisible, controlForm, sureAdd}
}