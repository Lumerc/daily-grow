<template>
  <Head>
    <meta http-equiv="refresh" content="30">
  </Head>

  <AdminLayout :current-path="$page.url" @logout="logout">
    <div class="space-y-2">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Подключить Яндекс</h1>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
      <div class="bg-white overflow-hidden">
        <div class="py-2">
          <div class="grid gap-2">
            <div class="col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Укажите ссылку на Яндекс, пример https://yandex.ru/maps/org/samoye_populyarnoye_kafe_tsentr/1010501395/reviews/
              </label>
            </div>
            <div class="col-span-2">
              <input 
                v-model="form.org_link"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="https://yandex.ru/maps/org/samoye_populyarnoye_kafe_tsentr/1010501395/reviews/"
              />
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-start">
        <button 
    
          type="submit"
          class="px-6 py-1 bg-gradient-to-r from-blue-400 to-blue-500 hover:from-blue-500 hover:to-blue-600 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
          Сохранить
        </button>
      </div>
      </form>
    <div 
        class="px-3 py-1 rounded-full text-xs font-medium"
        :class="{
        'bg-orange-100 text-orange-800': settings.progress < 100,
        'bg-green-100 text-green-800': settings.progress === 100
        }"
    >
        {{ settings.progress }}% завершено
    </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import AdminLayout from '../../Layouts/AdminLayout.vue'
import { router, useForm} from '@inertiajs/vue3'

const props = defineProps({
  admin: Object,
  settings: Object
})

// Состояние формы
const form = useForm({
  org_link: props.settings?.org_link || '',
})

const logout = () => {
  router.post('/admin/logout')
}

const submit = () => {
  form.put('/admin/settings', {
    preserveScroll: true
  })
}

</script>