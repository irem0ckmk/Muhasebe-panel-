<template>
    <div class="max-w-lg">
        <h1 class="text-2xl font-bold text-toprak-900 mb-6">{{ isEdit ? 'Faturayı Düzenle' : 'Yeni Fatura' }}</h1>

        <form @submit.prevent="submit" class="bg-white rounded-xl border border-toprak-100 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-toprak-700 mb-1">Müşteri Adı</label>
                <input v-model="form.client_name" type="text" required
                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400"
                    :class="{ 'border-terra-400': fieldError('client_name') }" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-toprak-700 mb-1">Tutar (₺)</label>
                    <input v-model="form.amount" type="number" step="0.01" min="0.01" required
                        class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400"
                        :class="{ 'border-terra-400': fieldError('amount') }" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-toprak-700 mb-1">KDV (%)</label>
                    <select v-model="form.tax_rate"
                        class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400">
                        <option :value="0">%0</option>
                        <option :value="10">%10</option>
                        <option :value="20">%20</option>
                    </select>
                </div>
            </div>

            <div v-if="form.amount && parseFloat(form.amount) > 0" class="bg-toprak-50 rounded-lg px-4 py-3 text-sm">
                <span class="text-toprak-500">KDV Dahil Toplam: </span>
                <span class="font-bold text-toprak-800">{{ fmt(parseFloat(form.amount) * (1 + form.tax_rate / 100)) }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-toprak-700 mb-1">Düzenlenme Tarihi</label>
                    <input v-model="form.issue_date" type="date" required
                        class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-toprak-700 mb-1">Vade Tarihi</label>
                    <input v-model="form.due_date" type="date" required
                        :min="form.issue_date"
                        class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400"
                        :class="{ 'border-terra-400': dueDateError }" />
                    <p v-if="dueDateError" class="text-xs text-terra-600 mt-1">Vade tarihi düzenlenme tarihinden önce olamaz.</p>
                </div>
            </div>

            <div v-if="isEdit">
                <label class="block text-sm font-medium text-toprak-700 mb-1">Durum</label>
                <select v-model="form.status"
                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400">
                    <option value="beklemede">Beklemede</option>
                    <option value="odendi">Ödendi</option>
                    <option value="iptal">İptal</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-toprak-700 mb-1">Açıklama</label>
                <textarea v-model="form.description" rows="2"
                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400 resize-none"></textarea>
            </div>

            <div v-if="errors.length" class="bg-terra-50 border border-terra-200 rounded-lg p-3 text-sm text-terra-700 space-y-1">
                <p v-for="e in errors" :key="e">{{ e }}</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" :disabled="saving || dueDateError"
                    class="bg-toprak-600 text-white px-5 py-2 rounded-lg hover:bg-toprak-700 transition-colors disabled:opacity-50">
                    {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
                </button>
                <RouterLink to="/invoices"
                    class="px-5 py-2 rounded-lg border border-toprak-200 hover:bg-toprak-50 text-toprak-600 transition-colors">
                    İptal
                </RouterLink>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../api/invoices'

const route  = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)

const today = new Date().toISOString().slice(0, 10)
const in30  = new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10)

const form = ref({
    client_name: '',
    amount: '',
    tax_rate: 20,
    status: 'beklemede',
    issue_date: today,
    due_date: in30,
    description: '',
})

const saving = ref(false)
const errors = ref([])

const dueDateError = computed(() =>
    !!(form.value.due_date && form.value.issue_date && form.value.due_date < form.value.issue_date)
)

function fieldError(field) {
    return errors.value.some(e => e.toLowerCase().includes(field))
}

onMounted(async () => {
    if (isEdit.value) {
        try {
            const { data } = await api.getOne(route.params.id)
            form.value = {
                client_name: data.client_name,
                amount:      data.amount,
                tax_rate:    data.tax_rate,
                status:      data.status,
                issue_date:  data.issue_date.slice(0, 10),
                due_date:    data.due_date.slice(0, 10),
                description: data.description || '',
            }
        } catch {
            errors.value = ['Fatura bilgileri yüklenemedi.']
        }
    }
})

async function submit() {
    if (dueDateError.value) return
    saving.value = true
    errors.value = []
    try {
        if (isEdit.value) {
            await api.update(route.params.id, form.value)
        } else {
            await api.create(form.value)
        }
        router.push('/invoices')
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = Object.values(err.response.data.errors).flat()
        } else {
            errors.value = ['Bir hata oluştu, tekrar deneyin.']
        }
    } finally {
        saving.value = false
    }
}

function fmt(val) {
    return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(val || 0)
}
</script>
