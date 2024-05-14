import request from './request.js'

export const airDetailApi = (id) => {
    return request({
        url: '/air/' + id
    })
}

export const getAirTrueDataApi = (id) => {
    return request({
        url: '/air/refresh/' + id
    })
}