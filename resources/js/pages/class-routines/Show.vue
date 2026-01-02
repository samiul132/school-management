<template>
  <AppLayout>
    <!-- Page Header (No Print) -->
    <div class="mb-2 no-print">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-2">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Class Routine Details</h1>
          <p class="text-xs text-gray-600 mt-1">
            <i class="fas fa-info-circle"></i> View complete class routine information
          </p>
        </div>
        <div class="flex gap-2">
          <button 
            @click="printRoutine"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-white bg-green-600 hover:bg-green-700 font-medium transition-colors cursor-pointer"
          >
            <i class="fas fa-print"></i>
            Print
          </button>
          <router-link 
            :to="`/class-routines/${routineId}/edit`"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-white bg-blue-600 hover:bg-blue-700 font-medium transition-colors"
          >
            <i class="fas fa-edit"></i>
            Edit
          </router-link>
          <router-link 
            to="/class-routines"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-gray-100 bg-blue-600 hover:bg-blue-700 font-medium transition-colors"
          >
            <i class="fas fa-arrow-left"></i>
            Back To List
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow border border-gray-100 p-6 no-print">
      <div class="flex flex-col items-center justify-center">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-3"></i>
        <p class="text-sm text-gray-600">Loading routine...</p>
      </div>
    </div>

    <!-- Printable Content -->
    <div v-else id="printable-content" class="routine-landscape-print">
      <printHeader/>

      <!-- Basic Information -->
      <div class="bg-white flex justify-center items-center border-b-4 border-double border-black" style="width: fit-content; margin: 0 auto;">
        <div class="px-2 py-1.5">
          <p class="text-sm font-bold text-gray-800 text-center">
            {{ routine.version?.version_name }},
            {{ routine.class?.class_name }},
            {{ routine.section?.section_name }}
          </p>
        </div>
      </div>

      <!-- Routine Table -->
      <div class="bg-white p-3">
        <div class="overflow-x-auto">
          <table class="routine-table w-full border-collapse">
            <thead>
              <!-- Period Numbers Row -->
              <tr>
                <th class="border border-gray-600 px-2 py-2 text-center text-sm font-bold" rowspan="2">
                  Day
                </th>
                <th 
                  v-for="period in routine.number_of_periods" 
                  :key="`period-${period}`"
                  class="border border-gray-600 px-2 py-2 text-center text-sm font-bold"
                >
                  {{ getOrdinal(period) }} Period
                </th>
              </tr>
              <!-- Time Row -->
              <tr>
                <th 
                  v-for="period in routine.number_of_periods" 
                  :key="`time-${period}`"
                  class="border border-gray-600 px-2 py-1 text-center text-xs"
                >
                  {{ getPeriodTime(period) || '-' }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="day in activeDays" 
                :key="day"
              >
                <td class="border border-gray-600 px-2 py-3 font-bold text-sm text-gray-800">
                  {{ day }}
                </td>
                <td 
                  v-for="period in routine.number_of_periods" 
                  :key="`${day}-${period}`"
                  class="border border-gray-600 px-2 py-3"
                >
                  <div v-if="getRoutineDetail(day, period)" class="space-y-1">
                    <div class="text-sm font-semibold text-gray-900 text-center">
                      {{ getRoutineDetail(day, period).subject?.subject_name }}
                    </div>
                    <div class="text-xs text-gray-600 text-center">
                      {{ getRoutineDetail(day, period).teacher?.first_name }} 
                      {{ getRoutineDetail(day, period).teacher?.last_name }}
                    </div>
                  </div>
                  <div v-else class="text-xs text-gray-400 italic text-center">
                    -
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showErrorAlert } from '../../utils/sweetAlert'
import printHeader from '../../components/printHeader.vue'

const router = useRouter()
const route = useRoute()

const loading = ref(true)
const routine = ref({})
const routineId = ref(null)
const schoolInfo = ref({
  name: 'Your School Name',
  address: 'Your School Address'
})

const days = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']

const activeDays = computed(() => {
  if (!routine.value.off_day) {
    return days
  }
  
  const offDays = routine.value.off_day.split(',').map(day => day.trim())
  return days.filter(day => !offDays.includes(day))
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
})

const loadRoutineData = async () => {
  try {
    loading.value = true
    const id = route.params.id
    routineId.value = id

    const response = await axios.get(`/api/class-routines/${id}`)
    routine.value = response.data.data
    
    if (routine.value.number_of_periods) {
      routine.value.number_of_periods = parseInt(routine.value.number_of_periods)
    }
    
    loading.value = false
  } catch (error) {
    console.error('Failed to load routine data:', error)
    showErrorAlert('Error', 'Failed to load class routine details')
    router.push('/class-routines')
  }
}

const getRoutineDetail = (day, period) => {
  if (!routine.value.details) return null
  
  return routine.value.details.find(detail => 
    detail.day_name === day && parseInt(detail.period_number) === period
  )
}

