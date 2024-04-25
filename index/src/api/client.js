import request from './request.js'

const token = localStorage.getItem("token")
export const clientList = () => {
    return request({
        url: '/client/list/' + token
    })
}

export const clientSelectTree = (id) => {
    return request({
        url: '/client/select/tree/' + token + '/' + id
    })
}
export const getParentApi = (id) => {
    return request({
        url: '/client/parent/' + id
    })
}

export const clientCreateApi = (data) => {
    return request({
        url: '/client',
        method: 'POST',
        data
    })
}

export const clientEditApi = (id, data) => {
    return request({
        url: '/client/' + id,
        method: 'PUT',
        data
    })
}

export const getMainClientApi = () => {
    return request({
        url: '/client/main/' + token
    })
}

export const getSystemClientApi = () => {
    return request({
        url: '/system/client'
    })
}

export const clientDeleteApi = (id) => {
    return request({
        url: '/client/' + id,
        method: 'DELETE'
    })
}

export const clientDetailApi = (id) => {
    return request({
        url: '/client/' + id
    })
}