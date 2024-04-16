import request from './request.js'

const token = localStorage.getItem("token")
// 登录日志
export const loginCreateApi = (data) => {
    return request({
        url: '/log',
        method: 'POST',
        data
    })
}

export const getLoginListApi = () => {
    return request({
        url: '/log'
    })
}