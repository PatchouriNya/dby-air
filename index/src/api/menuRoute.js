import request from './request.js'

export const menuRoute = (path) => {
    return request({
        url: '/menu/route?route=' + path
    })
}