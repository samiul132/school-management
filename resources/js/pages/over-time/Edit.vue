<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Edit Overtime Record #{{ overtimeId }}</h1>
          <p class="text-gray-600 mt-2">Update overtime record details</p>
        </div>

        <div class="mb-6">
          <router-link
            :to="{ name: 'over-time.index' }"
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
      <!-- Left Column - Overtime Information -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Overtime Details Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-clock text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Overtime Information</h3>
              <p class="text-gray-600 text-sm">Update overtime details</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Date -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Date <span class="text-red-500">*</span>
              </label>
              <input 
                type="date" 
                v-model="form.date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.date }"
              >
              <p v-if="errors.date" class="text-red-500 text-sm">{{ errors.date[0] }}</p>
            </div>

            <!-- Staff Selection -->
            <div class="space-y-2">
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

            <!-- Overtime Hours -->
            <div class="space-y-2 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">
                Overtime Hours <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input 
                  type="number" 
                  v-model="form.over_time_hour"
                  step="0.5"
                  min="0.5"
                  max="24"
                  placeholder="Enter overtime hours..."
                  class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                  :class="{ 'border-red-500': errors.over_time_hour }"
                >
                <span class="absolute right-4 top-2.5 text-gray-500 font-medium">hrs</span>
              </div>
              <p v-if="errors.over_time_hour" class="text-red-500 text-sm">{{ errors.over_time_hour[0] }}</p>
              <p class="text-xs text-gray-500">Minimum 0.5 hours, Maximum 24 hours</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex justify-end"> 
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
              <router-link
                :to="{ name: 'over-time.index' }"
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
                {{ loading ? 'Updating...' : 'Update Record' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Summary & Info -->
      <div class="space-y-8">
        <!-- Overtime Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-info-circle text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Overtime Summary</h3>
              <p class="text-gray-600 text-sm">Record details</p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-600 mb-2">Total Hours:</p>
              <p class="text-3xl font-bold text-blue-600">{{ form.over_time_hour || 0 }}</p>
              <p class="text-xs text-gray-500 mt-1">hours</p>
            </div>

            <div v-if="form.date" class="p-4 bg-blue-50 rounded-lg">
              <p class="text-sm text-blue-600 mb-2">Overtime Date:</p>
              <p class="text-sm font-medium text-blue-800">
                {{ formatDate(form.date) }}
              </p>
            </div>

            <div v-if="selectedStaffName" class="p-4 bg-green-50 rounded-lg">
              <p class="text-sm text-green-600 mb-2">Selected Staff:</p>
              <p class="text-sm font-medium text-green-800">
                {{ selectedStaffName }}
              </p>
            </div>
          </div>
        </div>

        <!-- Record Info -->
        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-info-circle text-gray-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-gray-800">Record Information</h4>
          </div>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Created:</span>
              <span class="text-gray-800 font-medium">{{ formatDate(overtimeData.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Last Updated:</span>
              <span class="text-gray-800 font-medium">{{ formatDate(overtimeData.updated_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Record ID:</span>
              <span class="text-gray-800 font-mono">{{ overtimeId }}</span>
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
              <span>Verify the overtime hours are correct</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Use increments of 0.5 hours</span>
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

const overtimeId = ref(route.params.id)
const overtimeData = ref({
  created_at: '',
  updated_at: ''
})

const form = reactive({
  staff_id: null,
  date: '',
  over_time_hour: ''
})

const errors = ref({})

const selectedStaffName = computed(() => {
  if (!form.staff_id) return ''
  const staff = staffList.value.find(s => s.id === form.staff_id)
  return staff ? staff.name : ''
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

const fetchOvertime = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/over-time/${overtimeId.value}`)
    
    let overtime
    if (response.data.success) {
      overtime = response.data.data
    } else {
      overtime = response.data
    }

    if (!overtime) {
      throw new Error('Overtime record not found')
    }

    form.staff_id = overtime.staff_id
    form.date = formatDateForInput(overtime.date)
    form.over_time_hour = overtime.over_time_hour

    overtimeData.value = {
      created_at: overtime.created_at,
      updated_at: overtime.updated_at
    }
  } catch (error) {
    console.error('Error fetching overtime:', error)
    showErrorAlert('Error', 'Failed to load overtime record')
    router.push({ name: 'over-time.index' })
  } finally {
    loading.value = false
  }
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

    if (!form.date) {
      errors.value.date = ['The date field is required.']
      loading.value = false
      return
    }

    if (!form.over_time_hour) {
      errors.value.over_time_hour = ['The overtime hours field is required.']
      loading.value = false
      return
    }

    showLoadingAlert('Updating overtime record...')

    const response = await axios.put(`/api/over-time/${overtimeId.value}`, {
      staff_id: form.staff_id,
      date: form.date,
      over_time_hour: form.over_time_hour
    })
    
    closeAlert()
    
    if (response.data.success) {
      showSuccessAlert('Success', 'Overtime record updated successfully')
      router.push({ 
        name: 'over-time.index',
        query: { updated: 'true' }
      })
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to update overtime record')
    }
    
  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response && error.response.data) {
      showErrorAlert('Error', error.response.data.message || 'Failed to update overtime record. Please try again.')
    } else {
      showErrorAlert('Error', 'Failed to update overtime record. Please try again.')
    }
  }
}

onMounted(async () => {
  await fetchStaffList()
  await fetchOvertime()
})
</script>