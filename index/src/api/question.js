import request from '@/api/request.js'

export const getQuestionListApi = (menu_id) => {
    return request({
        url: '/faq',
        params: {
            menu_id
        }
    })
}