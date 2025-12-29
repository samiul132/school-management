<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Edit Cash/Bank Account
          </h1>
          <p class="text-gray-600">
            Update account information
          </p>
        </div>
        <div class="flex items-center gap-3 mb-2">
          <router-link 
            :to="{ name: 'cash-banks.index' }"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors font-semibold"
          >
            <i class="fas fa-arrow-left"></i>
            Back to Accounts
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
              Account Information
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Update the account details
            </p>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div v-if="loading" class="text-center py-8">
              <i class="fas fa-spinner fa-spin text-2xl text-blue-600 mb-2"></i>
              <p class="text-gray-600">Loading account details...</p>
            </div>

            <div v-else class="space-y-6">
              <!-- Account Name -->
              <div>
                <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                  Account Name <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  id="account_name"
                  v-model="form.account_name"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                  :class="{ 'border-red-500': errors.account_name }"
                  placeholder="Enter account name (e.g., Cash in Hand, Bank ABC)"
                />
                <p v-if="errors.account_name" class="mt-1 text-sm text-red-600">
                  {{ errors.account_name[0] }}
                </p>
              </div>

              <!-- Opening Balance and Current Balance -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Opening Balance -->
                <div>
                  <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-2">
                    Opening Balance (BDT) <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      type="number"
                      id="opening_balance"
                      v-model="form.opening_balance"
                      step="0.01"
                      min="-999999999.99"
                      max="999999999.99"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                      :class="{ 'border-red-500': errors.opening_balance }"
                      placeholder="0.00"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <span class="text-gray-500 text-sm">৳</span>
                    </div>
                  </div>
                  <p v-if="errors.opening_balance" class="mt-1 text-sm text-red-600">
                    {{ errors.opening_balance[0] }}
                  </p>
                </div>

                <!-- Current Balance -->
                <div>
                  <label for="current_balance" class="block text-sm font-medium text-gray-700 mb-2">
                    Current Balance (BDT)
                  </label>
                  <div class="relative">
                    <input
                      type="number"
                      id="current_balance"
                      v-model="form.current_balance"
                      step="0.01"
                      min="-999999999.99"
                      max="999999999.99"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500"
                      :class="{ 'border-red-500': errors.current_balance }"
                      placeholder="Current account balance"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <span class="text-gray-500 text-sm">৳</span>
                    </div>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    Note: Changing opening balance will automatically adjust current balance
                  </p>
                  <p v-if="errors.current_balance" class="mt-1 text-sm text-red-600">
                    {{ errors.current_balance[0] }}
                  </p>
                </div>
              </div>

              <!-- Balance Preview -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Balance Overview</h4>
                <div class="grid grid-cols-2 gap-4">
                  <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Opening Balance</p>
                    <p class="text-lg font-semibold" :class="getBalanceColor(form.opening_balance)">
                      {{ formatCurrency(form.opening_balance || 0) }}
                    </p>
                  </div>
                  <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Current Balance</p>
                    <p class="text-lg font-semibold" :class="getBalanceColor(form.current_balance)">
                      {{ formatCurrency(form.current_balance || 0) }}
                    </p>
                  </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-200">
                  <p class="text-xs text-gray-500 text-center">
                    Balance Difference: 
                    <span :class="getBalanceColor(balanceDifference)">
                      {{ formatCurrency(balanceDifference) }}
                    </span>
                  </p>
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
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-y"
                  :class="{ 'border-red-500': errors.description }"
                  placeholder="Enter account description (e.g., Main business account, Petty cash, etc.)"
                ></textarea>
                <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                  {{ errors.description[0] }}
                </p>
              </div>

              <!-- Account Created Info -->
              <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-blue-800 mb-2">Account Details</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-blue-600">Account ID</p>
                    <p class="font-mono text-blue-800">{{ account.id }}</p>
                  </div>
                  <div>
                    <p class="text-blue-600">Created</p>
                    <p class="text-blue-800">{{ formatDate(account.created_at) }}</p>
                  </div>
                  <div>
                    <p class="text-blue-600">Last Updated</p>
                    <p class="text-blue-800">{{ formatDate(account.updated_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-between gap-3">
              <!-- Delete Button -->
              <button
                @click="deleteAccount"
                :disabled="loading"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-red-600 hover:bg-red-700 text-white
                     
                      disabled:opacity-50 disabled:cursor-not-allowed
                      w-full sm:w-auto text-center"
              >
                <i class="fas fa-trash"></i>
                Delete Account
              </button>

              <div class="flex flex-col sm:flex-row gap-3">
                <!-- Cancel Button -->
                <router-link
                  :to="{ name: 'cash-banks.index' }"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                        bg-gray-600 hover:bg-gray-700 text-white
                       
                        w-full sm:w-auto text-center"
                >
                  <i class="fas fa-times"></i>
                  Cancel
                </router-link>

                <!-- Update Button -->
                <button
                  @click="submitForm"
                  :disabled="loading || updateLoading"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                        bg-blue-600 hover:bg-blue-700 text-white
                       
                        disabled:opacity-50 disabled:cursor-not-allowed
                        w-full sm:w-auto text-center"
                >
                  <i v-if="updateLoading" class="fas fa-spinner fa-spin"></i>
                  <i v-else class="fas fa-save"></i>
                  {{ updateLoading ? 'Updating...' : 'Update Account' }}
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        

        <!-- Balance Guidelines -->
        <div class="bg-yellow-50 rounded-2xl border border-yellow-200 p-6">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center shrink-0">
              <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            </div>
            <div>
              <h4 class="font-semibold text-yellow-800 mb-2">
                Update Guidelines
              </h4>
              <ul class="text-sm text-yellow-700 space-y-1">
                <li>• Changing opening balance adjusts current balance</li>
                <li>• Use positive numbers for assets</li>
                <li>• Use negative numbers for liabilities</li>
              </ul>
            </div>
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
import { showSuccessAlert, showErrorAlert, showLoadingAlert, showConfirmDialog, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

// Data
const account = ref({})
const originalForm = ref({})
const loading = ref(true)
const updateLoading = ref(false)

// Form data
const form = reactive({
  account_name: '',
  account_type: 'cash',
  opening_balance: 0,
  current_balance: 0,
  description: ''
})

// UI states
const errors = ref({})

// Computed properties
const selectedAccountType = computed(() => {
  return accountTypes.find(type => type.value === form.account_type) || accountTypes[0]
})

const balanceDifference = computed(() => {
  return (parseFloat(form.current_balance) || 0) - (parseFloat(form.opening_balance) || 0)
})

// Methods
const formatCurrency = (amount) => {
  const numAmount = parseFloat(amount) || 0
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(numAmount)
}

const getBalanceColor = (balance) => {
  const numBalance = parseFloat(balance) || 0
  if (numBalance > 0) return 'text-green-600'
  if (numBalance < 0) return 'text-red-600'
  return 'text-gray-600'
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchAccount = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/cash-banks/${route.params.id}`)
    account.value = response.data
    
    // Set form data
    Object.assign(form, {
      account_name: response.data.account_name,
      account_type: response.data.account_type,
      opening_balance: parseFloat(response.data.opening_balance),
      current_balance: parseFloat(response.data.current_balance),
      description: response.data.description || ''
    })
    
    // Store original data for reset
    originalForm.value = { ...form }
    
  } catch (error) {
    console.error('Error fetching account:', error)
    showErrorAlert('Error', 'Failed to load account details')
    router.push({ name: 'cash-banks.index' })
  } finally {
    loading.value = false
  }
}

const validateForm = () => {
  const newErrors = {}

  if (!form.account_name.trim()) {
    newErrors.account_name = ['Account name is required']
  }

  if (form.opening_balance === '' || form.opening_balance === null) {
    newErrors.opening_balance = ['Opening balance is required']
  } else {
    const openingBalance = parseFloat(form.opening_balance)
    if (openingBalance < -999999999.99 || openingBalance > 999999999.99) {
      newErrors.opening_balance = ['Opening balance must be between -999,999,999.99 and 999,999,999.99']
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

  updateLoading.value = true

  try {
    showLoadingAlert('Updating account...')
    
    const submitData = {
      account_name: form.account_name,
      account_type: form.account_type,
      opening_balance: parseFloat(form.opening_balance),
      current_balance: parseFloat(form.current_balance),
      description: form.description
    }

    const response = await axios.put(`/api/cash-banks/${route.params.id}`, submitData)

    closeAlert()
    
    
    router.push({ 
      name: 'cash-banks.index',
      query: { updated: 'true' }
    })

  } catch (error) {
    closeAlert()
    updateLoading.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors')
    } else {
      console.error('Error updating account:', error)
      showErrorAlert('Error', 'Failed to update account. Please try again.')
    }
  }
}

const deleteAccount = async () => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'You won\'t be able to revert this! The account will be permanently deleted.',
    'warning',
    'Yes, delete it!'
  )

  if (result.isConfirmed) {
    try {
      showLoadingAlert('Deleting account...')
      await axios.delete(`/api/cash-banks/${route.params.id}`)
      
      closeAlert()
      
      router.push({ name: 'cash-banks.index' })
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting account:', error)
      showErrorAlert('Error', 'Failed to delete account. Please try again.')
    }
  }
}

const resetToOriginal = () => {
  Object.assign(form, originalForm.value)
  errors.value = {}
}

// Lifecycle
onMounted(() => {
  fetchAccount()
})
</script>