import http from './http'

export default {
    summary: () => http.get('/dashboard/summary'),
}
