import request from './request.js'

const token = localStorage.getItem("token")
// 登录日志
export const logCreateApi = (data) => {
    return request({
        url: '/log',
        method: 'POST',
        data
    })
}

export const logListApi = (type, currentPage, pageSize, filters) => {
    return request({
        url: '/log',
        params: {
            id: token,
            type,
            page: currentPage,
            pageSize,
            client: filters['client'],
            account: filters['account'],
            content: filters['content'],
            ip: filters['ip'],
            start_date: filters['start_date'],
            end_date: filters['end_date']
        }
    })
}