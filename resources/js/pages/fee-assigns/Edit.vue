<template>
  <AppLayout>
    <!-- Page Header -->
    <div class="mb-2">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-2">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Edit Fee Assignment</h1>
          <p class="text-xs text-gray-600 mt-1">
            <i class="fas fa-info-circle"></i> Update existing fee assignment details
          </p>
        </div>
        <router-link 
          to="/fee-assigns"
          class="inline-flex items-center gap-2 px-3 py-1.5 text-sm border border-gray-300 rounded-lg text-gray-100 bg-blue-600 hover:bg-blue-700 font-medium transition-colors"
        >
          <i class="fas fa-arrow-left"></i>
          Back to List
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
      <div class="flex flex-col items-center justify-center">
        <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
        <p class="text-gray-600">Loading fee assignment data...</p>
      </div>
    </div>

    <!-- Form Container -->
    <div v-else>
      <!-- Main Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow border border-gray-100"> 

          <!-- Form Body -->
          <div class="p-3">
            <!-- Basic Information (Read-only) -->
            <div class="mb-3">
              <h3 class="text-base font-medium text-gray-800 mb-2 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                Basic Information
              </h3>
              
              <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <!-- Session (Read-only) -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Session
                  </label>
                  <div class="px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded text-gray-900">
                    {{ getSelectedSessionName }}
                  </div>
                </div>

                <!-- Class (Read-only) -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Class
                  </label>
                  <div class="px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded text-gray-900">
                    {{ getSelectedClassName }}
                  </div>
                </div>

                <!-- Version (Read-only) -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Version
                  </label>
                  <div class="px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded text-gray-900">
                    {{ getSelectedVersionName }}
                  </div>
                </div>

                <!-- Month (Read-only) -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Month
                  </label>
                  <div class="px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded text-gray-900 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-purple-600"></i>
                    {{ getSelectedMonthName }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Fee Heads Table  -->
            <div class="mb-3">
              <div class="overflow-hidden">
                <div class="px-2 py-2 border-b border-gray-200 bg-linear-to-r from-gray-50 to-white">
                  <div class="flex items-center gap-2">
                      <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
                    <div>
                      <h3 class="text-base font-semibold text-gray-800">Fee Heads and Amounts</h3>
                    </div>
                  </div>
                </div>

                <div class="overflow-x-auto">
                  <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                          SL
                        </th>
                        <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          Fee Head
                        </th>
                        <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                          Amount (৳)
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                      <tr 
                        v-for="(head, index) in allFeeHeads" 
                        :key="head.id" 
                        :class="[
                          'hover:bg-gray-50 transition-colors',
                          isFeeHeadSelected(head.id) ? 'bg-blue-50' : ''
                        ]"
                      >
                        <td class="px-2 py-1.5 whitespace-nowrap">
                          <div class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100">
                            <span class="text-xs font-medium text-gray-700">{{ index + 1 }}</span>
                          </div>
                        </td>
                        
                        <td class="px-2 py-1.5">
                          <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded bg-green-100 flex items-center justify-center">
                              <i class="fas fa-file-invoice-dollar text-green-600 text-xs"></i>
                            </div>
                            <div>
                              <div class="text-xs font-semibold text-gray-900">{{ head.head_name }}</div>
                              <div v-if="head.description" class="text-xs text-gray-500 mt-0.5">
                                {{ head.description }}
                              </div>
                            </div>
                          </div>
                        </td>
                        
                        <td class="px-2 py-1.5 whitespace-nowrap">
                          <div class="relative">
                            <input 
                              type="number" 
                              v-model="feeAmounts[head.id]"
                              placeholder="Enter amount in ৳"
                              :min="0"
                              step="0.01"
                              :class="[
                                'pl-3 pr-8 py-1.5 text-sm border rounded focus:outline-none focus:ring-2 bg-white text-gray-800 w-full',
                                getFeeHeadError(index) ? 'border-red-300 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
                              ]"
                            />
                            <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                              <span class="text-gray-500 font-medium text-xs">৳</span>
                            </div>
                          </div>
                          <div v-if="getFeeHeadError(index)" class="mt-1 text-xs text-red-600">
                            <i class="fas fa-exclamation-circle"></i> {{ getFeeHeadError(index) }}
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="px-2 py-2 border-t border-gray-200 bg-linear-to-r from-gray-50 to-white">
                  <div v-if="errors.fee_heads && typeof errors.fee_heads === 'string'" class="mb-2 p-2 bg-red-50 border border-red-200 rounded">
                    <div class="flex items-center gap-2 text-red-700">
                      <i class="fas fa-exclamation-circle text-xs"></i>
                      <span class="text-xs">{{ errors.fee_heads }}</span>
                    </div>
                  </div>
                  
                  <div class="flex justify-between items-center">
                    <div class="text-xs text-gray-600">
                      <div class="flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span>
                          Selected fee heads: 
                          <span class="font-semibold">{{ getSelectedFeeHeadsCount() }}</span>
                          out of {{ allFeeHeads.length }}
                        </span>
                      </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                      <div class="text-sm font-semibold text-gray-800">
                        Total Amount: 
                        <span class="text-blue-600 text-base">৳{{ getTotalAmount() }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-200">
              <button 
                type="button"
                @click="router.push('/fee-assigns')"
                class="px-3 py-1.5 text-sm border border-gray-300 rounded text-gray-700 hover:bg-gray-50 font-medium transition-colors"
              >
                Cancel
              </button>
              <button 
                type="submit"
                @click="submitForm"
                :disabled="submitting"
                class="px-3 py-1.5 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <i v-if="submitting" class="fas fa-spinner fa-spin text-xs"></i>
                {{ submitting ? 'Updating...' : 'Update Fee Assignment' }}
              </button>
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
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert } from '../../utils/sweetAlert'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

