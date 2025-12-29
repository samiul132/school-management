<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Edit Salary Payment #{{ paymentId }}</h1>
          <p class="text-gray-600 mt-2">Update salary payment details</p>
        </div>

        <div class="mb-6">
          <router-link
            :to="{ name: 'staff-salary-payment.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors"
          >
            <i class="fas fa-arrow-left"></i>
            Back to List
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !form.sheet_id" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Main Form Section -->
    <div v-else class="grid grid-cols-1 gap-8">
      <div class="space-y-6">
        <!-- Payment Info Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-file-invoice-dollar text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Payment Information</h3>
              <p class="text-gray-600 text-sm">Update payment details</p>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Salary Sheet -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Salary Sheet <span class="text-red-500">*</span>
              </label>
              <v-select
                v-model="form.sheet_id"
                :options="salarySheets"
                :filterable="true"
                :searchable="true"
                label="title"
                :reduce="sheet => sheet.id"
                placeholder="Select Salary Sheet..."
                :class="{ 'border-red-500': errors.sheet_id }"
              >
                <template #selected-option="option">
                  <span class="font-semibold text-gray-700">
                    {{ option.title }}
                  </span>
                </template>
              </v-select>
              <p v-if="errors.sheet_id" class="text-red-500 text-sm">{{ errors.sheet_id[0] }}</p>
            </div>

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

            <!-- From Account -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                From Account <span class="text-red-500">*</span>
              </label>
              <v-select
                v-model="form.acc_id"
                :options="accounts"
                :filterable="true"
                :searchable="true"
                label="account_name"
                :reduce="account => account.id"
                placeholder="Select account..."
                :class="{ 'border-red-500': errors.account_id }"
              >
                <template #selected-option="option">
                  <span class="font-semibold text-gray-700">
                    {{ option.account_name }}
                  </span>
                </template>
              </v-select>

              <p v-if="errors.acc_id" class="text-red-500 text-sm">{{ errors.acc_id[0] }}</p>
            </div>

            <!-- Prepare Button -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">&nbsp;</label>
              <button 
                @click="preparePayment"
                :disabled="preparing || !form.sheet_id || !form.date"
                class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-4 py-2 rounded-lg font-semibold transition-colors flex justify-center items-center gap-2"
              >
                <i class="fas fa-sync-alt"></i>
                {{ preparing ? 'Preparing...' : 'Re-Prepare' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Staff Payment Table -->
        <div v-if="staffData.length > 0" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
              <i class="fas fa-users text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Staff Payment Details</h3>
              <p class="text-gray-600 text-sm">Update payment amounts for each staff</p>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff Name</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff ID</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Gross Salary</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Payable</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Due</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Payment Amount</th>
                  <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Current Due</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="staff in staffData" :key="staff.staff_id" class="hover:bg-gray-50">
                  <td class="px-4 py-3 text-sm text-gray-800">
                    <div class="font-medium">{{ staff.staff_name }}</div>
                  </td>
                  <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ staff.staff_id }}</td>
                  <td class="px-4 py-3 text-right text-sm text-gray-800">৳{{ formatCurrency(staff.gross_salary) }}</td>
                  <td class="px-4 py-3 text-right text-sm text-gray-800">৳{{ formatCurrency(staff.payable_salary) }}</td>
                  <td class="px-4 py-3 text-right text-sm text-blue-600 font-semibold">৳{{ formatCurrency(staff.total_paid) }}</td>
                  <td class="px-4 py-3 text-right text-sm text-orange-600 font-semibold">৳{{ formatCurrency(staff.due) }}</td>
                  <td class="px-4 py-3">
                    <input 
                      type="number" 
                      v-model.number="staff.payment_amount"
                      @input="updateCurrentDue(staff)"
                      min="0"
                      :max="staff.due"
                      step="0.01"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-right"
                    >
                  </td>
                  <td class="px-4 py-3 text-right text-sm font-semibold" :class="staff.current_due > 0 ? 'text-red-600' : 'text-green-600'">
                    ৳{{ formatCurrency(staff.current_due) }}
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-gray-100">
                <tr>
                  <td colspan="6" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Payment:</td>
                  <td class="px-4 py-3 text-right text-sm font-bold text-green-700">৳{{ formatCurrency(totalPayment) }}</td>
                  <td class="px-4 py-3 text-right text-sm font-bold text-red-700">৳{{ formatCurrency(totalCurrentDue) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Save Button -->
          <div class="mt-6 flex justify-end gap-3">
            <router-link
              :to="{ name: 'staff-salary-payment.index' }"
              class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
            >
              <i class="fas fa-times"></i>
              Cancel
            </router-link>

            <button 
              @click="updatePayment"
              :disabled="saving || totalPayment === 0"
              class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
            >
              <i class="fas fa-save"></i>
              {{ saving ? 'Updating...' : 'Update Payment' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()
const loading = ref(false)
const preparing = ref(false)
const saving = ref(false)
const staffData = ref([])
const salarySheets = ref([])
const accounts = ref([])
const paymentId = ref(route.params.id)

const form = reactive({
  sheet_id: '',
  date: '',
  acc_id: '',
})

const errors = ref({})

const totalPayment = computed(() => {
  return staffData.value.reduce((sum, staff) => sum + (Number(staff.payment_amount) || 0), 0)
})

const totalCurrentDue = computed(() => {
  return staffData.value.reduce((sum, staff) => sum + (Number(staff.current_due) || 0), 0)
})

const formatCurrency = (amount) => {
  return Number(amount || 0).toLocaleString('en-BD', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const updateCurrentDue = (staff) => {
  const paymentAmount = Number(staff.payment_amount) || 0
  staff.current_due = staff.due - paymentAmount
}

const fetchPayment = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/staff-salary-payment/${paymentId.value}`)
    
    let payment
    if (response.data.success) {
      payment = response.data.data
    } else {
      payment = response.data
    }

    if (!payment) {
      throw new Error('Payment not found')
    }

    form.sheet_id = payment.sheet_id
    form.date = payment.date
    form.acc_id = payment.acc_id

    if (payment.payment_details && payment.payment_details.length > 0) {
      staffData.value = payment.payment_details.map(detail => {
        const staff = detail.salary_details?.staff
        const salaryDetail = detail.salary_details
        
        const totalPaid = detail.paid_amount
        const due = salaryDetail.payable_salary - totalPaid

        return {
          id: detail.salary_details_id,
          staff_id: detail.staff_id,
          staff_name: staff ? `${staff.first_name} ${staff.last_name}` : 'N/A',
          gross_salary: salaryDetail.gross_salary,
          payable_salary: salaryDetail.payable_salary,
          advance_payment: salaryDetail.advance_payment,
          total_paid: detail.paid_amount,
          due: salaryDetail.payable_salary,
          payment_amount: detail.paid_amount,
          current_due: salaryDetail.payable_salary - detail.paid_amount
        }
      })
    }

  } catch (error) {
    console.error('Error fetching payment:', error)
    showErrorAlert('Error', 'Failed to load payment')
    router.push({ name: 'staff-salary-payment.index' })
  } finally {
    loading.value = false
  }
}

const fetchSalarySheets = async () => {
  try {
    const response = await axios.get('/api/staff-salary')
    if (response.data.success) {
      salarySheets.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      salarySheets.value = response.data
    }
  } catch (error) {
    console.error('Error fetching salary sheets:', error)
  }
}

const fetchAccounts = async () => {
  try {
    const response = await axios.get('/api/cash-banks')
    if (response.data.success) {
      accounts.value = response.data.data || []
    } else if (Array.isArray(response.data)) {
      accounts.value = response.data
    }
  } catch (error) {
    console.error('Error fetching accounts:', error)
  }
}

const preparePayment = async () => {
  if (!form.sheet_id || !form.date) {
    showErrorAlert('Error', 'Please select salary sheet and date')
    return
  }

  preparing.value = true
  errors.value = {}

  try {
    showLoadingAlert('Preparing payment...')

    const response = await axios.post('/api/staff-salary-payment/prepare', {
      sheet_id: form.sheet_id,
      date: form.date
    })

    closeAlert()

    if (response.data.success) {
      staffData.value = response.data.data.staffData.map(staff => ({
        ...staff,
        payment_amount: 0,
        current_due: staff.due
      }))

      showSuccessAlert('Success!', 'Payment data prepared successfully')
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to prepare payment')
    }

  } catch (error) {
    closeAlert()
    console.error('Error preparing payment:', error)
    
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', error.response.data.message || 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to prepare payment.')
    }
  } finally {
    preparing.value = false
  }
}

const updatePayment = async () => {
  if (staffData.value.length === 0) {
    showErrorAlert('Error', 'No staff data to update.')
    return
  }

  if (totalPayment.value === 0) {
    showErrorAlert('Error', 'Please enter payment amounts for at least one staff.')
    return
  }

  saving.value = true
  errors.value = {}

  try {
    showLoadingAlert('Updating payment...')

    const paymentAmounts = {}
    staffData.value.forEach(staff => {
      if (staff.payment_amount > 0) {
        paymentAmounts[staff.staff_id] = staff.payment_amount
      }
    })

    const response = await axios.put(`/api/staff-salary-payment/${paymentId.value}`, {
      sheet_id: form.sheet_id,
      date: form.date,
      acc_id: form.acc_id,
      payment_amount: paymentAmounts
    })

    closeAlert()

    if (response.data.success) {
      showSuccessAlert('Success!', 'Payment updated successfully')
      router.push({
        name: 'staff-salary-payment.index',
        query: { updated: 'true' }
      })
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to update payment')
    }

  } catch (error) {
    closeAlert()
    saving.value = false
    console.error('Error updating payment:', error)
    
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', error.response.data.message || 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to update payment.')
    }
  }
}

onMounted(async () => {
  await fetchSalarySheets()
  await fetchAccounts()
  await fetchPayment()
})
</script>