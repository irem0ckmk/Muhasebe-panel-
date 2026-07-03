import http from './http'

export default {
    getAll:  (params) => http.get('/invoices', { params }),
    getOne:  (id)     => http.get(`/invoices/${id}`),
    create:  (data)   => http.post('/invoices', data),
    update:  (id, data) => http.put(`/invoices/${id}`, data),
    destroy: (id)     => http.delete(`/invoices/${id}`),
}
