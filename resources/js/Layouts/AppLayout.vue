<template>
  <div :class="darkMode ? 'dark' : ''">
    <div class="bg-gray-50 text-gray-800 dark:text-gray-100 transition-colors duration-300 min-h-screen">
      
      <!-- Fixed Backdrop for Mobile -->
      <div 
        v-if="sidebarOpen && isMobile"
        class="fixed inset-0 z-50 transition-opacity duration-300"
        @click="sidebarOpen = false"
      ></div>

      <Sidebar 
        :sidebarOpen="sidebarOpen"
        :isMobile="isMobile"
        @update:sidebarOpen="sidebarOpen = $event" class="no-print"
      />

      <div :class="['transition-all duration-300', marginLeft, isMobile ? 'w-full' : '']">
        
        <Topbar 
          :sidebarOpen="sidebarOpen"
          :isMobile="isMobile"
          @update:sidebarOpen="sidebarOpen = $event"
          class="sticky top-0 z-40 no-print"
        />

        <!-- Main Content Slot -->
        <main class="px-4 pt-1 pb-12">
          <div class="max-w-7xl mx-auto">
            <slot />
          </div>
        </main>

        <Footer class="no-print" />
      </div>

      <DarkTheme :darkMode="darkMode" @update:darkMode="darkMode = $event" class="no-print" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Sidebar from '../components/Sidebar.vue'
import Topbar from '../components/Topbar.vue'
import Footer from '../Components/Footer.vue'
import DarkTheme from '../Components/DarkTheme.vue'

const sidebarOpen = ref(true)
const darkMode = ref(false)
const windowWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024)

const isMobile = computed(() => windowWidth.value < 768)
const marginLeft = computed(() => {
  if (isMobile.value) return 'ml-0'
  return sidebarOpen.value ? 'ml-60' : 'ml-28'
})

const handleResize = () => {
  windowWidth.value = window.innerWidth
  if (window.innerWidth < 768) {
    sidebarOpen.value = false
  }
}

onMounted(() => {
  window.addEventListener('resize', handleResize)
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme === 'dark') {
    darkMode.value = true
    document.documentElement.classList.add('dark')
  }
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>