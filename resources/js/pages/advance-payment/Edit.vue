<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Edit Advance Payment #{{ paymentId }}</h1>
          <p class="text-gray-600 mt-2">Update payment details</p>
        </div>

        <div class="mb-6">
          <router-link
            :to="{ name: 'advance-payment.index' }"
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
      <!-- Left Column - Payment Information -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Payment Details Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-money-bill-wave text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Payment Information</h3>
              <p class="text-gray-600 text-sm">Update payment details</p>
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

            <!-- Account Selection -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                From Account <span class="text-red-500">*</span>
              </label>
              <v-select
                v-model="form.account_id"
                :options="accountsList"
                :filterable="true"
                :searchable="true"
                label="name"
                :reduce="account => account.id"
                placeholder="Select account..."
                :class="{ 'border-red-500': errors.account_id }"
              >
                <template #option="option">
                  <div class="font-medium text-gray-800">
                    <span class="text-green-600 font-semibold mr-2 text-xs">#{{ option.id }}</span>
                    {{ option.name }}
                  </div>
                </template>
              </v-select>
              <p v-if="errors.account_id" class="text-red-500 text-sm">{{ errors.account_id[0] }}</p>
            </div>

            <!-- Amount -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Amount <span class="text-red-500">*</span>
              </label>
              <input 
                type="number" 
                step="0.01"
                v-model="form.amount"
                placeholder="0.00"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                :class="{ 'border-red-500': errors.amount }"
              >
              <p v-if="errors.amount" class="text-red-500 text-sm">{{ errors.amount[0] }}</p>
            </div>

            <!-- Month -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Month</label>
              <select 
                v-model="form.month"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
              >
                <option value="">Select Month</option>
                <option v-for="(name, value) in months" :key="value" :value="value">
                  {{ name }}
                </option>
              </select>
            </div>

            <!-- Year -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Year</label>
              <select 
                v-model="form.year"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
              >
                <option value="">Select Year</option>
                <option v-for="year in years" :key="year" :value="year">
                  {{ year }}
                </option>
              </select>
            </div>

            <!-- Remarks -->
            <div class="space-y-2 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700">Remarks</label>
              <textarea 
                v-model="form.remarks"
                rows="4"
                placeholder="Enter remarks..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-none"
              ></textarea>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex justify-end"> 
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
              <router-link
                :to="{ name: 'advance-payment.index' }"
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
                {{ loading ? 'Updating...' : 'Update Payment' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Summary & Info -->
      <div class="space-y-8">
        <!-- Payment Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-info-circle text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Payment Summary</h3>
              <p class="text-gray-600 text-sm">Payment details</p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="p-4 bg-gray-50 rounded-lg">
              <p class="text-sm text-gray-600 mb-2">Payment Amount:</p>
              <p class="text-3xl font-bold text-green-600">
                ৳{{ formatAmount(form.amount || 0) }}
              </p>
            </div>

            <div v-if="form.month && form.year" class="p-4 bg-blue-50 rounded-lg">
              <p class="text-sm text-blue-600 mb-2">Payment For:</p>
              <p class="text-lg font-medium text-blue-800">
                {{ months[form.month] }} {{ form.year }}
              </p>
            </div>

            <div v-if="form.date" class="p-4 bg-green-50 rounded-lg">
              <p class="text-sm text-green-600 mb-2">Payment Date:</p>
              <p class="text-sm font-medium text-green-800">
                {{ formatDate(form.date) }}
              </p>
            </div>
          </div>
        </div>

        <!-- Payment Info -->
        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6">
          <div class="flex items-center gap-3 mb-4">
            <i class="fas fa-info-circle text-gray-500 text-xl"></i>
            <h4 class="text-lg font-semibold text-gray-800">Payment Information</h4>
          </div>
          <div class="space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Created:</span>
              <span class="text-gray-800 font-medium">{{ formatDate(paymentData.created_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Last Updated:</span>
              <span class="text-gray-800 font-medium">{{ formatDate(paymentData.updated_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Payment ID:</span>
              <span class="text-gray-800 font-mono">{{ paymentId }}</span>
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
              <span>Verify amount before updating</span>
            </li>
            <li class="flex items-start gap-2">
              <i class="fas fa-check-circle text-blue-500 mt-1"></i>
              <span>Check account balance will be updated</span>
            </li>
          </ul>
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
const loading = ref(false)
const staffList = ref([])
const accountsList = ref([])

const paymentId = ref(route.params.id)
const paymentData = ref({
  created_at: '',
  updated_at: ''
})

const form = reactive({
  date: '',
  staff_id: null,
  account_id: null,
  month: '',
  year: '',
  amount: 0,
  remarks: ''
})

const errors = ref({})

const months = {
  '01': 'January', '02': 'February', '03': 'March', '04': 'April',
  '05': 'May', '06': 'June', '07': 'July', '08': 'August',
  '09': 'September', '10': 'October', '11': 'November', '12': 'December'
}

const years = (() => {
  const currentYear = new Date().getFullYear()
  const yearList = []
  for (let year = 2022; year <= currentYear; year++) {
    yearList.push(year)
  }
  return yearList
})()

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
    const response = await axios.get('/api/advance-payment/staff-list')
    if (response.data.success) {
      staffList.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch staff list:', error)
    showErrorAlert('Error', 'Failed to load staff list')
  }
}

const fetchAccountsList = async () => {
  try {
    const response = await axios.get('/api/advance-payment/accounts-list')
    if (response.data.success) {
      accountsList.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch accounts list:', error)
    showErrorAlert('Error', 'Failed to load accounts list')
  }
}

const fetchPayment = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/advance-payment/${paymentId.value}`)
    
    let payment
    if (response.data.success) {
      payment = response.data.data.advance_payment
      const transaction = response.data.data.transaction
      
      if (transaction) {
        form.account_id = transaction.account_id
      }
    } else {
      payment = response.data
    }

    if (!payment) {
      throw new Error('Payment not found')
    }

    form.date = formatDateForInput(payment.date)
    form.staff_id = payment.staff_id
    form.month = payment.month || ''
    form.year = payment.year ? payment.year.toString() : ''
    form.amount = parseFloat(payment.amount) || 0
    form.remarks = payment.remarks || ''

    paymentData.value = {
      created_at: payment.created_at,
      updated_at: payment.updated_at
    }
  } catch (error) {
    console.error('Error fetching payment:', error)
    showErrorAlert('Error', 'Failed to load payment data')
    router.push({ name: 'advance-payment.index' })
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

const formatAmount = (amount) => {
  return new Intl.NumberFormat('en-BD', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(amount)
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

    if (!form.account_id) {
      errors.value.account_id = ['The account field is required.']
      loading.value = false
      return
    }

    if (!form.date) {
      errors.value.date = ['The date field is required.']
      loading.value = false
      return
    }

    if (!form.amount || form.amount <= 0) {
      errors.value.amount = ['The amount must be greater than 0.']
      loading.value = false
      return
    }

    showLoadingAlert('Updating payment...')

    const response = await axios.put(`/api/advance-payment/${paymentId.value}`, {
      date: form.date,
      staff_id: form.staff_id,
      account_id: form.account_id,
      month: form.month,
      year: form.year,
      amount: form.amount,
      remarks: form.remarks
    })
    
    closeAlert()
    
    if (response.data.success) {
      showSuccessAlert('Success', 'Payment updated successfully')
      router.push({ 
        name: 'advance-payment.index',
        query: { updated: 'true' }
      })
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to update payment')
    }
    
  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors.')
    } else if (error.response && error.response.data) {
      showErrorAlert('Error', error.response.data.message || 'Failed to update payment. Please try again.')
    } else {
      showErrorAlert('Error', 'Failed to update payment. Please try again.')
    }
  }
}

onMounted(async () => {
  await fetchStaffList()
  await fetchAccountsList()
  await fetchPayment()
})
</script>