const router = useRouter()
const route = useRoute()

// Data
const loading = ref(true)
const dropdownData = ref({
  sessions: [],
  versions: [],
  classes: [],
  months: [],
  fee_heads: []
})
const form = ref({
  session_id: null,
  version_id: null,
  class_id: null,
  month_id: null
})
const errors = ref({}) 
const submitting = ref(false)
const feeAssignId = ref(null)

// Fee heads management
const allFeeHeads = ref([])
const feeAmounts = ref({}) 
const defaultAmounts = ref({}) 

// Computed
const getSelectedSessionName = computed(() => {
  const session = dropdownData.value.sessions.find(s => s.id == form.value.session_id)
  return session ? (session.session_name || session.name) : 'Not selected'
})

const getSelectedClassName = computed(() => {
  const cls = dropdownData.value.classes.find(c => c.id == form.value.class_id)
  return cls ? (cls.class_name || cls.name) : 'Not selected'
})

const getSelectedVersionName = computed(() => {
  const version = dropdownData.value.versions.find(v => v.id == form.value.version_id)
  return version ? (version.version_name || version.name) : 'Not selected'
})

const getSelectedMonthName = computed(() => {
  const month = dropdownData.value.months.find(m => m.id == form.value.month_id)
  return month ? (month.month_name || month.name) : 'Not selected'
})

// Methods
const loadDropdownData = async () => {
  try {
    const response = await axios.get('/api/fee-assigns/dropdown-data')
    dropdownData.value = response.data.data
    allFeeHeads.value = response.data.data.fee_heads || []
    
    // Initialize fee heads data
    initializeFeeHeadsData()
  } catch (error) {
    console.error('Failed to load dropdown data:', error)
    showErrorAlert('Error', 'Failed to load form data')
  }
}

const loadFeeAssignData = async () => {
  try {
    loading.value = true
    const id = route.params.id
    feeAssignId.value = id

    const response = await axios.get(`/api/fee-assigns/${id}`)
    const data = response.data.data.fee_assign
    
    // Set form data - including month_id
    form.value.session_id = data.session_id
    form.value.version_id = data.version_id
    form.value.class_id = data.class_id
    form.value.month_id = data.month_id
    
    // Load fee heads data from this specific assignment
    data.details.forEach(detail => {
      feeAmounts.value[detail.head_id] = parseFloat(detail.amount)
    })
    
    console.log('Loaded fee assignment:', {
      id: data.id,
      month_id: data.month_id,
      month_name: data.month?.month_name,
      details: data.details,
      feeAmounts: feeAmounts.value
    })
    
    loading.value = false
  } catch (error) {
    console.error('Failed to load fee assign data:', error)
    showErrorAlert('Error', 'Failed to load fee assignment data')
    router.push('/fee-assigns')
  }
}

