import request from '@/api/request.js'

export const getQuestionListApi = (menu_id) => {
    return request({
        url: '/faq',
        params: {
            menu_id
        }
    })
}

export const editQuestionApi = (id, form) => {
    return request({
        url: '/faq/' + id,
        method: 'PUT',
        data: {
            question: form.question,
            answer: form.answer,
            sort: form.sort
        }
    })
}
export const addQuestionApi = (form) => {
    return request({
        url: '/faq',
        method: 'POST',
        data: {
            menu_id: form.menu_id,
            question: form.question,
            answer: form.answer,
            sort: form.sort
        }
    })
}
export const deleteQuestionApi = (id) => {
    return request({
        url: '/faq/' + id,
        method: 'DELETE'
    })
}
