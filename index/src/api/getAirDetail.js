import request from './request.js'

const currentClient = localStorage.getItem("currentClient")
export const getOneAirClient = (clientId, filters, pageSize, currentPage) => {
    let url = '/airs/' + clientId + '?'

    // 构建筛选条件
    Object.keys(filters).forEach((key) => {
        if (filters[key]) {
            url += key + '=' + filters[key] + '&'
        }
    })

    // 添加 pageSize 和 currentPage 到 URL 中
    url += 'size=' + pageSize + '&'
    url += 'page=' + currentPage

    return request({
        url: url
    })
}