import request from './request.js'
import axios from 'axios'

export const controlAir = (id, data, show_id) => {
    return axios.post('http://47.103.60.199:1110/api/dby/air-control/' + id, {
        air_id: show_id,
        wind_speed: data.wind_speed,
        power_state: data.power_state,
        operation_mode: data.operation_mode,
        set_temperature: parseInt(data.set_temperature)
    })
}