import request from './request.js'

export const updateAir = (id, data) => {
    return request({
        url: '/air/' + id + '?_method=PUT',
        method: 'PUT',
        data: data
    })
}