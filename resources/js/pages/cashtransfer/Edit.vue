<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Edit Cash Transfer
          </h1>
          <p class="text-gray-600">
            Update transfer details between cash/bank accounts
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

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
        <p class="text-gray-600">Loading transfer details...</p>
      </div>
    </div>

    <!-- Form Section -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Form -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <!-- Form Header -->
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
              Transfer Information
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              Update the transfer details between accounts
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
                  <div v-if="parseFloat(form.amount) > parseFloat(form.from_account.current_balance)" class="mt-1 text-xs text-red-600">
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

              <!-- Original Transfer Info -->
              <div v-if="transfer" class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                <h4 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                  <i class="fas fa-history text-gray-500"></i>
                  Original Transfer
                </h4>
                <div class="space-y-1 text-sm text-gray-600">
                  <div class="flex justify-between">
                    <span>Created:</span>
                    <span>{{ formatDateTime(transfer.created_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Last Updated:</span>
                    <span>{{ formatDateTime(transfer.updated_at) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Transfer ID:</span>
                    <span class="font-mono">#{{ transfer.id }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span>Original Amount:</span>
                    <span class="font-medium">{{ formatCurrency(transfer.amount) }}</span>
                  </div>
                </div>
              </div>

              <!-- Changes Summary -->
              <div v-if="hasChanges" class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                <h4 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
                  <i class="fas fa-exchange-alt text-blue-600"></i>
                  Update Summary
                </h4>
                <div class="space-y-2 text-sm">
                  <div v-if="originalForm.date !== form.date" class="flex justify-between items-start">
                    <span class="text-blue-700">Date:</span>
                    <div class="text-right">
                      <div class="text-red-600 line-through">{{ formatDate(originalForm.date) }}</div>
                      <div class="text-green-600 font-medium">{{ formatDate(form.date) }}</div>
                    </div>
                  </div>
                  
                  <div v-if="originalForm.from_account?.id !== form.from_account?.id || 
                           (originalForm.from_account && !form.from_account) || 
                           (!originalForm.from_account && form.from_account)" class="flex justify-between items-start">
                    <span class="text-blue-700">From Account:</span>
                    <div class="text-right">
                      <div v-if="originalForm.from_account" class="text-red-600 line-through">
                        {{ originalForm.from_account.account_name }}
                      </div>
                      <div v-if="form.from_account" class="text-green-600 font-medium">
                        {{ form.from_account.account_name }}
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="originalForm.to_account?.id !== form.to_account?.id || 
                           (originalForm.to_account && !form.to_account) || 
                           (!originalForm.to_account && form.to_account)" class="flex justify-between items-start">
                    <span class="text-blue-700">To Account:</span>
                    <div class="text-right">
                      <div v-if="originalForm.to_account" class="text-red-600 line-through">
                        {{ originalForm.to_account.account_name }}
                      </div>
                      <div v-if="form.to_account" class="text-green-600 font-medium">
                        {{ form.to_account.account_name }}
                      </div>
                    </div>
                  </div>
                  
                  <div v-if="originalForm.amount !== form.amount" class="flex justify-between items-start">
                    <span class="text-blue-700">Amount:</span>
                    <div class="text-right">
                      <div class="text-red-600 line-through">{{ formatCurrency(originalForm.amount) }}</div>
                      <div class="text-green-600 font-medium">{{ formatCurrency(form.amount) }}</div>
                    </div>
                  </div>
                  
                  <div v-if="originalForm.description !== form.description" class="flex justify-between items-start">
                    <span class="text-blue-700">Description:</span>
                    <div class="text-right">
                      <div v-if="originalForm.description" class="text-red-600 line-through text-xs max-w-[200px] break-words">
                        {{ originalForm.description }}
                      </div>
                      <div v-if="form.description" class="text-green-600 font-medium text-xs max-w-[200px] break-words">
                        {{ form.description }}
                      </div>
                    </div>
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
                @click="deleteTransfer"
                :disabled="processing"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                      bg-red-600 hover:bg-red-700 text-white
                      disabled:opacity-50 disabled:cursor-not-allowed
                      w-full sm:w-auto text-center"
              >
                <i class="fas fa-trash"></i>
                Delete Transfer
              </button>

              <div class="flex flex-col sm:flex-row gap-3">
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

                <!-- Update Button -->
                <button
                  @click="submitForm"
                  :disabled="processing || !hasChanges || !isFormValid"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold transition-colors
                        bg-blue-600 hover:bg-blue-700 text-white
                        disabled:opacity-50 disabled:cursor-not-allowed
                        w-full sm:w-auto text-center"
                >
                  <i v-if="processing" class="fas fa-spinner fa-spin"></i>
                  <i v-else class="fas fa-save"></i>
                  {{ processing ? 'Updating...' : 'Update Transfer' }}
                </button>
              </div>
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
                Update Guidelines
              </h4>
              <ul class="text-sm text-yellow-700 space-y-1">
                <li>• Changing accounts will reverse original transfer</li>
                <li>• Ensure sufficient balance in new source account</li>
                <li>• All balance changes are automatically handled</li>
                <li>• Original accounting entries will be invalidated</li>
                <li>• New accounting entries will be created</li>
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
                <li>• Update only the fields you need to change</li>
                <li>• Account balances update automatically</li>
                <li>• Review changes in the summary section</li>
                <li>• Delete if you want to remove completely</li>
                <li>• All amounts are in BDT (৳)</li>
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
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'
import { showSuccessAlert, showErrorAlert, showLoadingAlert, showConfirmDialog, closeAlert } from '../../utils/sweetAlert'

const router = useRouter()
const route = useRoute()

const accounts = ref([])
const transfer = ref(null)
const loading = ref(true)
const processing = ref(false)
const errors = ref({})

const form = reactive({
  date: '',
  from_account: null,
  to_account: null,
  amount: '',
  description: ''
})

const originalForm = reactive({
  date: '',
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

const hasChanges = computed(() => {
  const original = {
    date: originalForm.date,
    from_account: originalForm.from_account?.id || originalForm.from_account,
    to_account: originalForm.to_account?.id || originalForm.to_account,
    amount: originalForm.amount ? parseFloat(originalForm.amount) : 0,
    description: originalForm.description || ''
  }
  
  const current = {
    date: form.date,
    from_account: form.from_account?.id || form.from_account,
    to_account: form.to_account?.id || form.to_account,
    amount: form.amount ? parseFloat(form.amount) : 0,
    description: form.description || ''
  }
  
  return current.date !== original.date ||
         current.from_account != original.from_account ||
         current.to_account != original.to_account ||
         current.amount !== original.amount ||
         current.description !== original.description
})

const isFormValid = computed(() => {
  const basicValid = form.date && 
                    form.from_account && 
                    form.to_account && 
                    form.amount && 
                    parseFloat(form.amount) > 0 &&
                    form.from_account.id !== form.to_account.id
  
  if (!basicValid) return false
  
  if (form.from_account && form.amount) {
    const amount = parseFloat(form.amount)
    const balance = parseFloat(form.from_account.current_balance)
    if (amount > balance) return false
  }
  
  return true
})

// Methods
const fetchTransfer = async () => {
  try {
    console.log('Fetching transfer details...')
    const response = await axios.get(`/api/cash-transfers/${route.params.id}`)
    console.log('Transfer data:', response.data)
    transfer.value = response.data
    
    // Find account objects from accounts list
    let fromAccount = null
    let toAccount = null
    
    if (transfer.value.from_account) {
      const fromAccountId = typeof transfer.value.from_account === 'object' 
        ? transfer.value.from_account.id 
        : transfer.value.from_account
      fromAccount = accounts.value.find(acc => acc.id == fromAccountId)
    }
    
    if (transfer.value.to_account) {
      const toAccountId = typeof transfer.value.to_account === 'object' 
        ? transfer.value.to_account.id 
        : transfer.value.to_account
      toAccount = accounts.value.find(acc => acc.id == toAccountId)
    }
    
    // Set form values
    form.date = transfer.value.date ? transfer.value.date.split('T')[0] : ''
    form.from_account = fromAccount
    form.to_account = toAccount
    form.amount = transfer.value.amount || ''
    form.description = transfer.value.description || ''
    
    // Set original values
    originalForm.date = form.date
    originalForm.from_account = fromAccount ? { ...fromAccount } : null
    originalForm.to_account = toAccount ? { ...toAccount } : null
    originalForm.amount = form.amount
    originalForm.description = form.description
    
  } catch (error) {
    console.error('Error fetching transfer:', error)
    showErrorAlert('Error', 'Failed to load transfer details')
  }
}

const fetchAccounts = async () => {
  try {
    console.log('Fetching accounts from API...')
    const response = await axios.get('/api/cash-banks')
    console.log('Accounts data:', response.data)
    accounts.value = response.data
  } catch (error) {
    console.error('Error fetching accounts:', error)
    showErrorAlert('Error', 'Failed to load accounts')
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

const formatDateTime = (dateString) => {
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
    } else if (form.from_account && amount > parseFloat(form.from_account.current_balance)) {
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

  processing.value = true

  try {
    showLoadingAlert('Updating transfer and accounting entries...')

    const submitData = {
      date: form.date,
      from_account: form.from_account.id,
      to_account: form.to_account.id,
      amount: parseFloat(form.amount),
      description: form.description
    }

    console.log('Updating transfer with data:', submitData)
    const response = await axios.put(`/api/cash-transfers/${route.params.id}`, submitData)

    closeAlert()
    showSuccessAlert('Success', 'Cash transfer updated successfully!')
    
    router.push({ 
      name: 'cash-transfers.index',
      query: { updated: 'true' }
    })

  } catch (error) {
    closeAlert()
    processing.value = false

    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      
      if (error.response.data.error === 'Insufficient balance in source account') {
        showErrorAlert('Insufficient Balance', 'The source account does not have sufficient balance for this transfer.')
      } else {
        showErrorAlert('Validation Error', 'Please check the form for errors')
      }
    } else {
      console.error('Error updating transfer:', error)
      showErrorAlert('Error', 'Failed to update transfer. Please try again.')
    }
  }
}

const deleteTransfer = async () => {
  const result = await showConfirmDialog(
    'Are you sure?',
    'This will delete the transfer, reverse the account balances, and invalidate accounting entries. This action cannot be undone!'
  )

  if (result.isConfirmed) {
    processing.value = true

    try {
      showLoadingAlert('Deleting transfer and reversing entries...')
      
      await axios.delete(`/api/cash-transfers/${route.params.id}`)
      
      closeAlert()
      showSuccessAlert('Deleted', 'Cash transfer deleted successfully!')
      
      router.push({ 
        name: 'cash-transfers.index',
        query: { deleted: 'true' }
      })
      
    } catch (error) {
      closeAlert()
      processing.value = false
      console.error('Error deleting transfer:', error)
      showErrorAlert('Error', 'Failed to delete transfer. Please try again.')
    }
  }
}

onMounted(async () => {
  console.log('Edit Transfer component mounted')
  try {
    // First fetch accounts
    await fetchAccounts()
    // Then fetch transfer (which needs accounts to find the account objects)
    await fetchTransfer()
  } catch (error) {
    console.error('Error in mounted:', error)
  } finally {
    loading.value = false
  }
})
</script>