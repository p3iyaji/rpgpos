import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../Pages/HomeRoute.vue')
    },
    {
        path: '/test',
        name: 'test',
        component: () => import("../Pages/TestRoute.vue")

    },

];

const router = createRouter({
    history: createWebHistory(),
    routes
});


export default router;