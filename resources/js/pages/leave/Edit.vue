<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Edit Leave Application #{{ leaveId }}</h1>
          <p class="text-gray-600 mt-2">Update leave application details</p>
        </div>

        <div class="mb-6">
          <router-link
            :to="{ name: 'leave.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-arrow-left"></i>
            Back to List
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !form.staff_id" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Main Form Section -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column - Leave Information -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Leave Details Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-calendar-alt text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Leave Information</h3>
              <p class="text-gray-600 text-sm">Update leave details</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Staff Selection -->
            <div class="space-y-2 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">
                Staff Name <span class="text-red-500">*</span>
              </label>
              <v-select
                v-model="form.staff_id"
                :options="staffList"
                :filterable="true"
                :searchable="true"
                label="name"
                :reduce="staff => staff.id"
                placeholder="Search or select staff..."
                :class="{ 'border-red-500': errors.staff_id }"
              >
                <template #option="option">
                  <div class="font-medium text-gray-800">
                    <span class="text-blue-600 font-semibold mr-2 text-xs">#{{ option.id }}</span>
                    {{ option.name }}
                  </div>
                </template>
                <template #no-options="{ search }">
                  <div class="p-2 text-gray-500 text-sm">
                    <p v-if="search">No staff found for "{{ search }}"</p>
                    <p v-else>Type to search staff</p>
                  </div>
                </template>
              </v-select>
              <p v-if="errors.staff_id" class="text-red-500 text-sm">{{ errors.staff_id[0] }}</p>
            </div>

            <!-- Application Date -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Application Date <span class="text-red-500">*</span>
              </label>
              <input 
                type="date" 
                v-model="form.application_date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.application_date }"
              >
              <p v-if="errors.application_date" class="text-red-500 text-sm">{{ errors.application_date[0] }}</p>
            </div>

            <!-- Status -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Status <span class="text-red-500">*</span>
              </label>
              <select 
                v-model="form.status"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.status }"
              >
                <option value="">Select status...</option>
                <option value="PENDING">Pending</option>
                <option value="APPROVED">Approved</option>
                <option value="DECLINED">Declined</option>
              </select>
              <p v-if="errors.status" class="text-red-500 text-sm">{{ errors.status[0] }}</p>
            </div>

            <!-- Leave From Date -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Leave From Date <span class="text-red-500">*</span>
              </label>
              <input 
                type="date" 
                v-model="form.leave_from_date"
                @change="calculateDays"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.leave_from_date }"
              >
              <p v-if="errors.leave_from_date" class="text-red-500 text-sm">{{ errors.leave_from_date[0] }}</p>
            </div>

            <!-- Leave To Date -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Leave To Date <span class="text-red-500">*</span>
              </label>
              <input 
                type="date" 
                v-model="form.leave_to_date"
                @change="calculateDays"
                :min="form.leave_from_date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.leave_to_date }"
              >
              <p v-if="errors.leave_to_date" class="text-red-500 text-sm">{{ errors.leave_to_date[0] }}</p>
            </div>

            <!-- Reason of Leave -->
            <div class="space-y-2 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Reason of Leave</label>
              <textarea 
                v-model="form.reason_of_leave"
                rows="4"
                placeholder="Enter reason for leave..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-none"
              ></textarea>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex justify-end"> 
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
              <router-link
                :to="{ name: 'leave.index' }"
                class="inline-flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg font-semibold transition-colors"
              >
                <i class="fas fa-times"></i>
                Cancel
              </router-link>

              <button 
                @click="submitForm"
                :disabled="loading"
                class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-3 py-2 rounded-lg font-semibold transition-colors flex justify-center items-center gap-2 w-full sm:w-auto"
              >
                <i class="fas fa-save"></i>
                {{ loading ? 'Updating...' : 'Update Application' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Summary & Info -->
      <div class="space-y-8">
        <!-- Leave Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-info-circle text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Leave Summary</h3>
              <p class="text-gray-600 text-sm">Application details</p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-600 mb-2">Total Leave Days:</p>
              <p class="text-3xl font-bold text-blue-600">{{ totalDays }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ totalDays === 1 ? 'day' : 'days' }}</p>
            </div>

            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-600 mb-2">Status Preview:</p>
              <span 
                :class="[
                  'px-3 py-1 rounded-full text-xs font-medium',
                  form.status === 'APPROVED' 
                    ? 'bg-green-100 text-green-800' 
                    : form.status === 'PENDING'
                    ? 'bg-yellow-100 text-yellow-800'
                    : form.status === 'DECLINED'
                    ? 'bg-red-100 text-red-800'
                    : 'bg-gray-100 text-gray-800'
                ]"
              >
                {{ form.status || 'Not selected' }}
              </span>
            </div>

            <div v-if="form.leave_from_date && form.leave_to_date" class="p-4 bg-blue-50 rounded-lg">
              <p class="text-sm text-blue-600 mb-2">Leave Period:</p>
              <p class="text-sm font-medium text-blue-800">
                {{ formatDate(form.leave_from_date) }}
              </p>
              <p class="text-xs text-blue-600 my-1">to</p>
              <p class="text-sm font-medium text-blue-800">
                {{ formatDate(form.leave_to_date) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Leave Info -->
        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-info-circle text-gray-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-gray-800">Leave Information</h4>
          </div>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Created:</span>
              <span class="text-gray-800 font-medium">{{ formatDate(leaveData.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Last Updated:</span>
              <span class="text-gray-800 font-medium">{{ formatDate(leaveData.updated_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Leave ID:</span>
              <span class="text-gray-800 font-mono">{{ leaveId }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Tips -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-lightbulb text-blue-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-blue-800">Quick Tips</h4>
          </div>
          <ul class="space-y-2 text-blue-700 text-sm">
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Update all necessary information</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Total days will be recalculated automatically</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Change status to approve or decline</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const staffList = ref([])

const leaveId = ref(route.params.id)
const leaveData = ref({
  created_at: '',
  updated_at: ''
})

const form = reactive({
  staff_id: null,
  application_date: '',
  leave_from_date: '',
  leave_to_date: '',
  reason_of_leave: '',
  status: ''
})

const errors = ref({})

const totalDays = computed(() => {
  if (!form.leave_from_date || !form.leave_to_date) return 0
  
  const fromDate = new Date(form.leave_from_date)
  const toDate = new Date(form.leave_to_date)
  
  if (toDate < fromDate) return 0
  
  const diffTime = Math.abs(toDate - fromDate)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  
  return diffDays + 1
})

const formatDateForInput = (dateString) => {
  if (!dateString) return ''
  
  if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
    return dateString
  }
  
  try {
    const date = new Date(dateString)
    if (isNaN(date.getTime())) return ''
    
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    
    return `${year}-${month}-${day}`
  } catch (error) {
    console.error('Date formatting error:', error)
    return ''
  }
}

const fetchStaffList = async () => {
  try {
    const response = await axios.get('/api/leave/staff-list')
    if (response.data.success) {
      staffList.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch staff list:', error)
    showErrorAlert('Error', 'Failed to load staff list')
  }
}

const fetchLeave = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/leave/${leaveId.value}`)
    
    let leave
    if (response.data.success) {
      leave = response.data.data
    } else {
      leave = response.data
    }

    if (!leave) {
      throw new Error('Leave not found')
    }

    form.staff_id = leave.staff_id
    form.application_date = formatDateForInput(leave.application_date)
    form.leave_from_date = formatDateForInput(leave.leave_from_date)
    form.leave_to_date = formatDateForInput(leave.leave_to_date)
    form.reason_of_leave = leave.reason_of_leave || ''
    form.status = leave.status

    leaveData.value = {
      created_at: leave.created_at,
      updated_at: leave.updated_at
    }
  } catch (error) {
    console.error('Error fetching leave:', error)
    showErrorAlert('Error', 'Failed to load leave data')
    router.push({ name: 'leave.index' })
  } finally {
    loading.value = false
  }
}

const calculateDays = () => {
  // This is handled by computed property
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const submitForm = async () => {
  loading.value = true
  errors.value = {}

  try {
    if (!form.staff_id) {
      errors.value.staff_id = ['The staff field is required.']
      loading.value = false
      return
    }

    if (!form.application_date) {
      errors.value.application_date = ['The application date field is required.']
      loading.value = false
      return
    }

    if (!form.leave_from_date) {
      errors.value.leave_from_date = ['The leave from date field is required.']
      loading.value = false
      return
    }

    if (!form.leave_to_date) {
      errors.value.leave_to_date = ['The leave to date field is required.']
      loading.value = false
      return
    }

    if (!form.status) {
      errors.value.status = ['The status field is required.']
      loading.value = false
      return
    }

    showLoadingAlert('Updating leave application...')

    const response = await axios.put(`/api/leave/${leaveId.value}`, {
      staff_id: form.staff_id,
      application_date: form.application_date,
      leave_from_date: form.leave_from_date,
      leave_to_date: form.leave_to_date,
      reason_of_leave: form.reason_of_leave,
      status: form.status
    })
    
    closeAlert()
    
    if (response.data.success) {
      showSuccessAlert('Success', 'Leave application updated successfully')
      router.push({ 
        name: 'leave.index',
        query: { updated: 'true' }
      })
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to update leave application')
    }
    
  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response && error.response.data) {
      showErrorAlert('Error', error.response.data.message || 'Failed to update leave application. Please try again.')
    } else {
      showErrorAlert('Error', 'Failed to update leave application. Please try again.')
    }
  }
}

onMounted(async () => {
  await fetchStaffList()
  await fetchLeave()
})
</script>