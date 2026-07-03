import { createRouter, createWebHistory } from 'vue-router'
import Login           from './pages/Login.vue'
import Dashboard       from './pages/Dashboard.vue'
import Transactions    from './pages/Transactions.vue'
import TransactionForm from './pages/TransactionForm.vue'
import Invoices        from './pages/Invoices.vue'
import InvoiceForm     from './pages/InvoiceForm.vue'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/login',                     component: Login,           meta: { guest: true } },
        { path: '/',                           component: Dashboard,       meta: { requiresAuth: true } },
        { path: '/transactions',               component: Transactions,    meta: { requiresAuth: true } },
        { path: '/transactions/new',           component: TransactionForm, meta: { requiresAuth: true } },
        { path: '/transactions/:id/edit',      component: TransactionForm, meta: { requiresAuth: true } },
        { path: '/invoices',                   component: Invoices,        meta: { requiresAuth: true } },
        { path: '/invoices/new',               component: InvoiceForm,     meta: { requiresAuth: true } },
        { path: '/invoices/:id/edit',          component: InvoiceForm,     meta: { requiresAuth: true } },
    ],
})

router.beforeEach((to, _from, next) => {
    const token = localStorage.getItem('token')
    if (to.meta.requiresAuth && !token) return next('/login')
    if (to.meta.guest && token)         return next('/')
    next()
})

export default router
