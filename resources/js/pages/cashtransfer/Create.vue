<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Create New Cash Transfer
          </h1>
          <p class="text-gray-600">
            Transfer funds between cash/bank accounts
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'cash-transfers.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Transfers
          </router-link>
        </div>
      </div>
    </div>

    <!-- Form Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <!-- Form Header -->
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Transfer Information
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Fill in the transfer details between accounts
            </p>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div class="space-y-6">
              <!-- Transfer Date -->
              <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                  Transfer Date <span class="text-red-500">*</span>
                </label>
                <input
                  type="date"
                  id="date"
                  v-model="form.date"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800"
                  :class="{ 'border-red-500': errors.date }"
                />
                <p v-if="errors.date" class="mt-1 text-sm text-red-600">
                  {{ errors.date[0] }}
                </p>
              </div>

              <!-- From Account -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  From Account <span class="text-red-500">*</span>
                </label>
                <v-select
                  v-model="form.from_account"
                  :options="filteredFromAccounts"
                  :reduce="account => account"
                  label="account_name"
                  placeholder="Select Source Account"
                  :class="{ 'border-red-500': errors.from_account }"
                  class="v-select-custom"
                >
                  <template #option="option">
                    <div class="flex justify-between items-center w-full">
                      <span>{{ option.account_name }}</span>
                      <span class="text-sm font-medium" :class="getBalanceColor(option.current_balance)">
                        {{ formatCurrency(option.current_balance) }}
                      </span>
                    </div>
                  </template>
                  <template #selected-option="option">
                    <div class="flex justify-between items-center w-full">
                      <span>{{ option.account_name }}</span>
                    </div>
                  </template>
                </v-select>
                <p v-if="errors.from_account" class="mt-1 text-sm text-red-600">
                  {{ errors.from_account[0] }}
                </p>
                
                <!-- Selected From Account Balance -->
                <div v-if="form.from_account" class="mt-2 p-3 bg-red-50 rounded-lg border border-red-200">
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-red-700">Available Balance:</span>
                    <span class="text-sm font-semibold text-red-700">
                      {{ formatCurrency(form.from_account.current_balance) }}
                    </span>
                  </div>
                  <div v-if="form.amount > form.from_account.current_balance" class="mt-1 text-xs text-red-600">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Insufficient balance for this transfer
                  </div>
                </div>
              </div>

              <!-- To Account -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  To Account <span class="text-red-500">*</span>
                </label>
                <v-select
                  v-model="form.to_account"
                  :options="filteredToAccounts"
                  :reduce="account => account"
                  label="account_name"
                  placeholder="Select Destination Account"
                  :class="{ 'border-red-500': errors.to_account }"
                  class="v-select-custom"
                >
                  <template #option="option">
                    <div class="flex justify-between items-center w-full">
                      <span>{{ option.account_name }}</span>
                      <span class="text-sm font-medium" :class="getBalanceColor(option.current_balance)">
                        {{ formatCurrency(option.current_balance) }}
                      </span>
                    </div>
                  </template>
                  <template #selected-option="option">
                    <div class="flex justify-between items-center w-full">
                      <span>{{ option.account_name }}</span>
                    </div>
                  </template>
                </v-select>
                <p v-if="errors.to_account" class="mt-1 text-sm text-red-600">
                  {{ errors.to_account[0] }}
                </p>
                
                <!-- Selected To Account Balance -->
                <div v-if="form.to_account" class="mt-2 p-3 bg-green-50 rounded-lg border border-green-200">
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-green-700">Current Balance:</span>
                    <span class="text-sm font-semibold text-green-700">
                      {{ formatCurrency(form.to_account.current_balance) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Transfer Amount -->
              <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                  Transfer Amount (BDT) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    type="number"
                    id="amount"
                    v-model="form.amount"
                    step="0.01"
                    min="0.01"
                    :max="form.from_account ? form.from_account.current_balance : 999999999.99"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                    :class="{ 'border-red-500': errors.amount }"
                    placeholder="0.00"
                  />
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <span class="text-gray-500 text-sm">৳</span>
                  </div>
                </div>
                <p v-if="errors.amount" class="mt-1 text-sm text-red-600">
                  {{ errors.amount[0] }}
                </p>
                
                <!-- Amount Validation -->
                <div v-if="form.amount && form.from_account" class="mt-2 space-y-1">
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Current Balance:</span>
                    <span class="font-medium">{{ formatCurrency(form.from_account.current_balance) }}</span>
                  </div>
                  <div class="flex justify-between text-xs">
                    <span class="text-gray-500">After Transfer:</span>
                    <span class="font-medium" :class="getBalanceColor(form.from_account.current_balance - form.amount)">
                      {{ formatCurrency(form.from_account.current_balance - form.amount) }}
                    </span>
                  </div>
                  <div v-if="form.amount > form.from_account.current_balance" class="text-xs text-red-600 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i>
                    Amount exceeds available balance
                  </div>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                  Description
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-y"
                  :class="{ 'border-red-500': errors.description }"
                  placeholder="Enter transfer description (optional)"
                ></textarea>
                <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                  {{ errors.description[0] }}
                </p>
              </div>

              <!-- Transfer Summary -->
              <div v-if="showTransferSummary" class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                <h4 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
                  <i class="fas fa-exchange-alt"></i>
                  Transfer Summary
                </h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-blue-700">From:</span>
                    <span class="font-medium text-blue-800">{{ form.from_account?.account_name }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-blue-700">To:</span>
                    <span class="font-medium text-blue-800">{{ form.to_account?.account_name }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-blue-700">Amount:</span>
                    <span class="font-bold text-blue-800">{{ formatCurrency(form.amount) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-blue-700">Date:</span>
                    <span class="font-medium text-blue-800">{{ formatDate(form.date) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-end gap-3">
              <!-- Cancel Button -->
              <router-link
                :to="{ name: 'cash-transfers.index' }"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-gray-600 hover:bg-gray-700 text-white
                      w-full sm:w-auto text-center"
              >
                <i class="fas fa-times"></i>
                Cancel
              </router-link>

              <!-- Create Button -->
              <button
                @click="submitForm"
                :disabled="loading || !isFormValid"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-blue-600 hover:bg-blue-700 text-white
                      disabled:opacity-50 disabled:cursor-not-allowed
                      w-full sm:w-auto text-center"
              >
                <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-exchange-alt"></i>
                {{ loading ? 'Processing...' : 'Create Transfer' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Transfer Guidelines -->
        <div class="bg-yellow-50 rounded-2xl border border-yellow-200 p-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-yellow-800 mb-2">
                Transfer Guidelines
              </h4>
              <ul class="text-sm text-yellow-700 space-y-1">
                <li>• Select different source and destination accounts</li>
                <li>• Ensure sufficient balance in source account</li>
                <li>• Minimum transfer amount: ৳ 0.01</li>
                <li>• Transfer cannot be reversed automatically</li>
                <li>• All amounts are in BDT (৳)</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Quick Tips -->
        <div class="bg-blue-50 rounded-2xl border border-blue-200 p-6 mt-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-blue-800 mb-2">
                Quick Tips
              </h4>
              <ul class="text-sm text-blue-700 space-y-1">
                <li>• Transfer date defaults to today</li>
                <li>• Real-time balance validation</li>
                <li>• Add description for better tracking</li>
                <li>• Review summary before submitting</li>
                <li>• Balances update immediately</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Account Balances -->
        <div class="bg-green-50 rounded-2xl border border-green-200 p-6 mt-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-wallet text-green-600"></i>
            </div>
            <div class="flex-1">
              <h4 class="font-semibold text-green-800 mb-3">
                Account Balances
              </h4>
              <div class="space-y-2 max-h-60 overflow-y-auto">
                <div 
                  v-for="account in accounts" 
                  :key="account.id"
                  class="flex justify-between items-center p-2 rounded hover:bg-green-100"
                >
                  <span class="text-xs font-medium text-green-700 truncate flex-1 mr-2">
                    {{ account.account_name }}
                  </span>
                  <span class="text-xs font-semibold shrink-0" :class="getBalanceColor(account.current_balance)">
                    {{ formatCurrency(account.current_balance) }}
                  </span>
                </div>
                <div v-if="accounts.length === 0" class="text-center py-4">
                  <i class="fas fa-wallet text-gray-400 text-lg mb-2"></i>
                  <p class="text-xs text-gray-500">No accounts found</p>
                </div>
              </div>
            </div>
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

// Reactive data
const accounts = ref([])
const loading = ref(false)
const errors = ref({})

// Form data
const form = reactive({
  date: new Date().toISOString().split('T')[0], // Today's date
  from_account: null,
  to_account: null,
  amount: '',
  description: ''
})

// Computed properties
const filteredFromAccounts = computed(() => {
  if (!form.to_account) return accounts.value
  return accounts.value.filter(account => account.id !== form.to_account.id)
})

const filteredToAccounts = computed(() => {
  if (!form.from_account) return accounts.value
  return accounts.value.filter(account => account.id !== form.from_account.id)
})

const showTransferSummary = computed(() => {
  return form.from_account && form.to_account && form.amount && form.date
})

const isFormValid = computed(() => {
  return form.date && 
         form.from_account && 
         form.to_account && 
         form.amount && 
         form.amount > 0 &&
         form.from_account.id !== form.to_account.id &&
         form.amount <= form.from_account.current_balance
})

// Methods
const fetchAccounts = async () => {
  try {
    console.log('Fetching accounts from API...')
    const response = await axios.get('/api/cash-banks')
    console.log('API Response:', response.data)
    accounts.value = response.data
    
    if (accounts.value && accounts.value.length > 0) {
      console.log('Accounts loaded successfully:', accounts.value)
    } else {
      console.log('No accounts found')
    }
  } catch (error) {
    console.error('Error fetching accounts:', error)
    console.error('Error details:', error.response)
    showErrorAlert('Error', 'Failed to load accounts. Please check console for details.')
  }
}

const formatCurrency = (amount) => {
  const numAmount = parseFloat(amount) || 0
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(numAmount)
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

const getBalanceColor = (balance) => {
  const numBalance = parseFloat(balance) || 0
  if (numBalance > 0) return 'text-green-600'
  if (numBalance < 0) return 'text-red-600'
  return 'text-gray-600'
}

const validateForm = () => {
  const newErrors = {}

  if (!form.date) {
    newErrors.date = ['Transfer date is required']
  }

  if (!form.from_account) {
    newErrors.from_account = ['Source account is required']
  }

  if (!form.to_account) {
    newErrors.to_account = ['Destination account is required']
  }

  if (form.from_account && form.to_account && form.from_account.id === form.to_account.id) {
    newErrors.to_account = ['Source and destination accounts must be different']
  }

  if (!form.amount) {
    newErrors.amount = ['Transfer amount is required']
  } else {
    const amount = parseFloat(form.amount)
    if (amount <= 0) {
      newErrors.amount = ['Transfer amount must be greater than 0']
    } else if (form.from_account && amount > form.from_account.current_balance) {
      newErrors.amount = ['Insufficient balance in source account']
    }
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const submitForm = async () => {
  if (!validateForm()) {
    showErrorAlert('Validation Error', 'Please fix the errors in the form')
    return
  }

  loading.value = true

  try {
    showLoadingAlert('Processing transfer...')

    const submitData = {
      date: form.date,
      from_account: form.from_account.id,
      to_account: form.to_account.id,
      amount: parseFloat(form.amount),
      description: form.description
    }

    console.log('Submitting transfer data:', submitData)
    const response = await axios.post('/api/cash-transfers', submitData)

    closeAlert()
    
    router.push({ 
      name: 'cash-transfers.index',
      query: { created: 'true' }
    })

  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      
      if (error.response.data.error === 'Insufficient balance in source account') {
        showErrorAlert('Insufficient Balance', 'The source account does not have sufficient balance for this transfer.')
      } else {
        showErrorAlert('Validation Error', 'Please check the form for errors')
      }
    } else {
      console.error('Error creating transfer:', error)
      showErrorAlert('Error', 'Failed to process transfer. Please try again.')
    }
  }
}

// Lifecycle
onMounted(() => {
  console.log('Create Transfer component mounted')
  fetchAccounts()
})
</script>
