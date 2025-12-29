<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Add Overtime Record</h1>
          <p class="text-gray-600 mt-2">Submit a new overtime record</p>
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

    <!-- Main Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
              <p class="text-gray-600 text-sm">Fill in the overtime details</p>
            </div>
          </div>

          <form @submit.prevent="submitForm">
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
                  required
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
                    required
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

        <!-- Quick Tips -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-lightbulb text-blue-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-blue-800">Quick Tips</h4>
          </div>
          <ul class="space-y-2 text-blue-700 text-sm">
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Select the date of overtime work</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Choose the staff member who worked overtime</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Enter exact overtime hours (0.5 to 24 hrs)</span>
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
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert } from '../../utils/sweetAlert'

const router = useRouter()
const loading = ref(false)
const staffList = ref([])

const form = reactive({
  staff_id: '',
  date: new Date().toISOString().split('T')[0],
  over_time_hour: ''
})

const errors = ref({})

const selectedStaffName = computed(() => {
  if (!form.staff_id) return ''
  const staff = staffList.value.find(s => s.id === form.staff_id)
  return staff ? staff.name : ''
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
    const response = await axios.post('/api/over-time', {
      staff_id: form.staff_id,
      date: form.date,
      over_time_hour: form.over_time_hour
    })
    
    if (response.data.success || response.data.status === 'success') {
      showSuccessAlert('Success!', 'Overtime record created successfully')
      router.push('/over-time')
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to create overtime record')
    }
    
  } catch (error) {
    loading.value = false

    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response && error.response.data) {
      showErrorAlert('Error', error.response.data.message || 'Failed to create overtime record. Please try again.')
    } else {
      showErrorAlert('Error', 'Failed to create overtime record. Please try again.')
    }
  }
}

onMounted(() => {
  fetchStaffList()
})
</script>