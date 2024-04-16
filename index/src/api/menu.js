import request from './request.js'

export const menuList = () => {
    return request({
        url: '/menu'
    })
}