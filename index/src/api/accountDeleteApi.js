import request from './request.js'


export const accountDelete = (id) => {
    return request({
        url: '/account/' + id,
        method: 'DELETE'
    })
}