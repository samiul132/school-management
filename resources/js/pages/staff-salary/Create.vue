<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Create Salary Sheet</h1>
          <p class="text-gray-600 mt-2">Generate monthly salary sheet for all staff</p>
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
      <!-- Left Column - Salary Sheet Info -->
      <div class="lg:col-span-8 space-y-2">
        <!-- Generate Salary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-file-invoice-dollar text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Salary Sheet Information</h3>
              <p class="text-gray-600 text-sm">Fill in the details to generate salary</p>
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

            <!-- Generate Button -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
              <button 
                @click="generateSalary"
                :disabled="generating || !form.month || !form.year || !form.title"
                class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-4 py-2 rounded-lg font-semibold transition-colors flex justify-center items-center gap-2"
              >
                <i class="fas fa-cogs"></i>
                {{ generating ? 'Generating...' : 'Generate' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Generated Salary Table -->
        <div v-if="staffData.length > 0" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-users text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Generated Salary Details</h3>
              <p class="text-gray-600 text-sm">Review staff salaries before saving</p>
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
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Basic Salary</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross Salary</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Over Time</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Advance Payment</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Payable Salary</th>
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

          <!-- Save Button -->
          <div class="mt-6 flex justify-end gap-3">
            <button 
              @click="resetForm"
              class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors"
            >
              <i class="fas fa-redo"></i>
              Reset
            </button>

            <button 
              @click="saveSalarySheet"
              :disabled="saving"
              class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-lg font-semibold transition-colors"
            >
              <i class="fas fa-save"></i>
              {{ saving ? 'Saving...' : 'Save Salary Sheet' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive} from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const generating = ref(false)
const saving = ref(false)
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

const generateSalary = async () => {
  if (!form.title || !form.month || !form.year) {
    showErrorAlert('Error', 'Please fill all required fields')
    return
  }

  generating.value = true
  errors.value = {}

  try {
    showLoadingAlert('Generating salary sheet...')

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

      showSuccessAlert('Success!', 'Salary sheet generated successfully')
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to generate salary sheet')
    }

  } catch (error) {
    closeAlert()
    console.error('Error generating salary:', error)
    
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', error.response.data.message || 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to generate salary sheet. Please try again.')
    }
  } finally {
    generating.value = false
  }
}


const saveSalarySheet = async () => {
  if (staffData.value.length === 0) {
    showErrorAlert('Error', 'No staff data to save. Please generate salary first.')
    return
  }

  saving.value = true
  errors.value = {}

  try {
    showLoadingAlert('Saving salary sheet...')

    const response = await axios.post('/api/staff-salary', {
      title: form.title,
      month: form.month,
      year: form.year,
      staffData: staffData.value
    })

    closeAlert()

    if (response.data.success) {
      showSuccessAlert('Success!', 'Salary sheet saved successfully')
      router.push({
        name: 'staff-salary.index',
        query: { created: 'true' }
      })
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to save salary sheet')
      saving.value = false
    }

  } catch (error) {
    closeAlert()
    saving.value = false
    console.error('Error saving salary:', error)
    
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', error.response.data.message || 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to save salary sheet.')
    }
  }
}

const resetForm = () => {
  form.title = ''
  form.month = ''
  form.year = ''
  staffData.value = []
  summary.totalStaff = 0
  summary.totalPayableSalary = 0
  summary.totalAdvance = 0
  summary.totalOvertime = 0
  errors.value = {}
}

</script>