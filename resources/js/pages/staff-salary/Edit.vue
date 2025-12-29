<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Edit Salary Sheet #{{ salaryId }}</h1>
          <p class="text-gray-600 mt-2">Update salary sheet details</p>
        </div>

        <div class="mb-6">
          <router-link
            :to="{ name: 'staff-salary.index' }"
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
      <div class="lg:col-span-8 space-y-2">
        <!-- Salary Sheet Info Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-file-invoice-dollar text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Salary Sheet Information</h3>
              <p class="text-gray-600 text-sm">Update salary sheet details</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Title -->
            <div class="space-y-2 md:col-span-3">
              <label class="block text-sm font-medium text-gray-700">
                Title <span class="text-red-500">*</span>
              </label>
              <input 
                type="text" 
                v-model="form.title"
                placeholder="January 2024 Salary Sheet"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.title }"
                required
              >
              <p v-if="errors.title" class="text-red-500 text-sm">{{ errors.title[0] }}</p>
            </div>

            <!-- Month -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Month <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.month"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.month }"
                required
              >
                <option value="">Select Month</option>
                <option v-for="(month, index) in months" :key="index" :value="month.value">
                  {{ month.label }}
                </option>
              </select>
              <p v-if="errors.month" class="text-red-500 text-sm">{{ errors.month[0] }}</p>
            </div>

            <!-- Year -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Year <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.year"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.year }"
                required
              >
                <option value="">Select Year</option>
                <option v-for="year in years" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
              <p v-if="errors.year" class="text-red-500 text-sm">{{ errors.year[0] }}</p>
            </div>

            <!-- Regenerate Button -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
              <button 
                @click="regenerateSalary"
                :disabled="generating || !form.month || !form.year"
                class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-4 py-2 rounded-lg font-semibold transition-colors flex justify-center items-center gap-2"
              >
                <i class="fas fa-sync-alt"></i>
                {{ generating ? 'Regenerating...' : 'Regenerate' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Salary Details Table -->
        <div v-if="staffData.length > 0" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-users text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Salary Details</h3>
              <p class="text-gray-600 text-sm">Review and update staff salaries</p>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Name</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">P</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">A</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">L</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">LV</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Basic</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">OT</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Advance</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="staff in staffData" :key="staff.id" class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm text-gray-800">
                    <div class="font-medium">{{ staff.staff_name }}</div>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ staff.id }}</td>
                  <td class="px-4 py-3 text-center text-sm text-green-600 font-semibold">{{ staff.present_days }}</td>
                  <td class="px-4 py-3 text-center text-sm text-red-600 font-semibold">{{ staff.absent_days }}</td>
                  <td class="px-4 py-3 text-center text-sm text-yellow-600 font-semibold">{{ staff.late_days }}</td>
                  <td class="px-4 py-3 text-center text-sm text-blue-600 font-semibold">{{ staff.leave_days }}</td>
                  <td class="px-4 py-3 text-right text-sm text-gray-800">৳{{ formatCurrency(staff.basic_salary) }}</td>
                  <td class="px-4 py-3 text-right text-sm text-gray-800">৳{{ formatCurrency(staff.gross_salary) }}</td>
                  <td class="px-4 py-3 text-right text-sm text-gray-800">৳{{ formatCurrency(staff.overtime) }}</td>
                  <td class="px-4 py-3 text-right text-sm text-gray-800">৳{{ formatCurrency(staff.advance_payment) }}</td>
                  <td class="px-4 py-3 text-right text-sm font-semibold text-green-700">৳{{ formatCurrency(staff.total_payable_salary) }}</td>
                </tr>
              </tbody>
              <tfoot class="bg-gray-100">
                <tr>
                  <td colspan="8" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total:</td>
                  <td class="px-4 py-3 text-right text-sm font-bold text-green-700">৳{{ formatCurrency(summary.totalOvertime) }}</td>
                  <td class="px-4 py-3 text-right text-sm font-bold text-green-700">৳{{ formatCurrency(summary.totalAdvance) }}</td>
                  <td class="px-4 py-3 text-right text-sm font-bold text-green-700">৳{{ formatCurrency(summary.totalPayableSalary) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Update Button -->
          <div class="mt-6 flex justify-end gap-3">
            <button 
              @click="updateSalarySheet"
              :disabled="saving"
              class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-lg font-semibold transition-colors"
            >
              <i class="fas fa-save"></i>
              {{ saving ? 'Updating...' : 'Update Salary Sheet' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const salaryId = route.params.id

const generating = ref(false)
const saving = ref(false)
const loading = ref(true)
const staffData = ref([])

const months = [
  { value: '01', label: 'January' },
  { value: '02', label: 'February' },
  { value: '03', label: 'March' },
  { value: '04', label: 'April' },
  { value: '05', label: 'May' },
  { value: '06', label: 'June' },
  { value: '07', label: 'July' },
  { value: '08', label: 'August' },
  { value: '09', label: 'September' },
  { value: '10', label: 'October' },
  { value: '11', label: 'November' },
  { value: '12', label: 'December' }
]

const currentYear = new Date().getFullYear()
const years = Array.from({ length: 10 }, (_, i) => currentYear - i + 1)

const form = reactive({
  title: '',
  month: '',
  year: '',
})

const summary = reactive({
  totalStaff: 0,
  totalPayableSalary: 0,
  totalAdvance: 0,
  totalOvertime: 0
})

const errors = ref({})

const formatCurrency = (amount) => {
  return Number(amount || 0).toLocaleString('en-BD', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

// ✅ Load existing salary data
const loadSalaryData = async () => {
  try {
    loading.value = true
    showLoadingAlert('Loading salary data...')

    const response = await axios.get(`/api/staff-salary/${salaryId}`)

    closeAlert()

    if (response.data.success) {
      const salaryData = response.data.data

      // Set form data
      form.title = salaryData.title
      form.month = salaryData.month
      form.year = salaryData.year

      // ✅ Convert salary_details to staffData format
      staffData.value = salaryData.salary_details.map(detail => ({
        id: detail.staff_id,
        staff_name: detail.staff ? `${detail.staff.first_name} ${detail.staff.last_name}` : 'Unknown',
        basic_salary: detail.staff ? detail.staff.basic_salary : 0,
        gross_salary: detail.gross_salary,
        overtime: detail.over_time_salary,
        advance_payment: detail.advance_payment,
        total_payable_salary: detail.payable_salary,
        // Attendance data will be loaded when regenerating
        present_days: 0,
        absent_days: 0,
        late_days: 0,
        leave_days: 0,
        absent_deduction: 0,
        over_time_hour: 0
      }))

      // Calculate summary
      summary.totalStaff = staffData.value.length
      summary.totalPayableSalary = salaryData.total_salary
      summary.totalAdvance = salaryData.total_advance
      summary.totalOvertime = salaryData.total_over_time

      // ✅ Load full details with attendance
      await regenerateSalary()
    }

  } catch (error) {
    closeAlert()
    console.error('Error loading salary data:', error)
    showErrorAlert('Error', error.response?.data?.message || 'Failed to load salary data')
  } finally {
    loading.value = false
  }
}

// Regenerate salary with updated attendance
const regenerateSalary = async () => {
  if (!form.month || !form.year) {
    showErrorAlert('Error', 'Please select month and year')
    return
  }

  generating.value = true
  errors.value = {}

  try {
    showLoadingAlert('Regenerating salary sheet...')

    const response = await axios.post('/api/staff-salary/generate', {
      month: form.month,
      year: form.year
    })

    closeAlert()

    if (response.data.success) {
      staffData.value = response.data.data.staffData
      summary.totalStaff = response.data.data.totalStaff
      summary.totalPayableSalary = response.data.data.totalPayableSalary
      summary.totalAdvance = response.data.data.totalAdvance
      summary.totalOvertime = response.data.data.totalOvertime

      showSuccessAlert('Success!', 'Salary sheet regenerated successfully')
    }

  } catch (error) {
    closeAlert()
    console.error('Error regenerating salary:', error)
    
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', error.response.data.message || 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to regenerate salary sheet.')
    }
  } finally {
    generating.value = false
  }
}

// Update salary sheet
const updateSalarySheet = async () => {
  if (staffData.value.length === 0) {
    showErrorAlert('Error', 'No staff data to update.')
    return
  }

  saving.value = true
  errors.value = {}

  try {
    showLoadingAlert('Updating salary sheet...')

    const response = await axios.put(`/api/staff-salary/${salaryId}`, {
      title: form.title,
      month: form.month,
      year: form.year,
      staffData: staffData.value
    })

    closeAlert()

    if (response.data.success) {
      showSuccessAlert('Success!', 'Salary sheet updated successfully')
      router.push({
        name: 'staff-salary.index',
        query: { updated: 'true' }
      })
    }

  } catch (error) {
    closeAlert()
    console.error('Error updating salary:', error)
    
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', error.response.data.message || 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to update salary sheet.')
    }
  } finally {
    saving.value = false
  }
}

// Load data on component mount
onMounted(() => {
  loadSalaryData()
})
</script>