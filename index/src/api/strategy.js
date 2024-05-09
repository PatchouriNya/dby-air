import request from '@/api/request.js'

export const getStrategyListApi = (allData = false, pageSize, currentPage, name, client_id) => {
    return request({
        url: '/strategy',
        params: {
            all_data: allData,
            pageSize,
            page: currentPage,
            name,
            client_id
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
