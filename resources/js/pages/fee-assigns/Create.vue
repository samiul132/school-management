<template>
  <AppLayout>
    <!-- Page Header -->
    <div class="mb-2 mt-2">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-2">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">
            {{ existingFeeAssignId ? 'Update' : 'Create' }} Fee Assign
          </h1>
          <p v-if="existingFeeAssignId" class="text-xs text-orange-600 mt-1">
            <i class="fas fa-info-circle"></i> Editing existing fee assignment
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

    <!-- Form Container -->
    <div>
      <!-- Main Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow border border-gray-100"> 

          <!-- Form Body -->
          <div class="p-3">
            <!-- Basic Information -->
            <div class="mb-3">
              <h3 class="text-base font-medium text-gray-800 mb-2 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500 text-sm"></i>
                Basic Information
              </h3>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <!-- Session -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Session <span class="text-red-500">*</span>
                  </label>
                  <v-select
                    v-model="form.session_id"
                    :options="dropdownData.sessions"
                    label="session_name"
                    :reduce="session => session.id"
                    placeholder="Select Session"
                    :clearable="false"
                    class="v-select-custom"
                    :class="{ 'border-red-500': errors.session_id }"
                    @option:selected="onSessionChange"
                  />
                  <div v-if="errors.session_id" class="mt-1 text-xs text-red-600">
                    <i class="fas fa-exclamation-circle"></i> {{ errors.session_id[0] }}
                  </div>
                </div>

                <!-- Version -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Version <span class="text-red-500">*</span>
                  </label>
                  <v-select
                    v-model="form.version_id"
                    :options="dropdownData.versions"
                    label="version_name"
                    :reduce="version => version.id"
                    placeholder="Select Version"
                    :clearable="false"
                    class="v-select-custom"
                    :class="{ 'border-red-500': errors.version_id }"
                    @option:selected="onVersionChange"
                  />
                  <div v-if="errors.version_id" class="mt-1 text-xs text-red-600">
                    <i class="fas fa-exclamation-circle"></i> {{ errors.version_id[0] }}
                  </div>
                </div>

                <!-- Class -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Class <span class="text-red-500">*</span>
                  </label>
                  <v-select
                    v-model="form.class_id"
                    :options="dropdownData.classes"
                    label="class_name"
                    :reduce="cls => cls.id"
                    placeholder="Select Class"
                    :clearable="false"
                    class="v-select-custom"
                    :class="{ 'border-red-500': errors.class_id }"
                    @option:selected="onClassChange"
                  />
                  <div v-if="errors.class_id" class="mt-1 text-xs text-red-600">
                    <i class="fas fa-exclamation-circle"></i> {{ errors.class_id[0] }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Months Selection -->
            <div class="mb-3">
              <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-2">
                  <div class="flex rounded">
                    <i class="fas fa-calendar-alt text-blue-600 text-xs"></i>
                  </div>
                  <h3 class="text-base font-semibold text-gray-800">Select Months <span class="text-red-500">*</span></h3>
                </div>

                <label class="flex items-center cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="selectAllMonths"
                    @change="toggleAllMonths"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-xs text-gray-700 font-medium">
                    {{ selectAllMonths ? 'Deselect All' : 'Select All' }}
                  </span>
                </label>
              </div>
              
              <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 p-2 border rounded"
                   :class="errors.month_ids ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50'">
                <div 
                    v-for="month in dropdownData.months" 
                    :key="month.id"
                    class="flex items-center p-2 rounded border transition bg-white border-gray-200 hover:bg-blue-50"
                  >
                  <input 
                    type="checkbox" 
                    :id="'month-' + month.id"
                    v-model="selectedMonths"
                    :value="month.id"
                    class="h-3.5 w-3.5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    @click.stop
                  >
                  <label 
                    :for="'month-' + month.id"
                    class="ml-2 text-xs flex-1 text-gray-700 cursor-pointer"
                  >
                    {{ month.month_name }}
                  </label>
                </div>
              </div>

              <div v-if="errors.month_ids" class="mt-1 text-xs text-red-600">
                <i class="fas fa-exclamation-circle"></i> {{ errors.month_ids[0] }}
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
                {{ submitting ? 'Creating...' : 'Create Fee Assign' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert } from '../../utils/sweetAlert'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

const router = useRouter()

// Data
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
  class_id: null
})
const errors = ref({}) 
const submitting = ref(false)
const selectedMonths = ref([])
const selectAllMonths = ref(false)
const assignedMonths = ref({}) 

// Wavers-style fee heads management
const allFeeHeads = ref([])
const feeAmounts = ref({}) 
const defaultAmounts = ref({}) 
const existingFeeAssignId = ref(null)

// Computed
const isFormValid = computed(() => {
  return true // Always return true, let backend handle validation
})

// Methods
const isMonthAssigned = (monthId) => {
  return assignedMonths.value.hasOwnProperty(monthId)
}

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

