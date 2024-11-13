import request from './request.js'
import axios from 'axios'
// 获取单跳账号信息
const token = localStorage.getItem('token')


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
export const setStrategyApi = (id, strategy_id) => {
    return request({
        url: '/group/strategy/' + id,
        method: 'put',
        data: {strategy_id}
    })
}

export const getGroupMemberApi = (id, page, pageSize) => {
    return request({
        url: '/group/air/' + id,
        method: 'get',
        params: {
            page,
            pageSize
        }
    })
}

export const groupControlApi = (id, data) => {
    return axios.post('http://106.14.160.207/api/dby/serial/control-air-group', {
        group_id: id,
        wind_speed: data.wind_speed,
        power_state: data.power_state,
        operation_mode: data.operation_mode,
        set_temperature: parseInt(data.set_temperature)
    })
}