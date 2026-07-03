<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-toprak-900">Faturalar</h1>
            <button @click="openModal()"
                class="bg-toprak-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-toprak-700 transition-colors">
                + Yeni Fatura
            </button>
        </div>

        <!-- Filtreler -->
        <div class="flex flex-wrap gap-3 mb-4">
            <select v-model="filters.status"
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 focus:outline-none focus:ring-2 focus:ring-toprak-400">
                <option value="">Tüm Durumlar</option>
                <option value="beklemede">Beklemede</option>
                <option value="odendi">Ödendi</option>
                <option value="iptal">İptal</option>
            </select>
            <input v-model="filters.start_date" type="date"
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            <input v-model="filters.end_date" type="date"
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            <input v-model="search" type="text" placeholder="Müşteri ara..."
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 w-48 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            <button @click="resetFilters"
                class="px-3 py-2 text-sm text-toprak-500 hover:text-toprak-700 transition-colors">
                Temizle
            </button>
        </div>

        <!-- Özet kartlar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white border border-toprak-100 rounded-xl p-4">
                <p class="text-xs text-toprak-400 mb-1">Toplam</p>
                <p class="text-xl font-bold text-toprak-800">{{ filtered.length }} fatura</p>
            </div>
            <div class="bg-white border border-toprak-100 rounded-xl p-4">
                <p class="text-xs text-toprak-400 mb-1">Beklemede</p>
                <p class="text-xl font-bold text-amber-700">{{ fmt(pendingTotal) }}</p>
            </div>
            <div class="bg-white border border-toprak-100 rounded-xl p-4">
                <p class="text-xs text-toprak-400 mb-1">Tahsil Edilen</p>
                <p class="text-xl font-bold text-sage-600">{{ fmt(paidTotal) }}</p>
            </div>
        </div>

        <div v-if="error" class="bg-terra-50 border border-terra-200 rounded-lg px-4 py-3 text-sm text-terra-700 mb-4">
            {{ error }}
        </div>

        <!-- Tablo -->
        <div class="bg-white rounded-xl border border-toprak-100 overflow-x-auto">
            <div v-if="loading" class="p-8 text-center text-toprak-400">Yükleniyor...</div>
            <div v-else-if="filtered.length === 0" class="p-8 text-center text-toprak-400">Fatura bulunamadı.</div>
            <table v-else class="w-full text-sm min-w-[700px]">
                <thead class="bg-toprak-50 border-b border-toprak-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Fatura No</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Müşteri</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Tutar</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">KDV Dahil</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Düzenlenme</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Vade</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Durum</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="inv in filtered" :key="inv.id"
                        class="border-b border-toprak-50 last:border-0 hover:bg-toprak-50"
                        :class="{ 'opacity-60': inv.status === 'iptal' }">
                        <td class="px-5 py-3 font-mono text-toprak-600 text-xs">{{ inv.invoice_no }}</td>
                        <td class="px-5 py-3 font-medium text-toprak-800">{{ inv.client_name }}</td>
                        <td class="px-5 py-3 text-toprak-700 whitespace-nowrap">{{ fmt(inv.amount) }}</td>
                        <td class="px-5 py-3 font-semibold text-toprak-800 whitespace-nowrap">{{ fmt(withTax(inv)) }}</td>
                        <td class="px-5 py-3 text-toprak-400 whitespace-nowrap">{{ fmtDate(inv.issue_date) }}</td>
                        <td class="px-5 py-3 whitespace-nowrap"
                            :class="isOverdue(inv) ? 'text-terra-600 font-medium' : 'text-toprak-400'">
                            {{ fmtDate(inv.due_date) }}
                            <span v-if="isOverdue(inv)" class="text-xs ml-1">⚠️</span>
                        </td>
                        <td class="px-5 py-3">
                            <select :value="inv.status" @change="changeStatus(inv, $event.target.value)"
                                class="text-xs rounded-full px-2 py-1 border-0 font-medium cursor-pointer focus:outline-none"
                                :class="statusClass(inv.status)">
                                <option value="beklemede">beklemede</option>
                                <option value="odendi">ödendi</option>
                                <option value="iptal">iptal</option>
                            </select>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                            <button @click="openModal(inv)" class="text-toprak-600 hover:underline">Düzenle</button>
                            <button @click="remove(inv.id)" class="text-terra-600 hover:underline">Sil</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="modal.show"
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                @click.self="closeModal">
                <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-toprak-100 sticky top-0 bg-white rounded-t-2xl">
                        <h2 class="text-xl font-bold text-toprak-900">
                            {{ modal.id ? 'Faturayı Düzenle' : 'Yeni Fatura' }}
                        </h2>
                        <button @click="closeModal" class="text-toprak-400 hover:text-toprak-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitModal" class="px-6 py-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-toprak-700 mb-1">Müşteri Adı</label>
                            <input v-model="modal.form.client_name" type="text" required
                                class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-toprak-700 mb-1">Tutar (₺)</label>
                                <input v-model="modal.form.amount" type="number" step="0.01" min="0.01" required
                                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-toprak-700 mb-1">KDV (%)</label>
                                <select v-model="modal.form.tax_rate"
                                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400">
                                    <option :value="0">%0</option>
                                    <option :value="10">%10</option>
                                    <option :value="20">%20</option>
                                </select>
                            </div>
                        </div>

                        <div v-if="modal.form.amount && parseFloat(modal.form.amount) > 0"
                            class="bg-toprak-50 rounded-lg px-4 py-3 text-sm">
                            <span class="text-toprak-500">KDV Dahil Toplam: </span>
                            <span class="font-bold text-toprak-800">
                                {{ fmt(parseFloat(modal.form.amount) * (1 + modal.form.tax_rate / 100)) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-toprak-700 mb-1">Düzenlenme Tarihi</label>
                                <input v-model="modal.form.issue_date" type="date" required
                                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-toprak-700 mb-1">Vade Tarihi</label>
                                <input v-model="modal.form.due_date" type="date" required
                                    :min="modal.form.issue_date"
                                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400"
                                    :class="{ 'border-terra-400': dueDateError }" />
                                <p v-if="dueDateError" class="text-xs text-terra-600 mt-1">
                                    Vade tarihi düzenlenme tarihinden önce olamaz.
                                </p>
                            </div>
                        </div>

                        <div v-if="modal.id">
                            <label class="block text-sm font-medium text-toprak-700 mb-1">Durum</label>
                            <select v-model="modal.form.status"
                                class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400">
                                <option value="beklemede">Beklemede</option>
                                <option value="odendi">Ödendi</option>
                                <option value="iptal">İptal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-toprak-700 mb-1">Açıklama</label>
                            <textarea v-model="modal.form.description" rows="2"
                                class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400 resize-none"></textarea>
                        </div>

                        <div v-if="modal.errors.length" class="bg-terra-50 border border-terra-200 rounded-lg p-3 text-sm text-terra-700 space-y-1">
                            <p v-for="e in modal.errors" :key="e">{{ e }}</p>
                        </div>

                        <div class="flex gap-3 pt-1 pb-1">
                            <button type="submit" :disabled="modal.saving || dueDateError"
                                class="bg-toprak-600 text-white px-5 py-2 rounded-lg hover:bg-toprak-700 transition-colors disabled:opacity-50">
                                {{ modal.saving ? 'Kaydediliyor...' : 'Kaydet' }}
                            </button>
                            <button type="button" @click="closeModal"
                                class="px-5 py-2 rounded-lg border border-toprak-200 hover:bg-toprak-50 text-toprak-600 transition-colors">
                                İptal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import api from '../api/invoices'

const invoices = ref([])
const loading  = ref(true)
const error    = ref('')
const search   = ref('')
const filters  = ref({ status: '', start_date: '', end_date: '' })

const today = new Date().toISOString().slice(0, 10)
const in30  = new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10)

const modal = reactive({
    show: false,
    id: null,
    saving: false,
    errors: [],
    form: defaultForm(),
})

function defaultForm() {
    return {
        client_name: '',
        amount: '',
        tax_rate: 20,
        status: 'beklemede',
        issue_date: today,
        due_date: in30,
        description: '',
    }
}

const dueDateError = computed(() =>
    !!(modal.form.due_date && modal.form.issue_date && modal.form.due_date < modal.form.issue_date)
)

function openModal(inv = null) {
    modal.errors = []
    modal.saving = false
    if (inv) {
        modal.id = inv.id
        modal.form = {
            client_name: inv.client_name,
            amount:      inv.amount,
            tax_rate:    inv.tax_rate,
            status:      inv.status,
            issue_date:  inv.issue_date.slice(0, 10),
            due_date:    inv.due_date.slice(0, 10),
            description: inv.description || '',
        }
    } else {
        modal.id = null
        modal.form = defaultForm()
    }
    modal.show = true
}

function closeModal() {
    modal.show = false
}

async function submitModal() {
    if (dueDateError.value) return
    modal.saving = true
    modal.errors = []
    try {
        if (modal.id) {
            const { data } = await api.update(modal.id, modal.form)
            const idx = invoices.value.findIndex(i => i.id === modal.id)
            if (idx !== -1) invoices.value[idx] = data
        } else {
            const { data } = await api.create(modal.form)
            invoices.value.unshift(data)
        }
        closeModal()
    } catch (err) {
        if (err.response?.status === 422) {
            modal.errors = Object.values(err.response.data.errors).flat()
        } else {
            modal.errors = ['Bir hata oluştu, tekrar deneyin.']
        }
    } finally {
        modal.saving = false
    }
}

onMounted(load)
watch(filters, load, { deep: true })

async function load() {
    loading.value = true
    error.value   = ''
    try {
        const { data } = await api.getAll(filters.value)
        invoices.value = data
    } catch {
        error.value = 'Faturalar yüklenirken bir hata oluştu.'
    } finally {
        loading.value = false
    }
}

async function changeStatus(inv, status) {
    try {
        await api.update(inv.id, { status })
        inv.status = status
    } catch {
        alert('Durum güncellenemedi, tekrar deneyin.')
    }
}

async function remove(id) {
    if (!confirm('Bu faturayı silmek istediğinize emin misiniz?')) return
    try {
        await api.destroy(id)
        invoices.value = invoices.value.filter(i => i.id !== id)
    } catch {
        alert('Silme işlemi başarısız oldu, tekrar deneyin.')
    }
}

function resetFilters() {
    filters.value = { status: '', start_date: '', end_date: '' }
    search.value = ''
}

const filtered = computed(() => {
    if (!search.value) return invoices.value
    const q = search.value.toLowerCase()
    return invoices.value.filter(i =>
        i.client_name.toLowerCase().includes(q) ||
        i.invoice_no.toLowerCase().includes(q)
    )
})

const pendingTotal = computed(() =>
    filtered.value.filter(i => i.status === 'beklemede').reduce((s, i) => s + withTax(i), 0)
)
const paidTotal = computed(() =>
    filtered.value.filter(i => i.status === 'odendi').reduce((s, i) => s + withTax(i), 0)
)

function withTax(inv) {
    return parseFloat(inv.amount) * (1 + (inv.tax_rate || 0) / 100)
}
function isOverdue(inv) {
    return inv.status === 'beklemede' && new Date(inv.due_date) < new Date()
}
function statusClass(status) {
    return {
        beklemede: 'bg-amber-100 text-amber-700',
        odendi:    'bg-sage-100 text-sage-700',
        iptal:     'bg-toprak-100 text-toprak-500',
    }[status] || ''
}
function fmt(val) {
    return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(val)
}
function fmtDate(d) {
    return new Date(d).toLocaleDateString('tr-TR')
}
</script>
