<template>
  <div class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <Link 
        v-for="link in links" 
        :key="link.label"
        :href="link.url || '#'" 
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md"
        :class="{
          'bg-indigo-600 text-white': link.active,
          'bg-white text-gray-700 hover:bg-gray-50': !link.active && link.url,
          'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url
        }"
        v-html="link.label"
      />
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700">
          с
          <span class="font-medium">{{ from }}</span>
          по
          <span class="font-medium">{{ to }}</span>
          из
          <span class="font-medium">{{ total }}</span>
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
          <Link
            v-for="link in links"
            :key="link.label"
            :href="link.url || '#'"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium border"
            :class="{
              'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
              'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': !link.active && link.url,
              'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed': !link.url
            }"
            v-html="link.label"
          />
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  links: Array
})

const from = computed(() => {
  const first = props.links.find(l => l.label === '1')
  return first?.url ? 1 : 0
})

const to = computed(() => {
  const last = props.links[props.links.length - 2]
  return last?.url ? parseInt(last.label) * 20 : 0
})

const total = computed(() => {
  const last = props.links[props.links.length - 2]
  return last?.url ? parseInt(last.label) * 20 : 0
})
</script>