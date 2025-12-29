<template>
  <AppLayout>
    <!-- Header -->
    <div class="mb-6">
      <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800">
            Account Details
          </h1>
          <p class="text-gray-600">
            View complete account information
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

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
      <p class="text-gray-600">Loading account details...</p>
    </div>

    <!-- Content -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Content -->
      <div class="lg:col-span-2">
        <!-- Account Information Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-6">
          <!-- Card Header -->
          <div class="px-6 py-4 border-b border-gray-200 bg-linear-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-500 flex items-center justify-center">
                  <i class="fas fa-wallet text-white text-xl"></i>
                </div>
                <div>
                  <h2 class="text-xl font-bold text-gray-800">{{ account.account_name }}</h2>
                  <p class="text-gray-600">Account Details</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Details -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Balance Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Balance Information</h3>
                
                <!-- Opening Balance -->
                <div>
                  <p class="text-sm text-gray-500 mb-1">Opening Balance</p>
                  <p class="text-2xl font-bold" :class="getBalanceColor(account.opening_balance)">
                    {{ formatCurrency(account.opening_balance) }}
                  </p>
                </div>

                <!-- Current Balance -->
                <div>
                  <p class="text-sm text-gray-500 mb-1">Current Balance</p>
                  <p class="text-3xl font-bold" :class="getBalanceColor(account.current_balance)">
                    {{ formatCurrency(account.current_balance) }}
                  </p>
                </div>

                <!-- Balance Difference -->
                <div>
                  <p class="text-sm text-gray-500 mb-1">Net Change</p>
                  <p class="text-lg font-semibold" :class="getBalanceColor(balanceDifference)">
                    {{ formatCurrency(balanceDifference) }}
                  </p>
                </div>
              </div>

              <!-- Account Metadata -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>
                
                <div class="space-y-3">
                  <div>
                    <p class="text-sm text-gray-500">Account ID</p>
                    <p class="font-mono text-gray-800">{{ account.id }}</p>
                  </div>
                  
                  
                  <div>
                    <p class="text-sm text-gray-500">Created Date</p>
                    <p class="text-gray-800">{{ formatDate(account.created_at) }}</p>
                  </div>
                  
                  <div>
                    <p class="text-sm text-gray-500">Last Updated</p>
                    <p class="text-gray-800">{{ formatDate(account.updated_at) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="mt-6 pt-6 border-t border-gray-200">
              <h3 class="text-lg font-semibold text-gray-800 mb-3">Description</h3>
              <p v-if="account.description" class="text-gray-700 leading-relaxed">
                {{ account.description }}
              </p>
              <p v-else class="text-gray-500 italic">
                No description provided for this account.
              </p>
            </div>
          </div>

          <!-- Card Footer -->
          <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
              <div class="text-sm text-gray-500">
                Last updated: {{ formatDateTime(account.updated_at) }}
              </div>
              <div class="flex items-center gap-3">
                <!-- Edit Button -->
                <router-link
                  :to="{ name: 'cash-banks.edit', params: { id: account.id } }"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors"
                >
                  <i class="fas fa-edit"></i>
                  Edit Account
                </router-link>

                <!-- Delete Button -->
                <button
                  @click="deleteAccount"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors"
                >
                  <i class="fas fa-trash"></i>
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1">
        <!-- Account Summary Card -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-6">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Summary</h3>
          
          <div class="space-y-4">
            <!-- Balance Status -->
            <div class="text-center p-4 rounded-lg" :class="getBalanceStatusClass(account.current_balance)">
              <i :class="getBalanceStatusIcon(account.current_balance)" class="text-2xl mb-2"></i>
              <p class="font-semibold">{{ getBalanceStatus(account.current_balance) }}</p>
              <p class="text-sm opacity-80">Current Status</p>
            </div>

            <!-- Quick Stats -->
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Balance Change</span>
                <span class="font-medium" :class="getBalanceColor(balanceDifference)">
                  {{ formatCurrency(balanceDifference) }}
                </span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500">Change Percentage</span>
                <span class="font-medium" :class="getBalanceColor(balanceDifference)">
                  {{ balancePercentage }}%
                </span>
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

// Data
const account = ref({})
const loading = ref(true)
const showTransactionModal = ref(false)

// Sample recent transactions (replace with actual API call)
const recentTransactions = ref([
  {
    id: 1,
    description: 'Sales Revenue',
    amount: 50000,
    type: 'credit',
    date: new Date().toISOString()
  },
  {
    id: 2,
    description: 'Office Supplies',
    amount: 1500,
    type: 'debit',
    date: new Date(Date.now() - 86400000).toISOString()
  },
  {
    id: 3,
    description: 'Client Payment',
    amount: 25000,
    type: 'credit',
    date: new Date(Date.now() - 172800000).toISOString()
  }
])

// Computed properties
const balanceDifference = computed(() => {
  return (parseFloat(account.value.current_balance) || 0) - (parseFloat(account.value.opening_balance) || 0)
})

const balancePercentage = computed(() => {
  const opening = parseFloat(account.value.opening_balance) || 1
  const difference = balanceDifference.value
  return ((difference / opening) * 100).toFixed(1)
})

const balanceHealthPercentage = computed(() => {
  const balance = parseFloat(account.value.current_balance) || 0
  if (balance > 0) return Math.min((balance / 100000) * 100, 100) // Scale for demo
  return 0
})

const getBalanceHealth = computed(() => {
  const balance = parseFloat(account.value.current_balance) || 0
  if (balance > 50000) return 'Excellent'
  if (balance > 10000) return 'Good'
  if (balance > 0) return 'Fair'
  if (balance === 0) return 'Zero'
  return 'Negative'
})

const getBalanceHealthColor = computed(() => {
  const health = getBalanceHealth.value
  switch (health) {
    case 'Excellent': return 'text-green-600'
    case 'Good': return 'text-blue-600'
    case 'Fair': return 'text-yellow-600'
    case 'Zero': return 'text-gray-600'
    default: return 'text-red-600'
  }
})

const getBalanceHealthBarColor = computed(() => {
  const health = getBalanceHealth.value
  switch (health) {
    case 'Excellent': return 'bg-green-500'
    case 'Good': return 'bg-blue-500'
    case 'Fair': return 'bg-yellow-500'
    case 'Zero': return 'bg-gray-500'
    default: return 'bg-red-500'
  }
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

const formatAccountType = (type) => {
  if (!type) return 'Unknown'
  return type.charAt(0).toUpperCase() + type.slice(1)
}

const getAccountTypeClass = (type) => {
  switch (type?.toLowerCase()) {
    case 'cash':
      return 'bg-green-100 text-green-800'
    case 'bank':
      return 'bg-blue-100 text-blue-800'
    case 'digital':
      return 'bg-purple-100 text-purple-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getAccountTypeIcon = (type) => {
  switch (type?.toLowerCase()) {
    case 'cash':
      return 'fas fa-money-bill-wave text-green-500'
    case 'bank':
      return 'fas fa-university text-blue-500'
    case 'digital':
      return 'fas fa-mobile-alt text-purple-500'
    default:
      return 'fas fa-wallet text-gray-500'
  }
}

const getBalanceStatus = (balance) => {
  const numBalance = parseFloat(balance) || 0
  if (numBalance > 0) return 'Positive'
  if (numBalance < 0) return 'Negative'
  return 'Zero Balance'
}

const getBalanceStatusClass = (balance) => {
  const numBalance = parseFloat(balance) || 0
  if (numBalance > 0) return 'bg-green-50 border border-green-200'
  if (numBalance < 0) return 'bg-red-50 border border-red-200'
  return 'bg-gray-50 border border-gray-200'
}

const getBalanceStatusIcon = (balance) => {
  const numBalance = parseFloat(balance) || 0
  if (numBalance > 0) return 'fas fa-arrow-up text-green-500'
  if (numBalance < 0) return 'fas fa-arrow-down text-red-500'
  return 'fas fa-minus text-gray-500'
}

const fetchAccount = async () => {
  try {
    loading.value = true
    const response = await axios.get(`/api/cash-banks/${route.params.id}`)
    account.value = response.data
  } catch (error) {
    console.error('Error fetching account:', error)
    showErrorAlert('Error', 'Failed to load account details')
    router.push({ name: 'cash-banks.index' })
  } finally {
    loading.value = false
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
      await showSuccessAlert('Deleted!', 'Account has been deleted successfully.')
      
      router.push({ name: 'cash-banks.index' })
      
    } catch (error) {
      closeAlert()
      console.error('Error deleting account:', error)
      showErrorAlert('Error', 'Failed to delete account. Please try again.')
    }
  }
}

const addSampleTransaction = () => {
  // Add sample transaction logic here
  showTransactionModal.value = false
  showSuccessAlert('Success', 'Sample transaction added!')
}

const viewTransactions = () => {
  showSuccessAlert('Info', 'Transactions page will be implemented soon!')
}

// Lifecycle
onMounted(() => {
  fetchAccount()
})
</script>