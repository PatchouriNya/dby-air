import request from './request.js'


export const accountEdit = (data, id) => {
    return request({
        url: '/account/' + id + '?_method=PUT',
        method: 'PUT',
        data: data
    })
}