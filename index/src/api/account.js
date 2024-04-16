import request from './request.js'
// 获取单跳账号信息
const token = localStorage.getItem("token")
export const account = () => {
    return request({
        url: '/account/' + token
    })
}