import request from '@/api/request.js'

export const getStrategyListApi = (pageSize, currentPage, name) => {
    return request({
        url: '/strategy',
        params: {
            pageSize,
            page: currentPage,
            name
        }
    })
}

export const addStrategyApi = (data) => {
    return request({
        url: '/strategy',
        method: 'post',
        data
    })
}

export const editStrategyApi = (id, data) => {
    return request({
        url: '/strategy/' + id,
        method: 'put',
        data
    })
}

export const deleteStrategyApi = (id) => {
    return request({
        url: '/strategy/' + id,
        method: 'delete'
    })
}