const loadExistingData = async () => {
  if (!form.value.session_id || !form.value.version_id || !form.value.class_id) {
    existingFeeAssignId.value = null
    assignedMonths.value = {}
    return
  }

  try {
    const response = await axios.get('/api/fee-assigns', {
      params: {
        session_id: form.value.session_id,
        version_id: form.value.version_id,
        class_id: form.value.class_id,
        per_page: 100
      }
    })

    if (response.data.success && response.data.data.data.length > 0) {
      assignedMonths.value = {}
      selectedMonths.value = []
      
      response.data.data.data.forEach(item => {
        assignedMonths.value[item.month_id] = {
          id: item.id,
          month_id: item.month_id,
          month_name: item.month?.month_name,
          total_amount: item.total_amount,
          fee_heads: item.details.map(detail => ({
            head_id: detail.head_id,
            head_name: detail.head?.head_name,
            amount: detail.amount,
            amount_type: detail.amount_type
          }))
        }
      })
      
      // const firstMonth = response.data.data.data[0]
      // if (firstMonth && firstMonth.details) {
      //   firstMonth.details.forEach(detail => {
      //     if (!feeAmounts.value[detail.head_id]) {
      //       feeAmounts.value[detail.head_id] = parseFloat(detail.amount)
      //     }
      //   })
      // }
      
      console.log('Loaded assigned months (locked):', Object.keys(assignedMonths.value))
    } else {
      assignedMonths.value = {}
      selectedMonths.value = []
      selectAllMonths.value = false
    }
  } catch (error) {
    console.error('Failed to load existing data:', error)
    assignedMonths.value = {}
    selectedMonths.value = []
    selectAllMonths.value = false
  }
}

const initializeFeeHeadsData = () => {
  allFeeHeads.value.forEach(head => {
    feeAmounts.value[head.id] = ''
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

const selectAllFeeHeads = () => {
  allFeeHeads.value.forEach(head => {
    feeAmounts.value[head.id] = defaultAmounts.value[head.id] || 0
  })
  errors.value = {} // Clear errors when selecting all
}

const resetFeeHeads = () => {
  allFeeHeads.value.forEach(head => {
    feeAmounts.value[head.id] = ''
  })
  errors.value = {} // Clear errors when resetting
}

const toggleAllMonths = () => {
  if (selectAllMonths.value) {
    selectedMonths.value = dropdownData.value.months.map(month => month.id)
  } else {
    selectedMonths.value = []
  }
  // Clear month errors
  if (errors.value.month_ids) {
    delete errors.value.month_ids
  }
}

const toggleMonthSelection = (monthId) => {
  const index = selectedMonths.value.indexOf(monthId)
  if (index === -1) {
    selectedMonths.value.push(monthId)
  } else {
    selectedMonths.value.splice(index, 1)
  }
  
  if (selectedMonths.value.length === dropdownData.value.months.length) {
    selectAllMonths.value = true
  } else {
    selectAllMonths.value = false
  }
  
  // Clear month errors
  if (errors.value.month_ids) {
    delete errors.value.month_ids
  }
}

const onSessionChange = () => {
  loadExistingData()
  // Clear session error
  if (errors.value.session_id) {
    delete errors.value.session_id
  }
}

const onClassChange = () => {
  loadExistingData()
  // Clear class error
  if (errors.value.class_id) {
    delete errors.value.class_id
  }
}

const onVersionChange = () => {
  loadExistingData()
  // Clear version error
  if (errors.value.version_id) {
    delete errors.value.version_id
  }
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

    // Prepare form data
    const formData = {
      session_id: form.value.session_id,
      version_id: form.value.version_id,
      class_id: form.value.class_id,
      month_ids: selectedMonths.value, 
      fee_heads: feeHeadsData,
      apply_to_students: true, 
      update_existing: true
    }

    console.log('Submitting form data:', formData)

    const response = await axios.post('/api/fee-assigns', formData)
    
    const createdCount = response.data.created_count || 0
    const updatedCount = response.data.updated_count || 0
    
    let message = ''
    if (createdCount > 0 && updatedCount > 0) {
      message = `Created ${createdCount} and updated ${updatedCount} fee assignment(s) successfully!`
    } else if (createdCount > 0) {
      message = `Created ${createdCount} fee assignment(s) successfully!`
    } else if (updatedCount > 0) {
      message = `Updated ${updatedCount} fee assignment(s) successfully!`
    } else {
      message = response.data.message || 'Fee assignment processed successfully!'
    }
    
    await showSuccessAlert('Success!', message)
    
    router.push({ 
      path: '/fee-assigns',
      query: { 
        created: 'true' 
      } 
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
      const errorMessage = error.response?.data?.message || error.message || 'Failed to save fee assignment'
      await showErrorAlert('Error', errorMessage)
    }
  } finally {
    submitting.value = false
  }
}

// Watch for form changes to load existing data
watch(
  () => [form.value.session_id, form.value.class_id, form.value.version_id],
  () => {
    loadExistingData()
  },
  { deep: true }
)

// Lifecycle
onMounted(() => {
  loadDropdownData()
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

input[type="checkbox"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
  background-size: 1em 1em;
}

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