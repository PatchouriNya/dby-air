import request from './request.js'

export const menuList = (faq) => {
    return request({
        url: '/menu/' + faq
    })
}