const getPeriodTime = (period) => {
  const detail = routine.value.details?.find(d => parseInt(d.period_number) === period)
  return detail?.time || ''
}

const getOrdinal = (num) => {
  const ordinals = {
    1: '1st',
    2: '2nd', 
    3: '3rd',
    4: '4th',
    5: '5th',
    6: '6th',
    7: '7th',
    8: '8th',
    9: '9th',
    10: '10th'
  }
  return ordinals[num] || `${num}th`
}

const printRoutine = () => {
  document.body.classList.add('printing-routine')
  window.print()
  setTimeout(() => {
    document.body.classList.remove('printing-routine')
  }, 1000)
}

onMounted(() => {
  loadRoutineData()
})
</script>

<style scoped>

.routine-table {
  min-width: 800px;
  border-collapse: collapse;
}

.routine-table td,
.routine-table th {
  min-width: 120px;
  border: 1px solid #666;
  padding: 8px;
  background: white;
  color: #000;
}

.routine-table th {
  font-weight: 700;
  text-align: center;
  background: white;
}

.routine-table td:first-child {
  font-weight: 700;
  text-align: center;
  vertical-align: middle;
  background: white;
}

.routine-table tbody td {
  vertical-align: top;
}

.fa-spinner.fa-spin {
  animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.print-header,
.print-footer {
  display: none;
}

/* Print Styles - Landscape Mode */
@media print {
  @page {
    size: landscape !important;
    margin: 5mm !important;
  }

  
  body.printing-routine {
    width: 297mm !important;
    height: 210mm !important;
  }

  .routine-landscape-print {
    page: landscape-routine;
    width: 100% !important;
  }

  #printable-content {
    page-break-inside: avoid !important;
    page-break-after: avoid !important;
    page-break-before: avoid !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
  }

  #printable-content > div {
    page-break-inside: avoid !important;
    page-break-after: avoid !important;
  }

  #printable-content > div:last-child {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
  }

  html, body {
    height: 100% !important;
    max-height: 100% !important;
    overflow: hidden !important;
  }

  body {
    margin: 0 !important;
    padding: 0 !important;
  }

  body * {
    max-height: 100% !important;
  }

  /* Hide UI Elements */
  .no-print,
  header,
  nav,
  aside,
  footer,
  button,
  .sidebar,
  .topbar,
  .navbar,
  .breadcrumb,
  [role="navigation"],
  [class*="sidebar"],
  [class*="topbar"],
  .dark-theme-toggle,
  .fixed,
  .sticky {
    display: none !important;
    visibility: hidden !important;
  }

  /* Show Print Elements */
  .print-header,
  .print-footer {
    display: block !important;
  }

  .print-header {
    margin-bottom: 4px !important;
  }

  .print-header h1 {
    font-size: 18px !important;
    margin: 0 !important;
    font-weight: 700 !important;
    color: #000 !important;
  }

  .print-header p {
    font-size: 16px !important;
    margin: 0 !important;
    color: #000 !important;
  }

  .print-header h2 {
    font-size: 18px !important;
    margin: 1px 0 2px 0 !important;
    color: #000 !important;
  }

  .print-footer {
    margin-top: 4px !important;
    page-break-inside: avoid !important;
    page-break-after: avoid !important;
  }

  .print-footer .flex {
    padding-top: 2px !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
  }

  .print-footer p {
    font-size: 16px !important;
    color: #000 !important;
    margin: 0 !important;
  }

  /* Full Width Layout */
  main,
  .main-content,
  [class*="ml-"],
  [class*="px-"],
  [class*="pt-"],
  [class*="pb-"],
  [class*="mx-"],
  .max-w-7xl {
    margin: 0 !important;
    padding: 0 !important;
    max-width: 100% !important;
  }

  /* Remove Decorative Styling */
  .bg-white,
  .shadow,
  .rounded-lg,
  .rounded {
    box-shadow: none !important;
    border-radius: 0 !important;
    background: white !important;
  }

  .border:not(.routine-table):not(.routine-table *),
  .border-gray-100,
  .border-blue-200,
  .border-purple-200,
  .border-orange-200,
  .border-green-200,
  .border-pink-200,
  .border-indigo-200 {
    border: none !important;
  }

  .border-gray-300 {
    border-color: #666 !important;
  }

  /* Compact Spacing */
  .p-3 {
    padding: 1px !important;
  }

  .mb-3,
  .mb-2 {
    margin-bottom: 2px !important;
  }

  .gap-2 {
    gap: 1px !important;
  }

  /* Hide Icons and Headers */
  .flex.items-center.gap-2.mb-2,
  .flex.items-center.gap-2.mb-3,
  .w-8.h-8,
  .fas,
  .fa,
  i {
    display: none !important;
  }

  /* Basic Info Cards */
  .grid.grid-cols-2,
  .grid.md\:grid-cols-6 {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    gap: 2px !important;
    margin-bottom: 2px !important;
  }

  .bg-blue-50,
  .bg-purple-50,
  .bg-orange-50,
  .bg-green-50,
  .bg-pink-50,
  .bg-indigo-50 {
    padding: 1px 3px !important;
    margin: 0 !important;
    background: white !important;
    border: none !important;
    border-right: 1px solid #ddd !important;
    flex: 1 !important;
    min-width: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    border-radius: 0 !important;
  }

  .bg-indigo-50 {
    border-right: none !important;
  }

  .bg-blue-50 p,
  .bg-purple-50 p,
  .bg-orange-50 p,
  .bg-green-50 p,
  .bg-pink-50 p,
  .bg-indigo-50 p {
    font-size: 12px !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1 !important;
    color: #000 !important;
  }

  .bg-blue-50 p:first-child,
  .bg-purple-50 p:first-child,
  .bg-orange-50 p:first-child,
  .bg-green-50 p:first-child,
  .bg-pink-50 p:first-child,
  .bg-indigo-50 p:first-child {
    font-weight: 400 !important;
  }

  .bg-blue-50 p:last-child,
  .bg-purple-50 p:last-child,
  .bg-orange-50 p:last-child,
  .bg-green-50 p:last-child,
  .bg-pink-50 p:last-child,
  .bg-indigo-50 p:last-child {
    font-weight: 700 !important;
  }
  @media print {
    /* Time Row - Smaller Font */
    .routine-table thead tr:nth-child(2) th {
      font-size: 11px !important;
      padding: 2px 3px !important;
      line-height: 1.2 !important;
      white-space: nowrap !important;
    }
  }

  /* Routine Table */
  .routine-table {
    page-break-inside: avoid;
    width: 100% !important;
    table-layout: fixed !important;
    font-size: 16px !important;
    border-collapse: collapse !important;
    min-width: unset !important;
  }

  .routine-table td,
  .routine-table th {
    min-width: unset !important;
    width: auto !important;
  }

  .routine-table th {
    padding: 3px 4px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    border: 1px solid #666 !important;
    background: white !important;
    color: #000 !important;
    white-space: nowrap !important;
    line-height: 1.4 !important;
  }

  .routine-table td {
    padding: 5px 4px !important;
    font-size: 14px !important;
    border: 1px solid #666 !important;
    line-height: 1.3 !important;
    background: white !important;
    color: #000 !important;
    vertical-align: top !important;
    min-height: 40px !important;
  }

  .routine-table tbody td div {
    line-height: 1.3 !important;
    margin: 0 !important;
    padding: 0 !important;
    min-height: 35px !important;
  }

  .routine-table tbody td div.space-y-1 {
    gap: 2px !important;
    display: block !important;
  }

  /* Subject Name - Centered */
  .routine-table tbody td div .text-sm {
    font-size: 16px !important;
    font-weight: 700 !important;
    color: #000 !important;
    line-height: 1.4 !important;
    display: block !important;
    margin-bottom: 2px !important;
    text-align: center !important;
  }

  /* Teacher Name - Centered */
  .routine-table tbody td div .text-xs {
    font-size: 14px !important;
    font-weight: 600 !important;
    color: #000 !important;
    line-height: 1.3 !important;
    display: block !important;
    text-align: center !important;
  }

  /* Day Column */
  .routine-table td:first-child,
  .routine-table th:first-child {
    width: 100px !important;
    min-width: 100px !important;
    max-width: 120px !important;
    font-weight: 700 !important;
    font-size: 16px !important;
    background: white !important;
    color: #000 !important;
    text-align: center !important;
    vertical-align: middle !important;
  }

  /* Period Columns */
  .routine-table th:not(:first-child),
  .routine-table td:not(:first-child) {
    width: calc((100% - 100px) / 8) !important;
  }

  /* Remove Gradients and Colors */
  .bg-gradient-to-r,
  .from-blue-600,
  .to-indigo-600,
  .bg-blue-600,
  .bg-indigo-600,
  .bg-blue-500,
  .bg-gray-50,
  .bg-blue-100,
  .bg-green-100 {
    background: white !important;
    color: #000 !important;
  }

  .hover\:bg-gray-50:hover,
  .hover\:bg-blue-700:hover,
  .hover\:bg-green-700:hover {
    background: white !important;
  }

  /* All Text Black */
  .text-white,
  .text-gray-800,
  .text-gray-600,
  .text-gray-900,
  .text-gray-400,
  .text-blue-600,
  .text-green-600 {
    color: #000 !important;
  }

  #printable-content {
    margin: 0 !important;
    padding: 0 !important;
  }

  #printable-content > div {
    margin-bottom: 2px !important;
    page-break-inside: avoid;
  }

  .overflow-x-auto {
    overflow: visible !important;
  }

  .space-y-1 {
    gap: 0 !important;
  }

  * {
    margin-block-start: 0 !important;
    margin-block-end: 0 !important;
  }
}
</style>