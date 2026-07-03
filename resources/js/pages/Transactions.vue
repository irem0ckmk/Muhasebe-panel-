<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-toprak-900">İşlemler</h1>
            <button @click="openModal()"
                class="bg-toprak-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-toprak-700 transition-colors">
                + Yeni İşlem
            </button>
        </div>

        <!-- Filtreler -->
        <div class="flex flex-wrap gap-3 mb-4">
            <select v-model="filters.type" @change="load"
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 focus:outline-none focus:ring-2 focus:ring-toprak-400">
                <option value="">Tümü</option>
                <option value="gelir">Gelir</option>
                <option value="gider">Gider</option>
            </select>
            <select v-model="filters.category" @change="load"
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 focus:outline-none focus:ring-2 focus:ring-toprak-400">
                <option value="">Tüm Kategoriler</option>
                <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
            </select>
            <input v-model="filters.start_date" @change="load" type="date"
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            <input v-model="filters.end_date" @change="load" type="date"
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            <input v-model="search" type="text" placeholder="Ara..."
                class="border border-toprak-200 rounded-lg px-3 py-2 text-sm text-toprak-700 w-44 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            <button @click="resetFilters"
                class="px-3 py-2 text-sm text-toprak-500 hover:text-toprak-700 transition-colors">
                Temizle
            </button>
        </div>

        <!-- Özet -->
        <div v-if="filtered.length" class="flex flex-wrap gap-4 mb-4 text-sm">
            <span class="text-sage-600 font-medium">Gelir: {{ fmt(filteredGelir) }}</span>
            <span class="text-toprak-300 hidden sm:inline">|</span>
            <span class="text-terra-600 font-medium">Gider: {{ fmt(filteredGider) }}</span>
            <span class="text-toprak-300 hidden sm:inline">|</span>
            <span class="font-medium" :class="filteredGelir - filteredGider >= 0 ? 'text-toprak-700' : 'text-terra-600'">
                Net: {{ fmt(filteredGelir - filteredGider) }}
            </span>
        </div>

        <div v-if="error" class="bg-terra-50 border border-terra-200 rounded-lg px-4 py-3 text-sm text-terra-700 mb-4">
            {{ error }}
        </div>

        <div class="bg-white rounded-xl border border-toprak-100 overflow-x-auto">
            <div v-if="loading" class="p-8 text-center text-toprak-400">Yükleniyor...</div>
            <div v-else-if="filtered.length === 0" class="p-8 text-center text-toprak-400">İşlem bulunamadı.</div>
            <table v-else class="w-full text-sm min-w-[600px]">
                <thead class="bg-toprak-50 border-b border-toprak-100">
                    <tr>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Tarih</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Tür</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Kategori</th>
                        <th class="px-5 py-3 text-left text-toprak-500 font-medium">Açıklama</th>
                        <th class="px-5 py-3 text-right text-toprak-500 font-medium">Tutar</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in filtered" :key="t.id" class="border-b border-toprak-50 last:border-0 hover:bg-toprak-50">
                        <td class="px-5 py-3 text-toprak-400 whitespace-nowrap">{{ fmtDate(t.date) }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="t.type === 'gelir' ? 'bg-sage-100 text-sage-700' : 'bg-terra-100 text-terra-700'">
                                {{ t.type }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-toprak-700">{{ t.category }}</td>
                        <td class="px-5 py-3 text-toprak-400">{{ t.description }}</td>
                        <td class="px-5 py-3 text-right font-semibold whitespace-nowrap"
                            :class="t.type === 'gelir' ? 'text-sage-600' : 'text-terra-600'">
                            {{ t.type === 'gelir' ? '+' : '-' }}{{ fmt(t.amount) }}
                        </td>
                        <td class="px-5 py-3 text-right space-x-2 whitespace-nowrap">
                            <button @click="openModal(t)" class="text-toprak-600 hover:underline">Düzenle</button>
                            <button @click="remove(t.id)" class="text-terra-600 hover:underline">Sil</button>
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
                <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl">
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-toprak-100">
                        <h2 class="text-xl font-bold text-toprak-900">
                            {{ modal.id ? 'İşlemi Düzenle' : 'Yeni İşlem' }}
                        </h2>
                        <button @click="closeModal" class="text-toprak-400 hover:text-toprak-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitModal" class="px-6 py-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-toprak-700 mb-1">Tür</label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="modal.form.type" value="gelir" class="accent-toprak-600" />
                                    <span class="text-sage-600 font-medium">Gelir</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" v-model="modal.form.type" value="gider" class="accent-toprak-600" />
                                    <span class="text-terra-600 font-medium">Gider</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-toprak-700 mb-1">Tutar (₺)</label>
                            <input v-model="modal.form.amount" type="number" step="0.01" min="0.01" required
                                class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-toprak-700 mb-1">Kategori</label>
                            <input v-model="modal.form.category" type="text" required list="modal-categories"
                                class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                            <datalist id="modal-categories">
                                <option v-for="c in categories" :key="c" :value="c" />
                            </datalist>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-toprak-700 mb-1">Açıklama</label>
                            <input v-model="modal.form.description" type="text"
                                class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-toprak-700 mb-1">Tarih</label>
                            <input v-model="modal.form.date" type="date" required
                                class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                        </div>

                        <div v-if="modal.errors.length" class="bg-terra-50 border border-terra-200 rounded-lg p-3 text-sm text-terra-700 space-y-1">
                            <p v-for="e in modal.errors" :key="e">{{ e }}</p>
                        </div>

                        <div class="flex gap-3 pt-1 pb-1">
                            <button type="submit" :disabled="modal.saving"
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
import { ref, reactive, computed, onMounted } from 'vue'
import api from '../api/transactions'

const categories = [
    'Kira', 'Fatura', 'Maaş', 'Malzeme', 'Pazarlama',
    'Ulaşım', 'Yemek', 'Vergi', 'Satış', 'Diğer',
]

const transactions = ref([])
const loading      = ref(true)
const error        = ref('')
const search       = ref('')
const filters      = ref({ type: '', category: '', start_date: '', end_date: '' })

const modal = reactive({
    show: false,
    id: null,
    saving: false,
    errors: [],
    form: {
        type: 'gelir',
        amount: '',
        category: '',
        description: '',
        date: today(),
    },
})

function today() {
    return new Date().toISOString().slice(0, 10)
}

function openModal(tx = null) {
    modal.errors = []
    modal.saving = false
    if (tx) {
        modal.id = tx.id
        modal.form = {
            type:        tx.type,
            amount:      tx.amount,
            category:    tx.category,
            description: tx.description || '',
            date:        tx.date.slice(0, 10),
        }
    } else {
        modal.id = null
        modal.form = { type: 'gelir', amount: '', category: '', description: '', date: today() }
    }
    modal.show = true
}

function closeModal() {
    modal.show = false
}

async function submitModal() {
    modal.saving = true
    modal.errors = []
    try {
        if (modal.id) {
            const { data } = await api.update(modal.id, modal.form)
            const idx = transactions.value.findIndex(t => t.id === modal.id)
            if (idx !== -1) transactions.value[idx] = data
        } else {
            const { data } = await api.create(modal.form)
            transactions.value.unshift(data)
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

async function load() {
    loading.value = true
    error.value   = ''
    try {
        const { data } = await api.getAll(filters.value)
        transactions.value = data
    } catch {
        error.value = 'İşlemler yüklenirken bir hata oluştu.'
    } finally {
        loading.value = false
    }
}

async function remove(id) {
    if (!confirm('Bu işlemi silmek istediğinize emin misiniz?')) return
    try {
        await api.destroy(id)
        transactions.value = transactions.value.filter(t => t.id !== id)
    } catch {
        alert('Silme işlemi başarısız oldu, tekrar deneyin.')
    }
}

function resetFilters() {
    filters.value = { type: '', category: '', start_date: '', end_date: '' }
    search.value = ''
    load()
}

const filtered = computed(() => {
    if (!search.value) return transactions.value
    const q = search.value.toLowerCase()
    return transactions.value.filter(t =>
        t.category.toLowerCase().includes(q) ||
        (t.description || '').toLowerCase().includes(q)
    )
})

const filteredGelir = computed(() =>
    filtered.value.filter(t => t.type === 'gelir').reduce((s, t) => s + parseFloat(t.amount), 0)
)
const filteredGider = computed(() =>
    filtered.value.filter(t => t.type === 'gider').reduce((s, t) => s + parseFloat(t.amount), 0)
)

function fmt(val) {
    return new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(val)
}
function fmtDate(d) {
    return new Date(d).toLocaleDateString('tr-TR')
}
</script>
