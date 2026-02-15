<template>
  <div class="flex h-screen bg-gray-100">
    <!-- Левая колонка - серый фон -->
    <aside class="w-64 bg-gray-50 flex flex-col h-screen">
      <!-- Логотип -->
      <div class="p-6">
        <div class="space-x-3 space-y-2">
          <svg width="170" height="30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g transform="translate(-28, -30)">
              <path d="M69.0161 53.3617C72.045 53.3617 73.6421 52.0326 74.4131 51.029C75.9826 48.9676 76.1203 45.7128 76.1203 43.6513C76.1203 41.3729 76.0377 38.6605 74.1653 36.6263C73.0088 35.3786 71.1914 34.375 68.5205 34.375H60.9758C59.8193 34.375 59.186 34.8903 58.9106 35.1887C58.2773 35.8939 58.2498 36.7076 58.2498 37.4129V50.9477C58.2498 52.0055 58.6903 52.5751 59.0483 52.8463C59.186 52.9548 59.7642 53.3617 61.1961 53.3617H69.0161ZM61.8844 38.3622C61.8844 37.9553 62.0497 37.5756 62.2699 37.4129C62.5178 37.223 62.9033 37.223 63.1235 37.223H66.8959C68.1901 37.223 69.9248 37.4671 71.0262 38.6063C72.2378 39.854 72.3755 41.7527 72.3755 43.3801C72.3755 46.6078 71.8798 48.0183 71.4117 48.7506C70.5857 50.0797 69.264 50.5137 67.2539 50.5137H63.0685C62.738 50.5137 62.3801 50.5137 62.1323 50.2424C61.8569 49.9441 61.8844 49.5915 61.8844 49.2117V38.3622Z" fill="#363740"></path>
              <path d="M79.0122 38.986V41.4272H86.1989C86.6946 41.4272 87.3279 41.5086 87.7684 42.051C88.1264 42.485 88.1815 42.9732 88.1815 43.4072V44.4108H81.8483C81.0773 44.4108 80.086 44.4651 79.1774 45.2516C78.076 46.201 77.9383 47.4758 77.9383 48.7506C77.9383 49.8085 78.0209 51.6529 79.2049 52.6565C80.031 53.3617 81.0498 53.3617 81.8483 53.3617H88.8148C89.5032 53.3617 90.3017 53.3074 90.8249 52.7921C91.3481 52.2767 91.4031 51.463 91.4031 50.9477V43.4886C91.4031 41.8883 90.935 40.722 90.3292 40.0981C89.6684 39.42 88.5394 38.986 87.3003 38.986H79.0122ZM88.1815 46.852V50.1611C88.1815 50.3781 88.1815 50.5679 87.9612 50.7578C87.7409 50.9205 87.438 50.9205 87.2453 50.9205H82.757C82.344 50.9205 81.9309 50.7035 81.7106 50.4866C81.3251 50.0797 81.2701 49.3202 81.2701 48.8862C81.2701 47.8555 81.5179 47.4758 81.628 47.3402C81.986 46.8791 82.5918 46.852 82.8121 46.852H88.1815Z" fill="#363740"></path>
              <path d="M93.5974 53.3617H96.819V38.986H93.5974V53.3617ZM96.819 36.8975V34.375H93.5974V36.8975H96.819Z" fill="#363740"></path><path d="M99.0292 53.3617H102.251V34.375H99.0292V53.3617Z" fill="#363740"></path>
              <path d="M118.945 39.0403H115.668L110.712 50.0254L106.03 38.986H102.699L109.197 53.3074L106.967 57.9999H110.243L118.945 39.0403Z" fill="#363740"></path>
              <path d="M143.627 44.0582H139.993V49.2389C139.993 49.5372 139.993 49.9169 139.69 50.2153C139.387 50.5137 139.056 50.5137 138.726 50.5137H134.017C132.475 50.5137 131.126 50.1068 130.328 48.5336C130.025 47.9369 129.557 46.635 129.557 43.8683C129.557 41.1288 130.025 39.8811 130.355 39.2844C131.099 37.9553 132.53 37.223 134.623 37.223H143.187V34.375H133.604C131.759 34.375 130.135 34.7818 128.951 35.5413C126.115 37.3586 125.812 40.8576 125.812 43.9768C125.812 46.635 126.004 49.9441 128.345 51.8699C129.226 52.6022 130.686 53.3617 132.833 53.3617H141.039C141.59 53.3617 142.416 53.1989 142.911 52.7921C143.269 52.4937 143.627 51.897 143.627 50.9205V44.0582Z" fill="#363740"></path>
              <path d="M145.818 53.3617H149.04V45.2516C149.04 44.1667 149.177 43.1631 149.783 42.4308C150.554 41.5357 151.683 41.4272 152.537 41.4272H153.886V38.986H151.711C150.582 38.986 148.682 39.0674 147.332 40.2337C146.093 41.2916 145.818 42.8376 145.818 44.4108V53.3617Z" fill="#363740"></path><path d="M161.829 38.8233C160.094 38.8233 157.534 38.8775 155.937 40.3422C154.257 41.8883 154.064 44.6007 154.064 46.2281C154.064 47.6928 154.202 50.4052 155.937 51.9784C157.534 53.4159 160.094 53.5244 161.829 53.5244C163.509 53.5244 166.152 53.4159 167.722 51.9784C168.796 50.9748 169.594 49.1575 169.594 46.1196C169.594 44.6007 169.401 41.8883 167.722 40.3422C166.125 38.8775 163.564 38.8233 161.829 38.8233ZM157.396 46.201C157.396 45.2788 157.396 43.4343 158.305 42.4036C159.213 41.3729 160.783 41.2644 161.829 41.2644C162.986 41.2644 164.39 41.3729 165.326 42.4036C166.345 43.5157 166.262 45.1432 166.262 46.201C166.262 47.4487 166.262 48.832 165.299 49.8898C164.335 50.9477 163.041 51.0833 161.829 51.0833C160.673 51.0833 159.268 50.9477 158.332 49.8898C157.424 48.8591 157.396 47.3944 157.396 46.201Z" fill="#363740"></path><path d="M172.739 38.986H169.655L173.125 51.5715C173.317 52.331 173.675 53.5244 175.63 53.5244C177.31 53.5244 177.916 52.6565 178.136 51.9241L180.669 42.919C180.697 42.8105 180.752 42.4579 180.89 42.4579C181.027 42.4579 181.082 42.8105 181.11 42.919L183.643 51.9241C184.056 53.3888 185.378 53.5244 186.149 53.5244C187.223 53.5244 188.186 53.2532 188.655 51.5715L192.124 38.986H189.04L186.259 49.6186C186.204 49.8085 186.094 50.0526 185.956 50.0526C185.818 50.0526 185.736 49.7542 185.708 49.6457L183.01 40.1524C182.679 38.9589 181.55 38.8233 180.89 38.8233C180.339 38.8233 179.127 38.8775 178.769 40.1524L176.098 49.6457C176.071 49.7271 175.988 50.0526 175.823 50.0526C175.685 50.0526 175.575 49.7542 175.548 49.6186L172.739 38.986Z" fill="#363740"></path>
              <path d="M32.6887 39.8269V30L38.0845 35.25L32.6887 39.8269Z" fill="#03A3EA"></path>
              <path d="M37.9387 45.1666L38.0846 35.25L43.1887 40.2083L37.9387 45.1666Z" fill="#039DE5"></path>
              <path d="M43.1904 49.8379V40.2081L48.4404 45.1664L43.1904 49.8379Z" fill="#0256B2"></path>
              <path d="M37.9399 45.1666L38.0857 35.25L32.6899 39.8426L37.9399 45.1666Z" fill="#0399E2"></path>
              <path d="M43.1904 49.8331V40.2081L37.9405 45.1664L43.1904 49.8331Z" fill="#0381D1"></path>
              <path d="M43.19 49.8334L37.9401 45.1667L33.8568 48.867L33.1268 49.5417C30.9393 51.7292 32.2526 56.5417 34.3794 58L43.19 49.8334Z" fill="#0256B2"></path>
            </g>
          </svg>
          <span class="font-medium text-gray-600">{{ admin?.name || 'Admin' }}</span>
        </div>
      </div>
      
      <!-- Вертикальное меню -->
      <nav class="flex-1 px-4 space-y-1">
        <a class="flex items-center px-4 mb-2 py-3 text-base font-medium rounded-lg transition-colors bg-white text-gray-600 shadow-sm">
          <component :is="DashboardIcon" class="w-5 h-5 mr-3" />
          Отзывы
        </a>
        <a
          v-for="item in menuItems"
          :key="item.path" 
          :href="item.path"
          :class="[
            'flex items-center pl-12 py-1 text-xs font-medium rounded-lg transition-colors',
            currentPath === item.path
              ? 'bg-white text-gray-600 shadow-sm' // Выделенный пункт - белый фон
              : 'text-gray-700 hover:bg-white/50'
          ]"
        >
          {{ item.label }}
        </a>
      </nav>
    </aside>

    <!-- Правая колонка -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Шапка с кнопкой выхода -->
      <header class="bg-white border-b border-gray-200">

        <div class="fixed inset-0 pointer-events-none flex flex-col items-center justify-start z-50 px-4 pt-20">
          <!-- Success сообщение -->
          <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4"
          >
            <div 
              v-if="showSuccess"
              class="pointer-events-auto max-w-md w-full bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg shadow-xl flex items-center space-x-3 mb-3"
            >
              <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium text-green-800">{{ flash.success }}</p>
              </div>
              <button @click="showSuccess = false" class="flex-shrink-0 text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </transition>

          <!-- Error сообщение -->
          <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4"
          >
            <div 
              v-if="showError"
              class="pointer-events-auto max-w-md w-full bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg shadow-xl flex items-center space-x-3 mb-3"
            >
              <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="flex-1">
                <p class="text-sm font-medium text-red-800">{{ flash.error }}</p>
              </div>
              <button @click="showError = false" class="flex-shrink-0 text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </transition>
        </div>


        <div class="flex items-center justify-end px-6 py-4">
          <button 
            @click="$emit('logout')"
            class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors"
          >
          <svg width="44" height="44">
            <rect width="44" height="44" fill="white"/>
            <path d="M15.2 29.7778H24V32H15.2C13.99 32 13 31 13 29.7778V14.2222C13 13 13.99 12 15.2 12H24V14.2222H15.2V29.7778Z" fill="#909AB4"/>
            <path d="M26.1 17L25.113 18.0071L28.319 21.2857H21V22.7143H28.319L25.106 25.9929L26.1 27L31 22L26.1 17Z" fill="#909AB4" stroke="#909AB4"/>
          </svg>
          </button>
        </div>
      </header>

      <!-- Контент страницы -->
      <main class="flex-1 overflow-y-auto bg-white">
        <div class="p-6">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const page = usePage()

