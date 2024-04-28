import request from './request.js'
// 获取单跳账号信息
const token = localStorage.getItem("token")


export const getGroupListByClientApi = (id, pageSize, currentPage, name) => {
    return request({
        url: '/group',
        params: {
            client_id: id,
            pageSize,
            page: currentPage,
            name
        }
    })
}

