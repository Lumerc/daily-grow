<template>
  <div class="max-w-[400px] mx-auto px-8 py-8 font-sans">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
      Вход в админ-панель
    </h1>

    <div v-if="hasErrors" class="bg-red-50 border border-red-300 rounded-md p-4 mb-4">
      <p class="text-red-600 text-sm">
        Не удалось войти. Проверьте email и пароль.
      </p>
    </div>

    <form @submit.prevent="submit" class="bg-gray-50 p-6 rounded-lg mt-6">
      <!-- Email -->
      <div class="mb-4">
        <label for="email" class="block mb-2 font-bold text-gray-700 text-sm">
          Email
        </label>
        <input
          id="email"
          v-model="form.email"
          type="email"
          required
          autofocus
          placeholder="admin@example.com"
          class="w-full px-3 py-2 border border-gray-300 rounded-md text-base box-border focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
          :class="{ 'border-red-500': errors.email || form.errors.email }"
        />
        <p v-if="errors.email || form.errors.email" class="text-red-600 text-xs mt-1">
          {{ errors.email || form.errors.email }}
        </p>
      </div>

      <div class="mb-4">
        <label for="password" class="block mb-2 font-bold text-gray-700 text-sm">
          Пароль
        </label>
        <div class="relative">
          <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            required
            placeholder="Введите пароль"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-base box-border focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
            :class="{ 'border-red-500': errors.password || form.errors.password }"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >
            <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
          </button>
        </div>
        <p v-if="errors.password || form.errors.password" class="text-red-600 text-xs mt-1">
          {{ errors.password || form.errors.password }}
        </p>
      </div>

      <div class="mt-6">
        <button
          type="submit"
          :disabled="form.processing"
          class="w-full px-3 py-3 bg-blue-500 text-white rounded-md text-base cursor-pointer transition-colors hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          {{ form.processing ? 'Вход...' : 'Войти' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { Ziggy } from '../../ziggy'

const showPassword = ref(false)

const form = useForm({
  email: '',
  password: '',
})

const props = defineProps({
  errors: {
    type: Object,
    default: () => ({})
  }
})

const hasErrors = computed(() => {
  const errorCount = Object.keys(props.errors || {}).length
  const formErrorCount = Object.keys(form.errors || {}).length
  return errorCount > 0 || formErrorCount > 0
})

const submit = () => {
  form.post(route('login', {}, Ziggy), {
    onSuccess: () => {
      console.log('Успешный вход! Редирект...')
    },
    onError: (errors) => {
      console.log('Ошибка входа', errors)
    },
  })
}
</script>