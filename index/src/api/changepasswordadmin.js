import request from './request.js'


export const changePasswordAdmin = (data, id) => {
    return request({
        url: '/cgp/admin/' + id + '?_method=PUT',
        method: 'PUT',
        data: data
    })
}