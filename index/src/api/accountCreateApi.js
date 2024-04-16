import request from './request.js'

export const accountCreateApi = (data) => {
    return request({
        url: '/account',
        method: 'POST',
        data
    })
}