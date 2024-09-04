import request from './request.js'
import axios from 'axios'

export const airDetailApi = (id) => {
    return request({
        url: '/air/' + id
    })
}

export const getAirTrueDataApi = (id) => {
    return axios.post('http://106.14.160.207/api/dby/serial', {client_id: id})
}