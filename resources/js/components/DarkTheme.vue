<template>
  <label
    class="fixed bottom-6 right-6 z-50 inline-block w-14 h-8 cursor-pointer select-none"
    title="Toggle Dark Mode"
  >
    <input
      type="checkbox"
      :checked="darkMode"
      @change="toggleDarkMode"
      class="peer sr-only"
    />
    <span class="absolute inset-0 bg-gray-300 rounded-full transition-colors duration-300 ease-in-out peer-checked:bg-blue-600 shadow-md"></span>
    <span 
      class="absolute top-1/2 left-1 bg-white rounded-full shadow-md w-6 h-6 -translate-y-1/2 transition-transform duration-300 ease-in-out peer-checked:translate-x-6 flex items-center justify-center text-lg"
      :style="iconStyle"
    >
      {{ darkMode ? '☀️' : '🌙' }}
    </span>
  </label>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'

const props = defineProps({
  darkMode: {
    type: Boolean,
    required: true
  }
})

const emit = defineEmits(['update:darkMode'])

const iconStyle = computed(() => {
  return props.darkMode ? { filter: 'invert(1) hue-rotate(180deg)' } : { filter: 'none' }
})

// Check system preference and localStorage
const getInitialTheme = () => {
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme) {
    return savedTheme === 'dark'
  }
  return window.matchMedia('(prefers-color-scheme: dark)').matches
}

const enableDarkMode = () => {
  document.body.classList.add('dark-mode')
  document.documentElement.style.filter = 'invert(1) hue-rotate(180deg)'
  
  // Invert images, videos, and iframes back to normal
  document.querySelectorAll('img, video, iframe').forEach(el => {
    el.style.filter = 'invert(1) hue-rotate(180deg)'
  })
  
  localStorage.setItem('theme', 'dark')
}

const disableDarkMode = () => {
  document.body.classList.remove('dark-mode')
  document.documentElement.style.filter = 'none'
  
  document.querySelectorAll('img, video, iframe').forEach(el => {
    el.style.filter = 'none'
  })
  
  localStorage.setItem('theme', 'light')
}

const applyDarkMode = (isDark) => {
  if (isDark) {
    enableDarkMode()
  } else {
    disableDarkMode()
  }
}

const toggleDarkMode = () => {
  const newValue = !props.darkMode
  emit('update:darkMode', newValue)
  applyDarkMode(newValue)
}

// Watch for route changes and apply dark mode
import { useRoute } from 'vue-router'
const route = useRoute()

watch(() => route.path, () => {
  // Route change হলে current theme apply করুন
  const currentTheme = localStorage.getItem('theme') === 'dark'
  applyDarkMode(currentTheme)
})

onMounted(() => {
  // Initialize dark mode based on localStorage or system preference
  const initialDarkMode = getInitialTheme()
  emit('update:darkMode', initialDarkMode)
  applyDarkMode(initialDarkMode)
  
  // System preference change listener
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem('theme')) {
      const newDarkMode = e.matches
      emit('update:darkMode', newDarkMode)
      applyDarkMode(newDarkMode)
    }
  })
})
</script>

<style>
body.dark-mode {
  background-color: #000;
}

/* Ensure dark mode styles persist */
html.dark-mode {
  filter: invert(1) hue-rotate(180deg);
}

html.dark-mode img,
html.dark-mode video,
html.dark-mode iframe {
  filter: invert(1) hue-rotate(180deg);
}
</style>