const admin = computed(() => page.props.admin)


const logout = () => {
  useForm({}).post(route('admin.logout'))
}

const props = defineProps({
  admin: Object,
  currentPath:String
})

defineEmits(['logout'])


const DashboardIcon = {
  template: `<svg width="58" height="24">
              <g transform="translate(-34, -134)">
                <path d="M54.71 147.58L50.19 143.07C50.2846 142.609 50.3315 142.14 50.33 141.67C50.3296 140.381 50.0042 139.112 49.3839 137.982C48.7636 136.852 47.8683 135.896 46.7809 135.203C45.6934 134.51 44.4489 134.103 43.1623 134.019C41.8757 133.934 40.5886 134.175 39.42 134.72C39.2719 134.788 39.1426 134.89 39.0432 135.019C38.9439 135.148 38.8776 135.3 38.85 135.46C38.8225 135.618 38.8335 135.781 38.8823 135.934C38.931 136.087 39.016 136.227 39.13 136.34L43.48 140.68L41.68 142.48L37.34 138.13C37.2256 138.018 37.086 137.935 36.9329 137.888C36.7797 137.841 36.6175 137.831 36.46 137.86C36.3005 137.887 36.1499 137.951 36.0211 138.049C35.8922 138.146 35.7889 138.274 35.72 138.42C35.1735 139.593 34.9324 140.884 35.0192 142.175C35.1061 143.466 35.5179 144.713 36.2165 145.802C36.915 146.891 37.8776 147.785 39.0147 148.402C40.1518 149.019 41.4264 149.338 42.72 149.33C43.1902 149.332 43.6593 149.285 44.12 149.19L48.63 153.71C48.7229 153.804 48.8335 153.878 48.9554 153.929C49.0772 153.98 49.2079 154.006 49.34 154.006C49.472 154.006 49.6027 153.98 49.7245 153.929C49.8464 153.878 49.957 153.804 50.05 153.71C50.1437 153.617 50.2181 153.506 50.2688 153.385C50.3196 153.263 50.3458 153.132 50.3458 153C50.3458 152.868 50.3196 152.737 50.2688 152.615C50.2181 152.494 50.1437 152.383 50.05 152.29L45.15 147.39C45.0275 147.268 44.8757 147.18 44.7092 147.135C44.5427 147.089 44.3673 147.088 44.2 147.13C43.7171 147.259 43.2198 147.327 42.72 147.33C41.972 147.337 41.2301 147.195 40.5371 146.914C39.8441 146.632 39.2135 146.217 38.6818 145.691C38.1502 145.164 37.7278 144.538 37.4392 143.848C37.1505 143.158 37.0012 142.418 37 141.67C36.9987 141.335 37.0254 141.001 37.08 140.67L41 144.6C41.0929 144.694 41.2035 144.768 41.3254 144.819C41.4472 144.87 41.5779 144.896 41.71 144.896C41.842 144.896 41.9727 144.87 42.0945 144.819C42.2164 144.768 42.327 144.694 42.42 144.6L45.6 141.39C45.7802 141.204 45.8809 140.954 45.8809 140.695C45.8809 140.436 45.7802 140.186 45.6 140L41.71 136.08C42.0405 136.026 42.375 135.999 42.71 136C44.212 136.003 45.6516 136.601 46.7128 137.664C47.774 138.727 48.37 140.168 48.37 141.67C48.3665 142.17 48.2993 142.667 48.17 143.15C48.1276 143.317 48.1293 143.493 48.1749 143.659C48.2204 143.826 48.3083 143.978 48.43 144.1L53.33 149C53.5183 149.188 53.7737 149.294 54.04 149.294C54.3063 149.294 54.5616 149.188 54.75 149C54.9383 148.812 55.044 148.556 55.044 148.29C55.044 148.024 54.9383 147.768 54.75 147.58H54.71Z" fill="#DCE4EA"/>
              </g>
              <defs>
                <clipPath id="clip0_1_704">
                  <rect width="58" height="24" fill="white"/>
                </clipPath>
              </defs>
              </svg>`
}

const menuItems = [
  { path: '/admin/reviews', label: 'Отзывы'},
  { path: '/admin/settings', label: 'Настройка'},
]


const flash = computed(() => page.props.flash)

const showSuccess = ref(false)
const showError = ref(false)

watch(() => flash.value, (newFlash) => {
  if (newFlash?.success) {
    showSuccess.value = true
    setTimeout(() => showSuccess.value = false, 5000)
  }
  if (newFlash?.error) {
    showError.value = true
    setTimeout(() => showError.value = false, 5000)
  }
}, { deep: true, immediate: true })
</script>