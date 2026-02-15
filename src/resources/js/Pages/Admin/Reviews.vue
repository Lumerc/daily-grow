<template>
  <AdminLayout :current-path="$page.url" :admin="admin">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Управление отзывами
      </h2>
    </template>

    <div class="flex gap-6">
      <!-- ЛЕВАЯ КОЛОНКА: Отзывы с пагинацией и сортировкой (70%) -->
      <div class="w-7/12">
        <!-- Сортировка -->
        <div class="mb-6 flex items-center space-x-4 bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-4">
          <span class="text-sm text-gray-600">Сортировать:</span>
          <Link 
            v-for="option in sortOptions" 
            :key="option.value"
            :href="route('admin.reviews', { sort: option.value })" 
            class="px-3 py-1 rounded-md text-sm"
            :class="filters.sort === option.value ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
          >
            {{ option.label }}
          </Link>
        </div>

        <!-- Список отзывов -->
        <div v-if="reviews.data.length === 0" class="text-center py-8 text-gray-500 bg-white overflow-hidden shadow-sm sm:rounded-lg">
          Отзывы пока не собраны
        </div>
        
        <div v-else class="bg-white">
          <div v-for="review in reviews.data" :key="review.id" class="shadow-sm sm:rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm text-gray-500">
                {{ formatDate(review.review_created_at) }}
              </span>
              <div class="flex items-center">
                <div class="flex mr-2" style="color: #fbbc04;">
                  <span v-for="star in 5" :key="star" class="text-lg">
                    {{ star <= review.rating ? '★' : '☆' }}
                  </span>
                </div>
              </div>
            </div>
            <div class="font-medium">{{ review.author_name }}</div>
            <div class="text-gray-700 whitespace-pre-line">{{ review.text }}</div>
          </div>
        </div>
        <!-- Пагинация -->
        <div v-if="reviews.last_page > 1" class="mt-6">
          <Pagination     
              :links="reviews.links" 
              :from="reviews.from" 
              :to="reviews.to" 
              :total="reviews.total" 
            />
        </div>

      </div>

      <div class="w-5/12">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
          <div class="p-6">
            
            <!-- Общая статистика -->
            <div class="space-y-4 mb-6">
              <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                <div class="text-4xl flex items-center">
                  <span class="font-bold mr-2">{{ stats.rating }}</span>
                  <div class="flex" style="color: #fbbc04;">
                    <span v-for="n in 5" :key="n">
                      {{ n <= Math.round(stats.rating) ? '★' : '☆' }}
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="flex justify-between items-center pb-2">
                <span class="text-gray-600">Всего отзывов</span>
                <span class="text-2xl font-bold">{{ stats.reviews_count }}</span>
              </div>
            </div>

            

          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { route } from 'ziggy-js'
import { Ziggy } from '../../ziggy'

defineProps({
  reviews: Object,
  stats: Object,
  filters: Object,
  admin: Object
})

const formatDate = (dateString) => {
  const d = new Date(dateString)
  const day = d.getDate().toString().padStart(2, '0')
  const month = (d.getMonth() + 1).toString().padStart(2, '0')
  const year = d.getFullYear()
  const hours = d.getHours().toString().padStart(2, '0')
  const minutes = d.getMinutes().toString().padStart(2, '0')
  
  return `${day}.${month}.${year} ${hours}:${minutes}`
}

const sortOptions = [
  { value: 'date_desc', label: 'Сначала новые' },
  { value: 'date_asc', label: 'Сначала старые' },
  { value: 'rating_desc', label: 'Лучшие' },
  { value: 'rating_asc', label: 'Худшие' }
]
</script>