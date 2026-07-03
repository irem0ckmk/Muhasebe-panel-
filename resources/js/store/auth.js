import { ref, computed } from 'vue'

const token = ref(localStorage.getItem('token') || null)
const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))

export function useAuth() {
    const isLoggedIn = computed(() => !!token.value)

    function setAuth(data) {
        token.value = data.token
        user.value  = data.user
        localStorage.setItem('token', data.token)
        localStorage.setItem('user', JSON.stringify(data.user))
    }

    function clearAuth() {
        token.value = null
        user.value  = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
    }

    return { token, user, isLoggedIn, setAuth, clearAuth }
}
