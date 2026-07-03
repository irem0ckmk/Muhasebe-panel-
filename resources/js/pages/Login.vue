<template>
    <div class="min-h-screen bg-toprak-50 flex items-center justify-center">
        <div class="w-full max-w-xl">
            <div class="text-center mb-8">
                <h1 class="text-5xl font-bold text-toprak-900">Muhasebe</h1>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-2xl border border-toprak-300 p-10 space-y-5 shadow-sm">
                <div>
                    <label class="block text-sm font-medium text-toprak-800 mb-1">Kullanıcı ID</label>
                    <input
                        v-model="form.username"
                        type="text"
                        required
                        autocomplete="username"
                        placeholder="Şirket tarafından atanan ID"
                        class="w-full border border-toprak-200 rounded-lg px-3 py-3.5 text-toprak-900 placeholder-toprak-300 focus:outline-none focus:ring-2 focus:ring-toprak-400 focus:border-transparent"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-toprak-700 mb-1">Şifre</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full border border-toprak-200 rounded-lg px-3 py-3.5 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400 focus:border-transparent"
                    />
                </div>

                <div v-if="error" class="bg-terra-50 border border-terra-200 rounded-lg px-4 py-3 text-sm text-terra-700">
                    {{ error }}
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-toprak-600 hover:bg-toprak-700 text-white font-medium py-3.5 rounded-lg transition-colors disabled:opacity-50"
                >
                    {{ loading ? 'Giriş yapılıyor...' : 'Giriş Yap' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import authApi from '../api/auth'
import { useAuth } from '../store/auth'

const router = useRouter()
const { setAuth } = useAuth()

const form    = ref({ username: '', password: '' })
const loading = ref(false)
const error   = ref('')

async function submit() {
    loading.value = true
    error.value   = ''
    try {
        const { data } = await authApi.login(form.value)
        setAuth(data)
        router.push('/')
    } catch (err) {
        error.value = err.response?.data?.message || 'Bir hata oluştu.'
    } finally {
        loading.value = false
    }
}
</script>
