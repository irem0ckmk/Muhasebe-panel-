<template>
    <div class="max-w-lg">
        <h1 class="text-2xl font-bold text-toprak-900 mb-6">{{ isEdit ? 'İşlemi Düzenle' : 'Yeni İşlem' }}</h1>

        <form @submit.prevent="submit" class="bg-white rounded-xl border border-toprak-100 p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-toprak-700 mb-1">Tür</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" v-model="form.type" value="gelir" class="accent-toprak-600" />
                        <span class="text-sage-600 font-medium">Gelir</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" v-model="form.type" value="gider" class="accent-toprak-600" />
                        <span class="text-terra-600 font-medium">Gider</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-toprak-700 mb-1">Tutar (₺)</label>
                <input v-model="form.amount" type="number" step="0.01" min="0.01" required
                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            </div>

            <div>
                <label class="block text-sm font-medium text-toprak-700 mb-1">Kategori</label>
                <input v-model="form.category" type="text" required
                    list="categories"
                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
                <datalist id="categories">
                    <option v-for="c in categories" :key="c" :value="c" />
                </datalist>
            </div>

            <div>
                <label class="block text-sm font-medium text-toprak-700 mb-1">Açıklama</label>
                <input v-model="form.description" type="text"
                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            </div>

            <div>
                <label class="block text-sm font-medium text-toprak-700 mb-1">Tarih</label>
                <input v-model="form.date" type="date" required
                    class="w-full border border-toprak-200 rounded-lg px-3 py-2 text-toprak-900 focus:outline-none focus:ring-2 focus:ring-toprak-400" />
            </div>

            <div v-if="errors.length" class="bg-terra-50 border border-terra-200 rounded-lg p-3 text-sm text-terra-700 space-y-1">
                <p v-for="e in errors" :key="e">{{ e }}</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" :disabled="saving"
                    class="bg-toprak-600 text-white px-5 py-2 rounded-lg hover:bg-toprak-700 transition-colors disabled:opacity-50">
                    {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
                </button>
                <RouterLink to="/transactions"
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
import api from '../api/transactions'

const route  = useRoute()
const router = useRouter()

const isEdit = computed(() => !!route.params.id)

const form = ref({
    type: 'gelir',
    amount: '',
    category: '',
    description: '',
    date: new Date().toISOString().slice(0, 10),
})

const categories = [
    'Kira', 'Fatura', 'Maaş', 'Malzeme', 'Pazarlama',
    'Ulaşım', 'Yemek', 'Vergi', 'Satış', 'Diğer',
]

const saving = ref(false)
const errors = ref([])

onMounted(async () => {
    if (isEdit.value) {
        try {
            const { data } = await api.getOne(route.params.id)
            form.value = {
                type:        data.type,
                amount:      data.amount,
                category:    data.category,
                description: data.description || '',
                date:        data.date.slice(0, 10),
            }
        } catch {
            errors.value = ['İşlem bilgileri yüklenemedi.']
        }
    }
})

async function submit() {
    saving.value = true
    errors.value = []
    try {
        if (isEdit.value) {
            await api.update(route.params.id, form.value)
        } else {
            await api.create(form.value)
        }
        router.push('/transactions')
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
</script>
