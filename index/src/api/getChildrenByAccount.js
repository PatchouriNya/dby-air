import request from './request.js'
// 获取某一客户的子客户，分页
export const getChildrenByAccount = (clientId, keyword = '', pageSize = 15, currentPage = 1) => {
    let url = '/client/children/' + clientId + '?'

    // 添加 pageSize 和 currentPage 到 URL 中
    url += 'size=' + pageSize + '&'
    url += 'page=' + currentPage + '&'
    url += 'keyword=' + keyword

    return request({
        url: url
    })
}