import http from './http'

export default {
    login:  (data) => http.post('/login', data),
    logout: ()     => http.post('/logout'),
    me:     ()     => http.get('/me'),
}
