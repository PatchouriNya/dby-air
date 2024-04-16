import request from './request.js'

export const controlAir = (id, data) => {
    return request({
        url: '/air/' + id + '?_method=PUT',
        method: 'PUT',
        data: data
    })
}