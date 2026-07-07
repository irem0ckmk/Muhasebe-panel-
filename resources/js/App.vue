<template>
    <div v-if="route.meta.guest" class="min-h-screen">
        <RouterView />
    </div>
    <div v-else class="min-h-screen bg-toprak-50">
        <div v-if="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/40 z-20 md:hidden" />
        <Sidebar :open="sidebarOpen" @close="sidebarOpen = false" />
        <div class="md:pl-14 flex flex-col min-h-screen transition-all duration-200">
            <header class="md:hidden flex items-center gap-3 px-4 py-3 bg-white border-b border-toprak-100 sticky top-0 z-40">
                <button
                    @click="sidebarOpen = !sidebarOpen"
                    @mouseenter="sidebarOpen = true"
                    class="text-toprak-700 p-1 rounded hover:bg-toprak-50"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="font-bold text-toprak-900">{{ pageTitle }}</span>
            </header>
            <main class="flex-1 p-4 md:p-8 overflow-auto">
                <RouterView />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from './components/Sidebar.vue'

const route = useRoute()
const sidebarOpen = ref(false)

const pageTitles = {
    '/':                  'Dashboard',
    '/transactions':      'İşlemler',
    '/transactions/new':  'Yeni İşlem',
    '/invoices':          'Faturalar',
    '/invoices/new':      'Yeni Fatura',
}

const pageTitle = computed(() => {
    const path = route.path
    if (pageTitles[path]) return pageTitles[path]
    if (path.startsWith('/transactions/') && path.endsWith('/edit')) return 'İşlem Düzenle'
    if (path.startsWith('/invoices/') && path.endsWith('/edit')) return 'Fatura Düzenle'
    return 'Muhasebe Panel'
})
</script>
