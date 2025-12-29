<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Edit Waiver
        </h1>
      </div>
      
      <div class="flex items-center gap-1">
        <button 
          @click="goBack"
          class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-gray-100 text-sm rounded-lg font-semibold transition-colors flex items-center gap-1 cursor-pointer"
        >
          <i class="fas fa-arrow-left text-xs"></i>
          Back to List
        </button>
      </div>
    </div>

    <!-- Student Information Section -->
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden mb-1">

      <!-- Student Details -->
      <div class="p-2">
        <div v-if="selectedStudent" class="flex flex-col md:flex-row gap-3 items-start md:items-center">
          <!-- Student Image -->
          <div class="shrink-0">
            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow">
              <img 
                v-if="selectedStudent && selectedStudent.student_image_url && isImageUrlValid(selectedStudent.student_image_url)"
                :src="selectedStudent.student_image_url" 
                :alt="selectedStudent.name"
                class="w-full h-full object-cover"
                @error="handleImageError"
              >
              <div v-else class="w-full h-full bg-linear-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                <i class="fas fa-user-graduate text-blue-400 text-2xl"></i>
              </div>
            </div>
          </div>

          <!-- Student Info -->
          <div class="flex-1">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2">
              <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                  <i class="fas fa-user text-xs"></i>
                  Student Name
                </div>
                <div class="font-semibold text-gray-800 text-sm">{{ selectedStudent.name }}</div>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                  <i class="fas fa-id-card text-xs"></i>
                  Student ID
                </div>
                <div class="font-semibold text-gray-800 text-sm">{{ selectedStudent.id_card_number || 'N/A' }}</div>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                  <i class="fas fa-sort-numeric-up text-xs"></i>
                  Class Roll
                </div>
                <div class="font-semibold text-gray-800">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ selectedStudent.roll }}
                  </span>
                </div>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                  <i class="fas fa-phone text-xs"></i>
                  Mobile
                </div>
                <div class="font-semibold text-gray-800 text-sm">{{ selectedStudent.mobile_number || 'N/A' }}</div>
              </div>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-4">
          <div class="inline-block p-3">
            <i class="fas fa-user-graduate text-3xl text-gray-400 mb-2"></i>
            <p class="text-gray-600 text-sm">No student data available</p>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-100 p-2 sticky top-2">
      <!-- Months Selection -->
      <div class="lg:col-span-4 mb-2">
        <div>
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-1">
              <div class="p-1.5 bg-blue-100 rounded-lg">
                <i class="fas fa-calendar-alt text-blue-600 text-sm"></i>
              </div>
              <h3 class="text-sm font-semibold text-gray-800">Select Month</h3>
            </div>
            
            <div class="flex items-center gap-1">
              <!-- Select All Button -->
              <button 
                @click="toggleSelectAllMonths"
                :class="[
                  'px-3 py-1.5 rounded-lg font-medium text-xs transition-colors flex items-center gap-1 cursor-pointer',
                  areAllMonthsSelected 
                    ? 'bg-blue-600 text-white hover:bg-blue-700' 
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                <i class="fas text-xs" :class="areAllMonthsSelected ? 'fa-check-square' : 'fa-square'"></i>
                {{ areAllMonthsSelected ? 'Deselect All' : 'Select All' }}
              </button>
            </div>
          </div>
          
          <div class="grid grid-cols-2 sm:grid-cols-6 lg:grid-cols-6 gap-2">
            <div 
              v-for="month in months" 
              :key="month.id"
              :class="[
                'flex items-center p-2 rounded-lg transition cursor-pointer',
                isMonthSelected(month.id) 
                  ? 'bg-blue-50' 
                  : 'bg-gray-50 hover:bg-gray-100'
              ]"
              @click="toggleMonthSelection(month.id)"
            >
              <input 
                type="checkbox" 
                :id="'month-' + month.id"
                v-model="selectedMonthIds"
                :value="month.id"
                class="h-3.5 w-3.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                @click.stop
              >
              <label 
                :for="'month-' + month.id"
                class="ml-1.5 text-gray-700 text-sm cursor-pointer flex-1 font-medium"
                @click.stop
              >
                {{ month.month_name }}
              </label>

            </div>
          </div>
          
          <div class="mt-1 pt-1 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-1">
              <div class="text-xs">
                <span class="text-gray-600">Selected Months:</span>
                <span v-if="selectedMonthNames.length > 0" class="ml-1 font-semibold text-blue-600">
                  {{ selectedMonthNames.join(', ') }}
                </span>
                <span v-else class="ml-1 text-gray-400">
                  Not selected
                </span>
              </div>
              
              <div class="text-xs">
                <span class="text-gray-600">Total Selected:</span>
                <span class="ml-1 font-semibold" :class="selectedMonthIds.length > 0 ? 'text-green-600' : 'text-gray-400'">
                  {{ selectedMonthIds.length }} / {{ months.length }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Fee Heads Table -->
      <div class="lg:col-span-4">
        <div class="overflow-hidden">
          <div class="px-2 py-1.5 border-b border-gray-200 bg-linear-to-r from-gray-50 to-white">
            <div class="flex items-center gap-2">
              <div class="p-1.5 bg-blue-100 rounded-lg">
                <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800">Fee Heads and Waiver Amounts</h3>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase w-12">
                    SL
                  </th>
                  <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase">
                    Fee Head
                  </th>
                  <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase w-40">
                    Waiver Type
                  </th>
                  <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase w-48">
                    Amount / Percentage
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr 
                  v-for="(head, index) in feeHeads" 
                  :key="head.id" 
                  :class="[
                    'hover:bg-gray-50 transition-colors',
                    isFeeHeadSelected(head.id) ? 'bg-blue-50' : ''
                  ]"
                >
                  <td class="px-2 py-2 whitespace-nowrap">
                    <div class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100">
                      <span class="text-xs font-medium text-gray-700">{{ index + 1 }}</span>
                    </div>
                  </td>
                  
                  <td class="px-2 py-2">
                    <div class="flex items-center gap-2">
                      <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-green-600 text-sm"></i>
                      </div>
                      <div>
                        <div class="text-sm font-semibold text-gray-900">{{ head.head_name }}</div>
                      </div>
                    </div>
                  </td>
                  
                  <td class="px-2 py-2 whitespace-nowrap">
                    <div class="flex flex-col space-y-1">
                      <!-- Fixed Amount Option -->
                      <label class="inline-flex items-center cursor-pointer">
                        <input 
                          type="radio" 
                          :name="'waver-type-' + head.id"
                          value="fixed"
                          v-model="selectedWaiverTypes[head.id]"
                          class="h-3.5 w-3.5 text-blue-600 focus:ring-blue-500 border-gray-300"
                        >
                        <span class="ml-1.5 text-xs text-gray-700">Fixed Amount (৳)</span>
                      </label>
                      
                      <!-- Percentage Option -->
                      <label class="inline-flex items-center cursor-pointer">
                        <input 
                          type="radio" 
                          :name="'waver-type-' + head.id"
                          value="percentage"
                          v-model="selectedWaiverTypes[head.id]"
                          class="h-3.5 w-3.5 text-blue-600 focus:ring-blue-500 border-gray-300"
                        >
                        <span class="ml-1.5 text-xs text-gray-700">Percentage (%)</span>
                      </label>
                    </div>
                  </td>
                  
                  <td class="px-2 py-2 whitespace-nowrap">
                    <div class="relative">
                      <div class="flex items-center gap-1">
                        <input 
                          type="number" 
                          v-model="waiverAmounts[head.id]"
                          :placeholder="selectedWaiverTypes[head.id]?.type === 'percentage' ? '0-100%' : 'Amount in ৳'"
                          :min="0"
                          :max="selectedWaiverTypes[head.id]?.type === 'percentage' ? 100 : undefined"
                          step="0.01"
                          :disabled="!selectedWaiverTypes[head.id]"
                          class="pl-2 pr-8 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 w-full disabled:bg-gray-50 disabled:text-gray-500"
                        />
                        <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                          <span class="text-gray-500 text-sm font-medium">
                            {{ selectedWaiverTypes[head.id]?.type === 'percentage' ? '%' : '৳' }}
                          </span>
                        </div>
                      </div>
                      <div 
                        v-if="selectedWaiverTypes[head.id]?.type === 'percentage'" 
                        class="text-xs text-gray-500 mt-0.5"
                      >
                        Enter percentage (0-100)
                      </div>
                    </div>
                  </td>
                  
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-3 pt-3 border-t border-gray-200">
            <div v-if="validationError" class="mb-2 p-2 bg-red-50 border border-red-200 rounded-lg">
              <div class="flex items-center gap-1 text-red-700 text-xs">
                <i class="fas fa-exclamation-circle text-xs"></i>
                <span>{{ validationError }}</span>
              </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
              <div class="text-xs text-gray-600">
                <div class="flex items-center gap-1">
                  <i class="fas fa-info-circle text-blue-500 text-xs"></i>
                  <span>Total fee heads with waiver: <span class="font-semibold">{{ getSelectedFeeHeadsCount() }}</span></span>
                </div>
              </div>
              
              <div class="flex items-center gap-2">
                <button 
                  @click="resetAll"
                  class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-colors flex items-center gap-1"
                >
                  <i class="fas fa-redo text-xs"></i>
                  Reset
                </button>
                
                <button 
                  @click="goBack"
                  class="px-3 py-1.5 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition-colors flex items-center gap-1"
                >
                  <i class="fas fa-times text-xs"></i>
                  Cancel
                </button>
                
                <button 
                  @click="updateOrCreateWaivers"
                  :disabled="isUpdating || !canUpdate"
                  class="px-3 py-1.5 text-sm bg-linear-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-1 shadow disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i v-if="isUpdating" class="fas fa-spinner fa-spin text-xs"></i>
                  <i v-else class="fas fa-save text-xs"></i>
                  {{ isUpdating ? 'Processing...' : 'Update Waivers' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'
import AppLayout from '../../Layouts/AppLayout.vue'

const router = useRouter()
const route = useRoute()

// Data
const selectedStudent = ref(null)
const existingWavers = ref({})
const months = ref([])
const feeHeads = ref([])
const selectedMonthIds = ref([])
const selectedWaiverTypes = ref({})
const waiverAmounts = ref({})
const validationError = ref('')
const isUpdating = ref(false)
const dropdownData = ref({})
const classWiseStudentDataId = ref(null)
const defaultWaiverType = ref('fixed')

// Computed properties
const selectedMonthNames = computed(() => {
  return months.value
    .filter(month => selectedMonthIds.value.includes(month.id))
    .map(month => month.month_name)
})

const areAllMonthsSelected = computed(() => {
  return months.value.length > 0 && selectedMonthIds.value.length === months.value.length
})

const canUpdate = computed(() => {
  if (!selectedStudent.value) return false
  if (selectedMonthIds.value.length === 0) return false
  return getSelectedFeeHeadsCount() > 0
})

const isStudentEditMode = computed(() => {
  return route.params.class_wise_student_id !== undefined
})

// New method to handle waiver type change
const handleWaiverTypeChange = async (headId, newType) => {
  if (newType === null) {
    // Type unselected - clear amount and delete existing waivers
    waiverAmounts.value[headId] = ''
    
    // Delete existing waivers for this fee head across all selected months
    await deleteExistingWaiversForHead(headId)
  } else {
    // Type selected - just clear the amount field
    waiverAmounts.value[headId] = ''
  }
}

// Method to delete existing waivers for a specific fee head
const deleteExistingWaiversForHead = async (headId) => {
  try {
    const monthsToDelete = []
    
    // Find all months where this head has existing waivers
    for (const monthId in existingWavers.value) {
      if (existingWavers.value[monthId]?.[headId]) {
        monthsToDelete.push({
          monthId: parseInt(monthId),
          waverId: existingWavers.value[monthId][headId]
        })
      }
    }
    
    // Delete each existing waiver
    for (const { monthId, waverId } of monthsToDelete) {
      try {
        await axios.delete(`/api/wavers/${waverId}`)
        console.log(`Deleted waiver ${waverId} for head ${headId}`)
        
        // Remove from existingWavers object
        if (existingWavers.value[monthId]) {
          delete existingWavers.value[monthId][headId]
          
          // If month object becomes empty, remove it
          if (Object.keys(existingWavers.value[monthId]).length === 0) {
            delete existingWavers.value[monthId]
          }
        }
      } catch (error) {
        console.error(`Failed to delete waiver ${waverId}:`, error)
      }
    }
    
    if (monthsToDelete.length > 0) {
      console.log(`Deleted ${monthsToDelete.length} waiver(s) for fee head ${headId}`)
    }
  } catch (error) {
    console.error('Error deleting waivers for head:', error)
  }
}

// Update clearWaiverAmount method to also handle type unselection
const clearWaiverAmount = async (headId) => {
  waiverAmounts.value[headId] = ''
  
  // If amount is cleared and type is null, delete existing waivers
  if (!selectedWaiverTypes.value[headId]) {
    await deleteExistingWaiversForHead(headId)
  }
}

// Methods
const loadWaiverData = async () => {
  try {
    showLoadingAlert('Loading', 'Loading waiver data...')
    
    if (isStudentEditMode.value) {
      classWiseStudentDataId.value = route.params.class_wise_student_id
      await loadStudentWavers()
    } else {
      await loadSingleWaiver()
    }
    
    closeAlert()
    
  } catch (error) {
    console.error('Error loading waiver:', error)
    closeAlert()
    showErrorAlert('Error', 'Failed to load waiver data')
    goBack()
  }
}

const loadStudentWavers = async () => {
  const response = await axios.get(`/api/wavers/student/${classWiseStudentDataId.value}/edit-form`)
  
  if (response.data.success) {
    const data = response.data.data
    
    // Fix student image URL
    selectedStudent.value = {
      ...data.student,
      student_image_url: data.student.student_image_url 
        ? getFullImageUrl(data.student.student_image_url)
        : null
    }
    
    await fetchDropdownData()
    
    for (const [monthId, monthData] of Object.entries(data.wavers)) {
      if (!selectedMonthIds.value.includes(parseInt(monthId))) {
        selectedMonthIds.value.push(parseInt(monthId))
      }
      
      for (const [headId, waverData] of Object.entries(monthData.fee_heads)) {
        const headIdInt = parseInt(headId)
        
        if (!existingWavers.value[monthId]) {
          existingWavers.value[monthId] = {}
        }
        existingWavers.value[monthId][headId] = waverData.id
        
        selectedWaiverTypes.value[headIdInt] = waverData.waver_type
        waiverAmounts.value[headIdInt] = parseFloat(waverData.waver_amount)
      }
    }
  } else {
    throw new Error('Failed to load student waivers')
  }
}

// Image URL helper functions
const getFullImageUrl = (url) => {
  if (!url) return null
  
  const baseUrl = window.location.origin
  
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url
  }
  
  let fileName = url
  if (url.includes('/')) {
    fileName = url.split('/').pop()
  }
  
  const actualPath = `assets/images/student_images/${fileName}`
  
  return `${baseUrl}/${actualPath}`
}
const isImageUrlValid = (url) => {
  if (!url) return false
  const trimmedUrl = url.toString().trim()
  return trimmedUrl.length > 0
}

const handleImageError = (event) => {
  console.error('Image failed to load:', event.target.src)
  // Hide the image and show fallback
  event.target.style.display = 'none'
}


const loadSingleWaiver = async () => {
  const response = await axios.get(`/api/wavers/${route.params.id}`)
  
  if (response.data.success) {
    const waiver = response.data.data
    
    selectedStudent.value = {
      // ... existing student data ...
    }
    
    await fetchDropdownData()
    
    if (waiver.month_id) {
      selectedMonthIds.value = [waiver.month_id]
    }
    
    if (waiver.head_id) {
      selectedWaiverTypes.value[waiver.head_id] = waiver.waver_type
      waiverAmounts.value[waiver.head_id] = parseFloat(waiver.waver_amount)
      
      if (!existingWavers.value[waiver.month_id]) {
        existingWavers.value[waiver.month_id] = {}
      }
      existingWavers.value[waiver.month_id][waiver.head_id] = waiver.id
    }
  }
}

const fetchDropdownData = async () => {
  try {
    const response = await axios.get('/api/wavers/dropdown-data')
    if (response.data.success) {
      dropdownData.value = response.data.data
      months.value = response.data.data.months || []
      feeHeads.value = response.data.data.fee_heads || []
      
      feeHeads.value.forEach(head => {
        if (selectedWaiverTypes.value[head.id] === undefined) {
          selectedWaiverTypes.value[head.id] = defaultWaiverType.value
          waiverAmounts.value[head.id] = ''
        }
      })
    }
  } catch (error) {
    showErrorAlert('Error', 'Failed to load dropdown data')
  }
}

const getSelectedFeeHeadsCount = () => {
  return feeHeads.value.filter(head => isFeeHeadSelected(head.id)).length
}

// Update isFeeHeadSelected to handle null type
const isFeeHeadSelected = (headId) => {
  const type = selectedWaiverTypes.value[headId]
  const amount = waiverAmounts.value[headId]
  
  // Only consider selected if type is not null and amount is valid
  return type && type !== 'null' && amount && parseFloat(amount) > 0 &&
         (type !== 'percentage' || parseFloat(amount) <= 100)
}

const isMonthSelected = (monthId) => {
  return selectedMonthIds.value.includes(monthId)
}

const toggleMonthSelection = (monthId) => {
  const index = selectedMonthIds.value.indexOf(monthId)
  if (index === -1) {
    selectedMonthIds.value.push(monthId)
  } else {
    selectedMonthIds.value.splice(index, 1)
  }
}

const toggleSelectAllMonths = () => {
  if (areAllMonthsSelected.value) {
    selectedMonthIds.value = []
  } else {
    selectedMonthIds.value = months.value.map(month => month.id)
  }
}

const resetAll = () => {
  selectedMonthIds.value = []
  feeHeads.value.forEach(head => {
    selectedWaiverTypes.value[head.id] = null
    waiverAmounts.value[head.id] = ''
  })
  validationError.value = ''
  showSuccessAlert('Reset', 'All selections have been reset')
}

const deleteDeselectedMonthWaivers = async () => {
  if (!existingWavers.value || Object.keys(existingWavers.value).length === 0) {
    return
  }

  const existingMonthIds = Object.keys(existingWavers.value).map(id => parseInt(id))
  const deselectedMonthIds = existingMonthIds.filter(
    monthId => !selectedMonthIds.value.includes(monthId)
  )

  if (deselectedMonthIds.length === 0) {
    return
  }

  for (const monthId of deselectedMonthIds) {
    const monthWaivers = existingWavers.value[monthId]
    
    if (monthWaivers && typeof monthWaivers === 'object') {
      for (const headId in monthWaivers) {
        const waverId = monthWaivers[headId]
        
        if (waverId) {
          try {
            await axios.delete(`/api/wavers/${waverId}`)
            console.log(`Deleted waiver ${waverId} for month ${monthId}, head ${headId}`)
          } catch (error) {
            console.error(`Failed to delete waiver ${waverId}:`, error)
          }
        }
      }
    }
    
    delete existingWavers.value[monthId]
  }
}

const updateOrCreateWaivers = async () => {
  if (!canUpdate.value) {
    validationError.value = 'Please select at least one fee head with valid waiver amount'
    return
  }

  validationError.value = ''
  isUpdating.value = true

  showLoadingAlert('Please wait', 'Processing waivers...')

  try {
    // First, delete waivers for heads with null type
    await deleteWaiversForNullTypes()
    
    const validFeeHeads = feeHeads.value.filter(head => isFeeHeadSelected(head.id))
    
    if (validFeeHeads.length === 0 || selectedMonthIds.value.length === 0) {
      validationError.value = 'Please select at least one fee head and month'
      isUpdating.value = false
      closeAlert()
      return
    }

    // Step 1: Delete deselected months এর waivers
    await deleteDeselectedMonthWaivers()

    const results = []
    const errors = []
    
    for (const selectedHead of validFeeHeads) {
      const type = selectedWaiverTypes.value[selectedHead.id]
      const amount = parseFloat(waiverAmounts.value[selectedHead.id])
      
      if (!type || !amount || amount <= 0) continue

      for (const monthId of selectedMonthIds.value) {
        try {
          const waiverData = {
            class_wise_student_id: selectedStudent.value.id,
            session_id: selectedStudent.value.session_id,
            version_id: selectedStudent.value.version_id,
            class_id: selectedStudent.value.class_id,
            section_id: selectedStudent.value.section_id,
            month_id: monthId,
            head_id: selectedHead.id,
            roll: selectedStudent.value.roll,
            waver_type: type,
            waver_amount: amount,
          }

          // Check if this combination has an existing waiver
          const existingWaverId = existingWavers.value[monthId]?.[selectedHead.id]

          if (existingWaverId) {
            // Update existing
            const response = await axios.put(`/api/wavers/${existingWaverId}`, waiverData)
            results.push({
              monthId,
              headId: selectedHead.id,
              action: 'updated',
              data: response.data
            })
          } else {
            // Create new
            const response = await axios.post('/api/wavers', waiverData)
            results.push({
              monthId,
              headId: selectedHead.id,
              action: response.data.action || 'created',
              data: response.data
            })
          }
          
          await new Promise(resolve => setTimeout(resolve, 100))
          
        } catch (error) {
          console.error(`Error for month ${monthId}, head ${selectedHead.id}:`, error)
          errors.push({
            monthId,
            headId: selectedHead.id,
            error: error.response?.data?.message || error.message
          })
        }
      }
    }

    closeAlert()
    
    const updatedCount = results.filter(r => r.action === 'updated').length
    const createdCount = results.filter(r => r.action === 'created').length
    const skippedCount = results.filter(r => r.action === 'skipped').length
    const errorCount = errors.length
    
    if (errorCount === 0) {
      let message = ''
      if (updatedCount > 0 && createdCount > 0) {
        message = `${updatedCount} waiver(s) updated, ${createdCount} new waiver(s) created`
      } else if (updatedCount > 0) {
        message = `${updatedCount} waiver(s) updated successfully`
      } else if (createdCount > 0) {
        message = `${createdCount} new waiver(s) created successfully`
      }
      
      if (skippedCount > 0) {
        message += `, ${skippedCount} already existed (skipped)`
      }
      
      showSuccessAlert('Success!', message)
      
      setTimeout(() => {
        router.push({ name: 'wavers.index' })
      }, 2000)
      
    } else {
      let successMessage = ''
      if (results.length > 0) {
        successMessage = `${results.length} waiver(s) processed successfully.\n`
      }
      if (errorCount > 0) {
        successMessage += `${errorCount} waiver(s) failed to process.`
      }
      
      showSuccessAlert('Partial Success', successMessage)
    }

  } catch (error) {
    closeAlert()
    showErrorAlert(
      'Error',
      error.response?.data?.message || 'Failed to process waivers. Please try again.'
    )
  } finally {
    isUpdating.value = false
  }
}

// New method to delete waivers for fee heads with null type
const deleteWaiversForNullTypes = async () => {
  try {
    const headsToDelete = []
    
    // Find all fee heads where type is null but have existing waivers
    for (const headId in selectedWaiverTypes.value) {
      if (selectedWaiverTypes.value[headId] === null) {
        // Check if this head has existing waivers
        for (const monthId in existingWavers.value) {
          if (existingWavers.value[monthId]?.[headId]) {
            headsToDelete.push({
              headId: parseInt(headId),
              monthId: parseInt(monthId),
              waverId: existingWavers.value[monthId][headId]
            })
          }
        }
      }
    }
    
    // Delete each existing waiver
    for (const { headId, monthId, waverId } of headsToDelete) {
      try {
        await axios.delete(`/api/wavers/${waverId}`)
        console.log(`Deleted waiver ${waverId} for head ${headId}, month ${monthId}`)
        
        // Remove from existingWavers object
        if (existingWavers.value[monthId]) {
          delete existingWavers.value[monthId][headId]
          
          // If month object becomes empty, remove it
          if (Object.keys(existingWavers.value[monthId]).length === 0) {
            delete existingWavers.value[monthId]
          }
        }
      } catch (error) {
        console.error(`Failed to delete waiver ${waverId}:`, error)
      }
    }
    
    if (headsToDelete.length > 0) {
      console.log(`Deleted ${headsToDelete.length} waiver(s) for null type fee heads`)
    }
  } catch (error) {
    console.error('Error deleting waivers for null types:', error)
  }
}

const goBack = () => {
  router.push({ name: 'wavers.index' })
}

// Lifecycle
onMounted(() => {
  loadWaiverData()
})
</script>

<style scoped>

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

table {
  min-width: 800px;
}

tbody tr {
  transition: all 0.2s ease;
}

tbody tr:hover {
  background-color: #f8fafc;
}

.fa-spinner.fa-spin {
  animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

input[type="radio"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3ccircle cx='8' cy='8' r='3'/%3e%3c/svg%3e");
  background-size: 1em 1em;
}

.cursor-pointer {
  cursor: pointer;
}

/* Custom checkbox styling */
input[type="checkbox"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
  background-size: 1em 1em;
}
</style>