<template>
  <AppLayout>
    <!-- Header Section -->
    <div class="mb-8">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">Create Salary Payment</h1>
          <p class="text-gray-600 mt-2">Process staff salary payment</p>
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

    <!-- Main Form Section -->
    <div class="grid grid-cols-1 gap-8">
      <div class="space-y-6">
        <!-- Payment Info Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
              <i class="fas fa-file-invoice-dollar text-white"></i>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-800">Payment Information</h3>
              <p class="text-gray-600 text-sm">Fill in the payment details</p>
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
                <template #option="option">
                  <div class="font-medium text-gray-800">
                    <span class="text-green-600 font-semibold mr-2 text-xs">
                      {{ option.id }}
                    </span>
                    {{ option.title }}
                  </div>
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
                <template #option="option">
                  <div class="font-medium text-gray-800">
                    <span class="text-green-600 font-semibold mr-2 text-xs">
                      {{ option.id }}
                    </span>
                    {{ option.account_name }}
                  </div>
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
                <i class="fas fa-cogs"></i>
                {{ preparing ? 'Preparing...' : 'Prepare' }}
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
              <p class="text-gray-600 text-sm">Enter payment amounts for each staff</p>
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
                <tr v-for="staff in staffData" :key="staff.id" class="hover:bg-gray-50">
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
            <button 
              @click="resetForm"
              class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
            >
              <i class="fas fa-redo"></i>
              Reset
            </button>

            <button 
              @click="savePayment"
              :disabled="saving || totalPayment === 0"
              class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-6 py-3 rounded-lg font-semibold transition-colors"
            >
              <i class="fas fa-save"></i>
              {{ saving ? 'Saving...' : 'Save Payment' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const preparing = ref(false)
const saving = ref(false)
const staffData = ref([])
const salarySheets = ref([])
const accounts = ref([])

const form = reactive({
  sheet_id: '',
  date: new Date().toISOString().split('T')[0],
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
    showErrorAlert('Error', 'Failed to load salary sheets')
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
    showErrorAlert('Error', 'Failed to load accounts')
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
      showErrorAlert('Error', error.response?.data?.message || 'Failed to prepare payment. Please try again.')
    }
  } finally {
    preparing.value = false
  }
}

const savePayment = async () => {
  if (staffData.value.length === 0) {
    showErrorAlert('Error', 'No staff data to save. Please prepare payment first.')
    return
  }

  if (totalPayment.value === 0) {
    showErrorAlert('Error', 'Please enter payment amounts for at least one staff.')
    return
  }

  saving.value = true
  errors.value = {}

  try {
    showLoadingAlert('Saving payment...')

    const paymentAmounts = {}
    staffData.value.forEach(staff => {
      if (staff.payment_amount > 0) {
        paymentAmounts[staff.id] = staff.payment_amount
      }
    })

    const response = await axios.post('/api/staff-salary-payment', {
      sheet_id: form.sheet_id,
      date: form.date,
      acc_id: form.acc_id,
      payment_amount: paymentAmounts
    })

    closeAlert()

    if (response.data.success) {
      showSuccessAlert('Success!', 'Payment saved successfully')
      router.push({
        name: 'staff-salary-payment.index',
        query: { created: 'true' }
      })
    } else {
      showErrorAlert('Error', response.data.message || 'Failed to save payment')
      saving.value = false
    }

  } catch (error) {
    closeAlert()
    saving.value = false
    console.error('Error saving payment:', error)
    
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', error.response.data.message || 'Please check the form for errors.')
    } else {
      showErrorAlert('Error', error.response?.data?.message || 'Failed to save payment.')
    }
  }
}

const resetForm = () => {
  form.sheet_id = ''
  form.date = new Date().toISOString().split('T')[0]
  form.acc_id = ''
  staffData.value = []
  errors.value = {}
}

onMounted(async () => {
  await fetchSalarySheets()
  await fetchAccounts()
})
</script>