const initializeFeeHeadsData = () => {
  allFeeHeads.value.forEach(head => {
    if (!feeAmounts.value[head.id]) {
      feeAmounts.value[head.id] = ''
    }
    defaultAmounts.value[head.id] = head.default_amount || 0
  })
}

const isFeeHeadSelected = (headId) => {
  const amount = feeAmounts.value[headId]
  return amount && parseFloat(amount) > 0
}

const getSelectedFeeHeadsCount = () => {
  return allFeeHeads.value.filter(head => isFeeHeadSelected(head.id)).length
}

const getTotalAmount = () => {
  let total = 0
  allFeeHeads.value.forEach(head => {
    if (isFeeHeadSelected(head.id)) {
      total += parseFloat(feeAmounts.value[head.id]) || 0
    }
  })
  return total.toFixed(2)
}

const getFeeHeadError = (index) => {
  // Check for nested fee_heads errors
  if (errors.value[`fee_heads.${index}.head_id`]) {
    return errors.value[`fee_heads.${index}.head_id`][0]
  }
  if (errors.value[`fee_heads.${index}.amount`]) {
    return errors.value[`fee_heads.${index}.amount`][0]
  }
  if (errors.value[`fee_heads.${index}.amount_type`]) {
    return errors.value[`fee_heads.${index}.amount_type`][0]
  }
  return null
}

const formatValidationErrors = (errorObj) => {
  let errorMessages = []
  
  for (const [key, value] of Object.entries(errorObj)) {
    if (Array.isArray(value)) {
      errorMessages.push(...value)
    } else if (typeof value === 'string') {
      errorMessages.push(value)
    }
  }
  
  return errorMessages.join('\n')
}

const submitForm = async () => {
  // Clear previous errors
  errors.value = {}
  submitting.value = true

  try {
    // Prepare fee heads data - send all fee heads with amounts (even 0 or empty)
    const feeHeadsData = allFeeHeads.value.map(head => ({
      head_id: head.id,
      amount: parseFloat(feeAmounts.value[head.id]) || 0
    }))

    // Prepare form data - single month only (as array per controller requirement)
    const formData = {
      session_id: form.value.session_id,
      version_id: form.value.version_id,
      class_id: form.value.class_id,
      month_ids: [form.value.month_id], // Controller expects month_ids as array
      fee_heads: feeHeadsData
    }

    console.log('Submitting update data:', formData)

    const response = await axios.put(`/api/fee-assigns/${feeAssignId.value}`, formData)
    
    let message = `Updated fee assignment for ${getSelectedMonthName.value} successfully!`
    
    await showSuccessAlert('Success!', message)
    
    router.push({ 
      path: '/fee-assigns',
      query: { updated: 'true' } 
    })
    
  } catch (error) {
    console.error('Submit error:', error)
    
    if (error.response?.status === 422) {
      // Validation errors from backend
      const backendErrors = error.response.data.errors || {}
      errors.value = backendErrors
      
      // Format and show validation errors in alert
      const errorMessage = formatValidationErrors(backendErrors)
      await showErrorAlert('Validation Error', errorMessage || 'Please check the form for errors')
      
    } else if (error.response?.status === 500) {
      // Server error
      const errorMessage = error.response?.data?.message || error.response?.data?.error || 'An unexpected error occurred'
      await showErrorAlert('Server Error', errorMessage)
      
    } else {
      // Other errors
      const errorMessage = error.response?.data?.message || error.message || 'Failed to update fee assignment'
      await showErrorAlert('Error', errorMessage)
    }
  } finally {
    submitting.value = false
  }
}

// Lifecycle
onMounted(async () => {
  await loadDropdownData()
  await loadFeeAssignData()
})
</script>

<style scoped>
.v-select-custom {
  --vs-border-color: #d1d5db;
  --vs-border-radius: 0.375rem;
  --vs-font-size: 0.75rem;
  --vs-line-height: 1rem;
}

.v-select-custom:hover {
  --vs-border-color: #3b82f6;
}

.v-select-custom :deep(.vs__dropdown-toggle) {
  border: 1px solid var(--vs-border-color);
  border-radius: var(--vs-border-radius);
  padding: 0.375rem 0.5rem;
  min-height: 34px;
}

.border-red-500 :deep(.vs__dropdown-toggle) {
  border-color: #ef4444 !important;
}

/* Table styles */
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

/* Scrollbar styling */
::-webkit-scrollbar {
  width: 6px;
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
</style>