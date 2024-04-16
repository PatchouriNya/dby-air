import request from './request.js'

export const getAccountListByClient = (id) => {
    return request({
        url: '/accountlist/' + id
    })
}