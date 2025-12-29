<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Create New Cash/Bank Account
          </h1>
          <p class="text-gray-600">
            Add a new cash or bank account to your system
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
              Fill in the basic information about the cash/bank account
            </p>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <div class="space-y-6">
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
                      placeholder="Auto-filled from opening balance"
                      :disabled="autoFillCurrentBalance"
                    />
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <span class="text-gray-500 text-sm">৳</span>
                    </div>
                  </div>
                  <p v-if="errors.current_balance" class="mt-1 text-sm text-red-600">
                    {{ errors.current_balance[0] }}
                  </p>
                  <div class="flex items-center gap-2 mt-2">
                    <input
                      type="checkbox"
                      id="auto_fill_balance"
                      v-model="autoFillCurrentBalance"
                      class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    <label for="auto_fill_balance" class="text-sm text-gray-600">
                      Auto-fill current balance from opening balance
                    </label>
                  </div>
                </div>
              </div>

              <!-- Balance Preview -->
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Balance Preview</h4>
                <div class="grid grid-cols-2 gap-4">
                  <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Opening Balance</p>
                    <p class="text-lg font-semibold" :class="getBalanceColor(form.opening_balance)">
                      {{ formatCurrency(form.opening_balance || 0) }}
                    </p>
                  </div>
                  <div class="text-center">
                    <p class="text-xs text-gray-500 mb-1">Current Balance</p>
                    <p class="text-lg font-semibold" :class="getBalanceColor(currentBalancePreview)">
                      {{ formatCurrency(currentBalancePreview) }}
                    </p>
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
                  rows="4"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 placeholder-gray-500 resize-y"
                  :class="{ 'border-red-500': errors.description }"
                  placeholder="Enter account description (e.g., Main business account, Petty cash, etc.)"
                ></textarea>
                <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                  {{ errors.description[0] }}
                </p>
              </div>
            </div>
          </div>
          
          <!-- Form Actions -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-end gap-3">

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

              <!-- Create Button -->
              <button
                @click="submitForm"
                :disabled="loading"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-blue-600 hover:bg-blue-700 text-white
                     
                      disabled:opacity-50 disabled:cursor-not-allowed
                      w-full sm:w-auto text-center"
              >
                <i v-if="loading" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-save"></i>
                {{ loading ? 'Creating...' : 'Create Account' }}
              </button>

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
                Balance Guidelines
              </h4>
              <ul class="text-sm text-yellow-700 space-y-1">
                <li>• Use positive numbers for assets</li>
                <li>• Use negative numbers for liabilities</li>
                <li>• Current balance auto-updates with transactions</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Form Tips -->
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
                <li>• Account name must be unique</li>
                <li>• Opening balance is required</li>
                <li>• Select appropriate account type</li>
                <li>• Add description for better tracking</li>
                <li>• All amounts are in BDT (৳)</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()

// Account types configuration
const accountTypes = [
  {
    value: 'cash',
    label: 'Cash',
    description: 'Physical cash and petty cash',
    icon: 'fas fa-money-bill-wave text-green-500',
    color: 'text-green-600'
  },
  {
    value: 'bank',
    label: 'Bank Account',
    description: 'Bank accounts and savings',
    icon: 'fas fa-university text-blue-500',
    color: 'text-blue-600'
  },
  {
    value: 'digital',
    label: 'Digital Wallet',
    description: 'Mobile banking, bKash, Nagad, etc.',
    icon: 'fas fa-mobile-alt text-purple-500',
    color: 'text-purple-600'
  }
]

// Form data
const form = reactive({
  account_name: '',
  account_type: 'cash',
  opening_balance: 0,
  current_balance: 0,
  description: ''
})

// UI states
const loading = ref(false)
const errors = ref({})
const autoFillCurrentBalance = ref(true)

// Computed properties
const selectedAccountType = computed(() => {
  return accountTypes.find(type => type.value === form.account_type) || accountTypes[0]
})

const currentBalancePreview = computed(() => {
  if (autoFillCurrentBalance.value) {
    return parseFloat(form.opening_balance) || 0
  }
  return parseFloat(form.current_balance) || 0
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

  if (!form.account_type) {
    newErrors.account_type = ['Account type is required']
  }

  errors.value = newErrors
  return Object.keys(newErrors).length === 0
}

const submitForm = async () => {
  // Prepare form data
  const submitData = {
    account_name: form.account_name,
    account_type: form.account_type,
    opening_balance: parseFloat(form.opening_balance) || 0,
    description: form.description
  }

  // Only include current_balance if manually set
  if (!autoFillCurrentBalance.value) {
    submitData.current_balance = parseFloat(form.current_balance) || 0
  }

  if (!validateForm()) {
    showErrorAlert('Validation Error', 'Please fix the errors in the form')
    return
  }

  loading.value = true

  try {
    showLoadingAlert('Creating account...')
    const response = await axios.post('/api/cash-banks', submitData)

    closeAlert()
    
    router.push({ 
      name: 'cash-banks.index',
      query: { created: 'true' }
    })

  } catch (error) {
    closeAlert()
    loading.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      showErrorAlert('Validation Error', 'Please check the form for errors')
    } else {
      console.error('Error creating account:', error)
      showErrorAlert('Error', 'Failed to create account. Please try again.')
    }
  }
}

// Watchers
watch(autoFillCurrentBalance, (newValue) => {
  if (newValue) {
    form.current_balance = form.opening_balance
  }
})

watch(() => form.opening_balance, (newValue) => {
  if (autoFillCurrentBalance.value) {
    form.current_balance = newValue
  }
})
</script>