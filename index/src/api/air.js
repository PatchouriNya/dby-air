import request from './request.js'
import axios from 'axios'

export const airDetailApi = (id) => {
    return request({
        url: '/air/' + id
    })
}

export const getAirTrueDataApi = (id) => {
    return axios.get('http://47.103.60.199:1110/api/dby/air-latest/' + id, {})
}