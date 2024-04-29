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

export const addGroupApi = (data) => {
    return request({
        url: '/group',
        method: 'post',
        data
    })
}

export const editGroupApi = (id, data) => {
    return request({
        url: '/group/' + id,
        method: 'put',
        data
    })
}

export const deleteGroupApi = (id) => {
    return request({
        url: '/group/' + id,
        method: 'delete'
    })
}

export const getUnGroupedAirByClientApi = (id, group_id) => {
    return request({
        url: '/air/ungrouped/' + id,
        params: {
            group_id
        }
    })
}

export const getGroupedAirByClientApi = (id) => {
    return request({
        url: '/air/grouped/' + id
    })
}

export const addAirToGroup = (id, data) => {
    return request({
        url: '/group/add/' + id,
        method: 'post',
        data: {air_id: data}
    })
}

export const removeAirFromGroup = (id, data) => {
    return request({
        url: '/group/remove/' + id,
        method: 'delete',
        data: {air_id: data}
    })
}
