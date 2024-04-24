import request from './request.js'

export const login = (data) => {
    return request({
        url: '/login',
        method: 'POST',
        data
    })
}

export const loginCheckApi = (id = localStorage.getItem('token')) => {
    return request({
        url: '/login/check/' + id
    })
}

export const loginOutApi = (id = localStorage.getItem('token')) => {
    return request({
        url: '/logout/' + id
    })
}
