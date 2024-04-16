import request from './request.js'


export const changePassword = (data, id) => {
    return request({
        url: '/cgp/' + id + '?_method=PUT',
        method: 'PUT',
        data: data
    })
}