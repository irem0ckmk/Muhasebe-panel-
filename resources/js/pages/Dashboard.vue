<template>
    <div>
        <h1 class="text-2xl font-bold text-toprak-900 mb-6">Dashboard</h1>

        <div v-if="error" class="bg-terra-50 border border-terra-200 rounded-lg px-4 py-3 text-sm text-terra-700 mb-6">
            {{ error }}
        </div>

        <!-- Özet Kartlar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-sage-50 border border-sage-200 rounded-xl p-5">
                <p class="text-sm text-sage-600 mb-1">Toplam Gelir</p>
                <p class="text-2xl font-bold text-sage-700">{{ fmt(totals.gelir) }}</p>
            </div>
            <div class="bg-terra-50 border border-terra-200 rounded-xl p-5">
                <p class="text-sm text-terra-600 mb-1">Toplam Gider</p>
                <p class="text-2xl font-bold text-terra-700">{{ fmt(totals.gider) }}</p>
            </div>
            <div class="rounded-xl p-5 border"
                :class="bakiye >= 0 ? 'bg-toprak-100 border-toprak-200' : 'bg-terra-50 border-terra-200'">
                <p class="text-sm mb-1"
                    :class="bakiye >= 0 ? 'text-toprak-600' : 'text-terra-600'">Bakiye</p>
                <p class="text-2xl font-bold"
                    :class="bakiye >= 0 ? 'text-toprak-800' : 'text-terra-700'">
                    {{ fmt(bakiye) }}
                </p>
            </div>
        </div>

        <!-- Grafik + Kategori -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl border border-toprak-100 p-5">
                <p class="font-medium text-toprak-800 mb-4">Aylık Gelir / Gider (Son 6 Ay)</p>
                <div v-if="chartLoading" class="h-48 flex items-center justify-center text-toprak-400 text-sm">
                    Yükleniyor...
                </div>
                <Bar v-else :data="chartData" :options="chartOptions" class="max-h-52" />
            </div>

            <div class="bg-white rounded-xl border border-toprak-100 p-5">
                <p class="font-medium text-toprak-800 mb-4">Bu Ay Kategoriler</p>
                <div v-if="chartLoading" class="text-toprak-400 text-sm">Yükleniyor...</div>
                <ul v-else class="space-y-2">
                    <li v-for="cat in byCategory" :key="cat.category + cat.type"
                        class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full inline-block"
                                :class="cat.type === 'gelir' ? 'bg-sage-600' : 'bg-terra-600'"></span>
                            <span class="text-toprak-700 truncate max-w-[110px]">{{ cat.category }}</span>
                        </span>
                        <span class="font-medium text-toprak-800 text-xs">{{ fmt(cat.total) }}</span>
                    </li>
                    <li v-if="byCategory.length === 0" class="text-toprak-400 text-sm">Veri yok</li>
                </ul>
            </div>
        </div>

        <!-- Son İşlemler -->
        <div class="bg-white rounded-xl border border-toprak-100 overflow-x-auto">
            <div class="px-5 py-3 border-b border-toprak-100 font-medium text-toprak-800">Son İşlemler</div>
            <div v-if="txLoading" class="p-6 text-center text-toprak-400">Yükleniyor...</div>
            <div v-else-if="recent.length === 0" class="p-6 text-center text-toprak-400">Henüz işlem yok.</div>
            <table v-else class="w-full text-sm min-w-[500px]">
                <tbody>
                    <tr v-for="t in recent" :key="t.id" class="border-b border-toprak-50 last:border-0 hover:bg-toprak-50">
                        <td class="px-5 py-3 text-toprak-400 whitespace-nowrap">{{ fmtDate(t.date) }}</td>
                        <td class="px-5 py-3 text-toprak-700">{{ t.category }}</td>
                        <td class="px-5 py-3 text-toprak-400">{{ t.description }}</td>
                        <td class="px-5 py-3 text-right font-semibold whitespace-nowrap"
                            :class="t.type === 'gelir' ? 'text-sage-600' : 'text-terra-600'">
                            {{ t.type === 'gelir' ? '+' : '-' }}{{ fmt(t.amount) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    CategoryScale, LinearScale, BarElement,
    Title, Tooltip, Legend
} from 'chart.js'
import dashApi from '../api/dashboard'
import txApi from '../api/transactions'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const chartLoading = ref(true)
const txLoading    = ref(true)
const monthly      = ref([])
const byCategory   = ref([])
const totals       = ref({ gelir: 0, gider: 0 })
const recent       = ref([])
const error        = ref('')

const bakiye = computed(() => parseFloat(totals.value.gelir || 0) - parseFloat(totals.value.gider || 0))

onMounted(async () => {
    try {
        const [summaryRes, txRes] = await Promise.all([
            dashApi.summary(),
            txApi.getAll(),
        ])
        monthly.value    = summaryRes.data.monthly || []
        byCategory.value = summaryRes.data.by_category || []
        totals.value     = summaryRes.data.totals || { gelir: 0, gider: 0 }
        recent.value     = (txRes.data || []).slice(0, 8)
    } catch {
        error.value = 'Veriler yüklenirken bir hata oluştu.'
    } finally {
        chartLoading.value = false
        txLoading.value    = false
    }
})

const chartData = computed(() => ({
    labels: monthly.value.map(m => {
        const [y, mo] = m.month.split('-')
        return new Date(y, mo - 1).toLocaleDateString('tr-TR', { month: 'short', year: '2-digit' })
    }),
    datasets: [
        {
            label: 'Gelir',
            data: monthly.value.map(m => parseFloat(m.gelir || 0)),
            backgroundColor: '#4a7a56cc',
            borderRadius: 4,
        },
        {
            label: 'Gider',
            data: monthly.value.map(m => parseFloat(m.gider || 0)),
            backgroundColor: '#b85c3acc',
            borderRadius: 4,
        },
    ],
}))

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { position: 'top' } },
    scales: {
        x: { grid: { display: false } },
        y: {
            ticks: {
                callback: v => new Intl.NumberFormat('tr-TR', { notation: 'compact' }).format(v),
            },
        },
    },
}

function fmt(val) {
    return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(val || 0)
}
function fmtDate(d) {
    return new Date(d).toLocaleDateString('tr-TR')
}
</script>
