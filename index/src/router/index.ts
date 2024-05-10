import {createRouter, createWebHistory} from 'vue-router'
import index from '@/views/index.vue'
import login from '@/views/login/login.vue'
import login2 from '@/views/login/index.vue'
import register from '@/views/register/register.vue'
import layout from '@/layout/index.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'wuhu',
            redirect: '/main'
        },
        {
            path: '/layout',
            name: 'layout-index',
            component: layout,
            children: [
                {
                    path: '/main',
                    name: 'index',
                    component: () => import('@/views/index.vue')
                },
                {
                    path: '/main/panel/workbench',
                    name: 'workbench',
                    component: () => import('@/views/panel/workbench/index.vue')
                },
                {
                    path: '/main/list/dtulist',
                    name: 'dtulist',
                    component: () => import('@/views/list/dtulist/index.vue')
                },
                {
                    path: '/main/energy/intelligentcontrol/splitcontrol',
                    name: 'splitcontrol',
                    component: () => import('@/views/energy/intelligentcontrol/splitcontrol/index.vue')
                },
                {
                    path: '/main/energy/intelligentcontrol/log',
                    name: 'air-log',
                    component: () => import('@/views/energy/intelligentcontrol/log/index.vue')

                },
                {
                    path: '/main/energy/intelligentcontrol/groupcontrol',
                    name: 'group-control',
                    component: () => import('@/views/energy/intelligentcontrol/groupcontrol/index.vue')

                },
                {
                    path: '/main/energy/group',
                    name: 'energy-group',
                    component: () => import('@/views/energy/group/index.vue')
                },
                {
                    path: '/main/energy/strategy',
                    name: 'energy-strategy',
                    component: () => import('@/views/energy/strategy/index.vue')
                },
                {
                    path: '/main/client/list',
                    name: 'client-list',
                    component: () => import('@/views/client/list/index.vue')
                },
                {
                    path: '/main/log/login',
                    name: 'log-login',
                    component: () => import('@/views/log/login/index.vue')
                },
                {
                    path: '/main/client/account',
                    name: 'client-account',
                    component: () => import('@/views/client/account/index.vue')
                },
                {
                    path: '/main/about/contact',
                    name: 'about-contact',
                    component: () => import ('@/views/about/contact/index.vue')
                },
                {
                    path: '/main/about/faq',
                    name: 'about-faq',
                    component: () => import ('@/views/about/faq/index.vue')
                }

            ]
        },
        {
            path: '/index',
            name: '系统主页1',
            component: index
        },
        {
            path: '/login',
            name: '登陆界面',
            component: login
        },
        {
            path: '/register',
            name: '用户注册',
            component: register
        },
        {
            path: '/login2',
            name: 'login2',
            component: login2
        }
    ]
})

export default router
