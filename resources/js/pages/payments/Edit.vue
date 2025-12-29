<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-1 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Edit Payment
        </h1>
      </div>
      <router-link 
        to="/payments"
        class="inline-flex items-center gap-1 px-3 py-1.5 border bg-blue-600 hover:bg-blue-700 rounded-lg text-gray-100 text-sm font-medium transition-colors"
      >
        <i class="fas fa-arrow-left text-xs"></i>
        Back to List
      </router-link>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-xl shadow border border-gray-100 p-4 text-center">
      <div class="inline-block">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
        <p class="text-gray-600 text-sm">Loading payment details...</p>
      </div>
    </div>

    <!-- Payment Not Found -->
    <div v-if="!loading && paymentNotFound" class="bg-white rounded-xl shadow border border-gray-100 p-4 text-center">
      <div class="inline-block p-4">
        <i class="fas fa-exclamation-triangle text-3xl text-red-400 mb-2"></i>
        <p class="text-gray-600 mb-1">Payment not found.</p>
        <p class="text-xs text-gray-500">The payment you're looking for doesn't exist.</p>
      </div>
    </div>

    <!-- Payment Found - Show Student Info and Payment Form -->
    <div v-if="!loading && payment && !paymentNotFound" class="space-y-3">
      <!-- Student Information Card -->
      <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="p-2">
          <div class="flex flex-col md:flex-row gap-3 items-start md:items-center">
            <!-- Student Image -->
            <div class="shrink-0">
              <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow">
                <img 
                  v-if="payment.class_wise_student?.student?.student_image"
                  :src="getStudentImageUrl(payment.class_wise_student.student.student_image)" 
                  :alt="payment.class_wise_student.student.student_name"
                  class="w-full h-full object-cover"
                  @error="handleImageError"
                >
                <div v-else class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
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
                  <div class="font-semibold text-gray-800 text-sm">{{ payment.class_wise_student?.student?.student_name || 'N/A' }}</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                  <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                    <i class="fas fa-id-card text-xs"></i>
                    ID Card No.
                  </div>
                  <div class="font-semibold text-gray-800 text-sm">{{ payment.class_wise_student?.student?.id_card_number || 'N/A' }}</div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                  <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                    <i class="fas fa-sort-numeric-up text-xs"></i>
                    Class Roll
                  </div>
                  <div class="font-semibold text-gray-800">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      {{ payment.class_wise_student?.class_roll || 'N/A' }}
                    </span>
                  </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-200">
                  <div class="text-xs text-gray-500 mb-0.5 flex items-center gap-1">
                    <i class="fas fa-phone text-xs"></i>
                    Mobile
                  </div>
                  <div class="font-semibold text-gray-800 text-sm">{{ payment.class_wise_student?.student?.mobile_number || 'N/A' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Academic Info -->
        <div class="px-3 py-2 border-t border-gray-200 bg-gray-50">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Session</div>
              <div class="font-semibold text-gray-800 text-sm">{{ payment.class_wise_student?.session?.session_name || 'N/A' }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Class</div>
              <div class="font-semibold text-gray-800 text-sm">{{ payment.class_wise_student?.class?.class_name || 'N/A' }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Version</div>
              <div class="font-semibold text-gray-800 text-sm">{{ payment.class_wise_student?.version?.version_name || 'N/A' }}</div>
            </div>
            <div class="text-center">
              <div class="text-xs text-gray-500 mb-0.5">Section</div>
              <div class="font-semibold text-gray-800 text-sm">{{ payment.class_wise_student?.section?.section_name || 'N/A' }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Form -->
      <div class="bg-white rounded-xl shadow border border-gray-100 p-2">
        <!-- Payment Details -->
        <div class="mb-3">
          <div class="grid grid-cols-2 gap-4 mb-2">
            <!-- Title Section -->
            <div class="flex items-center gap-2">
              <div class="p-1.5 bg-green-100 rounded-lg">
                <i class="fas fa-receipt text-green-600 text-sm"></i>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800">Payment Details</h3>
                <p class="text-xs text-gray-500">Edit payment amounts</p>
              </div>
            </div>

            <!-- Payment Date and Account -->
            <div class="grid grid-cols-2 gap-2">
              <!-- Payment Date -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
                  <i class="fas fa-calendar-day text-gray-400 text-xs"></i>
                  Payment Date
                  <span class="text-red-500">*</span>
                </label>
                <input 
                  type="date" 
                  v-model="paymentData.payment_date"
                  class="px-2 py-2 text-sm border border-gray-300 rounded-lg w-full"
                  required
                />
              </div>

              <!-- Account Selection -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-1">
                  <i class="fas fa-university text-gray-400 text-xs"></i>
                  Account
                  <span class="text-red-500">*</span>
                </label>
                <v-select
                  v-model="paymentData.account_id"
                  :options="accounts"
                  label="account_name"
                  :reduce="account => account.id"
                  placeholder="Select Account"
                  class="v-select-custom text-sm"
                >
                  <template #option="option">
                    <div class="flex items-center gap-1 text-sm">
                      <i class="fas fa-university text-blue-500 text-xs"></i>
                      {{ option.account_name }}
                    </div>
                  </template>
                  <template #selected-option="option">
                    <div class="flex items-center gap-1">
                      <i class="fas fa-university text-blue-500 text-xs"></i>
                      {{ option.account_name }}
                    </div>
                  </template>
                </v-select>
              </div>
            </div>
          </div>
        </div>

        <!-- Fee Heads Table -->
        <div>
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
              <div class="p-1.5 bg-blue-100 rounded-lg">
                <i class="fas fa-money-bill-wave text-blue-600 text-sm"></i>
              </div>
              <div>
                <h3 class="text-sm font-semibold text-gray-800">Fee Heads and Payment Amounts</h3>
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
                  <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 uppercase w-32">
                    Previous Amount
                  </th>
                  <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 uppercase w-48">
                    New Amount
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr 
                  v-for="(feeHead, index) in groupedFeeHeads" 
                  :key="feeHead.head_id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-2 py-2">
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
                        <div class="text-sm font-semibold text-gray-900">{{ feeHead.head_name }}</div>
                      </div>
                    </div>
                  </td>

                  <td class="px-2 py-2 text-right">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      {{ formatAmount(feeHead.total_paid_amount) }}
                    </span>
                  </td>
                  
                  <td class="px-2 py-2">
                    <div class="relative">
                      <input 
                        type="number" 
                        v-model="paymentAmounts[feeHead.head_id]"
                        :min="0"
                        step="0.01"
                        placeholder="Enter new amount"
                        class="pl-2 pr-4 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
                        @input="calculateTotal"
                        @blur="calculateTotal"
                      />
                    </div>
                  </td>
                </tr>
                
                <!-- Total Row -->
                <tr class="bg-blue-50 border-t-2 border-blue-300">
                  <td colspan="2" class="px-2 py-2.5 text-right text-sm text-gray-800">
                    <span class="font-bold text-base">Total Payment Amount:</span>
                  </td>
                  <td class="px-2 py-2.5">
                    <div class="text-lg font-bold text-gray-600 text-right">
                      {{ formatAmount(originalTotalAmount) }} /=
                    </div>
                  </td>
                  <td class="px-2 py-2.5">
                    <div class="text-lg font-bold text-blue-600 text-right">
                      {{ formatAmount(totalPayment) }} /=
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Action Buttons -->
          <div class="mt-3 pt-3 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
              <div class="text-xs text-gray-600">
                <i class="fas fa-info-circle text-blue-500"></i>
                Payment ID: <span class="font-semibold text-blue-600">#{{ payment.id }}</span>
              </div>
      
              <button 
                @click="updatePayment"
                :disabled="isSaving || !canSave"
                class="px-3 py-1.5 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-semibold transition-colors flex items-center gap-1 shadow disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i v-if="isSaving" class="fas fa-spinner fa-spin text-xs"></i>
                <i v-else class="fas fa-save text-xs"></i>
                {{ isSaving ? 'Updating...' : 'Update Payment' }}
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
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import { showErrorAlert, showSuccessAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

// Data
const loading = ref(false)
const paymentNotFound = ref(false)
const payment = ref(null)
const isSaving = ref(false)
const accounts = ref([])

// Payment data
const paymentData = ref({
  payment_date: '',
  account_id: null
})

const paymentAmounts = ref({})
const totalPayment = ref(0)
const originalTotalAmount = ref(0)

// Computed - Group payment details by head_id
const groupedFeeHeads = computed(() => {
  if (!payment.value || !payment.value.payment_details) return []
  
  const grouped = {}
  
  payment.value.payment_details.forEach(detail => {
    if (!grouped[detail.head_id]) {
      grouped[detail.head_id] = {
        head_id: detail.head_id,
        head_name: detail.fee_head?.head_name || 'Unknown',
        total_paid_amount: 0,
        details: []
      }
    }
    
    grouped[detail.head_id].total_paid_amount += parseFloat(detail.paid_amount || 0)
    grouped[detail.head_id].details.push(detail)
  })
  
  return Object.values(grouped)
})

// Computed
const canSave = computed(() => {
  if (!payment.value || !paymentData.value.payment_date || !paymentData.value.account_id) {
    return false
  }
  return totalPayment.value > 0
})

// Methods
const fetchAccounts = async () => {
  try {
    const response = await axios.get('/api/cash-banks')
    
    if (Array.isArray(response.data)) {
      accounts.value = response.data
    } else if (response.data.success && response.data.data) {
      accounts.value = response.data.data
    } else {
      accounts.value = []
    }
  } catch (error) {
    showErrorAlert('Error', 'Failed to load accounts')
  }
}

const fetchPaymentDetails = async () => {
  const paymentId = route.params.id
  
  if (!paymentId) {
    paymentNotFound.value = true
    return
  }

  loading.value = true
  paymentNotFound.value = false

  try {
    const response = await axios.get(`/api/payments/${paymentId}`)
    
    if (response.data.success) {
      payment.value = response.data.data
      
      // Set payment data
      paymentData.value.payment_date = payment.value.payment_date
      paymentData.value.account_id = payment.value.account_id
      
      // Initialize payment amounts from grouped fee heads
      originalTotalAmount.value = 0
      groupedFeeHeads.value.forEach(feeHead => {
        paymentAmounts.value[feeHead.head_id] = parseFloat(feeHead.total_paid_amount || 0)
        originalTotalAmount.value += parseFloat(feeHead.total_paid_amount || 0)
      })
      
      calculateTotal()
    }
  } catch (error) {
    if (error.response?.status === 404) {
      paymentNotFound.value = true
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to load payment details')
    }
  } finally {
    loading.value = false
  }
}

const calculateTotal = () => {
  let total = 0
  Object.keys(paymentAmounts.value).forEach(headId => {
    const amount = parseFloat(paymentAmounts.value[headId] || 0)
    total += amount
  })
  totalPayment.value = total
}

const resetPaymentForm = () => {
  // Reset to original amounts
  groupedFeeHeads.value.forEach(feeHead => {
    paymentAmounts.value[feeHead.head_id] = parseFloat(feeHead.total_paid_amount || 0)
  })
  
  paymentData.value.payment_date = payment.value.payment_date
  paymentData.value.account_id = payment.value.account_id
  
  calculateTotal()
}

const updatePayment = async () => {
  if (!canSave.value) {
    showErrorAlert('Error', 'Please fill all required fields and enter payment amounts')
    return
  }

  const payment_details = []
  
  groupedFeeHeads.value.forEach(feeHead => {
    const amount = parseFloat(paymentAmounts.value[feeHead.head_id] || 0)
    if (amount > 0) {
      payment_details.push({
        head_id: feeHead.head_id,
        paid_amount: amount
      })
    }
  })

  if (payment_details.length === 0) {
    showErrorAlert('Error', 'Please enter at least one payment amount')
    return
  }

  const paymentPayload = {
    payment_date: paymentData.value.payment_date,
    account_id: paymentData.value.account_id,
    total_paid_amount: totalPayment.value,
    payment_details: payment_details
  }

  isSaving.value = true
  showLoadingAlert('Updating Payment', 'Please wait...')

  try {
    const response = await axios.put(`/api/payments/${payment.value.id}`, paymentPayload)
    
    if (response.data.success) {
      closeAlert()
      await showSuccessAlert('Success', 'Payment updated successfully')
      router.push('/payments')
    }
  } catch (error) {
    closeAlert()
    const errorMessage = error.response?.data?.message || 'Failed to update payment'
    const errors = error.response?.data?.errors
    
    if (errors) {
      const errorList = Object.values(errors).flat().join('\n')
      showErrorAlert('Validation Error', errorList)
    } else {
      showErrorAlert('Error', errorMessage)
    }
  } finally {
    isSaving.value = false
  }
}

const formatAmount = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const getStudentImageUrl = (imagePath) => {
  if (!imagePath) return '/assets/images/default-avatar.png'
  if (imagePath.startsWith('http') || imagePath.startsWith('/assets')) {
    return imagePath
  }
  return `/assets/images/student_images/${imagePath}`
}

const handleImageError = (event) => {
  event.target.style.display = 'none'
  event.target.parentElement.innerHTML = `
    <div class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
      <i class="fas fa-user-graduate text-blue-400 text-2xl"></i>
    </div>
  `
}

onMounted(() => {
  fetchAccounts()
  fetchPaymentDetails()
})
</script>