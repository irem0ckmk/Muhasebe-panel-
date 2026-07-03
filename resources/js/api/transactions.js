import http from './http'

export default {
    getAll:     (params)    => http.get('/transactions', { params }),
    getOne:     (id)        => http.get(`/transactions/${id}`),
    create:     (data)      => http.post('/transactions', data),
    update:     (id, data)  => http.put(`/transactions/${id}`, data),
    destroy:    (id)        => http.delete(`/transactions/${id}`),
}
