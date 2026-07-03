<template>
    <aside
        :class="[
            'fixed inset-y-0 left-0 z-30 bg-toprak-900 text-white flex flex-col overflow-hidden transition-all duration-200',
            open ? 'w-56 translate-x-0' : 'w-56 -translate-x-full',
            desktopExpanded ? 'md:w-56 md:translate-x-0' : 'md:w-14 md:translate-x-0',
        ]"
        @mouseenter="desktopExpanded = true"
        @mouseleave="desktopExpanded = false"
    >
        <!-- Top bar -->
        <div class="h-14 flex items-center flex-shrink-0 border-b border-toprak-800">
            <div class="w-14 flex items-center justify-center flex-shrink-0 text-toprak-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </div>
            <span class="font-bold text-sm text-white whitespace-nowrap tracking-wide uppercase">Muhasebe Panel</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-4 space-y-0.5">
            <RouterLink
                v-for="item in menu"
                :key="item.to"
                :to="item.to"
                @click="$emit('close')"
                class="flex items-center h-11 transition-colors"
                :class="isActive(item.to)
                    ? 'bg-toprak-600'
                    : 'hover:bg-toprak-800'"
            >
                <div class="w-14 flex items-center justify-center flex-shrink-0">
                    <span v-if="isActive(item.to)" class="w-1.5 h-1.5 rounded-full bg-white block"></span>
                    <span v-else class="w-1.5 h-1.5 rounded-full bg-toprak-600 block"></span>
                </div>
                <span class="whitespace-nowrap font-bold text-sm uppercase tracking-wider"
                      :class="isActive(item.to) ? 'text-white' : 'text-toprak-200'">
                    {{ item.label }}
                </span>
            </RouterLink>
        </nav>

        <!-- User / Logout -->
        <div class="border-t border-toprak-800 flex-shrink-0">
            <div class="flex items-center h-9">
                <div class="w-14 flex-shrink-0"></div>
                <span class="text-xs text-toprak-400 whitespace-nowrap">{{ user?.name }}</span>
            </div>
            <button
                @click="handleLogout"
                class="w-full flex items-center h-11 hover:bg-toprak-800 transition-colors"
            >
                <div class="w-14 flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-toprak-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                </div>
                <span class="whitespace-nowrap font-bold text-sm uppercase tracking-wider text-toprak-300">Çıkış Yap</span>
            </button>
        </div>
    </aside>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '../store/auth'
import authApi from '../api/auth'

defineProps({ open: Boolean })
defineEmits(['close'])

const desktopExpanded = ref(false)
const route  = useRoute()
const router = useRouter()
const { user, clearAuth } = useAuth()

const menu = [
    { to: '/',            label: 'Dashboard' },
    { to: '/transactions', label: 'İşlemler' },
    { to: '/invoices',    label: 'Faturalar' },
]

function isActive(to) {
    if (to === '/') return route.path === '/'
    return route.path.startsWith(to)
}

async function handleLogout() {
    try { await authApi.logout() } catch {}
    clearAuth()
    router.push('/login')
}
</script>
