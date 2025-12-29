<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Create Leave Application</h1>
          <p class="text-gray-600 mt-2">Submit a new leave application</p>
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

    <!-- Main Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
              <p class="text-gray-600 text-sm">Fill in the leave details</p>
            </div>
          </div>

          <form @submit.prevent="submitForm">
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
                  required
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
                  required
                >
                  <option value="">Select status...</option>
                  <option value="PENDING">PENDING</option>
                  <option value="APPROVED">APPROVED</option>
                  <option value="DECLINED">DECLINED</option>
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
                  required
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
                  required
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
                  type="submit"
                  :disabled="loading"
                  class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-3 py-2 rounded-lg font-semibold transition-colors flex justify-center items-center gap-2 w-full sm:w-auto"
                >
                  <i class="fas fa-save"></i>
                  {{ loading ? 'Saving...' : 'Save' }}
                </button>
              </div>
            </div>
          </form>
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

        <!-- Quick Tips -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-lightbulb text-blue-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-blue-800">Quick Tips</h4>
          </div>
          <ul class="space-y-2 text-blue-700 text-sm">
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Select the staff member applying for leave</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Provide clear reason for the leave</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Total days will be calculated automatically</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Set appropriate status for the application</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert } from '../../utils/sweetAlert'

const router = useRouter()
const loading = ref(false)
const staffList = ref([])

const form = reactive({
  staff_id: '',
  application_date: new Date().toISOString().split('T')[0],
  leave_from_date: '',
  leave_to_date: '',
  reason_of_leave: '',
  status: 'PENDING'
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

const fetchStaffList = async () => {
  try {
    const response = await axios.get('/api/staffs')
    if (response.data.success) {
      staffList.value = response.data.data.map(staff => ({
        id: staff.id,
        name: `${staff.first_name} ${staff.last_name}`
      }))
    } else if (Array.isArray(response.data)) {
      staffList.value = response.data.map(staff => ({
        id: staff.id,
        name: `${staff.first_name} ${staff.last_name}`
      }))
    }
  } catch (error) {
    console.error('Failed to fetch staff list:', error)
    showErrorAlert('Error', 'Failed to load staff list')
  }
}

const calculateDays = () => {
  // This is handled by computed property
}

const formatDate = (dateString) => {
  if (!dateString) return ''
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
    const response = await axios.post('/api/leave', {
      staff_id: form.staff_id,
      application_date: form.application_date,
      leave_from_date: form.leave_from_date,
      leave_to_date: form.leave_to_date,
      reason_of_leave: form.reason_of_leave,
      status: form.status
    })
    
    if (response.data.success || response.data.status === 'success') {
      showSuccessAlert('Success!', 'Leave application created successfully')
      router.push('/leave')
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to create leave application')
    }
    
  } catch (error) {
    loading.value = false

    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response && error.response.data) {
      showErrorAlert('Error', error.response.data.message || 'Failed to create leave application. Please try again.')
    } else {
      showErrorAlert('Error', 'Failed to create leave application. Please try again.')
    }
  }
}

onMounted(() => {
  fetchStaffList()
})
</script>