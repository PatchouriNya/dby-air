import request from './request.js'

export const login = (data) => {
    return request({
        url: '/login',
        method: 'POST',
        data
    })
}