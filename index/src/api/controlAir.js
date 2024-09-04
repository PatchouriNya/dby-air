import request from './request.js'
import axios from 'axios'

export const controlAir = (id, data, show_id) => {
    return axios.post('http://106.14.160.207/api/dby/serial/control-air', {
        client_id: id,
        air_id: show_id,
        wind_speed: data.wind_speed,
        power_state: data.power_state,
        operation_mode: data.operation_mode,
        set_temperature: parseInt(data.set_temperature)
    })
}