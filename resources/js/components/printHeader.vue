<template>
  <div class="relative">
    <!-- Header -->
    <div class="flex pb-3 border-b-1 border-gray-900 print:pb-2 print:mb-2">
      <!-- Left - Logo -->
      <div class="w-1/7 flex items-center p-2">
        <img 
          :src="schoolLogo" 
          :alt="schoolSettings?.school_name || 'School Logo'" 
          class="h-20 object-contain"
        />
      </div>
      
      <!-- Center - School Info -->
      <div class="w-5/7 text-center">
        <h1 class="text-3xl font-bold text-gray-900 print:text-1xl">
          {{ schoolSettings?.school_name || 'SCHOOL NAME' }}
        </h1>
        <div class="mt-1 text-sm text-gray-600 print:mt-1 print:text-xs">
          <p v-if="schoolSettings?.mobile_number || schoolSettings?.email" class="flex justify-center gap-3">
            <span v-if="schoolSettings?.mobile_number">
              Phone: {{ schoolSettings.mobile_number }}
            </span>
            <span v-if="schoolSettings?.email">
              | Email: {{ schoolSettings.email }}
            </span>
          </p>
          <!-- Address -->
          <p class="text-sm mt-1">
            {{ schoolSettings?.address || 'School Address' }}
          </p>
        </div>
      </div>

      
      <!-- Right - Empty space for symmetry -->
      <div class="w-1/7"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useSchoolStore } from '@/stores/schoolStore'

const schoolStore = useSchoolStore()

// Fetch school settings on component mount
onMounted(async () => {
  await schoolStore.fetchSchoolSettings()
})

// Computed properties for reactive school data
const schoolSettings = computed(() => schoolStore.settings)

const schoolLogo = computed(() => {
  if (schoolSettings.value?.logo_url) {
    return schoolSettings.value.logo_url
  }
  // Fallback to default logo
  return '/assets/favicon.png'
})
</script>

<style scoped>
/* Print-specific styles */
@media print {
  .print\:pb-2 {
    padding-bottom: 0.5rem;
  }
  .print\:mb-2 {
    margin-bottom: 0.5rem;
  }
  .print\:text-1xl {
    font-size: 1.5rem;
  }
  .print\:mt-1 {
    margin-top: 0.25rem;
  }
  .print\:text-xs {
    font-size: 0.75rem;
  }
  .print\:space-y-0 > * + * {
    margin-top: 0;
  }
}
</style>